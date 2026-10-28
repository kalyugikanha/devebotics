<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Services\UserInvitationService;
use App\Support\Audit\AuditLogger;
use App\Support\Permissions\EscalationGuard;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Invite user';

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        /** @var User $actor */
        $actor = Auth::user();
        $requestedRoles = (array) ($data['role_ids'] ?? []);
        unset($data['role_ids'], $data['is_active']);

        $roleIds = $actor->can('roles.manage')
            ? app(EscalationGuard::class)->resolveRoleIds($actor, null, $requestedRoles, 'data.role_ids')
            : [];

        $user = new User($data);
        // Random, never-disclosed password; the user chooses their own via the invitation link.
        $user->password = Str::password(48);
        $user->is_active = true;
        $user->save();

        $roles = Role::query()->whereKey($roleIds)->get();
        $user->syncRoles($roles);

        if ($roles->isNotEmpty()) {
            app(AuditLogger::class)->log('user.roles_changed', $user, ['roles' => []], ['roles' => $roles->pluck('name')->all()], 'Roles assigned to new user '.$user->email, true);
        }

        app(UserInvitationService::class)->send($user);

        return $user;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'User created and invitation sent';
    }
}
