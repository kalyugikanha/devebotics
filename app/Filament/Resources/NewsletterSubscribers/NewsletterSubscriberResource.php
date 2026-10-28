<?php

namespace App\Filament\Resources\NewsletterSubscribers;

use App\Enums\NewsletterStatus;
use App\Filament\AdminNavigationGroup;
use App\Filament\Resources\NewsletterSubscribers\Pages\ManageNewsletterSubscribers;
use App\Models\NewsletterSubscriber;
use App\Support\Audit\AuditLogger;
use App\Support\Csv\CsvExport;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelopeOpen;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Leads;

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Newsletter';

    protected static ?string $modelLabel = 'subscriber';

    protected static ?string $recordTitleAttribute = 'email';

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('email')->searchable()->copyable()->weight('medium'),
                TextColumn::make('name')->searchable()->placeholder('—')->toggleable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('source')->placeholder('—')->toggleable(),
                TextColumn::make('confirmed_at')->label('Confirmed')->dateTime('M j, Y')->sortable()->placeholder('—'),
                TextColumn::make('created_at')->label('Signed up')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(NewsletterStatus::class),
            ])
            ->headerActions([
                Action::make('exportConfirmed')
                    ->label('Export confirmed (CSV)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->authorize(fn (): bool => (bool) Auth::user()?->can('newsletter.manage'))
                    ->action(fn (): StreamedResponse => self::export(
                        NewsletterSubscriber::query()->where('status', NewsletterStatus::Confirmed->value)->orderBy('email')->get(),
                    )),
            ])
            ->recordActions([
                DeleteAction::make()->modalDescription('The subscriber is removed permanently. Prefer letting people unsubscribe themselves where possible.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('exportSelected')
                        ->label('Export selected (CSV)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->authorize(fn (): bool => (bool) Auth::user()?->can('newsletter.manage'))
                        ->action(fn (Collection $records): StreamedResponse => self::export($records)),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-envelope-open')
            ->emptyStateHeading('No subscribers yet')
            ->emptyStateDescription('Visitors who confirm the newsletter double opt-in appear here.');
    }

    /**
     * @param  Collection<int, NewsletterSubscriber>  $subscribers
     */
    private static function export(Collection $subscribers): StreamedResponse
    {
        app(AuditLogger::class)->log('newsletter.exported', description: 'Exported '.$subscribers->count().' newsletter subscribers');

        return CsvExport::download(
            'newsletter-subscribers-'.now()->format('Y-m-d').'.csv',
            ['Email', 'Name', 'Status', 'Confirmed at', 'Signed up'],
            $subscribers->map(fn (NewsletterSubscriber $s): array => [$s->email, $s->name, $s->status, $s->confirmed_at, $s->created_at])->all(),
        );
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageNewsletterSubscribers::route('/'),
        ];
    }
}
