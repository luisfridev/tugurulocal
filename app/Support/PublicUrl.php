<?php

namespace App\Support;

use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;

class PublicUrl
{
    public static function listing(Listing $listing): string
    {
        return route('listings.show', ['listing' => $listing->slug]);
    }

    public static function city(City|string $city = 'playa-del-carmen'): string
    {
        return route('cities.show', ['city' => $city instanceof City ? $city->slug : $city]);
    }

    public static function businesses(City|string $city = 'playa-del-carmen'): string
    {
        return route('businesses.index', ['city' => $city instanceof City ? $city->slug : $city]);
    }

    public static function category(City $city, Category $category): string
    {
        return route('categories.show', ['city' => $city->slug, 'category' => $category->slug]);
    }

    public static function area(City $city, Area $area): string
    {
        return route('areas.show', ['city' => $city->slug, 'area' => $area->slug]);
    }

    public static function canonical(string $base, int $page = 1, bool $filtered = false): string
    {
        return $base.(! $filtered && $page > 1 ? '?page='.$page : '');
    }
}
