<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\FlagsDemoContent;
use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

#[Fillable([
    'disk', 'path', 'original_name', 'mime_type', 'extension', 'size', 'width', 'height',
    'title', 'alt_text', 'caption', 'category', 'variants', 'checksum', 'is_demo', 'uploaded_by',
])]
class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use Auditable, FlagsDemoContent, HasFactory;

    protected $table = 'media';

    /**
     * @var array<int, string>
     */
    protected array $auditExclude = ['variants', 'checksum'];

    protected function casts(): array
    {
        return [
            'variants' => 'array',
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleted(function (Media $media): void {
            $disk = Storage::disk($media->disk);
            $disk->delete(array_filter([$media->path, ...array_values($media->variants['webp'] ?? [])]));
        });
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function variantUrl(int $width): ?string
    {
        $path = $this->variants['webp'][(string) $width] ?? null;

        return $path ? Storage::disk($this->disk)->url($path) : null;
    }

    /**
     * WebP srcset built from generated variants, largest last.
     */
    public function webpSrcset(): ?string
    {
        $variants = $this->variants['webp'] ?? [];

        if ($variants === []) {
            return null;
        }

        ksort($variants, SORT_NUMERIC);

        return collect($variants)
            ->map(fn (string $path, string|int $width): string => Storage::disk($this->disk)->url($path).' '.$width.'w')
            ->implode(', ');
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isSvg(): bool
    {
        return $this->mime_type === 'image/svg+xml';
    }

    public function humanSize(): string
    {
        return Number::fileSize($this->size, precision: 1);
    }

    public function label(): string
    {
        return $this->title ?: $this->original_name;
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * @return HasMany<MediaAttachment, $this>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(MediaAttachment::class);
    }
}
