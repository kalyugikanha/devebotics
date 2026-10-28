<?php

namespace Database\Seeders;

use App\Support\Permissions\PermissionRegistry;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Creates/updates every permission and default role. Idempotent — safe to run
 * on production after adding permissions to PermissionRegistry. Existing
 * custom roles are left untouched; default roles are re-synced.
 */
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (PermissionRegistry::all() as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        Permission::query()->whereNotIn('name', PermissionRegistry::all())->delete();

        foreach (array_keys(PermissionRegistry::roles()) as $roleName) {
            Role::findOrCreate($roleName, 'web')->syncPermissions(PermissionRegistry::permissionsForRole($roleName));
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
