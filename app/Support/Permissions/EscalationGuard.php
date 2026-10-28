<?php

namespace App\Support\Permissions;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

/**
 * Server-side protection against privilege escalation in the user and role
 * editors. A non-super-administrator can only hand out roles and
 * permissions they already hold themselves, and can never grant the Super
 * Administrator role. Roles/permissions the actor cannot manage are left
 * untouched on the target instead of being silently removed.
 */
class EscalationGuard
{
    /**
     * Roles the actor may assign to other users.
     *
     * @return Collection<int, Role>
     */
    public function assignableRoles(User $actor): Collection
    {
        $roles = Role::query()->where('guard_name', 'web')->with('permissions')->orderBy('name')->get();

        if ($actor->isSuperAdmin()) {
            return $roles;
        }

        $held = $this->actorPermissions($actor);

        return $roles
            ->reject(fn (Role $role) => $role->name === PermissionRegistry::SUPER_ADMIN)
            ->filter(fn (Role $role) => $role->permissions->pluck('name')->diff($held)->isEmpty())
            ->values();
    }

    /**
     * Resolve the final role ids for a target user.
     *
     * @param  array<int, int|string>  $requestedIds
     * @return array<int, int>
     *
     * @throws ValidationException when a role outside the actor's reach is requested
     */
    public function resolveRoleIds(User $actor, ?User $target, array $requestedIds, string $field = 'role_ids'): array
    {
        $requested = collect($requestedIds)->map(fn ($id) => (int) $id)->filter()->unique();
        $assignable = $this->assignableRoles($actor)->modelKeys();
        $current = $target?->roles()->pluck('roles.id')->map(fn ($id) => (int) $id) ?? collect();

        $forbidden = $requested->diff($assignable)->diff($current);

        if ($forbidden->isNotEmpty()) {
            throw ValidationException::withMessages([$field => 'You are not allowed to assign one or more of the selected roles.']);
        }

        $kept = $current->diff($assignable);

        return $requested->intersect($assignable)->merge($kept)->unique()->values()->all();
    }

    /**
     * Permission names the actor may grant or revoke on a role.
     *
     * @return Collection<int, string>
     */
    public function grantablePermissions(User $actor): Collection
    {
        return $actor->isSuperAdmin() ? collect(PermissionRegistry::all()) : $this->actorPermissions($actor);
    }

    /**
     * Resolve the final permission set for a role: the actor can only toggle
     * permissions they hold; everything else keeps its current state.
     *
     * @param  array<int, string>  $requested
     * @param  array<int, string>  $current
     * @return array<int, string>
     *
     * @throws ValidationException
     */
    public function resolvePermissions(User $actor, array $requested, array $current = [], string $field = 'permissions'): array
    {
        $requested = collect($requested)->filter(fn ($name) => is_string($name))->unique();
        $current = collect($current);
        $grantable = $this->grantablePermissions($actor);

        $unknown = $requested->diff(PermissionRegistry::all());

        if ($unknown->isNotEmpty()) {
            throw ValidationException::withMessages([$field => 'Unknown permission(s): '.$unknown->implode(', ')]);
        }

        $forbidden = $requested->diff($grantable)->diff($current);

        if ($forbidden->isNotEmpty()) {
            throw ValidationException::withMessages([$field => 'You cannot grant permissions you do not hold: '.$forbidden->implode(', ')]);
        }

        return $requested->intersect($grantable)
            ->merge($current->diff($grantable))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, string>
     */
    private function actorPermissions(User $actor): Collection
    {
        return $actor->getAllPermissions()->pluck('name');
    }
}
