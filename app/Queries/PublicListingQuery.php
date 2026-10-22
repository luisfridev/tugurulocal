<?php

namespace App\Queries;

use App\Models\City;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicListingQuery
{
    /** @var array<int, list<int>> */
    private array $available = [];

    private ?Request $request = null;

    public function builder(): Builder
    {
        return Listing::query()->when(! app()->environment(['local', 'testing']), fn (Builder $q): Builder => $q->where('is_demo', false))
            ->where('logo_approved', true)->whereDate('logo_reviewed_at', '<=', now('America/Cancun')->toDateString())
            ->whereNotNull('logo_alt')->where('logo_alt', '!=', '')
            ->whereHas('media', fn (Builder $q): Builder => $q->where('collection_name', 'logo')->where('disk', 'listing_media')->whereIn('mime_type', ['image/jpeg', 'image/png', 'image/webp']))
            ->where('type', 'business')->where('status', 'published')->where('content_approved', true)
            ->whereNotNull('published_at')->where('published_at', '<=', now())
            ->whereHas('city', fn (Builder $q): Builder => $q->where('is_public', true)->where('slug', 'playa-del-carmen'))
            ->whereHas('primaryCategory', fn (Builder $q): Builder => $q->where('is_active', true)->where('listing_type', 'business'));
    }

    /** @return list<int> */
    public function availableIds(City $city): array
    {
        if ($this->request !== request()) {
            $this->request = request();
            $this->available = [];
        }
        if (array_key_exists($city->id, $this->available)) {
            return $this->available[$city->id];
        }

        $ids = [];
        $this->builder()->where('city_id', $city->id)
            ->select(['listings.id', 'logo_approved', 'logo_alt', 'logo_reviewed_at'])
            ->with(['media' => fn (MorphMany $query): MorphMany => $query->where('collection_name', 'logo')])
            ->chunkById(100, function (Collection $listings) use (&$ids): void {
                foreach ($listings as $listing) {
                    if ($this->hasLogoFile($listing)) {
                        $ids[] = $listing->id;
                    }
                }
            });

        return $this->available[$city->id] = $ids;
    }

    public function inCity(City $city): Builder
    {
        return $this->builder()->where('city_id', $city->id)->whereIn('id', $this->availableIds($city));
    }

    /** @return Collection<int, Listing> */
    public function forCity(City $city): Collection
    {
        return $this->prepare($this->inCity($city)->with(['city', 'area', 'primaryCategory', 'categories', 'media'])
            ->orderByDesc('published_at')->orderByDesc('id')->get());
    }

    /** @param Collection<int, Listing> $listings
     * @return Collection<int, Listing>
     */
    public function prepare(Collection $listings): Collection
    {
        foreach ($listings as $listing) {
            foreach ($listing->media as $media) {
                $media->setRelation('model', $listing);
                if ($media->hasGeneratedConversion('card') && ! Storage::disk($media->conversions_disk)->exists($media->getPathRelativeToRoot('card'))) {
                    $media->generated_conversions = [...$media->generated_conversions, 'card' => false];
                }
            }
        }

        return $listings;
    }

    public function find(string $slug): Listing
    {
        $listing = $this->builder()->with(['city', 'area', 'primaryCategory', 'categories', 'media'])->where('slug', $slug)->firstOrFail();
        abort_unless($this->hasLogoFile($listing), 404);

        $this->prepare(new Collection([$listing]));

        return $listing;
    }

    private function hasLogoFile(Listing $listing): bool
    {
        $logo = $listing->getFirstMedia('logo');

        return $logo && $listing->hasAuthorizedLogo()
            && Storage::disk($logo->disk)->exists($logo->getPathRelativeToRoot());
    }

    public function contains(Listing $listing): bool
    {
        return $this->builder()->whereKey($listing->id)->exists() && $this->hasLogoFile($listing);
    }
}
