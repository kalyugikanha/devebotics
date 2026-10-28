<?php

namespace Tests\Feature\Admin;

use App\Models\Author;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\ContactInquiry;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke test: every blog / reviews / CRM / careers admin page renders for a super administrator.
 */
class OperationsAdminPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_operations_pages_render(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $post = BlogPost::factory()->create();
        BlogCategory::factory()->create();
        Author::factory()->create();
        $testimonial = Testimonial::factory()->create();
        $inquiry = ContactInquiry::factory()->create();
        $job = JobOpening::factory()->create();
        $application = JobApplication::factory()->create(['job_opening_id' => $job->id]);

        $this->actingAs($admin);

        $urls = [
            '/admin/blog-posts', '/admin/blog-posts/create', "/admin/blog-posts/{$post->id}", "/admin/blog-posts/{$post->id}/edit",
            '/admin/blog-categories', '/admin/blog-tags', '/admin/authors', '/admin/blog-comments',
            '/admin/testimonials', '/admin/testimonials/create', "/admin/testimonials/{$testimonial->id}", "/admin/testimonials/{$testimonial->id}/edit",
            '/admin/contact-inquiries', "/admin/contact-inquiries/{$inquiry->id}", '/admin/newsletter-subscribers',
            '/admin/job-openings', '/admin/job-openings/create', "/admin/job-openings/{$job->id}/edit",
            '/admin/job-applications', "/admin/job-applications/{$application->id}",
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }
}
