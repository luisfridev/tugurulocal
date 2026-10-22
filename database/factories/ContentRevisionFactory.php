<?php

namespace Database\Factories;

use App\Models\ContentRevision;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ContentRevision> */
class ContentRevisionFactory extends Factory
{
    public function definition(): array
    {
        return ['listing_id' => Listing::factory(), 'actor_id' => User::factory()->superAdmin()->withAppAuthentication(), 'action' => 'saved', 'snapshot' => []];
    }
}
