<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            
            'search' => [
                'nullable',
                'string',
                'max:300'
            ],
            'role' => [
                'nullable',
                'string',
                'exists:roles,name'
            ],
            'status' => [
                'nullable',
                'string',
                Rule::in([
                    'active',
                    'inactive',
                    'blocked'
                ])
            ],

            'sort' => [
                'nullable',
                Rule::in([
                    'asc',
                    'desc'
                ])
            ]
        ];
    }
}
