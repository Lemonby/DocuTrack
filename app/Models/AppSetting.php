<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    /**
     * Get setting value properly casted based on its type.
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set setting value.
     */
    public static function setValue(string $key, $value, string $type = 'string')
    {
        $stringValue = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value),
            default => (string) $value,
        };

        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $stringValue, 'type' => $type]
        );
    }
}
