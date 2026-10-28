<?php

namespace Tests\Feature\Admin;

use App\Enums\CommentStatus;
use App\Filament\Resources\BlogComments\Pages\ManageBlogComments;
use App\Models\BlogComment;
use App\Models\BlogPost;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BlogCommentModerationTest extends TestCase
{
    use RefreshDatabase;

    private function comment(BlogPost $post): BlogComment
    {
        return $post->comments()->create([
            'name' => 'Reader',
            'email' => 'reader@example.test',
            'body' => 'Great article, thanks!',
            'status' => CommentStatus::Pending,
        ]);
    }

    public function test_moderator_approves_and_marks_comments_as_spam(): void
    {
        $moderator = User::factory()->withRole('Reviewer / Moderator')->create();
        $post = BlogPost::factory()->create();
        $first = $this->comment($post);
        $second = $this->comment($post);

        $this->actingAs($moderator);

        Livewire::test(ManageBlogComments::class)
            ->assertCanSeeTableRecords([$first, $second])
            ->callAction(TestAction::make('approve')->table($first))
            ->callAction(TestAction::make('spam')->table($second));

        $this->assertSame(CommentStatus::Approved, $first->refresh()->status);
        $this->assertSame($moderator->id, $first->moderated_by);
        $this->assertSame(CommentStatus::Spam, $second->refresh()->status);
    }

    public function test_bulk_approval(): void
    {
        $moderator = User::factory()->withRole('Reviewer / Moderator')->create();
        $post = BlogPost::factory()->create();
        $comments = collect([$this->comment($post), $this->comment($post)]);

        $this->actingAs($moderator);

        Livewire::test(ManageBlogComments::class)
            ->callTableBulkAction('approveSelected', $comments);

        $comments->each(fn (BlogComment $c) => $this->assertSame(CommentStatus::Approved, $c->refresh()->status));
    }

    public function test_users_without_moderation_permission_are_forbidden(): void
    {
        $author = User::factory()->withRole('Blog Author')->create();

        $this->actingAs($author)->get('/admin/blog-comments')->assertForbidden();
    }
}
