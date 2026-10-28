<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Support\Media\MediaException;
use App\Support\Media\MediaLibrary;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    protected static ?string $title = 'Upload file';

    /**
     * Files are imported through the MediaLibrary service (MIME sniffing, SVG
     * sanitising, metadata stripping, responsive variants).
     *
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        try {
            return app(MediaLibrary::class)->importFromDisk('local', $data['upload'], [
                'title' => $data['title'] ?? null,
                'alt_text' => $data['alt_text'] ?? null,
                'caption' => $data['caption'] ?? null,
                'category' => $data['category'] ?? null,
            ], $data['upload_name'] ?? null);
        } catch (MediaException $exception) {
            Notification::make()->title('Upload rejected')->body($exception->getMessage())->danger()->send();

            throw new Halt;
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
