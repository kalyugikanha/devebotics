<?php

namespace Database\Factories;

use App\Models\User;
use App\Support\Permissions\PermissionRegistry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }

    /**
     * Assign a role from PermissionRegistry (creating it with its default permissions if needed).
     */
    public function withRole(string $role): static
    {
        return $this->afterCreating(function (User $user) use ($role): void {
            $model = Role::findOrCreate($role, 'web');

            if ($model->permissions()->doesntExist()) {
                foreach (PermissionRegistry::permissionsForRole($role) as $permission) {
                    Permission::findOrCreate($permission, 'web');
                }
                $model->syncPermissions(PermissionRegistry::permissionsForRole($role));
            }

            $user->assignRole($model);
        });
    }

    public function superAdmin(): static
    {
        return $this->withRole(PermissionRegistry::SUPER_ADMIN);
    }
}
