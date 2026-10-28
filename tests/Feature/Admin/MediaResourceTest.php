<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Media\Pages\CreateMedia;
use App\Filament\Resources\Media\Pages\EditMedia;
use App\Filament\Resources\Media\Pages\ListMedia;
use App\Models\Media;
use App\Models\Service;
use App\Models\User;
use App\Support\Media\MediaLibrary;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MediaResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Storage::fake('local');
    }

    public function test_images_are_uploaded_through_the_media_library_service(): void
    {
        $this->actingAs(User::factory()->withRole('Content Manager')->create());

        Livewire::test(CreateMedia::class)
            ->fillForm([
                'upload' => UploadedFile::fake()->image('Team Photo.png', 1200, 800),
                'title' => 'Team photo',
                'alt_text' => 'The team in the office',
                'category' => 'team',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $media = Media::query()->sole();

        $this->assertSame('image/png', $media->mime_type);
        $this->assertSame(1200, $media->width);
        $this->assertSame('The team in the office', $media->alt_text);
        $this->assertSame('team', $media->category);
        Storage::disk('public')->assertExists($media->path);
        $this->assertNotEmpty($media->variants['webp'] ?? []);
        $this->assertSame([], Storage::disk('local')->allFiles('media-incoming'));

        Livewire::test(ListMedia::class)->assertOk()->assertCanSeeTableRecords([$media]);
    }

    public function test_media_in_use_cannot_be_deleted(): void
    {
        $this->actingAs(User::factory()->withRole('Content Manager')->create());
        $media = $this->storedImage();
        Service::factory()->create(['hero_media_id' => $media->id, 'title' => 'Cloud Migration']);

        Livewire::test(EditMedia::class, ['record' => $media->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertNotified('This file is in use and cannot be deleted');

        $this->assertModelExists($media);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_unused_media_can_be_deleted_with_its_files(): void
    {
        $this->actingAs(User::factory()->withRole('Content Manager')->create());
        $media = $this->storedImage();
        $path = $media->path;

        Livewire::test(EditMedia::class, ['record' => $media->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertModelMissing($media);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_users_without_delete_permission_cannot_delete_media(): void
    {
        $this->actingAs(User::factory()->withRole('Blog Author')->create());
        $media = $this->storedImage();

        Livewire::test(EditMedia::class, ['record' => $media->getRouteKey()])
            ->assertActionHidden(DeleteAction::class);
    }

    public function test_replacing_a_file_keeps_the_same_media_id(): void
    {
        $this->actingAs(User::factory()->withRole('Content Manager')->create());
        $media = $this->storedImage();
        $oldPath = $media->path;

        Livewire::test(EditMedia::class, ['record' => $media->getRouteKey()])
            ->callAction('replaceFile', ['upload' => UploadedFile::fake()->image('new-version.png', 640, 400)])
            ->assertHasNoActionErrors();

        $media->refresh();
        $this->assertNotSame($oldPath, $media->path);
        $this->assertSame(640, $media->width);
        Storage::disk('public')->assertMissing($oldPath);
        $this->assertSame(1, Media::query()->count());
    }

    public function test_media_library_requires_permission(): void
    {
        $this->actingAs(User::factory()->withRole('Sales Manager')->create())
            ->get('/admin/media')
            ->assertForbidden();
    }

    private function storedImage(): Media
    {
        $file = UploadedFile::fake()->image('original.png', 800, 600);

        return app(MediaLibrary::class)->storeUploadedFile($file, ['title' => 'Original']);
    }
}
