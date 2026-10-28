<?php

namespace App\Support\Media;

use App\Models\Media;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Finds every record that references a media library item, so files in use
 * are never deleted (which would silently break pages or galleries).
 */
class MediaUsage
{
    /**
     * table => [label, title column, media FK columns...]
     *
     * @var array<string, array{0: string, 1: string, 2: array<int, string>}>
     */
    public const REFERENCES = [
        'services' => ['Service', 'title', ['hero_media_id']],
        'industries' => ['Industry', 'name', ['hero_media_id']],
        'products' => ['Product', 'name', ['logo_media_id', 'hero_media_id']],
        'portfolios' => ['Case study', 'title', ['hero_media_id']],
        'pages' => ['Page', 'title', ['hero_media_id']],
        'page_sections' => ['Page section', 'title', ['media_id']],
        'technologies' => ['Technology', 'name', ['logo_media_id']],
        'clients' => ['Client', 'name', ['logo_media_id']],
        'partners' => ['Partner', 'name', ['logo_media_id']],
        'team_members' => ['Team member', 'name', ['photo_media_id']],
        'achievements' => ['Achievement', 'title', ['media_id']],
        'authors' => ['Author', 'name', ['avatar_media_id']],
        'blog_posts' => ['Blog post', 'title', ['featured_media_id']],
        'testimonials' => ['Testimonial', 'client_name', ['photo_media_id']],
        'seo_meta' => ['SEO social image', 'meta_title', ['og_image_id']],
        'media_attachments' => ['Gallery item', 'caption', ['media_id']],
    ];

    /**
     * Human-readable list of places using the media item (empty when unused).
     *
     * @return array<int, string>
     */
    public function usages(Media $media, int $limit = 10): array
    {
        $usages = [];

        foreach (self::REFERENCES as $table => [$label, $titleColumn, $columns]) {
            $rows = DB::table($table)
                ->where(function ($query) use ($columns, $media): void {
                    foreach ($columns as $column) {
                        $query->orWhere($column, $media->getKey());
                    }
                })
                ->limit($limit)
                ->get(['id', $titleColumn]);

            foreach ($rows as $row) {
                $title = trim((string) ($row->{$titleColumn} ?? ''));
                $usages[] = $label.' #'.$row->id.($title !== '' ? ' — '.Str::limit($title, 60) : '');
            }
        }

        foreach (Setting::query()->where('key', 'like', '%media_id')->get(['group', 'key', 'value']) as $setting) {
            if ((string) $setting->value === (string) $media->getKey()) {
                $usages[] = 'Setting: '.Str::headline($setting->group).' › '.Str::headline(Str::beforeLast($setting->key, '_media_id'));
            }
        }

        return array_slice($usages, 0, $limit);
    }

    public function isUsed(Media $media): bool
    {
        return $this->usages($media, 1) !== [];
    }
}
