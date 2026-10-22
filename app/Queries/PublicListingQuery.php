<?php

namespace App\Queries;

use App\Models\Listing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class PublicListingQuery
{
    public function builder(): Builder
    {
        return Listing::query()->when(! app()->environment(['local', 'testing']), fn (Builder $q): Builder => $q->where('is_demo', false))
            ->where('logo_approved', true)->whereDate('logo_reviewed_at', '<=', now('America/Cancun')->toDateString())
            ->whereNotNull('logo_alt')->where('logo_alt', '!=', '')
            ->whereHas('media', fn (Builder $q): Builder => $q->where('collection_name', 'logo')->where('disk', 'listing_media')->whereIn('mime_type', ['image/jpeg', 'image/png', 'image/webp']))
            ->where('type', 'business')->where('status', 'published')->where('content_approved', true)
            ->whereNotNull('published_at')->where('published_at', '<=', now())
            ->whereHas('city', fn (Builder $q): Builder => $q->where('is_public', true)->where('slug', 'playa-del-carmen'))
            ->whereHas('primaryCategory', fn (Builder $q): Builder => $q->where('is_active', true));
    }

    public function find(string $slug): Listing
    {
        $listing = $this->builder()->with(['city', 'area', 'primaryCategory', 'categories', 'media'])->where('slug', $slug)->firstOrFail();
        abort_unless($this->hasLogoFile($listing), 404);

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
