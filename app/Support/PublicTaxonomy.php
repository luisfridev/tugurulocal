<?php

namespace App\Support;

use App\Actions\Listings\SaveTaxonomy;
use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PublicTaxonomy
{
    public function cities(): Builder
    {
        return City::query()->where('slug', 'playa-del-carmen')->where('is_public', true);
    }

    public function categories(): Builder
    {
        return Category::query()->where('is_active', true)->where('listing_type', 'business')
            ->whereNotIn('slug', SaveTaxonomy::RESERVED)->where('slug', '!=', 'lugares-publicos');
    }

    public function areas(City $city): Builder
    {
        return Area::query()->where('city_id', $city->id)->where('is_active', true)
            ->when(! app()->environment(['local', 'testing']), fn (Builder $query): Builder => $query->where('is_demo', false));
    }

    public function city(string $slug): City
    {
        return $this->cities()->with('media')->where('slug', $slug)->firstOrFail();
    }

    public function category(string $slug): Category
    {
        return $this->categories()->with('media')->where('slug', $slug)->firstOrFail();
    }

    public function area(City $city, string $slug): Area
    {
        return $this->areas($city)->with('media')->where('slug', $slug)->firstOrFail();
    }

    public function visible(Model $record): bool
    {
        return match (true) {
            $record instanceof City => $this->cities()->whereKey($record->id)->exists(),
            $record instanceof Category => $this->cities()->exists() && $this->categories()->whereKey($record->id)->exists(),
            $record instanceof Area => $this->cities()->whereKey($record->city_id)->exists()
                && $record->is_active && (app()->environment(['local', 'testing']) || ! $record->is_demo),
            default => false,
        };
    }

    public function authorizedMedia(Model $record, string $collection): ?Media
    {
        if (! in_array($collection, ['icon', 'og_image'], true) || ! $record->{$collection.'_approved'}
            || blank($record->{$collection.'_alt'}) || ! $record->{$collection.'_reviewed_at'}
            || $record->{$collection.'_reviewed_at'}->toDateString() > now('America/Cancun')->toDateString()) {
            return null;
        }
        $media = $record->getFirstMedia($collection);
        if (! $media || $media->disk !== 'listing_media' || ! in_array($media->mime_type, ['image/jpeg', 'image/png', 'image/webp'], true)
            || (! app()->environment(['local', 'testing']) && $media->getCustomProperty('is_demo', false))
            || ! Storage::disk('listing_media')->exists($media->getPathRelativeToRoot())) {
            return null;
        }

        return $media;
    }

    public function mediaUrl(Model $record, string $collection): ?string
    {
        $media = $this->authorizedMedia($record, $collection);
        if (! $media) {
            return null;
        }
        $conversion = $media->conversions_disk === 'listing_media' && $media->hasGeneratedConversion('card')
            && Storage::disk('listing_media')->exists($media->getPathRelativeToRoot('card')) ? 'card' : null;

        return route('taxonomy.public-media', ['type' => $record->getTable(), 'record' => $record->id, 'media' => $media->id, 'conversion' => $conversion]);
    }
}
