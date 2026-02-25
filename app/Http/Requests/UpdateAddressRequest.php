<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'label' => ['nullable', 'string', 'max:100'],
            'city' => ['sometimes', 'required', 'string', 'max:100'],
            'address_line' => ['sometimes', 'required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'phone' => ['sometimes', 'required', 'string', 'max:50'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }
}
