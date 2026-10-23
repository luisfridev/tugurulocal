<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\City;
use App\Support\SubmissionIdentity;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();
        $address = fake()->streetAddress();

        return [
            'city_id' => City::factory(), 'category_id' => Category::factory(), 'type' => 'business',
            'name' => $name, 'address' => $address, 'reason' => 'Propuesta de prueba para revisión editorial.',
            'name_key' => SubmissionIdentity::text($name), 'address_key' => SubmissionIdentity::text($address),
            'credit_consent' => false, 'status' => 'pending',
        ];
    }

    public function withCreditConsent(): static
    {
        return $this->state(fn (): array => ['credit_consent' => true, 'instagram' => 'recomendacion_prueba']);
    }
}
