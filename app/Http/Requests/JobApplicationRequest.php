<?php

namespace App\Http\Requests;

class JobApplicationRequest extends PublicFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+()\-.\s]{6,40}$/'],
            'location' => ['nullable', 'string', 'max:120'],
            'linkedin_url' => ['nullable', 'url:https', 'max:2048'],
            'portfolio_url' => ['nullable', 'url:https,http', 'max:2048'],
            'cover_letter' => ['nullable', 'string', 'max:5000'],
            'resume' => ['required', 'file', 'mimes:'.implode(',', (array) config('softec.uploads.resume_mimes')), 'max:'.(int) config('softec.uploads.max_attachment_kb')],
            'privacy_consent' => ['accepted'],
        ];
    }
}
