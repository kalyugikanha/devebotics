<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use App\Support\Permissions\EscalationGuard;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Account')
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')->required()->maxLength(120),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(190)
                                    ->unique('users', 'email', ignoreRecord: true),
                                TextInput::make('job_title')->maxLength(120),
                                TextInput::make('phone')->tel()->maxLength(40)->regex('/^[0-9+()\-.\s]{6,40}$/'),
                            ]),

                        Section::make('Access')
                            ->description('Roles control what this person can see and do in the admin panel. You can only assign roles whose permissions you hold yourself.')
                            ->visible(fn (?User $record, string $operation): bool => $operation === 'create'
                                ? (bool) Auth::user()?->can('roles.manage')
                                : Gate::allows('assignRoles', $record) || Gate::allows('toggleActive', $record))
                            ->schema([
                                Select::make('role_ids')
                                    ->label('Roles')
                                    ->multiple()
                                    ->preload()
                                    ->options(fn (): array => app(EscalationGuard::class)->assignableRoles(Auth::user())->pluck('name', 'id')->all())
                                    ->visible(fn (?User $record, string $operation): bool => $operation === 'create'
                                        ? (bool) Auth::user()?->can('roles.manage')
                                        : Gate::allows('assignRoles', $record))
                                    ->helperText('Users without a role cannot sign in to the admin panel.'),
                                Toggle::make('is_active')
                                    ->label('Account active')
                                    ->default(true)
                                    ->helperText('Deactivated users are signed out immediately and cannot sign in.')
                                    ->visible(fn (?User $record, string $operation): bool => $operation === 'edit' && Gate::allows('toggleActive', $record)),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Invitation')
                            ->visibleOn('create')
                            ->schema([
                                TextEntry::make('invitation_help')
                                    ->hiddenLabel()
                                    ->state('After saving, the user receives an email with a secure link to choose their own password. You never see or set their password.'),
                            ]),

                        Section::make('Security')
                            ->hiddenOn('create')
                            ->schema([
                                IconEntry::make('is_active')->label('Active')->boolean(),
                                IconEntry::make('mfa')
                                    ->label('Two-factor authentication')
                                    ->state(fn (User $record): bool => $record->hasMultiFactorAuthentication())
                                    ->boolean(),
                                TextEntry::make('last_login_at')->label('Last sign-in')->since()->placeholder('Never'),
                                TextEntry::make('last_login_ip')->label('Last sign-in IP')->placeholder('—'),
                                TextEntry::make('invited_at')->label('Invited')->since()->placeholder('—'),
                                TextEntry::make('password_changed_at')->label('Password changed')->since()->placeholder('—'),
                            ]),
                    ]),
            ]);
    }
}
