<?php

declare(strict_types=1);

use App\Services\Settings\SettingService;

if (!function_exists('settings')) {
    /**
     * Get a setting value by key.
     *
     * @param string $key The setting key
     * @param mixed $default Default value if setting not found
     * @return mixed
     *
     * @example
     * settings('site_name') → 'Herkobi'
     * settings('email') → 'info@herkobi.com'
     * settings('company_name', 'N/A') → 'N/A' if not set
     */
    function settings(string $key, mixed $default = null): mixed
    {
        return app(SettingService::class)->get($key, $default);
    }
}

if (!function_exists('site_name')) {
    /**
     * Get the site name.
     *
     * @return string
     *
     * @example
     * site_name() → 'Herkobi'
     */
    function site_name(): string
    {
        return (string) settings('site_name', config('app.name'));
    }
}

if (!function_exists('site_slogan')) {
    /**
     * Get the site slogan.
     *
     * @return string|null
     *
     * @example
     * site_slogan() → 'Otomasyon Sistemi'
     */
    function site_slogan(): ?string
    {
        return settings('site_slogan');
    }
}

if (!function_exists('site_email')) {
    /**
     * Get the system email address.
     *
     * @return string
     *
     * @example
     * site_email() → 'info@herkobi.com'
     */
    function site_email(): string
    {
        return (string) settings('email', config('mail.from.address'));
    }
}

if (!function_exists('sender_name')) {
    /**
     * Get the email sender name.
     *
     * @return string
     *
     * @example
     * sender_name() → 'Herkobi'
     */
    function sender_name(): string
    {
        return (string) settings('sender_name', config('mail.from.name'));
    }
}

if (!function_exists('logo')) {
    /**
     * Get the logo URL.
     *
     * @param string $type The logo type ('light' or 'dark')
     * @return string
     *
     * @example
     * logo() → URL for light theme logo
     * logo('dark') → URL for dark theme logo
     */
    function logo(string $type = 'light'): string
    {
        $key = $type === 'dark' ? 'logo_dark' : 'logo_light';
        $path = settings($key);

        if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        // Fallback to default logo
        $defaultLogo = $type === 'dark'
            ? 'herkobi-dark.png'
            : 'herkobi.png';

        return asset($defaultLogo);
    }
}

if (!function_exists('favicon')) {
    /**
     * Get the favicon URL.
     *
     * @return string
     *
     * @example
     * favicon() → URL for favicon
     */
    function favicon(): string
    {
        $path = settings('favicon');

        if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        // Fallback to default favicon
        return asset('herkobi-favicon.png');
    }
}
