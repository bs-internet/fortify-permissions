<?php

declare(strict_types=1);

namespace App\Services\Settings;

use App\Events\SettingsUpdated;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Setting Service
 *
 * Service implementation for managing system settings with caching support
 * and physical file management for branding assets.
 */
class SettingService
{
    /**
     * Cache key for all settings.
     */
    private const CACHE_KEY = 'settings.all';

    /**
     * Cache TTL in seconds (1 hour).
     */
    private const CACHE_TTL = 3600;

    /**
     * Storage disk for settings assets.
     */
    private const STORAGE_DISK = 'public';

    /**
     * Directory for uploaded settings files.
     */
    private const UPLOAD_DIRECTORY = 'settings';

    /**
     * Get a setting value by key.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->getCachedSettings();

        return $settings[$key] ?? $default;
    }

    /**
     * Set a single setting value. Creates the record if it doesn't exist.
     *
     * @param string $key The setting key
     * @param mixed $value The value to set (string, UploadedFile, etc.)
     * @param string $type The value type (default: 'string')
     * @return array{old: string|null, new: string}|null Returns change info or null if unchanged
     */
    public function set(string $key, mixed $value, string $type = 'string'): ?array
    {
        $setting = Setting::firstOrCreate(
            ['key' => $key],
            ['value' => '', 'type' => $type]
        );

        $oldValue = $setting->value;

        if ($value instanceof UploadedFile) {
            $value = $this->handleFileUpload($setting, $value);
        }

        if ($oldValue === (string) $value) {
            return null;
        }

        $setting->setTypedValue($value);
        $setting->save();

        $this->clearCache();

        return ['old' => $oldValue, 'new' => (string) $value];
    }

    /**
     * Update multiple settings and dispatch event.
     *
     * @param User $user
     * @param array<string, mixed> $data
     * @param string $ipAddress
     * @param string $userAgent
     */
    public function update(User $user, array $data, string $ipAddress, string $userAgent): void
    {
        $changes = [];

        foreach ($data as $key => $value) {
            $change = $this->set($key, $value);

            if ($change !== null) {
                $changes[$key] = $change;
            }
        }

        if (!empty($changes)) {
            event(new SettingsUpdated(
                user: $user,
                changes: $changes,
                ipAddress: $ipAddress,
                userAgent: $userAgent
            ));
        }
    }

    /**
     * Handle physical file upload and delete old file if exists.
     */
    private function handleFileUpload(Setting $setting, UploadedFile $file): string
    {
        if ($setting->value) {
            Storage::disk(self::STORAGE_DISK)->delete($setting->value);
        }

        return $file->store(self::UPLOAD_DIRECTORY, self::STORAGE_DISK);
    }

    /**
     * Get all settings as key-value pairs.
     */
    public function all(): array
    {
        return $this->getCachedSettings();
    }

    /**
     * Clear the settings cache.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get all settings from cache or database.
     */
    private function getCachedSettings(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Setting::all()
                ->mapWithKeys(fn(Setting $setting) => [$setting->key => $setting->typed_value])
                ->toArray();
        });
    }
}
