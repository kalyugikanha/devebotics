<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Support\Permissions\EscalationGuard;
use App\Support\Permissions\PermissionRegistry;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RoleForm
{
    public const PROTECTED_ROLES = [PermissionRegistry::SUPER_ADMIN, PermissionRegistry::ADMIN];

    public static function fieldFor(string $group): string
    {
        return 'permissions_'.Str::snake(Str::slug($group, '_'));
    }

    public static function configure(Schema $schema): Schema
    {
        $grantable = app(EscalationGuard::class)->grantablePermissions(Auth::user());

        $sections = collect(PermissionRegistry::groups())->map(fn (array $permissions, string $group) => Section::make($group)
            ->collapsible()
            ->compact()
            ->schema([
                CheckboxList::make(self::fieldFor($group))
                    ->hiddenLabel()
                    ->options(collect($permissions)->mapWithKeys(fn (string $description, string $name) => [$name => $name])->all())
                    ->descriptions($permissions)
                    ->disableOptionWhen(fn (string $value): bool => ! $grantable->contains($value))
                    ->bulkToggleable()
                    ->columns(2),
            ]))->values()->all();

        return $schema
            ->columns(1)
            ->components([
                Section::make('Role')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(80)
                            ->unique('roles', 'name', ignoreRecord: true)
                            ->disabled(fn (?Role $record): bool => $record !== null && in_array($record->name, self::PROTECTED_ROLES, true))
                            ->helperText('Built-in roles referenced by the application cannot be renamed.'),
                    ]),
                Callout::make('You can only grant permissions you hold yourself')
                    ->description('Greyed-out permissions are outside your own access level and keep their current state.')
                    ->info()
                    ->visible(fn (): bool => ! Auth::user()?->isSuperAdmin()),
                ...$sections,
            ]);
    }
}
