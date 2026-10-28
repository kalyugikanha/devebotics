<?php

namespace Database\Seeders;

use App\Support\Audit\AuditLogger;
use Database\Seeders\Concerns\SeedsRichContent;
use Illuminate\Database\Seeder;

/**
 * Applies the latest seeded copy to an existing database: service and industry
 * descriptions, SEO metadata, service FAQs, the home page introduction and the
 * Insights articles (former demo posts become genuine articles). Seeded copy
 * replaces stored copy; records an editor has deleted are not restored.
 *
 *     php artisan db:seed --class=ContentRefreshSeeder
 */
class ContentRefreshSeeder extends Seeder
{
    use SeedsRichContent;

    public function run(): void
    {
        AuditLogger::withoutLogging(fn () => $this->seedRichContent(overwrite: true));

        $this->command?->info('Service, industry, page and Insights content refreshed.');
    }
}
