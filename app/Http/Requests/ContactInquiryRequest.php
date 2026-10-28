<?php

namespace App\Http\Requests;

use App\Enums\ContentStatus;
use App\Enums\InquiryTopic;
use App\Enums\InquiryType;
use Illuminate\Validation\Rule;

class ContactInquiryRequest extends PublicFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isProject = $this->input('type') === InquiryType::Project->value;
        $attachments = $isProject && (bool) settings('forms.attachments_enabled', true);

        return [
            'type' => ['required', Rule::enum(InquiryType::class)],
            // Project briefs are always sales; the quick form asks what the message is about.
            'topic' => [$isProject ? 'nullable' : 'required', Rule::enum(InquiryTopic::class)],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+()\-.\s]{6,40}$/'],
            'company' => ['nullable', 'string', 'max:160'],
            'service_id' => ['nullable', 'integer', Rule::exists('services', 'id')->where('status', ContentStatus::Published->value)->whereNull('deleted_at')],
            'budget_range' => ['nullable', 'string', Rule::in((array) settings('forms.budget_ranges', []))],
            'timeline' => ['nullable', 'string', Rule::in((array) settings('forms.timelines', []))],
            'subject' => [$isProject ? 'required' : 'nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            'privacy_consent' => ['accepted'],
            'attachment' => $attachments
                ? ['nullable', 'file', 'mimes:'.implode(',', (array) config('softec.uploads.inquiry_attachment_mimes')), 'max:'.(int) config('softec.uploads.max_attachment_kb')]
                : ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['service_id' => 'service of interest', 'budget_range' => 'budget', 'message' => 'message', 'topic' => 'topic'];
    }
}
