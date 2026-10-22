<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/** @extends Factory<Listing> */
class ListingFactory extends Factory
{
    public function definition(): array
    {
        return ['city_id' => City::factory(), 'primary_category_id' => Category::factory(), 'name' => fake()->company(), 'slug' => fake()->unique()->slug(3), 'summary' => 'Información práctica verificada.', 'description' => 'Descripción editorial verificada de este negocio.', 'source_url' => 'https://example.org/fuente', 'reviewed_at' => today(), 'content_approved' => true, 'document_button_label' => 'Descargar menú o folleto'];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Listing $listing): void {
            $listing->categories()->syncWithoutDetaching([$listing->primary_category_id]);
        });
    }

    public function published(): static
    {
        return $this->withAuthorizedLogo()->afterCreating(function (Listing $listing): void {
            $listing->forceFill(['status' => 'published', 'published_at' => now(), 'was_published' => true])->save();
        });
    }

    public function withAuthorizedLogo(): static
    {
        return $this->afterCreating(function (Listing $listing): void {
            $listing->addMedia(UploadedFile::fake()->image('logo-test.png', 40, 40))->toMediaCollection('logo');
            $listing->forceFill(['logo_approved' => true, 'logo_alt' => 'Logo sintético de prueba', 'logo_reviewed_at' => today('America/Cancun')])->save();
        });
    }

    public function archived(): static
    {
        return $this->state(fn (): array => ['status' => 'archived']);
    }
}
