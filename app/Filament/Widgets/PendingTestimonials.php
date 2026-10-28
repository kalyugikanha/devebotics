<?php

namespace App\Filament\Widgets;

use App\Enums\TestimonialStatus;
use App\Models\Testimonial;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class PendingTestimonials extends TableWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = ['lg' => 1, 'default' => 'full'];

    public static function canView(): bool
    {
        return (bool) Auth::user()?->canAny(['testimonials.view', 'testimonials.moderate']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Feedback awaiting moderation')
            ->query(Testimonial::query()->where('status', TestimonialStatus::Pending->value)->latest())
            ->paginated([5])
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('client_name')->label('Client')->description(fn (Testimonial $record): ?string => $record->company_name),
                TextColumn::make('rating')->formatStateUsing(fn (int $state): string => str_repeat('★', $state).str_repeat('☆', 5 - $state))->color('warning'),
                TextColumn::make('created_at')->label('Received')->since(),
            ])
            ->recordUrl(fn (Testimonial $record): ?string => Route::has('filament.admin.resources.testimonials.edit')
                ? route('filament.admin.resources.testimonials.edit', $record)
                : null)
            ->emptyStateHeading('Nothing to moderate')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
