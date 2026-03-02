<?php

declare(strict_types=1);

namespace App\Http\Requests\Definitions;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request for creating a currency.
 *
 * Handles validation for currency creation.
 */
class CurrencyCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:10', 'unique:currencies,code'],
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['required', 'string', 'max:10'],
            'decimal_places' => ['integer', 'min:0', 'max:10'],
            'thousand_separator' => ['string', 'max:5'],
            'decimal_separator' => ['string', 'max:5'],
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
            'code.required' => 'Para birimi kodu zorunludur.',
            'code.unique' => 'Bu para birimi kodu zaten kullanılıyor.',
            'code.max' => 'Para birimi kodu en fazla 10 karakter olabilir.',
            'name.required' => 'Para birimi adı zorunludur.',
            'symbol.required' => 'Para birimi sembolü zorunludur.',
            'symbol.max' => 'Sembol en fazla 10 karakter olabilir.',
        ];
    }
}
