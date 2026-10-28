<?php

namespace App\Filament\Resources\ContactInquiries;

use App\Enums\InquiryStatus;
use App\Filament\AdminNavigationGroup;
use App\Filament\Resources\ContactInquiries\Pages\ListContactInquiries;
use App\Filament\Resources\ContactInquiries\Pages\ViewContactInquiry;
use App\Filament\Resources\ContactInquiries\Schemas\ContactInquiryInfolist;
use App\Filament\Resources\ContactInquiries\Tables\ContactInquiriesTable;
use App\Models\ContactInquiry;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ContactInquiryResource extends Resource
{
    protected static ?string $model = ContactInquiry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Leads;

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Inquiries';

    protected static ?string $modelLabel = 'inquiry';

    protected static ?string $pluralModelLabel = 'inquiries';

    protected static ?string $recordTitleAttribute = 'reference';

    public static function infolist(Schema $schema): Schema
    {
        return ContactInquiryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactInquiriesTable::configure($table);
    }

    /**
     * Record-level visibility: without `inquiries.view_all`, users only see
     * (and can only open) inquiries assigned to them. Also applies to route
     * model binding, so guessing another inquiry's URL returns 404.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        return $user instanceof User ? $query->visibleTo($user) : $query->whereRaw('1 = 0');
    }

    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'name', 'email', 'company'];
    }

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();

        if (! $user instanceof User || ! $user->canAny(['inquiries.view', 'inquiries.view_team', 'inquiries.view_all'])) {
            return null;
        }

        $count = ContactInquiry::query()->visibleTo($user)->where('status', InquiryStatus::New->value)->where('is_spam', false)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'New inquiries';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactInquiries::route('/'),
            'view' => ViewContactInquiry::route('/{record}'),
        ];
    }
}
