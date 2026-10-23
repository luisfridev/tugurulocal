<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DiscoveryPageData
{
    public function __construct(private PublicTaxonomy $public) {}

    /** @return array{title: string, tagline: ?string, description: string, background: string, foreground: string, iconUrl: ?string, iconAlt: ?string} */
    public function identity(Model $record, ?string $title = null): array
    {
        return ['title' => $title ?? $record->name, 'tagline' => DemoTaxonomyIdentity::publicText($record, $record->tagline),
            'description' => TaxonomyContent::sanitize(DemoTaxonomyIdentity::publicText($record, $record->description)),
            'background' => $record->identityBackground(), 'foreground' => $record->identityForeground(),
            'iconUrl' => $this->public->mediaUrl($record, 'icon'), 'iconAlt' => $record->icon_alt];
    }

    /** @return array{title: string, description: string, canonical: string, noindex: bool, socialImage: ?string} */
    public function seo(Model $record, string $title, string $fallbackDescription, string $canonical, bool $noindex): array
    {
        $tagline = DemoTaxonomyIdentity::publicText($record, $record->tagline);
        $description = TaxonomyContent::sanitize(DemoTaxonomyIdentity::publicText($record, $record->description));
        $description = $tagline ?: trim(html_entity_decode(strip_tags($description), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return PublicMetadata::make($title.' · Tu Gurú Local', $description ? Str::limit($description, 180) : $fallbackDescription,
            $canonical, ! $noindex, $this->public->mediaUrl($record, 'og_image'));
    }
}
