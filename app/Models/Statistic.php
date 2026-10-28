<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\FlagsDemoContent;
use App\Models\Concerns\HasActiveFlag;
use App\Models\Concerns\Sortable;
use Database\Factories\StatisticFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

#[Fillable(['label', 'value', 'decimals', 'prefix', 'suffix', 'description', 'icon', 'is_verified', 'sort_order', 'is_active', 'is_demo'])]
class Statistic extends Model
{
    /** @use HasFactory<StatisticFactory> */
    use Auditable, FlagsDemoContent, HasActiveFlag, HasFactory, Sortable;

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'decimals' => 'integer',
            'is_verified' => 'boolean',
        ];
    }

    /**
     * Statistics are displayed only once an administrator has verified them.
     */
    #[Scope]
    protected function publishable(Builder $query): void
    {
        $query->where('is_active', true)->where('is_verified', true);
    }

    public function formattedValue(): string
    {
        return ($this->prefix ?? '').Number::format((float) $this->value, $this->decimals).($this->suffix ?? '');
    }
}
