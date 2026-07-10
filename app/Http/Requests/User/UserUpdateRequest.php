<?php

namespace App\Http\Requests\User;

use App\Enums\UserStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UserUpdateRequest extends FormRequest
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
        $user = $this->route('user');
        return [

            'name' => [
                'required',
                'string',
                Rule::unique('users', 'name')->ignore($user->id)
            ],
            'email' => [
                'required',
                'string',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'password' => [
                'nullable',
                'string',
                'min:8'
            ],
            'role' => [
                'required',
                'exists:roles,name'
            ],
            'status' => [
                'required',
                new Enum(UserStatus::class)
            ]
        ];
    }
}
