<?php

namespace App\Filament\Resources\ContactInquiries\Tables;

use App\Enums\InquiryPriority;
use App\Enums\InquiryStatus;
use App\Enums\InquiryTopic;
use App\Enums\InquiryType;
use App\Filament\Resources\ContactInquiries\Actions\InquiryActions;
use App\Models\ContactInquiry;
use App\Services\Crm\InquiryManager;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ContactInquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['service', 'assignee', 'team']))
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (ContactInquiry $record): string => route('filament.admin.resources.contact-inquiries.view', $record))
            ->columns([
                TextColumn::make('reference')->searchable()->copyable()->fontFamily('mono')->size('sm')->sortable(),
                TextColumn::make('name')
                    ->label('Contact')
                    ->searchable(['name', 'email', 'company'])
                    ->weight('medium')
                    ->description(fn (ContactInquiry $record): ?string => $record->company ?: $record->email),
                TextColumn::make('topic')->badge()->toggleable(),
                TextColumn::make('team.name')->label('Team')->badge()->color('gray')->placeholder('Unrouted')->toggleable(),
                TextColumn::make('service.title')->label('Service')->placeholder('—')->limit(30)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('type')->badge()->color('gray')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('priority')->badge()->sortable(),
                TextColumn::make('assignee.name')->label('Assigned to')->placeholder('Unassigned')->toggleable(),
                TextColumn::make('first_response')
                    ->label('First response')
                    ->badge()
                    ->state(fn (ContactInquiry $record): string => match (true) {
                        $record->first_response_at !== null => 'Responded',
                        $record->status->isClosed() || $record->is_spam => '—',
                        $record->isOverdue() => 'Overdue',
                        default => 'Due '.$record->responseDueAt()?->diffForHumans(),
                    })
                    ->color(fn (ContactInquiry $record): string => match (true) {
                        $record->first_response_at !== null => 'success',
                        $record->isOverdue() => 'danger',
                        default => 'gray',
                    })
                    ->tooltip(fn (ContactInquiry $record): ?string => $record->first_response_at
                        ? 'First response '.$record->first_response_at->toDayDateTimeString()
                        : ($record->status->isClosed() ? null : 'Respond by '.$record->responseDueAt()?->toDayDateTimeString()))
                    ->toggleable(),
                TextColumn::make('follow_up_at')
                    ->label('Follow-up')
                    ->dateTime('M j, H:i')
                    ->sortable()
                    ->placeholder('—')
                    ->color(fn (ContactInquiry $record): ?string => $record->follow_up_at?->isPast() ? 'danger' : null),
                TextColumn::make('created_at')->label('Received')->since()->sortable()->dateTimeTooltip(),
            ])
            ->filters([
                SelectFilter::make('status')->options(InquiryStatus::class)->multiple(),
                SelectFilter::make('priority')->options(InquiryPriority::class)->multiple(),
                SelectFilter::make('topic')->options(InquiryTopic::class)->multiple(),
                SelectFilter::make('team_id')->label('Team')->relationship('team', 'name'),
                SelectFilter::make('type')->options(InquiryType::class),
                Filter::make('overdue')
                    ->label('Overdue first response')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->overdue()),
                SelectFilter::make('assignee')
                    ->label('Assigned to')
                    ->options(fn (): array => ['me' => 'Assigned to me', 'unassigned' => 'Unassigned'] + app(InquiryManager::class)->assignableUsers(null, Auth::user())->pluck('name', 'id')->all())
                    ->query(fn (Builder $query, array $data): Builder => match ($value = $data['value'] ?? null) {
                        null, '' => $query,
                        'me' => $query->where('assigned_to', Auth::id()),
                        'unassigned' => $query->whereNull('assigned_to'),
                        default => $query->where('assigned_to', (int) $value),
                    }),
                SelectFilter::make('service_id')->label('Service')->relationship('service', 'title'),
                Filter::make('received')
                    ->schema([
                        DatePicker::make('from')->label('Received from'),
                        DatePicker::make('until')->label('Received until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date))),
                TernaryFilter::make('is_spam')->label('Spam')->default(false),
            ])
            ->recordActions([
                ViewAction::make(),
                InquiryActions::claim()->iconButton()->tooltip('Pick up'),
                ActionGroup::make([
                    InquiryActions::assign(),
                    InquiryActions::changeStatus(),
                    InquiryActions::changePriority(),
                    InquiryActions::addNote(),
                    InquiryActions::toggleSpam(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    InquiryActions::bulkAssign(),
                    InquiryActions::bulkStatus(),
                    InquiryActions::bulkSpam(),
                    InquiryActions::bulkExport(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-inbox-stack')
            ->emptyStateHeading('No inquiries yet')
            ->emptyStateDescription('Messages from the contact and project forms appear here, routed to the right team.');
    }
}
