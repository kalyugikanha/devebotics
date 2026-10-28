<?php

namespace App\Filament\Pages\Settings;

use App\Filament\AdminNavigationGroup;
use App\Filament\Forms\MediaIdSelect;
use BackedEnum;
use Filament\Forms\Components\Repeater;
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

class CompanySettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Settings;

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Company & contact';

    protected static ?string $title = 'Company, contact & social';

    protected static ?string $slug = 'settings/company';

    protected static function settingsGroups(): array
    {
        return ['company', 'contact', 'social'];
    }

    protected function formComponents(): array
    {
        $https = 'regex:/^https:\/\//i';

        return [
            Tabs::make('Settings')->persistTabInQueryString()->tabs([
                Tab::make('Company')->icon('heroicon-o-building-office-2')->schema([
                    Section::make('Identity')->columns(2)->schema([
                        TextInput::make('company.name')->label('Company name')->required()->maxLength(120),
                        TextInput::make('company.legal_name')->label('Legal name')->maxLength(160),
                        TextInput::make('company.tagline')->maxLength(160),
                        TextInput::make('company.founded_year')->label('Founded (year)')->numeric()->minValue(1900)->maxValue((int) date('Y'))->helperText('Only enter verified facts.'),
                        Textarea::make('company.short_description')->label('Short description')->rows(3)->maxLength(400)->columnSpanFull()
                            ->helperText('Used in the footer and as the organisation description in structured data.'),
                    ]),
                    Section::make('Brand assets')->columns(2)->schema([
                        MediaIdSelect::make('company.logo_mark_media_id', 'Logo mark (icon)', category: 'brand')->helperText('Square or near-square symbol shown in the header beside the wordmark.'),
                        MediaIdSelect::make('company.favicon_media_id', 'Favicon (PNG, 512×512 recommended)', category: 'brand'),
                        MediaIdSelect::make('company.logo_light_media_id', 'Full logo — light backgrounds', category: 'brand'),
                        MediaIdSelect::make('company.logo_dark_media_id', 'Full logo — dark backgrounds', category: 'brand'),
                        Toggle::make('company.show_wordmark')->label('Show text wordmark next to the logo mark')->helperText('Disable to show the uploaded full logo instead.')->columnSpanFull(),
                        MediaIdSelect::make('company.brochure_media_id', 'Company profile / brochure (PDF)', imagesOnly: false, category: 'documents')->columnSpanFull(),
                    ]),
                    Section::make('Header call to action')->columns(2)->schema([
                        TextInput::make('company.header_cta_label')->label('Button label')->maxLength(30),
                        TextInput::make('company.header_cta_url')->label('Button link')->maxLength(2048)->regex('/^(\/[^\s]*|https:\/\/[^\s]+)$/')
                            ->helperText('A site path such as /start-a-project or a full https:// URL.'),
                    ]),
                ]),
                Tab::make('Contact')->icon('heroicon-o-phone')->schema([
                    Section::make('Email & phone')->columns(2)->schema([
                        TextInput::make('contact.email')->label('General email')->email()->maxLength(190),
                        TextInput::make('contact.sales_email')->label('Sales email')->email()->maxLength(190),
                        TextInput::make('contact.support_email')->label('Support email')->email()->maxLength(190),
                        TextInput::make('contact.phone')->label('Phone')->tel()->maxLength(40)->regex('/^[0-9+()\-.\s]{6,40}$/'),
                        TextInput::make('contact.whatsapp_number')->label('WhatsApp number')->maxLength(20)->regex('/^\+?[0-9]{8,15}$/')
                            ->helperText('International format without spaces, e.g. +15551234567. Leave empty to hide the WhatsApp link.'),
                        TextInput::make('contact.whatsapp_message')->label('WhatsApp pre-filled message')->maxLength(200),
                    ]),
                    Section::make('WhatsApp chat widget')
                        ->description('Floating chat button shown on every public page when a WhatsApp number is set.')
                        ->schema([
                            Toggle::make('contact.whatsapp_widget_enabled')->label('Show the WhatsApp chat widget'),
                            Textarea::make('contact.whatsapp_greeting')->label('Greeting message')->rows(2)->maxLength(240),
                            TagsInput::make('contact.whatsapp_quick_replies')
                                ->label('Quick-reply messages')
                                ->helperText('Visitors tap one to open WhatsApp with that message pre-filled. Up to 6.')
                                ->nestedRecursiveRules(['string', 'max:160'])
                                ->rules(['array', 'max:6']),
                        ]),
                    Section::make('Address')->columns(2)->schema([
                        Toggle::make('contact.show_address')->label('Show address publicly')->columnSpanFull(),
                        TextInput::make('contact.address_line1')->label('Address line 1')->maxLength(160),
                        TextInput::make('contact.address_line2')->label('Address line 2')->maxLength(160),
                        TextInput::make('contact.city')->maxLength(100),
                        TextInput::make('contact.region')->label('State / region')->maxLength(100),
                        TextInput::make('contact.postal_code')->maxLength(20),
                        TextInput::make('contact.country')->maxLength(100),
                    ]),
                    Section::make('Hours, map & booking')->schema([
                        Repeater::make('contact.business_hours')
                            ->label('Business hours')
                            ->schema([
                                TextInput::make('days')->required()->maxLength(60)->placeholder('Monday – Friday'),
                                TextInput::make('hours')->required()->maxLength(60)->placeholder('09:00 – 18:00'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->maxItems(7)
                            ->reorderableWithButtons()
                            ->addActionLabel('Add hours'),
                        TextInput::make('contact.map_embed_url')
                            ->label('Google Maps embed URL')
                            ->maxLength(2048)
                            ->regex('/^https:\/\/www\.google\.com\/maps\/embed\?[^\s"<>]+$/')
                            ->validationMessages(['regex' => 'Use the “Embed a map” URL from Google Maps (https://www.google.com/maps/embed?...).']),
                        TextInput::make('contact.booking_url')->label('Consultation booking URL')->url()->rule($https)->maxLength(2048)
                            ->helperText('e.g. a Calendly or Microsoft Bookings link.'),
                    ]),
                ]),
                Tab::make('Social')->icon('heroicon-o-share')->schema([
                    Grid::make(2)->schema(collect(['linkedin' => 'LinkedIn', 'x' => 'X (Twitter)', 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'github' => 'GitHub'])
                        ->map(fn (string $label, string $key) => TextInput::make('social.'.$key)->label($label)->url()->rule($https)->maxLength(2048))
                        ->values()->all()),
                ]),
            ]),
        ];
    }

    protected function afterSave(): void
    {
        foreach (['logo_mark_media_id', 'favicon_media_id', 'logo_light_media_id', 'logo_dark_media_id', 'brochure_media_id'] as $key) {
            if ($id = data_get($this->data, 'company.'.$key)) {
                cache()->forget('branding.media.'.$id);
            }
        }
    }
}
