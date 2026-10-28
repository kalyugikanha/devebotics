<?php

namespace App\Support\Media;

use App\Jobs\GenerateMediaVariants;
use App\Models\Media;
use enshrined\svgSanitize\Sanitizer;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Throwable;

/**
 * Stores files in the public media library.
 *
 * - Detects the real MIME type from file contents (never trusts the extension)
 * - Sanitises SVG markup, strips EXIF/GPS metadata from photos
 * - Generates meaningful, collision-free filenames
 * - Queues responsive WebP variants for raster images
 */
class MediaLibrary
{
    /**
     * Allowed MIME types → canonical extension.
     *
     * @var array<string, string>
     */
    public const ALLOWED_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/avif' => 'avif',
        'image/gif' => 'gif',
        'image/svg+xml' => 'svg',
        'application/pdf' => 'pdf',
    ];

    public function disk(): string
    {
        return (string) config('softec.media.disk', 'public');
    }

    /**
     * Import a file that a form upload placed on another disk (e.g. Filament's
     * FileUpload on the private "local" disk) and remove the incoming copy.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function importFromDisk(string $disk, string $path, array $attributes = [], ?string $originalName = null): Media
    {
        $absolute = Storage::disk($disk)->path($path);

        try {
            return $this->store($absolute, $originalName ?? basename($path), $attributes);
        } finally {
            Storage::disk($disk)->delete($path);
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function storeUploadedFile(UploadedFile $file, array $attributes = []): Media
    {
        return $this->store($file->getRealPath(), $file->getClientOriginalName(), $attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function store(string $absolutePath, string $originalName, array $attributes = []): Media
    {
        $mime = $this->detectMime($absolutePath);

        if (! array_key_exists($mime, self::ALLOWED_MIME_TYPES)) {
            throw new MediaException("Files of type [{$mime}] are not allowed in the media library.");
        }

        $maxBytes = (int) config('softec.media.max_upload_kb', 8192) * 1024;

        if (filesize($absolutePath) > $maxBytes) {
            throw new MediaException('The file exceeds the maximum upload size.');
        }

        $extension = self::ALLOWED_MIME_TYPES[$mime];
        $workingPath = $this->prepare($absolutePath, $mime);
        [$width, $height] = $this->dimensions($workingPath, $mime);

        $maxDimension = (int) config('softec.media.max_dimension', 6000);

        if ($width !== null && ($width > $maxDimension || $height > $maxDimension)) {
            throw new MediaException("Images may not exceed {$maxDimension}px in either dimension.");
        }

        $directory = trim((string) config('softec.media.directory', 'media'), '/').'/'.now()->format('Y/m');
        $filename = $this->filename($originalName, $extension);

        $storedPath = Storage::disk($this->disk())->putFileAs($directory, new File($workingPath), $filename, ['visibility' => 'public']);

        if ($workingPath !== $absolutePath) {
            @unlink($workingPath);
        }

        $media = Media::query()->create([
            'disk' => $this->disk(),
            'path' => $storedPath,
            'original_name' => Str::limit($this->cleanOriginalName($originalName), 250, ''),
            'mime_type' => $mime,
            'extension' => $extension,
            'size' => Storage::disk($this->disk())->size($storedPath),
            'width' => $width,
            'height' => $height,
            'title' => $attributes['title'] ?? Str::headline(pathinfo($this->cleanOriginalName($originalName), PATHINFO_FILENAME)),
            'alt_text' => $attributes['alt_text'] ?? null,
            'caption' => $attributes['caption'] ?? null,
            'category' => $attributes['category'] ?? null,
            'is_demo' => (bool) ($attributes['is_demo'] ?? false),
            'checksum' => hash_file('sha256', Storage::disk($this->disk())->path($storedPath)) ?: null,
            'uploaded_by' => $attributes['uploaded_by'] ?? Auth::id(),
        ]);

        if ($this->supportsVariants($mime)) {
            GenerateMediaVariants::dispatch($media->getKey());
        }

        return $media;
    }

    /**
     * Create responsive WebP variants narrower than the original.
     */
    public function generateVariants(Media $media): void
    {
        if (! $this->supportsVariants($media->mime_type) || $media->width === null) {
            return;
        }

        $disk = Storage::disk($media->disk);
        $manager = ImageManager::usingDriver(Driver::class);
        $variants = [];

        foreach ((array) config('softec.media.variant_widths', [480, 960, 1600]) as $width) {
            $width = (int) $width;

            // Always produce at least one WebP version, even for small originals.
            if ($width >= $media->width && $variants !== []) {
                continue;
            }

            $targetWidth = min($width, $media->width);
            $image = $manager->decodePath($disk->path($media->path))->scaleDown(width: $targetWidth);
            $variantPath = preg_replace('/\.[a-z0-9]+$/i', '', $media->path).'-'.$targetWidth.'w.webp';

            $disk->put($variantPath, (string) $image->encode(new WebpEncoder(quality: 80, strip: true)), ['visibility' => 'public']);
            $variants[(string) $targetWidth] = $variantPath;
        }

        $media->forceFill(['variants' => ['webp' => $variants]])->saveQuietly();
    }

    public function supportsVariants(string $mime): bool
    {
        return in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true);
    }

    public function detectMime(string $absolutePath): string
    {
        $mime = (string) (new \finfo(FILEINFO_MIME_TYPE))->file($absolutePath);

        // finfo reports some SVGs as text/xml or text/plain; confirm by content.
        if (in_array($mime, ['text/xml', 'application/xml', 'text/plain', 'text/html'], true)) {
            $head = (string) file_get_contents($absolutePath, false, null, 0, 2048);

            if (preg_match('/<svg[\s>]/i', $head) === 1) {
                return 'image/svg+xml';
            }
        }

        return $mime;
    }

    /**
     * Returns the path of a cleaned working copy (sanitised SVG, metadata-free raster).
     */
    private function prepare(string $absolutePath, string $mime): string
    {
        if ($mime === 'image/svg+xml') {
            $sanitizer = new Sanitizer;
            $sanitizer->removeRemoteReferences(true);
            $clean = $sanitizer->sanitize((string) file_get_contents($absolutePath));

            if ($clean === false || trim($clean) === '') {
                throw new MediaException('The SVG file could not be sanitised safely.');
            }

            return $this->writeTemp($clean, 'svg');
        }

        if (in_array($mime, ['image/jpeg', 'image/png'], true)) {
            try {
                $image = ImageManager::usingDriver(Driver::class)->decodePath($absolutePath)->orient();
                $encoder = $mime === 'image/jpeg' ? new JpegEncoder(quality: 88, strip: true) : new PngEncoder;

                return $this->writeTemp((string) $image->encode($encoder), self::ALLOWED_MIME_TYPES[$mime]);
            } catch (Throwable $exception) {
                throw new MediaException('The image could not be processed: '.$exception->getMessage(), previous: $exception);
            }
        }

        return $absolutePath;
    }

    /**
     * @return array{0: ?int, 1: ?int}
     */
    private function dimensions(string $path, string $mime): array
    {
        if ($mime === 'image/svg+xml') {
            $svg = @simplexml_load_string((string) file_get_contents($path));

            if ($svg !== false) {
                $viewBox = preg_split('/[\s,]+/', trim((string) ($svg['viewBox'] ?? '')));

                if (is_array($viewBox) && count($viewBox) === 4) {
                    return [(int) round((float) $viewBox[2]), (int) round((float) $viewBox[3])];
                }
            }

            return [null, null];
        }

        if (! str_starts_with($mime, 'image/')) {
            return [null, null];
        }

        $size = @getimagesize($path);

        return $size === false ? [null, null] : [(int) $size[0], (int) $size[1]];
    }

    private function filename(string $originalName, string $extension): string
    {
        $base = Str::slug(pathinfo($this->cleanOriginalName($originalName), PATHINFO_FILENAME)) ?: 'file';

        return Str::limit($base, 80, '').'-'.Str::lower(Str::random(6)).'.'.$extension;
    }

    private function cleanOriginalName(string $name): string
    {
        // Filament stores uploads under random names; keep a readable base name.
        return basename(str_replace(['\\', "\0"], ['/', ''], $name));
    }

    private function writeTemp(string $contents, string $extension): string
    {
        $path = tempnam(sys_get_temp_dir(), 'media').'.'.$extension;
        file_put_contents($path, $contents);

        return $path;
    }
}
