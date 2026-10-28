<?php

namespace App\Http\Requests;

use App\Enums\ContentStatus;
use Illuminate\Validation\Rule;

class FeedbackRequest extends PublicFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rating = ['nullable', 'integer', 'between:1,5'];

        return [
            'client_name' => ['required', 'string', 'max:120'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'string', 'email:rfc', 'max:190'],
            'service_id' => ['nullable', 'integer', Rule::exists('services', 'id')->where('status', ContentStatus::Published->value)->whereNull('deleted_at')],
            'project_name' => ['nullable', 'string', 'max:160'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'rating_communication' => $rating,
            'rating_quality' => $rating,
            'rating_delivery' => $rating,
            'feedback' => ['required', 'string', 'min:20', 'max:3000'],
            'publish_permission' => ['nullable', 'boolean'],
            'display_identity_consent' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4000,max_height=4000'],
            'privacy_consent' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return parent::messages() + ['rating.required' => 'Please choose an overall rating from 1 to 5 stars.'];
    }
}
