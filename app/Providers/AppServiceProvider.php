<?php

namespace App\Providers;

use App\Models;
use App\Observers\FlushSiteCache;
use App\Policies\RolePolicy;
use App\Support\Art\TechLogoSprite;
use App\Support\Settings\Settings;
use App\Support\Theme\MotionConfig;
use App\Support\Theme\ThemeManager;
use App\View\Composers\LayoutComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Scoped (not singleton) so queue workers re-read settings for every job.
        $this->app->scoped(Settings::class);
        $this->app->scoped(ThemeManager::class);
        $this->app->scoped(MotionConfig::class);
        $this->app->scoped(TechLogoSprite::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureModels();
        $this->configureAuthorization();
        $this->configureRateLimiting();

        View::composer('components.layout', LayoutComposer::class);

        // Default error bag for views rendered outside the session middleware
        // (e.g. 404/410 raised before routing). ShareErrorsFromSession overrides it.
        View::share('errors', new ViewErrorBag);

        foreach ([
            Models\NavigationMenu::class, Models\NavigationItem::class, Models\Service::class, Models\ServiceCategory::class,
            Models\Page::class, Models\Product::class, Models\Industry::class, Models\Portfolio::class, Models\BlogPost::class,
            Models\BlogCategory::class, Models\JobOpening::class, Models\Author::class, Models\SeoMeta::class,
        ] as $model) {
            $model::observe(FlushSiteCache::class);
        }

        Password::defaults(fn () => $this->app->isProduction()
            ? Password::min(12)->mixedCase()->numbers()->symbols()->uncompromised()
            : Password::min(12)->mixedCase()->numbers());

        if (filled($proxies = config('softec.trusted_proxies'))) {
            TrustProxies::at($proxies === '*' ? '*' : array_map('trim', explode(',', (string) $proxies)));
        }

        if (config('softec.force_https')) {
            URL::forceHttps();
        }
    }

    private function configureModels(): void
    {
        // Stable, short polymorphic type names that survive class renames.
        Relation::enforceMorphMap([
            'user' => Models\User::class,
            'role' => Role::class,
            'permission' => Permission::class,
            'media' => Models\Media::class,
            'page' => Models\Page::class,
            'page_section' => Models\PageSection::class,
            'announcement' => Models\Announcement::class,
            'navigation_menu' => Models\NavigationMenu::class,
            'navigation_item' => Models\NavigationItem::class,
            'redirect' => Models\Redirect::class,
            'technology' => Models\Technology::class,
            'service_category' => Models\ServiceCategory::class,
            'service' => Models\Service::class,
            'industry' => Models\Industry::class,
            'product_category' => Models\ProductCategory::class,
            'product' => Models\Product::class,
            'product_plan' => Models\ProductPlan::class,
            'client' => Models\Client::class,
            'partner' => Models\Partner::class,
            'team_member' => Models\TeamMember::class,
            'milestone' => Models\Milestone::class,
            'achievement' => Models\Achievement::class,
            'statistic' => Models\Statistic::class,
            'faq_category' => Models\FaqCategory::class,
            'faq' => Models\Faq::class,
            'portfolio_category' => Models\PortfolioCategory::class,
            'portfolio' => Models\Portfolio::class,
            'author' => Models\Author::class,
            'blog_category' => Models\BlogCategory::class,
            'blog_tag' => Models\BlogTag::class,
            'blog_post' => Models\BlogPost::class,
            'blog_comment' => Models\BlogComment::class,
            'testimonial' => Models\Testimonial::class,
            'contact_inquiry' => Models\ContactInquiry::class,
            'team' => Models\Team::class,
            'newsletter_subscriber' => Models\NewsletterSubscriber::class,
            'job_opening' => Models\JobOpening::class,
            'job_application' => Models\JobApplication::class,
            'email_template' => Models\EmailTemplate::class,
        ]);

        // Surface mass-assignment and N+1 mistakes during development and tests.
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation): void {
            logger()->warning('Lazy loading ['.$relation.'] on ['.$model::class.'].');
        });
    }

    private function configureAuthorization(): void
    {
        Gate::policy(Role::class, RolePolicy::class);

        // Super Administrators hold every permission string ("module.action").
        // Policy-level guard rails (e.g. never delete the last super admin or
        // yourself) still run because policy abilities contain no dot.
        Gate::before(function (Models\User $user, string $ability): ?bool {
            if (str_contains($ability, '.') && $user->is_active && $user->isSuperAdmin()) {
                return true;
            }

            return null;
        });
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('public-forms', fn (Request $request) => [
            Limit::perMinute(5)->by('form-minute:'.$request->ip()),
            Limit::perDay(30)->by('form-day:'.$request->ip()),
        ]);

        RateLimiter::for('newsletter', fn (Request $request) => Limit::perHour(5)->by('newsletter:'.$request->ip()));

        RateLimiter::for('search', fn (Request $request) => Limit::perMinute(30)->by('search:'.$request->ip()));

        RateLimiter::for('downloads', fn (Request $request) => Limit::perMinute(30)->by('downloads:'.($request->user()?->getKey() ?? $request->ip())));
    }
}
