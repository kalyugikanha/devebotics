<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\Auth\RequestPasswordReset;
use App\Mail\TemplatedMail;
use App\Models\User;
use Filament\Auth\Notifications\ResetPassword;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class AccountSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');
    }

    public function test_password_reset_request_gives_the_same_response_for_unknown_and_known_accounts(): void
    {
        Notification::fake();
        $user = User::factory()->withRole('Content Manager')->create(['email' => 'known@example.test']);

        Livewire::test(RequestPasswordReset::class)
            ->fillForm(['email' => 'nobody@example.test'])
            ->call('request')
            ->assertNotified(__('passwords.sent'));

        Livewire::test(RequestPasswordReset::class)
            ->fillForm(['email' => 'known@example.test'])
            ->call('request')
            ->assertNotified(__('passwords.sent'));

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_and_two_factor_changes_send_security_alerts(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $user->update(['password' => 'An0ther-Secure-Passw0rd']);
        $user->toggleEmailAuthentication(true);

        Mail::assertQueued(TemplatedMail::class, fn (TemplatedMail $mail) => $mail->templateKey === 'security_alert'
            && $mail->hasTo($user->email)
            && str_contains((string) $mail->variables['event'], 'password'));
        Mail::assertQueued(TemplatedMail::class, fn (TemplatedMail $mail) => $mail->templateKey === 'security_alert'
            && str_contains((string) $mail->variables['event'], 'Email two-factor'));
    }

    public function test_creating_an_account_does_not_send_a_security_alert(): void
    {
        Mail::fake();

        User::factory()->create();

        Mail::assertNotQueued(TemplatedMail::class);
    }
}
