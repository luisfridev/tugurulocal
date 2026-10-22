<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class DemoTaxonomyIdentity
{
    public const CITY_TAGLINE = 'DEMO · Identidad de ciudad para revisión local';

    public const CATEGORY_TAGLINE = 'DEMO · Contenido de categoría';

    public const AREA_TAGLINE = 'DEMO · Zona sintética sin ubicación real';

    public const DESCRIPTION = '<h2>Identidad DEMO</h2><p>Contenido <strong>sintético</strong> para revisión local. Conserva emojis 👨‍👩‍👧‍👦 y enlaces de ejemplo.</p><ul><li>Primera idea DEMO</li><li>Segunda idea</li></ul><blockquote>Sin datos de negocios reales.</blockquote><p><a href="https://example.org">Referencia DEMO</a></p>';

    public static function publicText(Model $record, ?string $value): ?string
    {
        if (! app()->environment(['local', 'testing']) && $record->demo_identity_seeded
            && in_array($value, [self::CITY_TAGLINE, self::CATEGORY_TAGLINE, self::AREA_TAGLINE, self::DESCRIPTION], true)) {
            return null;
        }

        return $value;
    }
}
