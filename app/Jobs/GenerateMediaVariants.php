<?php

namespace App\Jobs;

use App\Models\Media;
use App\Support\Media\MediaLibrary;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateMediaVariants implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(public int $mediaId) {}

    public function uniqueId(): string
    {
        return (string) $this->mediaId;
    }

    public function handle(MediaLibrary $library): void
    {
        $media = Media::query()->find($this->mediaId);

        if ($media !== null) {
            $library->generateVariants($media);
        }
    }
}
