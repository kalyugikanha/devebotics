<?php

namespace App\Filament\Resources\EmailTemplates;

use App\Filament\AdminNavigationGroup;
use App\Filament\Resources\EmailTemplates\Pages\EditEmailTemplate;
use App\Filament\Resources\EmailTemplates\Pages\ListEmailTemplates;
use App\Filament\Resources\EmailTemplates\Schemas\EmailTemplateForm;
use App\Filament\Resources\EmailTemplates\Tables\EmailTemplatesTable;
use App\Mail\TemplatedMail;
use App\Models\EmailTemplate;
use App\Support\Mail\EmailTemplateRegistry;
use App\Support\Mail\Outbox;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use UnitEnum;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Settings;

    protected static ?int $navigationSort = 40;

    protected static ?string $navigationLabel = 'Email templates';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function form(Schema $schema): Schema
    {
        return EmailTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailTemplatesTable::configure($table);
    }

    /**
     * Templates are defined in code (EmailTemplateRegistry) — editable, never created or deleted here.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function sendTestAction(): Action
    {
        return Action::make('sendTest')
            ->label('Send test')
            ->icon('heroicon-o-paper-airplane')
            ->color('gray')
            ->requiresConfirmation()
            ->modalDescription(fn (): string => 'A test message with sample values will be sent to '.Auth::user()?->email.'.')
            ->authorize(fn (EmailTemplate $record): bool => Gate::allows('update', $record))
            ->action(function (EmailTemplate $record): void {
                $sample = collect(EmailTemplateRegistry::variables($record->key))
                    ->mapWithKeys(fn (string $name) => [$name => match ($name) {
                        'name', 'client_name' => Auth::user()?->name,
                        'reference' => 'SAI-'.now()->format('ym').'-SAMPLE',
                        'time' => now()->toDayDateTimeString(),
                        'rating' => 5,
                        default => 'Sample '.Str::headline($name),
                    }])
                    ->all();

                $sent = app(Outbox::class)->send((string) Auth::user()?->email, new TemplatedMail($record->key, $sample, url('/')));

                $sent
                    ? Notification::make()->title('Test email queued')->body('Check the inbox of '.Auth::user()?->email.' (and the email log).')->success()->send()
                    : Notification::make()->title('The template is inactive or the address is invalid')->warning()->send();
            });
    }

    public static function restoreDefaultAction(): Action
    {
        return Action::make('restoreDefault')
            ->label('Restore default')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('gray')
            ->requiresConfirmation()
            ->authorize(fn (EmailTemplate $record): bool => Gate::allows('update', $record))
            ->visible(fn (EmailTemplate $record): bool => array_key_exists($record->key, EmailTemplateRegistry::all()))
            ->action(function (EmailTemplate $record, $livewire): void {
                $default = EmailTemplateRegistry::all()[$record->key];
                $record->update([
                    'subject' => $default['subject'],
                    'heading' => $default['heading'],
                    'body' => $default['body'],
                    'action_label' => $default['action_label'],
                ]);

                if (method_exists($livewire, 'refreshFormData')) {
                    $livewire->refreshFormData(['subject', 'heading', 'body', 'action_label']);
                }

                Notification::make()->title('Default content restored')->success()->send();
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailTemplates::route('/'),
            'edit' => EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
