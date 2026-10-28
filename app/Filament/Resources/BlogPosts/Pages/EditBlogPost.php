<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Enums\PostStatus;
use App\Filament\Actions\RevisionsAction;
use App\Filament\Resources\BlogPosts\Actions\BlogWorkflowActions;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Filament\Tables\ContentTable;
use App\Models\BlogPost;
use App\Services\Blog\BlogWorkflow;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EditBlogPost extends EditRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ContentTable::viewOnSite(),
            ContentTable::preview(),
            ActionGroup::make(BlogWorkflowActions::all())->label('Workflow')->icon('heroicon-m-arrow-path')->button()->color('gray'),
            RevisionsAction::make(),
            ActionGroup::make([
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var BlogPost $record */
        $record = $this->getRecord();
        $user = Auth::user();

        if (array_key_exists('status', $data)) {
            $status = $data['status'] instanceof PostStatus ? $data['status'] : PostStatus::tryFrom((string) $data['status']);

            if ($status !== $record->status && ! in_array($status, app(BlogWorkflow::class)->allowedStatuses($user), true)) {
                throw ValidationException::withMessages(['data.status' => 'You are not allowed to set this status.']);
            }
        }

        if (array_key_exists('author_id', $data) && ! $user->canAny(['blogs.edit', 'blogs.publish'])) {
            unset($data['author_id']);
        }

        return $data;
    }
}
