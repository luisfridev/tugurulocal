<?php

namespace App\Support;

use App\Models\Listing;
use DateTimeInterface;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class ListingMediaUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        if ($this->media->disk !== 'listing_media') {
            return parent::getUrl();
        }

        if (! $this->media->model instanceof Listing) {
            return route('taxonomy.media', ['type' => $this->media->model->getTable(), 'record' => $this->media->model_id, 'media' => $this->media->id, 'conversion' => $this->conversion?->getName()]);
        }

        return route('listings.media', ['listing' => $this->media->model->slug, 'media' => $this->media->id, 'conversion' => $this->conversion?->getName()]);
    }

    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        return $this->media->disk === 'listing_media' ? $this->getUrl() : parent::getTemporaryUrl($expiration, $options);
    }
}
