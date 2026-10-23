<?php

namespace App\Support;

use Illuminate\Support\Str;

class SubmissionIdentity
{
    public const INSTAGRAM_PATTERN = '/\A(?!\.)(?!.*\.\.)(?!.*\.$)[a-zA-Z0-9._]{1,30}\z/';

    public static function text(string $value): string
    {
        return hash('sha256', Str::slug($value, ' '));
    }

    public static function url(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }
        $parts = parse_url($url);
        $query = [];
        parse_str($parts['query'] ?? '', $query);
        foreach (array_keys($query) as $key) {
            if (str_starts_with($key, 'utm_')) {
                unset($query[$key]);
            }
        }
        ksort($query);

        return hash('sha256', preg_replace('/^www\./', '', strtolower($parts['host'] ?? '')).(isset($parts['port']) ? ':'.$parts['port'] : '').rtrim($parts['path'] ?? '', '/').'?'.http_build_query($query));
    }
}
