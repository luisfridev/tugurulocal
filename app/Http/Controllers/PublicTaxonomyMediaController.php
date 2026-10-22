<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Support\PublicTaxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicTaxonomyMediaController extends Controller
{
    public function __invoke(Request $request, string $type, string $record, string $media, PublicTaxonomy $public): StreamedResponse
    {
        abort_unless(filter_var($record, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false
            && filter_var($media, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false, 404);
        $model = match ($type) {
            'cities' => City::class,
            'categories' => Category::class,
            'areas' => Area::class,
            default => abort(404),
        };
        $taxonomy = $model::query()->with('media')->findOrFail($record);
        abort_unless($public->visible($taxonomy), 404);
        $file = $taxonomy->media->firstWhere('id', $media);
        abort_unless($file && $public->authorizedMedia($taxonomy, $file->collection_name)?->id === $file->id, 404);
        $conversion = $request->query('conversion', '');
        abort_unless(in_array($conversion, ['', 'card'], true), 404);
        abort_unless($conversion === '' || ($file->conversions_disk === 'listing_media' && $file->hasGeneratedConversion($conversion)), 404);
        $path = $file->getPathRelativeToRoot($conversion);
        abort_unless(Storage::disk('listing_media')->exists($path), 404);

        return Storage::disk('listing_media')->response($path, null, ['X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store', 'X-Robots-Tag' => 'noindex']);
    }
}
