<?php

declare(strict_types=1);

namespace App\Http\Requests\Definitions;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request for creating a tax.
 *
 * Handles validation for tax creation.
 */
class TaxCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'countries' => ['array'],
            'countries.*' => ['string', 'exists:countries,id'],
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
            'name.required' => 'Vergi adı zorunludur.',
            'name.max' => 'Vergi adı en fazla 255 karakter olabilir.',
            'rate.required' => 'Vergi oranı zorunludur.',
            'rate.numeric' => 'Vergi oranı sayısal bir değer olmalıdır.',
            'rate.min' => 'Vergi oranı 0\'dan küçük olamaz.',
            'rate.max' => 'Vergi oranı 100\'den büyük olamaz.',
            'countries.*.exists' => 'Seçilen ülke geçerli değil.',
        ];
    }
}
