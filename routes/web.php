<?php

use App\Http\Controllers\BlogCommentController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BrochureController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CookieConsentController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FileDownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\ThemePreviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/industries', [IndustryController::class, 'index'])->name('industries.index');
Route::get('/industries/{slug}', [IndustryController::class, 'show'])->name('industries.show');

Route::get('/case-studies', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/case-studies/{slug}', [PortfolioController::class, 'show'])->name('portfolio.show');

Route::prefix('blog')->name('blog.')->group(function (): void {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/category/{slug}', [BlogController::class, 'category'])->name('category');
    Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('tag');
    Route::get('/author/{slug}', [BlogController::class, 'author'])->name('author');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
    Route::post('/{slug}/comments', [BlogCommentController::class, 'store'])->middleware('throttle:public-forms')->name('comments.store');
});

Route::get('/testimonials', [TestimonialController::class, 'index'])->name('testimonials.index');
Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
Route::post('/feedback', [FeedbackController::class, 'store'])->middleware('throttle:public-forms')->name('feedback.store');
Route::get('/feedback/verify/{testimonial}', [FeedbackController::class, 'verify'])->middleware(['signed', 'throttle:20,1'])->name('feedback.verify');

Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::get('/start-a-project', [ContactController::class, 'project'])->name('project.create');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:public-forms')->name('contact.store');

Route::get('/careers', [CareerController::class, 'index'])->name('careers.index');
Route::get('/careers/apply', [CareerController::class, 'general'])->name('careers.general');
Route::get('/careers/{slug}', [CareerController::class, 'show'])->name('careers.show');
Route::post('/careers/apply/{slug?}', [JobApplicationController::class, 'store'])->middleware('throttle:public-forms')->name('careers.apply');

Route::get('/team', [TeamController::class, 'index'])->name('team.index');
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');
Route::get('/search', SearchController::class)->middleware('throttle:search')->name('search');
Route::get('/company-profile', BrochureController::class)->middleware('throttle:downloads')->name('brochure');

Route::post('/newsletter', [NewsletterController::class, 'store'])->middleware('throttle:newsletter')->name('newsletter.store');
Route::get('/newsletter/confirm/{subscriber}', [NewsletterController::class, 'confirm'])->middleware('signed')->name('newsletter.confirm');
Route::get('/newsletter/unsubscribe/{subscriber}', [NewsletterController::class, 'unsubscribe'])->middleware('signed')->name('newsletter.unsubscribe');

Route::post('/cookie-consent', CookieConsentController::class)->middleware('throttle:30,1')->name('cookie-consent');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemaps/{type}.xml', [SitemapController::class, 'show'])->where('type', '[a-z\-]+')->name('sitemap.type');
Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/llms.txt', fn () => response()->file(public_path('llms.txt'), ['Content-Type' => 'text/plain; charset=UTF-8']))->name('llms');

/*
|--------------------------------------------------------------------------
| Authenticated helpers (admin session required)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function (): void {
    Route::get('/preview/{type}/{id}', PreviewController::class)->middleware('signed')->name('preview.show');

    Route::get('/theme-preview/start', [ThemePreviewController::class, 'start'])->name('theme-preview.start');
    Route::get('/theme-preview/stop', [ThemePreviewController::class, 'stop'])->name('theme-preview.stop');

    Route::middleware('throttle:downloads')->prefix('admin/files')->name('files.')->group(function (): void {
        Route::get('/inquiries/{inquiry}/attachment', [FileDownloadController::class, 'inquiryAttachment'])->name('inquiry-attachment');
        Route::get('/applications/{application}/resume', [FileDownloadController::class, 'resume'])->name('resume');
        Route::get('/testimonials/{testimonial}/photo', [FileDownloadController::class, 'testimonialPhoto'])->name('testimonial-photo');
    });
});

/*
|--------------------------------------------------------------------------
| Local development helpers
|--------------------------------------------------------------------------
*/

if (app()->isLocal()) {
    Route::view('/dev/illustrations', 'dev.illustrations')->name('dev.illustrations');
}

/*
|--------------------------------------------------------------------------
| CMS pages (must stay last)
|--------------------------------------------------------------------------
*/

Route::get('/{slug}', [PageController::class, 'show'])->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('pages.show');
