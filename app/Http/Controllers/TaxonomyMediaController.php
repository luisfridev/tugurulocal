<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaxonomyMediaController extends Controller
{
    public function __invoke(Request $request, string $type, int $record, int $media): StreamedResponse
    {
        $model = match ($type) {
            'cities' => City::class,
            'categories' => Category::class,
            'areas' => Area::class,
            default => abort(404),
        };
        $taxonomy = $model::query()->findOrFail($record);
        Gate::authorize('view', $taxonomy);
        $file = $taxonomy->media()->whereKey($media)->whereIn('collection_name', ['og_image', 'icon'])->firstOrFail();
        $conversion = $request->query('conversion', '');
        abort_unless(in_array($conversion, ['', 'card'], true) && ($conversion === '' || $file->hasGeneratedConversion($conversion)), 404);
        $path = $file->getPathRelativeToRoot($conversion);
        abort_unless(Storage::disk('listing_media')->exists($path), 404);

        return Storage::disk('listing_media')->response($path, null, ['X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store', 'X-Robots-Tag' => 'noindex']);
    }
}
