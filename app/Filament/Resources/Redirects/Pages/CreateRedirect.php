<?php

namespace App\Filament\Resources\Redirects\Pages;

use App\Filament\Resources\Redirects\RedirectResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateRedirect extends CreateRecord
{
    protected static string $resource = RedirectResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();
        $data['is_automatic'] = false;

        $data['status_code'] = (int) ($data['status_code'] instanceof \BackedEnum ? $data['status_code']->value : $data['status_code']);

        if ($data['status_code'] === 410) {
            $data['to_url'] = null;
        }

        return $data;
    }
}
