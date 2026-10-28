<?php

namespace App\Filament\Resources\SecurityLogs\Pages;

use App\Filament\Resources\SecurityLogs\SecurityLogResource;
use Filament\Resources\Pages\ListRecords;

class ListSecurityLogs extends ListRecords
{
    protected static string $resource = SecurityLogResource::class;
}
