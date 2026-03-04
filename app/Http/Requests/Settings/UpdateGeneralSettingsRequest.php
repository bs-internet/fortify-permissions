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
            'site_name' => ['required', 'string', 'max:255'],
            'site_slogan' => ['nullable', 'string', 'max:1000'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'sender_name' => ['required', 'string', 'max:255'],
            'mail_from_address' => ['required', 'email', 'max:255'],

            // Varsayılan tanımlamalar
            'default_language' => ['nullable', 'uuid', 'exists:languages,id'],
            'default_currency' => ['nullable', 'uuid', 'exists:currencies,id'],
            'default_country' => ['nullable', 'uuid', 'exists:countries,id'],
            'default_tax' => ['nullable', 'uuid', 'exists:taxes,id'],

            // Dosya yükleme kuralları
            'logo_light' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'logo_dark' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:png,ico', 'max:512'],
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
            'email.required' => 'Sistem e-posta adresi zorunludur.',
            'email.email' => 'Lütfen geçerli bir e-posta adresi giriniz.',
            'sender_name.required' => 'E-posta gönderen adı zorunludur.',
            'mail_from_address.required' => 'E-posta gönderen adresi zorunludur.',
            'mail_from_address.email' => 'Gönderen adresi geçerli bir e-posta olmalıdır.',

            'logo_light.image' => 'Logo (açık tema) bir resim dosyası olmalıdır.',
            'logo_light.mimes' => 'Logo (açık tema) PNG, JPG veya JPEG formatında olmalıdır.',
            'logo_light.max' => 'Logo (açık tema) boyutu en fazla 2MB olabilir.',
            'logo_dark.image' => 'Logo (koyu tema) bir resim dosyası olmalıdır.',
            'logo_dark.mimes' => 'Logo (koyu tema) PNG, JPG veya JPEG formatında olmalıdır.',
            'logo_dark.max' => 'Logo (koyu tema) boyutu en fazla 2MB olabilir.',
            'favicon.image' => 'Favicon bir resim dosyası olmalıdır.',
            'favicon.mimes' => 'Favicon PNG veya ICO formatında olmalıdır.',
            'favicon.max' => 'Favicon boyutu en fazla 512KB olabilir.',
        ];
    }
}
