<?php

namespace App\Http\Requests;

class BlogCommentRequest extends PublicFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email:rfc', 'max:190'],
            'body' => ['required', 'string', 'min:5', 'max:2000'],
            'parent_id' => ['nullable', 'integer'],
            'privacy_consent' => ['accepted'],
        ];
    }
}
