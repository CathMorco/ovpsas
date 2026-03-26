<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'lowercase', 
                'email', 
                'max:255', 
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            // Avatar validation: Max 1MB (1024kb), allowed web formats
            'avatar'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:1024'], 
            'office_id'   => ['required', 'exists:offices,id'],
            'designation' => ['required', 'string', 'max:255'],
            'suffix'      => ['nullable', 'string', 'max:10'],
            'phone'       => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * Optional: Custom error messages if you want specific wording for the avatar
     */
    public function messages(): array
    {
        return [
            'avatar.image' => 'The file must be an image.',
            'avatar.max'   => 'The avatar may not be greater than 1MB.',
        ];
    }
}