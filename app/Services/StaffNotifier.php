<?php

namespace App\Services;

use App\Mail\TemplatedMail;
use App\Models\User;
use App\Support\Mail\Outbox;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Alerts staff about new submissions: an in-panel database notification for
 * every active user holding the permission, plus an email to the recipients
 * configured in Settings › Forms (falling back to the company email).
 */
class StaffNotifier
{
    public function __construct(private Outbox $outbox) {}

    /**
     * @param  array<int, string>  $configuredRecipients
     */
    public function notify(
        string $permission,
        string $title,
        string $body,
        ?string $adminUrl,
        array $configuredRecipients,
        TemplatedMail $mail,
        ?Model $related = null,
    ): void {
        $this->staffWith($permission)->each(function (User $user) use ($title, $body, $adminUrl): void {
            Notification::make()
                ->title($title)
                ->body($body)
                ->icon('heroicon-o-bell-alert')
                ->actions($adminUrl ? [Action::make('open')->label('Open')->url($adminUrl)->markAsRead()] : [])
                ->sendToDatabase($user);
        });

        $recipients = array_values(array_filter($configuredRecipients ?: [settings('contact.sales_email') ?: settings('contact.email')]));

        if ($recipients !== []) {
            $this->outbox->send($recipients, $mail, $related);
        }
    }

    /**
     * @return Collection<int, User>
     */
    private function staffWith(string $permission): Collection
    {
        return User::query()->active()->get()->filter(fn (User $user) => $user->can($permission))->values();
    }
}
