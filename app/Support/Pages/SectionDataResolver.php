<?php

namespace App\Support\Pages;

use App\Enums\SectionType;
use App\Models\Achievement;
use App\Models\BlogPost;
use App\Models\Client;
use App\Models\Faq;
use App\Models\Industry;
use App\Models\Milestone;
use App\Models\PageSection;
use App\Models\Partner;
use App\Models\Portfolio;
use App\Models\Product;
use App\Models\Service;
use App\Models\Statistic;
use App\Models\TeamMember;
use App\Models\Technology;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Loads the records each section type needs (eager loaded, limited) and
 * decides whether a section has anything to show. Sections backed by
 * verified data (stats, reviews, certifications, logos) disappear when empty.
 */
class SectionDataResolver
{
    /**
     * @param  Collection<int, PageSection>  $sections
     * @return Collection<int, array{section: PageSection, records: mixed}>
     */
    public function resolve(Collection $sections): Collection
    {
        return $sections
            ->filter(fn (PageSection $section) => $section->is_enabled && $section->type !== null)
            ->map(fn (PageSection $section) => ['section' => $section, 'records' => $this->records($section)])
            ->filter(fn (array $item) => $this->hasContent($item['section'], $item['records']))
            ->values();
    }

    public function records(PageSection $section): mixed
    {
        $limit = max(1, min(24, (int) $section->setting('limit', 6)));

        return match ($section->type) {
            SectionType::Hero => Technology::query()->with('logo')->active()->where('show_on_home', true)->ordered()->limit(8)->get(),
            SectionType::Stats => Statistic::query()->publishable()->ordered()->get(),
            SectionType::Logos => $this->logos((string) $section->setting('source', 'clients'), $limit),
            SectionType::Partners => Partner::query()->publiclyListed()->with('logo')->ordered()->limit($limit)->get(),
            SectionType::Services => Service::query()->published()->with(['category', 'heroMedia'])
                ->when($section->setting('mode', 'featured') === 'featured', fn (Builder $q) => $q->orderByDesc('is_featured'))
                ->ordered()->limit($limit)->get(),
            SectionType::Products => Product::query()->published()->with(['category', 'logo', 'heroMedia'])
                ->orderByDesc('is_featured')->ordered()->limit($limit)->get(),
            SectionType::Industries => Industry::query()->published()
                ->with(['heroMedia', 'services' => fn ($q) => $q->published()->ordered()->limit(4)])
                ->orderByDesc('is_featured')->ordered()->limit($limit)->get(),
            SectionType::TechStack => Technology::query()->with('logo')->active()->where('show_on_home', true)->ordered()->get()->groupBy(fn (Technology $t) => $t->category->getLabel()),
            SectionType::CaseStudies => Portfolio::query()->published()->with(['industry', 'category', 'heroMedia', 'client'])
                ->orderByDesc('is_featured')->latest('published_at')->limit($limit)->get(),
            SectionType::Testimonials => Testimonial::query()->publiclyVisible()->with(['photo', 'service'])
                ->orderByDesc('is_featured')->ordered()->limit($limit)->get(),
            SectionType::Blog => BlogPost::query()->published()->with(['author', 'category', 'featuredMedia'])
                ->latest('published_at')->limit($limit)->get(),
            SectionType::Faq => Faq::query()->active()
                ->when(
                    filled($section->setting('faq_category')),
                    fn (Builder $q) => $q->whereHas('category', fn (Builder $c) => $c->where('slug', $section->setting('faq_category'))),
                    fn (Builder $q) => $q->where('show_on_home', true),
                )
                ->ordered()->limit($limit)->get(),
            SectionType::Team => TeamMember::query()->active()->with('photo')
                ->when((bool) $section->setting('leadership_only', false), fn (Builder $q) => $q->where('is_leadership', true))
                ->ordered()->limit($limit)->get(),
            SectionType::Milestones => Milestone::query()->active()->ordered()->get(),
            SectionType::Certifications => Achievement::query()->publishable()->with('media')->ordered()->get(),
            SectionType::WhyChoose, SectionType::Process, SectionType::Features, SectionType::Values, SectionType::EngagementModels => collect((array) $section->setting('items', []))
                ->filter(fn ($item) => is_array($item) && filled($item['title'] ?? null))
                ->values(),
            default => null,
        };
    }

    private function hasContent(PageSection $section, mixed $records): bool
    {
        if ($section->type === SectionType::Hero) {
            return true;
        }

        if ($records instanceof Collection) {
            return $records->isNotEmpty() || ($section->type === SectionType::Values && filled($section->setting('mission')));
        }

        return match ($section->type) {
            SectionType::RichText => filled($section->content),
            SectionType::Statement => filled($section->title),
            SectionType::MediaText => filled($section->content) || $section->media_id !== null,
            default => true,
        };
    }

    /**
     * @return Collection<int, Client|Partner>
     */
    private function logos(string $source, int $limit): Collection
    {
        $clients = in_array($source, ['clients', 'both'], true)
            ? Client::query()->publiclyListed()->where('show_on_home', true)->with('logo')->ordered()->limit($limit)->get()
            : collect();

        $partners = in_array($source, ['partners', 'both'], true)
            ? Partner::query()->publiclyListed()->with('logo')->ordered()->limit($limit)->get()
            : collect();

        return $clients->toBase()->merge($partners)->take($limit)->values();
    }
}
