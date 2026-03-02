<?php

declare(strict_types=1);

namespace App\Http\Requests\Definitions;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request for creating a country.
 *
 * Handles validation for country creation.
 */
class CountryCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:10', 'unique:countries,code'],
            'name' => ['required', 'string', 'max:255'],
            'is_default' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Ülke kodu zorunludur.',
            'code.unique' => 'Bu ülke kodu zaten kullanılıyor.',
            'code.max' => 'Ülke kodu en fazla 10 karakter olabilir.',
            'name.required' => 'Ülke adı zorunludur.',
            'name.max' => 'Ülke adı en fazla 255 karakter olabilir.',
        ];
    }
}
