<?php

namespace App\Filament\Resources\Roles\Tables;

use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Support\Permissions\PermissionRegistry;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->badge(fn (Role $record): bool => in_array($record->name, RoleForm::PROTECTED_ROLES, true))
                    ->color(fn (Role $record): ?string => $record->name === PermissionRegistry::SUPER_ADMIN ? 'danger' : null)
                    ->description(fn (Role $record): ?string => $record->name === PermissionRegistry::SUPER_ADMIN ? 'All permissions (implicit) — locked' : null),
                TextColumn::make('permissions_count')->label('Permissions')->sortable()
                    ->formatStateUsing(fn (Role $record, $state): string => $record->name === PermissionRegistry::SUPER_ADMIN ? 'All' : (string) $state),
                TextColumn::make('users_count')->label('Users')->sortable(),
                TextColumn::make('updated_at')->label('Updated')->since()->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->recordUrl(fn (Role $record): ?string => $record->name === PermissionRegistry::SUPER_ADMIN ? null : route('filament.admin.resources.roles.edit', $record));
    }
}
