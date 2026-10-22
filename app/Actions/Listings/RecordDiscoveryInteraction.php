<?php

namespace App\Actions\Listings;

use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\InteractionEvent;
use Illuminate\Http\Request;

class RecordDiscoveryInteraction
{
    public function handle(Request $request, string $surface, City $city, ?Category $category = null, ?Area $area = null, ?int $page = null): void
    {
        if (! $request->isMethod('GET') || ! in_array($surface, ['city', 'businesses', 'category', 'area'], true)) {
            return;
        }
        $key = implode('.', ['discovery', $surface, $city->id, $category?->id ?? 0, $area?->id ?? 0, $page ?? 0]);
        if ($request->session()->get($key, 0) > now()->timestamp - 60) {
            return;
        }
        InteractionEvent::query()->create(['type' => $surface.'.view', 'city_id' => $city->id, 'category_id' => $category?->id,
            'area_id' => $area?->id, 'page' => $page, 'occurred_at' => now()]);
        $request->session()->put($key, now()->timestamp);
    }
}
