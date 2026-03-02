<?php

declare(strict_types=1);

namespace App\Http\Requests\Users;

use App\Enums\UserStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request for creating a user.
 *
 * Handles validation for user creation.
 */
class UserCreateRequest extends FormRequest
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
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'title' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::enum(UserStatus::class)],
            'language_id' => ['nullable', 'exists:languages,id'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator): void {
            $roles = $this->input('roles', []);
            $permissions = $this->input('permissions', []);

            if (empty($roles) && empty($permissions)) {
                $validator->errors()->add('roles', 'En az bir rol veya bir yetki seçilmelidir.');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Ad alanı zorunludur.',
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi zaten kullanılıyor.',
'status.required' => 'Durum alanı zorunludur.',
            'language_id.exists' => 'Seçilen dil geçersiz.',
            'roles.*.exists' => 'Seçilen rollerden biri geçersiz.',
            'permissions.*.exists' => 'Seçilen yetkilerden biri geçersiz.',
        ];
    }
}
