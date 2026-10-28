<?php

namespace Database\Seeders;

use App\Support\Audit\AuditLogger;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Production: permissions, the first super administrator and core site
     * structure only. Demo content is added outside production, or explicitly
     * with `php artisan db:seed --class=DemoContentSeeder`.
     */
    public function run(): void
    {
        AuditLogger::withoutLogging(function (): void {
            $this->call([
                PermissionSeeder::class,
                AdminUserSeeder::class,
                CoreContentSeeder::class,
                TeamSeeder::class,
            ]);

            if (! app()->isProduction()) {
                $this->call(DemoContentSeeder::class);
            }
        });
    }
}
