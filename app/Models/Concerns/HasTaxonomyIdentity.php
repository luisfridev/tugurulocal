<?php

namespace App\Models\Concerns;

use App\Support\IdentityColor;
use App\Support\TaxonomyContent;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasTaxonomyIdentity
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        foreach (['og_image', 'icon'] as $collection) {
            $this->addMediaCollection($collection)->useDisk('listing_media')->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])->singleFile();
        }
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('card')->fit(Fit::Max, 1200, 900)->format('webp')->quality(82)->nonQueued()->performOnCollections('og_image', 'icon');
    }

    public function identityBackground(): string
    {
        return IdentityColor::background($this->primary_color);
    }

    public function identityForeground(): string
    {
        return IdentityColor::foreground($this->primary_color);
    }

    public function sanitizedDescription(): string
    {
        return TaxonomyContent::sanitize($this->description);
    }
}
