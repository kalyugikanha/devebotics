<?php

namespace App\Http\Controllers;

use App\Enums\CommentStatus;
use App\Enums\ConsentType;
use App\Http\Requests\BlogCommentRequest;
use App\Models\BlogPost;
use App\Support\Forms\ConsentRecorder;
use App\Support\Forms\SpamGuard;
use Illuminate\Http\RedirectResponse;

/**
 * Comments are always held for moderation before they appear.
 */
class BlogCommentController extends Controller
{
    public function store(BlogCommentRequest $request, string $slug, ConsentRecorder $consents): RedirectResponse
    {
        abort_unless(settings('features.blog_comments_enabled'), 404);

        $post = BlogPost::query()->published()->where('slug', $slug)->firstOrFail();
        abort_unless($post->allow_comments, 403);

        $parentId = $request->integer('parent_id') ?: null;

        if ($parentId !== null && ! $post->comments()->approved()->whereKey($parentId)->exists()) {
            $parentId = null;
        }

        $comment = $post->comments()->create([
            'parent_id' => $parentId,
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'body' => strip_tags((string) $request->validated('body')),
            'status' => CommentStatus::Pending,
            'ip_hash' => SpamGuard::hashIp($request->ip()),
        ]);

        $consents->record(ConsentType::PrivacyPolicy, true, $comment, $request);

        return redirect()->to($post->publicUrl().'#comments')->with('comment_status', 'Thank you! Your comment will appear after moderation.');
    }
}
