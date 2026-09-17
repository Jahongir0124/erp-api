<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class InventoryRequest extends FormRequest
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
                'max:100'
            ],
            'stock_status' => [
                'nullable',
                Rule::in([
                    'in_stock',
                    'low_stock',
                    'out_of_stock'
                ])
            ],
            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,id'
            ]
        ];
    }
}
