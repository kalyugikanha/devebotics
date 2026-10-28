<?php

namespace App\Http\Requests;

use App\Support\Forms\SpamGuard;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Base request for anonymous public forms: runs honeypot, timing and
 * optional Turnstile checks before field validation.
 */
abstract class PublicFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        app(SpamGuard::class)->validate($this);

        $this->merge(collect($this->only($this->trimmable()))
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->all());
    }

    /**
     * @return array<int, string>
     */
    protected function trimmable(): array
    {
        return ['name', 'email', 'phone', 'company', 'subject', 'client_name', 'company_name'];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'privacy_consent.accepted' => 'Please confirm you have read the privacy notice.',
        ];
    }
}
