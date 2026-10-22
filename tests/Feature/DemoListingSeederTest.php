<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\User;
use Database\Seeders\DemoListingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use LogicException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class DemoListingSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeder_persists_thirty_public_businesses_and_is_repeatable_without_overwriting_edits_or_files(): void
    {
        Storage::fake('listing_media');
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        $adminBefore = $admin->fresh()->getRawOriginal();
        $this->seed();
        $city = City::query()->sole();
        $city->update(['tagline' => 'Texto del propietario']);
        $category = Category::query()->where('slug', 'servicios')->firstOrFail();
        $category->update(['description' => '<p>Contenido previo</p>']);

        $this->seed(DemoListingSeeder::class);

        $this->assertDatabaseCount('listings', 32);
        $this->assertDatabaseCount('users', 1);
        $this->assertSame(30, Listing::query()->where('is_demo', true)->where('status', 'published')->count());
        $this->assertSame(4, Listing::query()->where('status', 'published')->distinct()->count('primary_category_id'));
        $this->assertSame(2, Area::query()->where('is_demo', true)->count());
        $this->assertSame($adminBefore, $admin->fresh()->getRawOriginal());
        $this->assertSame('Texto del propietario', $city->fresh()->tagline);
        foreach (['og_image', 'icon'] as $collection) {
            $this->assertNull($city->fresh()->{$collection.'_source'});
            $this->assertNull($city->fresh()->{$collection.'_permission'});
            $this->assertNotNull($city->fresh()->{$collection.'_reviewed_at'});
        }
        $this->assertSame('<p>Contenido previo</p>', $category->fresh()->description);
        foreach (Listing::query()->get() as $listing) {
            $this->assertNull($listing->phone);
            $this->assertNull($listing->whatsapp);
            $this->assertNull($listing->address);
            $this->assertNull($listing->google_rating);
            $this->assertNull($listing->google_review_count);
            $this->assertTrue($listing->logo_approved);
            foreach (['logo_source', 'logo_permission', 'media_source', 'media_permission'] as $retired) {
                $this->assertNull($listing->{$retired});
            }
            $this->assertNotNull($listing->logo_reviewed_at);
            $this->assertNotNull($listing->media_reviewed_at);
            $this->assertTrue($listing->getFirstMedia('logo')->getCustomProperty('is_demo'));
        }
        $listing = Listing::query()->where('slug', 'demo-cafe-menta')->firstOrFail();
        $listing->update(['summary' => 'Cambio manual conservado']);
        $newListing = Listing::query()->where('slug', 'demo-estudio-papel')->firstOrFail();
        $newListing->update(['summary' => 'Cambio manual en la ampliación']);
        $city->refresh()->update(['primary_color' => null]);
        $listingsBefore = Listing::query()->orderBy('id')->get()->map(fn (Listing $record): array => $record->getRawOriginal())->all();
        $files = Storage::disk('listing_media')->allFiles();
        $hashes = collect($files)->mapWithKeys(fn (string $path): array => [$path => hash('sha256', Storage::disk('listing_media')->get($path))])->all();
        $mediaBefore = Media::query()->orderBy('id')->get()->toArray();

        $this->seed(DemoListingSeeder::class);

        $this->assertDatabaseCount('listings', 32);
        $this->assertSame('Cambio manual conservado', $listing->fresh()->summary);
        $this->assertSame('Cambio manual en la ampliación', $newListing->fresh()->summary);
        $this->assertSame($listingsBefore, Listing::query()->orderBy('id')->get()->map(fn (Listing $record): array => $record->getRawOriginal())->all());
        $this->assertNull($city->fresh()->primary_color);
        $this->assertSame($mediaBefore, Media::query()->orderBy('id')->get()->toArray());
        $this->assertSame($files, Storage::disk('listing_media')->allFiles());
        foreach ($hashes as $path => $hash) {
            $this->assertSame($hash, hash('sha256', Storage::disk('listing_media')->get($path)));
        }
        foreach (Listing::query()->where('status', 'published')->get() as $publicListing) {
            $this->get('/@'.$publicListing->slug)->assertOk()->assertSee('DEMO');
        }
        $first = $this->get('/playa-del-carmen/negocios')->assertOk()->assertSee('noindex');
        $second = $this->get('/playa-del-carmen/negocios?page=2')->assertOk();
        $third = $this->get('/playa-del-carmen/negocios?page=3')->assertOk();
        $this->assertSame(30, $first['listings']->total());
        $this->assertSame(12, $first['listings']->count());
        $this->assertSame(12, $second['listings']->count());
        $this->assertSame(6, $third['listings']->count());
        $this->assertSame(30, $first['listings']->concat($second['listings'])->concat($third['listings'])->unique('id')->count());
        $this->get('/playa-del-carmen/negocios?page=4')->assertNotFound();
        $this->get('/@demo-borrador-privado')->assertNotFound();
        $this->get('/@demo-negocio-pausado')->assertNotFound();
        $this->app->detectEnvironment(fn (): string => 'production');
        $this->get('/@demo-cafe-menta')->assertNotFound();
        $this->get('/@demo-estudio-papel')->assertNotFound();
        $this->get('/playa-del-carmen/negocios')->assertOk()->assertViewHas('listings', fn (LengthAwarePaginator $listings): bool => $listings->total() === 0);
        $this->get($listing->getFirstMedia('logo')->getUrl())->assertNotFound();
    }

    public function test_demo_expansion_keeps_an_existing_business_and_its_media_unchanged(): void
    {
        Storage::fake('listing_media');
        $this->seed();
        $city = City::query()->sole();
        $category = Category::query()->where('slug', 'donde-comer-y-beber')->firstOrFail();
        $existing = Listing::factory()->for($city)->for($category, 'primaryCategory')->published()->create([
            'slug' => 'demo-cafe-menta', 'name' => 'DEMO · Nombre editado previamente', 'is_demo' => true,
        ]);
        $before = $existing->fresh()->getRawOriginal();
        $logo = $existing->getFirstMedia('logo');
        $mediaBefore = $logo->getRawOriginal();
        $logoPath = $logo->getPathRelativeToRoot();
        $logoHash = hash('sha256', Storage::disk('listing_media')->get($logoPath));

        $this->seed(DemoListingSeeder::class);

        $this->assertDatabaseCount('listings', 32);
        $this->assertSame(30, Listing::query()->where('status', 'published')->count());
        $this->assertSame($before, $existing->fresh()->getRawOriginal());
        $this->assertSame($mediaBefore, $logo->fresh()->getRawOriginal());
        $this->assertSame($logoHash, hash('sha256', Storage::disk('listing_media')->get($logoPath)));
    }

    public function test_demo_seeder_is_blocked_in_production_before_creating_any_data(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        try {
            app(DemoListingSeeder::class)->run();
            $this->fail('Ejecutó DEMO en producción.');
        } catch (LogicException $exception) {
            $this->assertSame('Los datos DEMO solo se permiten en local/testing.', $exception->getMessage());
        }

        $this->assertDatabaseCount('cities', 0);
        $this->assertDatabaseCount('listings', 0);
        $this->assertDatabaseCount('media', 0);
        $this->assertDatabaseCount('users', 0);
    }
}
