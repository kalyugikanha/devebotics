<?php

namespace App\Filament\Pages\Settings;

use App\Filament\AdminNavigationGroup;
use BackedEnum;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class FormsPrivacySettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Settings;

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Forms, privacy & features';

    protected static ?string $title = 'Forms, privacy & features';

    protected static ?string $slug = 'settings/forms-privacy';

    protected static function settingsGroups(): array
    {
        return ['forms', 'privacy', 'features'];
    }

    protected function formComponents(): array
    {
        $emails = fn (string $field, string $label, string $help) => TagsInput::make($field)
            ->label($label)
            ->placeholder('name@company.com')
            ->nestedRecursiveRules(['email:rfc', 'max:190'])
            ->splitKeys(['Tab', ',', ' '])
            ->helperText($help);

        return [
            Tabs::make('Settings')->persistTabInQueryString()->tabs([
                Tab::make('Forms')->icon('heroicon-o-inbox')->schema([
                    Section::make('Notification recipients')
                        ->description('Staff with the relevant permission also receive in-panel notifications. When empty, the sales or general company email is used.')
                        ->schema([
                            $emails('forms.inquiry_recipients', 'New inquiry alerts', 'Contact and project inquiries.'),
                            $emails('forms.feedback_recipients', 'Client feedback alerts', 'New feedback awaiting moderation.'),
                            $emails('forms.careers_recipients', 'Job application alerts', 'New job applications.'),
                        ]),
                    Section::make('Behaviour')->columns(2)->schema([
                        Toggle::make('forms.send_acknowledgements')->label('Send acknowledgement emails to visitors'),
                        Toggle::make('forms.feedback_email_verification')->label('Ask feedback submitters to confirm their email'),
                        Toggle::make('forms.attachments_enabled')->label('Allow attachments on project inquiries'),
                    ]),
                    Section::make('Query desk')
                        ->description('Inquiries without a first response within this time are flagged as overdue in the query desk.')
                        ->columns(2)
                        ->schema([
                            TextInput::make('forms.response_target_hours')->label('First response target (hours)')
                                ->numeric()->integer()->minValue(1)->maxValue(720)->required(),
                        ]),
                    Section::make('Project inquiry options')->columns(2)->schema([
                        TagsInput::make('forms.budget_ranges')->label('Budget ranges')->nestedRecursiveRules(['string', 'max:60'])->reorderable(),
                        TagsInput::make('forms.timelines')->label('Timelines')->nestedRecursiveRules(['string', 'max:60'])->reorderable(),
                    ]),
                ]),
                Tab::make('Privacy')->icon('heroicon-o-lock-closed')->schema([
                    Section::make('Consent')->columns(2)->schema([
                        Toggle::make('privacy.cookie_banner_enabled')->label('Show cookie consent banner')
                            ->helperText('Required when analytics or marketing tags are enabled.'),
                        TextInput::make('privacy.consent_version')->label('Consent / policy version')->required()->maxLength(32)->regex('/^[A-Za-z0-9.\-_]+$/')
                            ->helperText('Change this after updating your policies to ask visitors for consent again.'),
                        Textarea::make('privacy.form_privacy_notice')->label('Privacy notice shown on forms')->rows(3)->maxLength(500)->columnSpanFull(),
                    ]),
                    Section::make('Data retention')
                        ->description('Applied nightly by the softec:prune-data scheduled command.')
                        ->columns(2)
                        ->schema([
                            TextInput::make('privacy.inquiry_retention_days')->label('Closed inquiries (days)')->numeric()->integer()->minValue(30)->maxValue(3650)->required(),
                            TextInput::make('privacy.application_retention_days')->label('Job applications (days)')->numeric()->integer()->minValue(30)->maxValue(3650)->required(),
                        ]),
                ]),
                Tab::make('Features')->icon('heroicon-o-squares-2x2')->schema([
                    Grid::make(2)->schema([
                        Toggle::make('features.blog_comments_enabled')->label('Blog comments (moderated)'),
                        Toggle::make('features.newsletter_enabled')->label('Newsletter sign-up'),
                        Toggle::make('features.careers_enabled')->label('Careers section'),
                        Toggle::make('features.open_applications_enabled')->label('Open (general) job applications'),
                        Toggle::make('features.feedback_form_enabled')->label('Public client feedback form'),
                        Toggle::make('features.search_enabled')->label('Site search'),
                        Toggle::make('features.show_demo_badges')->label('Label demo content publicly')
                            ->helperText('Keep enabled until all seeded demo content has been replaced.'),
                    ]),
                ]),
            ]),
        ];
    }

    protected function mutateGroupBeforeSave(string $group, array $values): array
    {
        $values = parent::mutateGroupBeforeSave($group, $values);

        foreach (['inquiry_recipients', 'feedback_recipients', 'careers_recipients', 'budget_ranges', 'timelines'] as $listKey) {
            if (array_key_exists($listKey, $values)) {
                $values[$listKey] = array_values(array_unique(array_filter(array_map('trim', (array) $values[$listKey]))));
            }
        }

        foreach (['inquiry_retention_days', 'application_retention_days', 'response_target_hours'] as $intKey) {
            if (array_key_exists($intKey, $values)) {
                $values[$intKey] = (int) $values[$intKey];
            }
        }

        return $values;
    }
}
