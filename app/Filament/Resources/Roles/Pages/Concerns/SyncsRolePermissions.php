<?php

namespace App\Filament\Resources\Roles\Pages\Concerns;

use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Models\User;
use App\Support\Audit\AuditLogger;
use App\Support\Permissions\EscalationGuard;
use App\Support\Permissions\PermissionRegistry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

/**
 * Splits the per-group checkbox fields out of the form data and syncs them
 * through the escalation guard.
 */
trait SyncsRolePermissions
{
    /**
     * @param  array<string, mixed>  $data
     * @return array{0: array<string, mixed>, 1: array<int, string>}
     */
    protected function extractPermissions(array $data): array
    {
        $requested = [];

        foreach (array_keys(PermissionRegistry::groups()) as $group) {
            $field = RoleForm::fieldFor($group);
            $requested = array_merge($requested, (array) ($data[$field] ?? []));
            unset($data[$field]);
        }

        return [$data, $requested];
    }

    /**
     * @param  array<int, string>  $requested
     */
    protected function syncPermissions(Role $role, array $requested): void
    {
        /** @var User $actor */
        $actor = Auth::user();
        $before = $role->permissions()->pluck('name')->sort()->values()->all();
        $guard = app(EscalationGuard::class);
        $grantable = $guard->grantablePermissions($actor);

        // Report a tampered/forbidden permission against the group field it belongs to.
        foreach (PermissionRegistry::groups() as $group => $permissions) {
            $forbidden = array_diff(array_intersect($requested, array_keys($permissions)), $grantable->all(), $before);

            if ($forbidden !== []) {
                throw ValidationException::withMessages([
                    'data.'.RoleForm::fieldFor($group) => 'You cannot grant permissions you do not hold: '.implode(', ', $forbidden),
                ]);
            }
        }

        $final = $guard->resolvePermissions($actor, $requested, $before, 'data.name');
        $role->syncPermissions($final);

        $added = array_values(array_diff($final, $before));
        $removed = array_values(array_diff($before, $final));

        if ($added !== [] || $removed !== []) {
            app(AuditLogger::class)->log(
                'role.permissions_changed',
                $role,
                ['removed' => $removed],
                ['added' => $added],
                'Permissions changed for role "'.$role->name.'"',
                true,
            );
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function fillPermissionFields(Role $role, array $data): array
    {
        $current = $role->permissions()->pluck('name')->all();

        foreach (PermissionRegistry::groups() as $group => $permissions) {
            $data[RoleForm::fieldFor($group)] = array_values(array_intersect(array_keys($permissions), $current));
        }

        return $data;
    }
}
