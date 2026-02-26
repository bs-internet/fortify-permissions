<?php

declare(strict_types=1);

namespace App\Http\Requests\Definitions;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request for updating a language.
 *
 * Handles validation for language updates.
 */
class LanguageUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:10', 'unique:languages,code,' . $this->route('language')->id],
            'name' => ['required', 'string', 'max:255'],
            'native_name' => ['required', 'string', 'max:255'],
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
            'code.required' => 'Dil kodu zorunludur.',
            'code.unique' => 'Bu dil kodu zaten kullanılıyor.',
            'code.max' => 'Dil kodu en fazla 10 karakter olabilir.',
            'name.required' => 'Dil adı zorunludur.',
            'native_name.required' => 'Yerel ad zorunludur.',
        ];
    }
}
