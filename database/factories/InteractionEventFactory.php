<?php

namespace Database\Factories;

use App\Models\InteractionEvent;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<InteractionEvent> */
class InteractionEventFactory extends Factory
{
    public function definition(): array
    {
        return ['listing_id' => Listing::factory(), 'type' => 'listing.view', 'occurred_at' => now()];
    }
}
