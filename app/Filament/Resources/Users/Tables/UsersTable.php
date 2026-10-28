<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use App\Services\UserInvitationService;
use App\Support\Audit\AuditLogger;
use App\Support\Permissions\PermissionRegistry;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('roles'))
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (User $record): string => $record->email),
                TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color(fn (string $state): string => $state === PermissionRegistry::SUPER_ADMIN ? 'danger' : 'primary'),
                IconColumn::make('is_active')->label('Active')->boolean()->sortable(),
                IconColumn::make('mfa')
                    ->label('2FA')
                    ->state(fn (User $record): bool => $record->hasMultiFactorAuthentication())
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->falseColor('warning'),
                TextColumn::make('last_login_at')->label('Last sign-in')->since()->sortable()->placeholder('Never'),
                TextColumn::make('created_at')->dateTime('M j, Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')->relationship('roles', 'name')->multiple()->preload(),
                TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
                ActionGroup::make([
                    self::activateAction(),
                    self::deactivateAction(),
                    self::resendInvitationAction(),
                    self::resetMfaAction(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->authorizeIndividualRecords('delete'),
                ]),
            ])
            ->emptyStateHeading('No users yet');
    }

    public static function activateAction(): Action
    {
        return Action::make('activate')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn (User $record): bool => ! $record->is_active)
            ->authorize(fn (User $record): bool => Gate::allows('toggleActive', $record))
            ->action(function (User $record): void {
                $record->update(['is_active' => true]);
                app(AuditLogger::class)->log('user.activated', $record, ['is_active' => false], ['is_active' => true], 'Account activated: '.$record->email, true);
                Notification::make()->title('Account activated')->success()->send();
            });
    }

    public static function deactivateAction(): Action
    {
        return Action::make('deactivate')
            ->icon('heroicon-o-no-symbol')
            ->color('danger')
            ->requiresConfirmation()
            ->modalDescription('The user is signed out of every session immediately and can no longer sign in.')
            ->visible(fn (User $record): bool => $record->is_active)
            ->authorize(fn (User $record): bool => Gate::allows('toggleActive', $record))
            ->action(function (User $record): void {
                $record->update(['is_active' => false]);
                app(AuditLogger::class)->log('user.deactivated', $record, ['is_active' => true], ['is_active' => false], 'Account deactivated: '.$record->email, true);
                Notification::make()->title('Account deactivated')->success()->send();
            });
    }

    public static function resendInvitationAction(): Action
    {
        return Action::make('resendInvitation')
            ->label('Resend invitation')
            ->icon('heroicon-o-envelope')
            ->requiresConfirmation()
            ->visible(fn (User $record): bool => $record->last_login_at === null && $record->is_active)
            ->authorize(fn (User $record): bool => Gate::allows('update', $record))
            ->action(function (User $record): void {
                app(UserInvitationService::class)->send($record);
                Notification::make()->title('Invitation sent')->success()->send();
            });
    }

    public static function resetMfaAction(): Action
    {
        return Action::make('resetMfa')
            ->label('Reset two-factor')
            ->icon('heroicon-o-key')
            ->color('warning')
            ->requiresConfirmation()
            ->modalDescription('Removes this user\'s authenticator app, recovery codes and email codes. They can set up two-factor authentication again from their profile.')
            ->visible(fn (User $record): bool => $record->hasMultiFactorAuthentication())
            ->authorize(fn (User $record): bool => (bool) Auth::user()?->isSuperAdmin() && ! Auth::user()->is($record))
            ->action(function (User $record): void {
                $record->forceFill([
                    'app_authentication_secret' => null,
                    'app_authentication_recovery_codes' => null,
                    'has_email_authentication' => false,
                ])->save();

                app(AuditLogger::class)->log('user.mfa_reset', $record, description: 'Two-factor authentication reset for '.$record->email, isSecurityEvent: true);
                Notification::make()->title('Two-factor authentication reset')->success()->send();
            });
    }
}
