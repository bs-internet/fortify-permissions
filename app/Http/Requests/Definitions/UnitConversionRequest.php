<?php

declare(strict_types=1);

namespace App\Http\Requests\Definitions;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request for managing unit conversions.
 */
class UnitConversionRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'to_unit_id' => ['required', 'uuid', 'exists:units,id', 'different:from_unit_id'],
            'factor' => ['required', 'numeric', 'gt:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'to_unit_id.required' => 'Hedef birim zorunludur.',
            'to_unit_id.exists' => 'Seçilen birim bulunamadı.',
            'to_unit_id.different' => 'Hedef birim kaynak birimden farklı olmalıdır.',
            'factor.required' => 'Dönüşüm katsayısı zorunludur.',
            'factor.numeric' => 'Dönüşüm katsayısı sayısal olmalıdır.',
            'factor.gt' => 'Dönüşüm katsayısı sıfırdan büyük olmalıdır.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'from_unit_id' => $this->route('unit')?->id,
        ]);
    }
}
