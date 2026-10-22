<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Queries\PublicListingQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListingMediaController extends Controller
{
    public function __invoke(Request $request, string $listing, int $media, PublicListingQuery $query): StreamedResponse
    {
        $record = Listing::query()->where('slug', $listing)->firstOrFail();
        $isAdmin = $request->user()?->is_super_admin && filled($request->user()?->getAppAuthenticationSecret());

        $file = $record->media()->whereKey($media)->whereIn('collection_name', ['logo', 'cover', 'gallery', 'document'])->firstOrFail();
        abort_unless($isAdmin || ($query->contains($record) && ($file->collection_name === 'logo' ? $record->hasAuthorizedLogo() : $record->hasAuthorizedMedia())), 404);
        $conversion = $request->query('conversion', '');
        abort_unless(in_array($conversion, ['', 'card'], true) && ($conversion === '' || $file->hasGeneratedConversion($conversion)), 404);
        $path = $file->getPathRelativeToRoot($conversion);
        abort_unless(Storage::disk('listing_media')->exists($path), 404);
        $headers = ['X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store', 'X-Robots-Tag' => 'noindex'];
        if ($file->collection_name === 'document') {
            return Storage::disk('listing_media')->download($path, 'menu-o-folleto.pdf', $headers);
        }

        return Storage::disk('listing_media')->response($path, null, $headers);
    }
}
