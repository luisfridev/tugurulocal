<?php

namespace App\Actions\Listings;

use App\Models\InteractionEvent;
use App\Models\Listing;
use Illuminate\Http\Request;

class RecordInteraction
{
    public const CONTACTS = ['maps', 'phone', 'whatsapp', 'website', 'facebook', 'instagram', 'document'];

    public function handle(Listing $listing, string $type, Request $request): void
    {
        if (! in_array($type, ['listing.view', ...array_map(fn (string $kind): string => 'contact.'.$kind, self::CONTACTS)], true)) {
            return;
        }
        $key = 'interaction.'.$listing->id.'.'.$type;
        if ($request->session()->has($key) && $request->session()->get($key) > now()->timestamp - 60) {
            return;
        }
        InteractionEvent::query()->create(['listing_id' => $listing->id, 'type' => $type, 'occurred_at' => now()]);
        $request->session()->put($key, now()->timestamp);
    }
}
