<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\Tables\UsersTable;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Support\Audit\AuditLogger;
use App\Support\Permissions\EscalationGuard;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                UsersTable::activateAction(),
                UsersTable::deactivateAction(),
                UsersTable::resendInvitationAction(),
                UsersTable::resetMfaAction(),
                DeleteAction::make(),
            ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var User $record */
        $record = $this->getRecord();
        $data['role_ids'] = $record->roles()->pluck('roles.id')->all();

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var User $record */
        /** @var User $actor */
        $actor = Auth::user();

        if (array_key_exists('role_ids', $data) && Gate::allows('assignRoles', $record)) {
            $before = $record->roles()->pluck('name')->sort()->values()->all();
            $roleIds = app(EscalationGuard::class)->resolveRoleIds($actor, $record, (array) $data['role_ids'], 'data.role_ids');
            $record->syncRoles(Role::query()->whereKey($roleIds)->get());
            $after = $record->roles()->pluck('name')->sort()->values()->all();

            if ($before !== $after) {
                app(AuditLogger::class)->log('user.roles_changed', $record, ['roles' => $before], ['roles' => $after], 'Roles changed for '.$record->email, true);
            }
        }

        unset($data['role_ids']);

        if (array_key_exists('is_active', $data)) {
            if (Gate::allows('toggleActive', $record) && (bool) $data['is_active'] !== $record->is_active) {
                app(AuditLogger::class)->log($data['is_active'] ? 'user.activated' : 'user.deactivated', $record, ['is_active' => $record->is_active], ['is_active' => (bool) $data['is_active']], 'Account '.($data['is_active'] ? 'activated' : 'deactivated').': '.$record->email, true);
            } else {
                unset($data['is_active']);
            }
        }

        $record->update($data);

        return $record;
    }
}
