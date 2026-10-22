<?php

namespace App\Http\Controllers;

use App\Actions\Listings\RecordInteraction;
use App\Models\Listing;
use App\Queries\PublicListingQuery;
use App\Support\ListingPageData;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ListingController extends Controller
{
    public function __construct(private PublicListingQuery $query, private RecordInteraction $interactions, private ListingPageData $page) {}

    public function show(Request $request, string $listing): Response
    {
        $record = $this->query->find($listing);
        $this->interactions->handle($record, 'listing.view', $request);

        return response()->view('listings.show', ['listing' => $record, 'preview' => false, ...$this->page->make($record, false)]);
    }

    public function preview(Listing $listing): Response
    {
        return response()->view('listings.show', ['listing' => $listing->load(['city', 'area', 'primaryCategory', 'categories', 'media']), 'preview' => true, ...$this->page->make($listing, true)])
            ->header('X-Robots-Tag', 'noindex, nofollow')->header('Cache-Control', 'private, no-store');
    }
}
