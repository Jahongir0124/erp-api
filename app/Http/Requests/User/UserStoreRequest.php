<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class UserStoreRequest extends FormRequest
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
            "name" => ['required', 'string', 'max:255', 'unique:users,name'],
            'email' => ['required', 'string', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'exists:roles,name']
        ];
    }


    #[Override]
    public function messages(): array
    {
        return [
            'name.required' => 'Foydalanuvchi nomi majburiy',
            'name.unique' => 'Bunday nom bilan foydalanuvchi mavjud',
            'email.required' => 'Email yuborish majburiy',
            'email.unique' => 'Bunday email mavjud',
            'password.min' => 'Parol uzunligi minimal 8 ta belgi',
            'password.required' => 'Parol kiritilsihi shart',
            'role.exists' => 'Bunday role mavjud emas!' 
        ];
    }
}
