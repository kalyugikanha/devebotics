<?php

namespace App\Policies;

use App\Enums\PostStatus;
use App\Models\BlogPost;
use App\Models\User;

/**
 * Editorial workflow:
 * - Authors (blogs.create) write and edit their own drafts and submit them for review.
 * - Editors (blogs.edit) edit anyone's posts; reviewers (blogs.review) leave feedback.
 * - Publishers (blogs.publish) schedule, publish and unpublish.
 * - SEO managers (seo.manage) may edit SEO metadata only.
 */
class BlogPostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny(['blogs.view', 'blogs.create', 'blogs.review', 'blogs.publish', 'seo.manage']);
    }

    public function view(User $user, BlogPost $post): bool
    {
        return $user->canAny(['blogs.view', 'blogs.review', 'blogs.publish', 'seo.manage']) || $this->owns($user, $post);
    }

    public function create(User $user): bool
    {
        return $user->can('blogs.create');
    }

    public function update(User $user, BlogPost $post): bool
    {
        if ($user->canAny(['blogs.edit', 'blogs.publish', 'seo.manage'])) {
            return true;
        }

        return $user->can('blogs.create') && $this->owns($user, $post) && $post->status === PostStatus::Draft;
    }

    /**
     * Content fields (title, body, ...) — SEO-only users are excluded.
     */
    public function editContent(User $user, BlogPost $post): bool
    {
        if ($user->canAny(['blogs.edit', 'blogs.publish'])) {
            return true;
        }

        return $user->can('blogs.create') && $this->owns($user, $post) && $post->status === PostStatus::Draft;
    }

    public function review(User $user, BlogPost $post): bool
    {
        return $user->can('blogs.review') && $post->status === PostStatus::InReview;
    }

    public function publish(User $user, ?BlogPost $post = null): bool
    {
        return $user->can('blogs.publish');
    }

    public function delete(User $user, BlogPost $post): bool
    {
        if ($user->can('blogs.delete')) {
            return true;
        }

        return $user->can('blogs.create') && $this->owns($user, $post) && $post->status === PostStatus::Draft && $post->published_at === null;
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('blogs.delete');
    }

    public function restore(User $user, BlogPost $post): bool
    {
        return $user->can('blogs.delete');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('blogs.delete');
    }

    public function forceDelete(User $user, BlogPost $post): bool
    {
        return $user->can('blogs.delete');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('blogs.delete');
    }

    public function owns(User $user, BlogPost $post): bool
    {
        return $post->created_by === $user->getKey()
            || ($post->author_id !== null && $user->author?->getKey() === $post->author_id);
    }
}
