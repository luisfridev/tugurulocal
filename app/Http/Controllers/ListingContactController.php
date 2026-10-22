<?php

namespace App\Http\Controllers;

use App\Actions\Listings\RecordInteraction;
use App\Queries\PublicListingQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ListingContactController extends Controller
{
    public function __invoke(Request $request, string $listing, string $kind, PublicListingQuery $query, RecordInteraction $interactions): RedirectResponse
    {
        abort_unless(in_array($kind, RecordInteraction::CONTACTS, true), 404);
        $record = $query->find($listing);
        $url = match ($kind) {
            'maps' => $record->maps_url, 'website' => $record->website_url,'facebook' => $record->facebook_url,'instagram' => $record->instagram_url,
            'phone' => filled($record->phone) ? 'tel:'.preg_replace('/[^+0-9]/', '', $record->phone) : null,
            'whatsapp' => filled($record->whatsapp) ? 'https://wa.me/'.$record->whatsapp : null,
            'document' => $record->hasAuthorizedMedia() ? $record->getFirstMedia('document')?->getUrl() : null,
        };
        abort_if(blank($url), 404);
        $interactions->handle($record, 'contact.'.$kind, $request);

        return redirect()->away($url)->header('Cache-Control', 'private, no-store')->header('X-Robots-Tag', 'noindex');
    }
}
