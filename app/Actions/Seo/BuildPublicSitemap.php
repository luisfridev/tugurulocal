<?php

namespace App\Actions\Seo;

use App\Models\Listing;
use App\Queries\PublicListingQuery;
use App\Support\PublicIndexability;
use App\Support\PublicTaxonomy;
use App\Support\PublicUrl;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class BuildPublicSitemap
{
    public function __construct(private PublicListingQuery $listings, private PublicTaxonomy $taxonomy) {}

    public function handle(): Sitemap
    {
        $sitemap = Sitemap::create();

        foreach ($this->taxonomy->cities()->get() as $city) {
            $realListings = $this->listings->inCity($city)->where('is_demo', false)->with('categories')->get();
            if (! PublicIndexability::discovery($realListings)) {
                continue;
            }

            $sitemap->add(Url::create(PublicUrl::city($city)));
            $sitemap->add(Url::create(PublicUrl::businesses($city)));

            $categoryIds = $realListings->flatMap(fn (Listing $listing): array => $listing->categories->modelKeys())->unique()->all();
            foreach ($this->taxonomy->categories()->whereIn('id', $categoryIds)->orderBy('id')->get() as $category) {
                $sitemap->add(Url::create(PublicUrl::category($city, $category)));
            }

            $areaIds = $realListings->pluck('area_id')->filter()->unique()->all();
            foreach ($this->taxonomy->areas($city)->where('is_demo', false)->whereIn('id', $areaIds)->orderBy('id')->get() as $area) {
                $sitemap->add(Url::create(PublicUrl::area($city, $area)));
            }

            foreach ($realListings as $listing) {
                $sitemap->add(Url::create(PublicUrl::listing($listing)));
            }
        }

        return $sitemap;
    }
}
