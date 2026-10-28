<?php

namespace App\Console\Commands;

use App\Models\Achievement;
use App\Models\BlogPost;
use App\Models\Client;
use App\Models\Industry;
use App\Models\JobOpening;
use App\Models\Media;
use App\Models\Milestone;
use App\Models\Partner;
use App\Models\Portfolio;
use App\Models\Product;
use App\Models\Service;
use App\Models\Statistic;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Support\Audit\AuditLogger;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Removes every record flagged `is_demo` (and demo media files) so the live
 * site only shows real, verified content. Real records are never touched.
 */
class PurgeDemoContent extends Command
{
    protected $signature = 'softec:purge-demo {--force : Skip the confirmation prompt}';

    protected $description = 'Delete all seeded demo content (records flagged is_demo) and demo media files';

    /**
     * Content first, media last (media rows also delete their files).
     *
     * @var array<int, class-string<Model>>
     */
    public const MODELS = [
        Testimonial::class,
        BlogPost::class,
        Portfolio::class,
        Product::class,
        JobOpening::class,
        Service::class,
        Industry::class,
        Client::class,
        Partner::class,
        TeamMember::class,
        Milestone::class,
        Achievement::class,
        Statistic::class,
        Media::class,
    ];

    public function handle(AuditLogger $audit): int
    {
        $counts = collect(self::MODELS)->mapWithKeys(fn (string $model) => [$model => $this->query($model)->count()]);

        if ($counts->sum() === 0) {
            $this->info('No demo content found.');

            return self::SUCCESS;
        }

        $this->table(['Content type', 'Demo records'], $counts->map(fn (int $count, string $model) => [class_basename($model), $count])->values()->all());

        if (! $this->option('force') && ! $this->confirm('Permanently delete these demo records?')) {
            $this->warn('Aborted.');

            return self::FAILURE;
        }

        AuditLogger::withoutLogging(function (): void {
            DB::transaction(function (): void {
                foreach (self::MODELS as $model) {
                    $this->query($model)->get()->each(function (Model $record): void {
                        method_exists($record, 'forceDelete') ? $record->forceDelete() : $record->delete();
                    });
                }
            });
        });

        Cache::flush();

        $audit->log('system.demo_purged', null, null, $counts->mapWithKeys(fn ($count, $model) => [class_basename($model) => $count])->all(), 'Demo content purged ('.$counts->sum().' records)', true);

        $this->info('Removed '.$counts->sum().' demo records.');

        return self::SUCCESS;
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function query(string $model): Builder
    {
        $query = $model::query()->where('is_demo', true);

        if (method_exists($model, 'bootSoftDeletes')) {
            $query->withTrashed();
        }

        return $query;
    }
}
