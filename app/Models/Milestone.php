<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\FlagsDemoContent;
use App\Models\Concerns\HasActiveFlag;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['year', 'title', 'description', 'sort_order', 'is_active', 'is_demo'])]
class Milestone extends Model
{
    use Auditable, FlagsDemoContent, HasActiveFlag, Sortable;
}
