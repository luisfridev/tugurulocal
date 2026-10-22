<?php

namespace App\Support;

class IdentityColor
{
    public const BRAND = '#2C254E';

    public static function background(?string $hex): string
    {
        return preg_match('/^#[0-9a-fA-F]{6}$/', $hex ?? '') ? strtoupper($hex) : self::BRAND;
    }

    public static function foreground(?string $hex): string
    {
        $hex = self::background($hex);
        $channels = array_map(function (int $offset) use ($hex): float {
            $value = hexdec(substr($hex, $offset, 2)) / 255;

            return $value <= 0.04045 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
        }, [1, 3, 5]);
        $luminance = 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];

        return ($luminance + 0.05) / 0.05 >= 1.05 / ($luminance + 0.05) ? '#000000' : '#FFFFFF';
    }
}
