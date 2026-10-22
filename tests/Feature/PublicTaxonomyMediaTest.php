<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class PublicTaxonomyMediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('listing_media');
    }

    public function test_city_category_and_area_authorized_media_have_public_routes_and_protected_admin_routes(): void
    {
        $city = City::factory()->create();
        $category = Category::factory()->create();
        $area = Area::factory()->for($city)->create();

        foreach ([$city, $category, $area] as $taxonomy) {
            $media = $this->authorizedImage($taxonomy, 'icon');
            $url = $this->publicUrl($taxonomy, $media);
            $this->get($url)->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
            $this->get($url.'?conversion=card')->assertOk();
            $this->get($media->getUrl())->assertRedirect('/dashboard/login');
            Storage::disk('listing_media')->assertExists($media->getPathRelativeToRoot());
        }
    }

    public function test_each_collection_requires_its_own_permission_alt_and_manual_review_date(): void
    {
        $city = City::factory()->create();
        $icon = $this->authorizedImage($city, 'icon');
        $og = $city->addMedia(UploadedFile::fake()->image('og.png', 80, 60))->toMediaCollection('og_image');
        $this->get($this->publicUrl($city, $icon))->assertOk();
        $this->get($this->publicUrl($city, $og))->assertNotFound();

        $city->update(['og_image_approved' => true, 'og_image_alt' => 'Imagen autorizada', 'og_image_reviewed_at' => today('America/Cancun')]);
        $this->get($this->publicUrl($city, $og))->assertOk();
        $city->update(['icon_approved' => false]);
        $this->get($this->publicUrl($city, $icon))->assertNotFound();
        $this->get($this->publicUrl($city, $og))->assertOk();
    }

    #[DataProvider('invalidAuthorization')]
    public function test_missing_or_future_evidence_denies_direct_media_urls(string $field, mixed $value): void
    {
        $this->travelTo(now()->setDate(2026, 10, 23)->setTime(2, 0));
        $city = City::factory()->create();
        $media = $this->authorizedImage($city, 'icon');
        $city->forceFill([$field => $value])->save();

        $this->get($this->publicUrl($city, $media))->assertNotFound();
    }

    public static function invalidAuthorization(): array
    {
        return [
            'no autorizado' => ['icon_approved', false],
            'alt ausente' => ['icon_alt', null],
            'alt vacío' => ['icon_alt', '   '],
            'fecha ausente' => ['icon_reviewed_at', null],
            'mañana en Cancún' => ['icon_reviewed_at', '2026-10-23'],
        ];
    }

    public function test_media_belongs_to_the_requested_taxonomy_and_allowed_collection(): void
    {
        $city = City::factory()->create();
        $category = Category::factory()->create();
        $cityImage = $this->authorizedImage($city, 'icon');
        $categoryImage = $this->authorizedImage($category, 'icon');
        $unknownCollection = $city->addMedia(UploadedFile::fake()->image('other.png', 50, 50))->toMediaCollection('other');

        $this->get($this->publicUrl($city, $categoryImage))->assertNotFound();
        $this->get($this->publicUrl($category, $cityImage))->assertNotFound();
        $this->get($this->publicUrl($city, $unknownCollection))->assertNotFound();
        $this->get('/identidad/users/'.$city->id.'/medios/'.$cityImage->id)->assertNotFound();
        $this->get('/identidad/cities/999999/medios/'.$cityImage->id)->assertNotFound();
        $this->get('/identidad/cities/999999999999999999999999/medios/'.$cityImage->id)->assertNotFound();
        $this->get('/identidad/cities/'.$city->id.'/medios/999999999999999999999999')->assertNotFound();
    }

    public function test_invalid_mime_disk_missing_files_and_unavailable_conversions_return_404(): void
    {
        $city = City::factory()->create();
        $media = $this->authorizedImage($city, 'icon');
        $url = $this->publicUrl($city, $media);
        $this->get($url.'?conversion=unknown')->assertNotFound();
        $this->get($url.'?conversion[]=card')->assertNotFound();
        Storage::disk('listing_media')->delete($media->getPathRelativeToRoot('card'));
        $this->get($url.'?conversion=card')->assertNotFound();
        $media->forceFill(['mime_type' => 'image/svg+xml'])->save();
        $this->get($url)->assertNotFound();
        $media->forceFill(['mime_type' => 'image/png', 'disk' => 'public'])->save();
        $this->get($url)->assertNotFound();
        $media->forceFill(['disk' => 'listing_media', 'generated_conversions' => []])->save();
        $this->get($url.'?conversion=card')->assertNotFound();
        $this->get($url)->assertOk();
        Storage::disk('listing_media')->delete($media->getPathRelativeToRoot());

        $this->get($url)->assertNotFound();
        Storage::disk('listing_media')->assertMissing($media->getPathRelativeToRoot());
    }

    public function test_private_city_and_its_areas_lose_public_media_immediately(): void
    {
        $city = City::factory()->create();
        $area = Area::factory()->for($city)->create();
        $cityImage = $this->authorizedImage($city, 'icon');
        $areaImage = $this->authorizedImage($area, 'icon');
        $this->get($this->publicUrl($city, $cityImage))->assertOk();
        $this->get($this->publicUrl($area, $areaImage))->assertOk();
        $city->update(['is_public' => false]);

        $this->get($this->publicUrl($city, $cityImage))->assertNotFound();
        $this->get($this->publicUrl($area, $areaImage))->assertNotFound();
        $this->actingAs(User::factory()->superAdmin()->withAppAuthentication()->create())->get($cityImage->getUrl())->assertOk();
        $this->get($this->publicUrl($city, $cityImage))->assertNotFound();
    }

    public function test_missing_generated_conversion_uses_the_existing_original_in_public_metadata(): void
    {
        $city = City::factory()->create();
        $media = $this->authorizedImage($city, 'og_image');
        $url = $this->publicUrl($city, $media);
        Storage::disk('listing_media')->delete($media->getPathRelativeToRoot('card'));

        $this->get('/playa-del-carmen')->assertOk()->assertSee('<meta property="og:image" content="'.$url.'"', false)->assertDontSee($url.'?conversion=card', false);
        $this->get($url)->assertOk();
        $this->get($url.'?conversion=card')->assertNotFound();
        Storage::disk('listing_media')->assertExists($media->getPathRelativeToRoot());
    }

    public function test_inactive_categories_areas_places_and_other_cities_never_serve_public_identity(): void
    {
        $city = City::factory()->create();
        $otherCity = City::factory()->create(['slug' => 'otra-ciudad']);
        $inactiveCategory = Category::factory()->create(['is_active' => false]);
        $place = Category::factory()->create(['slug' => 'lugares-publicos', 'listing_type' => 'place']);
        $inactiveArea = Area::factory()->for($city)->create(['is_active' => false]);
        $foreignArea = Area::factory()->for($otherCity)->create();

        foreach ([$otherCity, $inactiveCategory, $place, $inactiveArea, $foreignArea] as $taxonomy) {
            $media = $this->authorizedImage($taxonomy, 'icon');
            $this->get($this->publicUrl($taxonomy, $media))->assertNotFound();
        }
    }

    public function test_demo_media_and_demo_areas_are_available_locally_but_not_in_production(): void
    {
        $city = City::factory()->create();
        $area = Area::factory()->for($city)->create(['is_demo' => true]);
        $areaImage = $this->authorizedImage($area, 'icon');
        $cityImage = $this->authorizedImage($city, 'icon');
        $cityImage->setCustomProperty('is_demo', true)->save();
        $this->get($this->publicUrl($area, $areaImage))->assertOk();
        $this->get($this->publicUrl($city, $cityImage))->assertOk();

        $this->app->detectEnvironment(fn (): string => 'production');

        $this->get($this->publicUrl($area, $areaImage))->assertNotFound();
        $this->get($this->publicUrl($city, $cityImage))->assertNotFound();
        $this->get('/playa-del-carmen/zonas/'.$area->slug)->assertNotFound();
    }

    public function test_public_identity_uses_authorized_icon_and_og_urls_and_removes_revoked_media(): void
    {
        $city = City::factory()->create(['tagline' => 'Tagline de la ciudad']);
        $icon = $this->authorizedImage($city, 'icon');
        $og = $this->authorizedImage($city, 'og_image');
        $iconUrl = $this->publicUrl($city, $icon);
        $ogUrl = $this->publicUrl($city, $og);

        $this->get('/playa-del-carmen')->assertOk()->assertSee('Tagline de la ciudad')->assertSee($iconUrl, false)->assertSee('<meta property="og:image"', false)->assertSee($ogUrl, false)->assertDontSee($icon->getUrl(), false);
        $city->update(['og_image_approved' => false, 'icon_approved' => false]);

        $this->get('/playa-del-carmen')->assertOk()->assertDontSee($iconUrl, false)->assertDontSee($ogUrl, false);
        $this->get($iconUrl)->assertNotFound();
        $this->get($ogUrl)->assertNotFound();
    }

    private function authorizedImage(City|Category|Area $taxonomy, string $collection): Media
    {
        $media = $taxonomy->addMedia(UploadedFile::fake()->image($collection.'.png', 80, 60))->toMediaCollection($collection);
        $taxonomy->forceFill([$collection.'_approved' => true, $collection.'_alt' => 'Imagen sintética autorizada', $collection.'_reviewed_at' => today('America/Cancun')])->save();

        return $media;
    }

    private function publicUrl(City|Category|Area $taxonomy, Media $media): string
    {
        $type = match (true) {
            $taxonomy instanceof City => 'cities',
            $taxonomy instanceof Category => 'categories',
            $taxonomy instanceof Area => 'areas',
        };

        return route('taxonomy.public-media', ['type' => $type, 'record' => $taxonomy->id, 'media' => $media->id]);
    }
}
