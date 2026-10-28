<?php

namespace App\Filament\Resources\ContactInquiries\Pages;

use App\Filament\Resources\ContactInquiries\Actions\InquiryActions;
use App\Filament\Resources\ContactInquiries\ContactInquiryResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewContactInquiry extends ViewRecord
{
    protected static string $resource = ContactInquiryResource::class;

    protected function resolveRecord(int|string $key): Model
    {
        return parent::resolveRecord($key)->load(['activities.user', 'emailLogs', 'service', 'assignee', 'team', 'duplicateOf']);
    }

    public function getTitle(): string
    {
        return 'Inquiry '.$this->getRecord()->reference;
    }

    protected function getHeaderActions(): array
    {
        return [
            InquiryActions::claim(),
            InquiryActions::assign(),
            InquiryActions::changeStatus(),
            InquiryActions::addNote(),
            ActionGroup::make([
                InquiryActions::moveToTeam(),
                InquiryActions::changePriority(),
                InquiryActions::scheduleFollowUp(),
                InquiryActions::emailClient(),
                InquiryActions::downloadAttachment(),
                InquiryActions::toggleSpam(),
                DeleteAction::make(),
            ]),
        ];
    }
}
