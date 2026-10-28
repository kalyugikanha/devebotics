<?php

namespace Tests\Feature\Admin;

use App\Enums\PostStatus;
use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishScheduledPostsTest extends TestCase
{
    use RefreshDatabase;

    public function test_due_scheduled_posts_are_published_and_future_ones_are_left_alone(): void
    {
        $due = BlogPost::factory()->create(['status' => PostStatus::Scheduled, 'published_at' => now()->addMinutes(5)]);
        $future = BlogPost::factory()->scheduled()->create();

        $this->travel(10)->minutes();

        $this->artisan('softec:publish-scheduled')->assertSuccessful();

        $this->assertSame(PostStatus::Published, $due->refresh()->status);
        $this->assertSame(PostStatus::Scheduled, $future->refresh()->status);

        // Idempotent: a second run changes nothing.
        $this->artisan('softec:publish-scheduled')->expectsOutputToContain('No scheduled posts are due')->assertSuccessful();
    }
}
