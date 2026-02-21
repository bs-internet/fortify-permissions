<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request for updating general system settings.
 *
 * Handles validation for branding assets (logo, favicon)
 * and core communication details.
 */
class UpdateGeneralSettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'site_name'    => ['required', 'string', 'max:255'],
            'site_slogan'  => ['nullable', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255'],
            'sender_name'  => ['required', 'string', 'max:255'],

            // Dosya yükleme kuralları
            'logo_light'   => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:2048'],
            'logo_dark'    => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:2048'],
            'favicon'      => ['nullable', 'image', 'mimes:png,ico', 'max:512'],
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
            'site_name.required' => 'Site adı alanı zorunludur.',
            'email.required'     => 'Sistem e-posta adresi zorunludur.',
            'email.email'        => 'Lütfen geçerli bir e-posta adresi giriniz.',
            'sender_name.required' => 'E-posta gönderen adı zorunludur.',

            'logo_light.image'   => 'Logo bir resim dosyası olmalıdır.',
            'logo_light.max'     => 'Logo boyutu en fazla 2MB olabilir.',
            'favicon.image'      => 'Favicon bir resim dosyası olmalıdır.',
            'favicon.max'        => 'Favicon boyutu en fazla 512KB olabilir.',
        ];
    }
}
