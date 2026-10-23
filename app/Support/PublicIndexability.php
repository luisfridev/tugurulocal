<?php

namespace App\Support;

use App\Models\Listing;
use Illuminate\Support\Collection;

class PublicIndexability
{
    public static function listing(Listing $listing, bool $preview = false): bool
    {
        return ! $preview && ! $listing->is_demo;
    }

    /** @param Collection<int, Listing> $listings */
    public static function discovery(Collection $listings, bool $filtered = false): bool
    {
        return ! $filtered && $listings->contains(fn (Listing $listing): bool => self::listing($listing));
    }
}
