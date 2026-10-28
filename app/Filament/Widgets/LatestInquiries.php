<?php

namespace App\Filament\Widgets;

use App\Models\ContactInquiry;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class LatestInquiries extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return (bool) Auth::user()?->canAny(['inquiries.view', 'inquiries.view_team', 'inquiries.view_all']);
    }

    public function table(Table $table): Table
    {
        /** @var User $user */
        $user = Auth::user();

        return $table
            ->heading('Latest inquiries')
            ->query(ContactInquiry::query()->visibleTo($user)->where('is_spam', false)->with(['service', 'assignee'])->latest())
            ->paginated([5])
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('reference')->fontFamily('mono')->weight('medium'),
                TextColumn::make('name')->description(fn (ContactInquiry $record): ?string => $record->company),
                TextColumn::make('service.title')->label('Service')->placeholder('—'),
                TextColumn::make('status')->badge(),
                TextColumn::make('priority')->badge(),
                TextColumn::make('assignee.name')->label('Assigned to')->placeholder('Unassigned'),
                TextColumn::make('created_at')->label('Received')->since(),
            ])
            ->recordUrl(fn (ContactInquiry $record): ?string => Route::has('filament.admin.resources.contact-inquiries.view')
                ? route('filament.admin.resources.contact-inquiries.view', $record)
                : null)
            ->emptyStateHeading('No inquiries yet');
    }
}
