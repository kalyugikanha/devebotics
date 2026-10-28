<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Enums\PostStatus;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Services\Blog\BlogWorkflow;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateBlogPost extends CreateRecord
{
    protected static string $resource = BlogPostResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        $workflow = app(BlogWorkflow::class);

        $status = $data['status'] ?? PostStatus::Draft;
        $status = $status instanceof PostStatus ? $status : (PostStatus::tryFrom((string) $status) ?? PostStatus::Draft);

        // Page-level guard (the model enforces the same rule as a last line of defence).
        if (! in_array($status, $workflow->allowedStatuses($user), true)) {
            throw ValidationException::withMessages(['data.status' => 'You are not allowed to set this status.']);
        }

        // Authors always write as themselves; editors may pick another author.
        if (blank($data['author_id'] ?? null) || ! $user->canAny(['blogs.edit', 'blogs.publish'])) {
            $data['author_id'] = $workflow->authorFor($user)->getKey();
        }

        $data['is_ai_assisted'] = (bool) ($data['is_ai_assisted'] ?? false);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
