<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Permissions\PermissionRegistry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Creates the initial Super Administrator from SEED_ADMIN_* environment
 * variables. When no password is configured a strong random one is
 * generated and printed once — change it after first sign-in.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) config('softec.seed_admin.email');

        if (User::query()->where('email', $email)->exists()) {
            $this->command?->info("Super administrator {$email} already exists — skipped.");

            return;
        }

        $password = config('softec.seed_admin.password') ?: Str::password(20);

        $user = User::query()->forceCreate([
            'name' => (string) config('softec.seed_admin.name'),
            'email' => $email,
            'password' => $password,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $user->assignRole(PermissionRegistry::SUPER_ADMIN);

        $this->command?->warn('Super administrator created: '.$email);

        if (! config('softec.seed_admin.password')) {
            $this->command?->warn('Generated password (shown once): '.$password);
        }
    }
}
