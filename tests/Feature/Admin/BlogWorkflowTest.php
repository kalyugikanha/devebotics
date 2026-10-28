<?php

namespace Tests\Feature\Admin;

use App\Enums\PostStatus;
use App\Filament\Resources\BlogPosts\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPosts\Pages\EditBlogPost;
use App\Filament\Resources\BlogPosts\Pages\ListBlogPosts;
use App\Filament\Resources\BlogPosts\Pages\ViewBlogPost;
use App\Models\BlogPost;
use App\Models\User;
use App\Support\Settings\Settings;
use Filament\Actions\Testing\TestAction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class BlogWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_creates_a_draft_attributed_to_their_own_author_profile(): void
    {
        $author = User::factory()->withRole('Blog Author')->create(['name' => 'Ada Writer']);

        $this->actingAs($author);

        Livewire::test(CreateBlogPost::class)
            ->fillForm([
                'title' => 'Designing resilient APIs',
                'slug' => 'designing-resilient-apis',
                'excerpt' => 'How we approach API design for long-lived systems.',
                'body' => '<p>Body text for the article.</p>',
                'status' => PostStatus::Draft->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $post = BlogPost::query()->where('slug', 'designing-resilient-apis')->firstOrFail();

        $this->assertSame(PostStatus::Draft, $post->status);
        $this->assertSame('Ada Writer', $post->author->name);
        $this->assertSame($author->id, $post->author->user_id);
    }

    public function test_author_cannot_publish_through_a_tampered_status_payload(): void
    {
        $author = User::factory()->withRole('Blog Author')->create();

        $this->actingAs($author);

        Livewire::test(CreateBlogPost::class)
            ->fillForm([
                'title' => 'Sneaky publish',
                'slug' => 'sneaky-publish',
                'excerpt' => 'Trying to bypass the review workflow.',
                'body' => '<p>Body</p>',
                'status' => PostStatus::Published->value,
            ])
            ->call('create')
            ->assertHasFormErrors(['status']);

        $this->assertDatabaseMissing('blog_posts', ['slug' => 'sneaky-publish']);
    }

    public function test_author_cannot_publish_own_draft_when_editing(): void
    {
        $author = User::factory()->withRole('Blog Author')->create();
        $this->actingAs($author);

        $post = BlogPost::factory()->draft()->create(['created_by' => $author->id]);

        Livewire::test(EditBlogPost::class, ['record' => $post->getRouteKey()])
            ->fillForm(['status' => PostStatus::Published->value])
            ->call('save')
            ->assertHasFormErrors(['status']);

        $this->assertSame(PostStatus::Draft, $post->refresh()->status);
    }

    public function test_model_guard_blocks_publishing_without_permission(): void
    {
        $editor = User::factory()->withRole('Blog Author')->create();
        $post = BlogPost::factory()->draft()->create(['created_by' => $editor->id]);

        $this->actingAs($editor);
        $this->expectException(AuthorizationException::class);

        $post->update(['status' => PostStatus::Published]);
    }

    public function test_authors_only_see_their_own_posts(): void
    {
        $author = User::factory()->withRole('Blog Author')->create();
        $mine = BlogPost::factory()->draft()->create(['created_by' => $author->id]);
        $theirs = BlogPost::factory()->create();

        $this->actingAs($author);

        Livewire::test(ListBlogPosts::class)
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$theirs]);

        $this->get(route('filament.admin.resources.blog-posts.edit', $theirs))->assertNotFound();
    }

    public function test_full_review_and_publish_workflow(): void
    {
        $author = User::factory()->withRole('Blog Author')->create();
        $editor = User::factory()->withRole('Blog Editor')->create();
        $post = BlogPost::factory()->draft()->create(['created_by' => $author->id]);

        $this->actingAs($author);
        Livewire::test(ViewBlogPost::class, ['record' => $post->getRouteKey()])
            ->assertActionHidden('publishNow')
            ->callAction('submitForReview');

        $this->assertSame(PostStatus::InReview, $post->refresh()->status);
        $this->assertNotNull($post->submitted_at);
        $this->assertSame(1, $editor->notifications()->count(), 'Reviewers are notified of submissions.');

        // The author can no longer edit while the post is in review.
        $this->get(route('filament.admin.resources.blog-posts.edit', $post))->assertForbidden();

        $this->actingAs($editor);
        Livewire::test(EditBlogPost::class, ['record' => $post->getRouteKey()])
            ->callAction('requestChanges', data: ['notes' => 'Please add a conclusion.']);

        $post->refresh();
        $this->assertSame(PostStatus::Draft, $post->status);
        $this->assertSame('Please add a conclusion.', $post->review_notes);
        $this->assertSame(1, $author->notifications()->count());

        $this->actingAs($author);
        Livewire::test(EditBlogPost::class, ['record' => $post->getRouteKey()])->callAction('submitForReview');
        $this->assertNull($post->refresh()->reviewed_at, 'Resubmitting starts a fresh review cycle.');

        $this->actingAs($editor);
        Livewire::test(EditBlogPost::class, ['record' => $post->getRouteKey()])
            ->callAction('approve', data: ['notes' => 'Looks great'])
            ->callAction('schedule', data: ['published_at' => now()->addDays(2)->format('Y-m-d H:i:s')]);

        $post->refresh();
        $this->assertSame(PostStatus::Scheduled, $post->status);
        $this->assertSame($editor->id, $post->reviewed_by);
        $this->assertFalse($post->isPubliclyVisible());

        Livewire::test(EditBlogPost::class, ['record' => $post->getRouteKey()])->callAction('publishNow');
        $this->assertTrue($post->refresh()->isPubliclyVisible());
    }

    public function test_seo_manager_cannot_change_post_content(): void
    {
        $seo = User::factory()->withRole('SEO Manager')->create();
        $post = BlogPost::factory()->create(['title' => 'Original']);

        $this->actingAs($seo);

        Livewire::test(EditBlogPost::class, ['record' => $post->getRouteKey()])
            ->assertFormFieldDisabled('title')
            ->fillForm(['title' => 'Changed', 'seo.meta_title' => 'Optimised title'])
            ->call('save')
            ->assertHasNoFormErrors();

        $post->refresh();
        $this->assertSame('Original', $post->title);
        $this->assertSame('Optimised title', $post->seo?->meta_title);
    }

    public function test_ai_outline_inserts_a_draft_outline_without_changing_status(): void
    {
        config(['softec.ai.anthropic_api_key' => 'test-key', 'softec.ai.anthropic_model' => 'claude-test']);
        app(Settings::class)->set('integrations', 'ai_drafting_enabled', true);

        Http::fake([
            'api.anthropic.com/*' => Http::response(['content' => [['type' => 'text', 'text' => "## Why it matters\n\n- Point one\n\n<script>alert(1)</script>"]]]),
        ]);

        $author = User::factory()->withRole('Blog Author')->create();
        $this->actingAs($author);
        $post = BlogPost::factory()->draft()->create(['created_by' => $author->id]);

        Livewire::test(EditBlogPost::class, ['record' => $post->getRouteKey()])
            ->callAction(TestAction::make('aiOutline')->schemaComponent('body'), data: ['topic' => 'Edge AI'])
            ->call('save')
            ->assertHasNoFormErrors();

        $post->refresh();
        $this->assertStringContainsString('Why it matters', $post->body);
        $this->assertStringNotContainsString('<script', $post->body);
        $this->assertTrue($post->is_ai_assisted);
        $this->assertSame(PostStatus::Draft, $post->status);

        Http::assertSent(fn ($request) => $request->hasHeader('x-api-key', 'test-key')
            && $request['model'] === 'claude-test'
            && str_contains($request['messages'][0]['content'], 'Edge AI'));
    }

    public function test_ai_outline_is_hidden_when_not_configured(): void
    {
        config(['softec.ai.anthropic_api_key' => null]);
        $author = User::factory()->withRole('Blog Author')->create();
        $this->actingAs($author);

        Livewire::test(CreateBlogPost::class)
            ->assertActionDoesNotExist(TestAction::make('aiOutline')->schemaComponent('body'));
    }
}
