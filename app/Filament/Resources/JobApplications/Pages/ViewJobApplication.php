<?php

namespace App\Filament\Resources\JobApplications\Pages;

use App\Filament\Resources\JobApplications\Actions\JobApplicationActions;
use App\Filament\Resources\JobApplications\JobApplicationResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewJobApplication extends ViewRecord
{
    protected static string $resource = JobApplicationResource::class;

    protected function resolveRecord(int|string $key): Model
    {
        return parent::resolveRecord($key)->load(['jobOpening', 'reviewer', 'consents']);
    }

    public function getTitle(): string
    {
        return $this->getRecord()->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            JobApplicationActions::changeStatus(),
            JobApplicationActions::rate(),
            JobApplicationActions::notes(),
            ActionGroup::make([
                JobApplicationActions::downloadResume(),
                JobApplicationActions::emailCandidate(),
                DeleteAction::make(),
            ]),
        ];
    }
}
