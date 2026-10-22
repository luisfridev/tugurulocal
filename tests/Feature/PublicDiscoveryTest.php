<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\HomeSelection;
use App\Models\InteractionEvent;
use App\Models\Listing;
use App\Support\DemoTaxonomyIdentity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('listing_media');
    }

    public function test_business_category_and_area_routes_show_the_same_published_business(): void
    {
        $city = City::factory()->create();
        $category = Category::factory()->create(['name' => 'Servicios', 'slug' => 'servicios']);
        $area = Area::factory()->for($city)->create(['name' => 'Centro', 'slug' => 'centro']);
        $listing = Listing::factory()->for($city)->for($category, 'primaryCategory')->published()->create(['area_id' => $area->id]);

        foreach (['/playa-del-carmen', '/playa-del-carmen/negocios', '/playa-del-carmen/servicios', '/playa-del-carmen/zonas/centro'] as $url) {
            $this->get($url)->assertOk()->assertSee('lang="es"', false)->assertSee($listing->name)->assertSee('/@'.$listing->slug)->assertDontSee('/premium')->assertDontSee('name="q"', false);
        }

        $this->assertDatabaseHas('interaction_events', ['city_id' => $city->id, 'type' => 'city.view', 'listing_id' => null]);
        $this->assertDatabaseHas('interaction_events', ['city_id' => $city->id, 'type' => 'businesses.view']);
        $this->assertDatabaseHas('interaction_events', ['city_id' => $city->id, 'category_id' => $category->id, 'type' => 'category.view']);
        $this->assertDatabaseHas('interaction_events', ['city_id' => $city->id, 'area_id' => $area->id, 'type' => 'area.view']);
    }

    public function test_only_initial_public_city_active_business_categories_and_local_areas_are_reachable(): void
    {
        $city = City::factory()->create();
        $otherCity = City::factory()->create(['name' => 'Otra ciudad', 'slug' => 'otra-ciudad']);
        $inactive = Category::factory()->create(['is_active' => false]);
        $place = Category::factory()->create(['slug' => 'lugares-publicos', 'listing_type' => 'place']);
        $inactiveArea = Area::factory()->for($city)->create(['is_active' => false]);
        $foreignArea = Area::factory()->for($otherCity)->create();

        foreach (['/otra-ciudad', '/otra-ciudad/negocios', '/playa-del-carmen/'.$inactive->slug, '/playa-del-carmen/'.$place->slug, '/playa-del-carmen/zonas/'.$inactiveArea->slug, '/playa-del-carmen/zonas/'.$foreignArea->slug, '/playa-del-carmen/no-existe'] as $url) {
            $this->get($url)->assertNotFound();
        }

        $city->update(['is_public' => false]);
        $this->get('/playa-del-carmen')->assertNotFound();
        $this->get('/playa-del-carmen/negocios')->assertNotFound();
        $this->assertDatabaseCount('interaction_events', 0);
    }

    public function test_specific_routes_win_and_future_reserved_categories_never_open(): void
    {
        City::factory()->create();
        Category::factory()->create(['slug' => 'negocios']);

        $this->get('/playa-del-carmen/negocios')->assertOk()->assertViewIs('discovery.index');

        foreach (['explorar', 'ofertas', 'zonas', 'listas', 'blog', 'lugares-publicos'] as $slug) {
            Category::factory()->create(['slug' => $slug]);
            $this->get('/playa-del-carmen/'.$slug)->assertNotFound();
        }
    }

    public function test_filters_intersect_and_secondary_category_matches_without_duplicate_results(): void
    {
        $this->freezeTime();
        $city = City::factory()->create();
        $primary = Category::factory()->create();
        $secondary = Category::factory()->create(['slug' => 'servicios']);
        $area = Area::factory()->for($city)->create(['slug' => 'centro']);
        $match = Listing::factory()->for($city)->for($primary, 'primaryCategory')->published()->create(['area_id' => $area->id]);
        $match->categories()->syncWithoutDetaching([$secondary->id]);
        $primaryMatch = Listing::factory()->for($city)->for($secondary, 'primaryCategory')->published()->create(['area_id' => $area->id]);
        $wrongArea = Listing::factory()->for($city)->for($secondary, 'primaryCategory')->published()->create();
        $wrongCategory = Listing::factory()->for($city)->for($primary, 'primaryCategory')->published()->create(['area_id' => $area->id]);

        foreach (['/playa-del-carmen/negocios?categoria=servicios&zona=centro', '/playa-del-carmen/servicios?zona=centro', '/playa-del-carmen/zonas/centro?categoria=servicios'] as $url) {
            $response = $this->get($url)->assertOk()->assertSee($match->name)->assertSee($primaryMatch->name)->assertDontSee($wrongArea->name)->assertDontSee($wrongCategory->name);
            $this->assertSame(2, $response['listings']->total());
            $this->assertSame([$primaryMatch->id, $match->id], $response['listings']->pluck('id')->all());
        }
    }

    public function test_invalid_filters_and_pages_return_spanish_404_without_recording_views(): void
    {
        $city = City::factory()->create();
        Category::factory()->create(['slug' => 'inactiva', 'is_active' => false]);
        $otherCity = City::factory()->create(['slug' => 'otra-ciudad']);
        Area::factory()->for($otherCity)->create(['slug' => 'zona-ajena']);
        Area::factory()->for($city)->create(['slug' => 'zona-inactiva', 'is_active' => false]);

        foreach (['categoria=no-existe', 'categoria=inactiva', 'categoria[]=servicios', 'zona=no-existe', 'zona=zona-ajena', 'zona=zona-inactiva', 'zona[]=centro', 'page=0', 'page=-1', 'page=abc', 'page=1.5', 'page[]=1', 'page=9999999999999999999999999', 'page=2'] as $query) {
            $this->get('/playa-del-carmen/negocios?'.$query)->assertNotFound()->assertSee('lang="es"', false);
        }

        $this->assertDatabaseCount('interaction_events', 0);
    }

    public function test_valid_empty_combinations_have_noindex_and_a_working_context_link(): void
    {
        $city = City::factory()->create();
        Category::factory()->create(['slug' => 'servicios']);
        Area::factory()->for($city)->create(['slug' => 'centro']);

        foreach (['/playa-del-carmen', '/playa-del-carmen/negocios?categoria=servicios&zona=centro', '/playa-del-carmen/servicios', '/playa-del-carmen/zonas/centro'] as $url) {
            $this->get($url)->assertOk()->assertSee('noindex')->assertSee('/playa-del-carmen/negocios')->assertDontSee('name="q"', false);
        }
    }

    public function test_pagination_has_twelve_items_stable_ties_and_excludes_missing_logos_before_counting(): void
    {
        $this->freezeTime();
        $city = City::factory()->create();
        $category = Category::factory()->create(['slug' => 'servicios']);
        $valid = Listing::factory()->count(13)->for($city)->for($category, 'primaryCategory')->published()->create();
        $missing = Listing::factory()->for($city)->for($category, 'primaryCategory')->published()->create();
        Storage::disk('listing_media')->delete($missing->getFirstMedia('logo')->getPathRelativeToRoot());

        $first = $this->get('/playa-del-carmen/negocios?categoria=servicios&tracking=PRIVATE-TRACKING')->assertOk()->assertDontSee('PRIVATE-TRACKING')->assertDontSee($missing->name);
        $second = $this->get('/playa-del-carmen/negocios?categoria=servicios&page=2')->assertOk();

        $this->assertSame(13, $first['listings']->total());
        $this->assertSame(12, $first['listings']->count());
        $this->assertSame($valid->last()->id, $first['listings']->first()->id);
        $this->assertSame($valid->get(1)->id, $first['listings']->last()->id);
        $this->assertSame([$valid->first()->id], $second['listings']->pluck('id')->all());
        $this->assertStringContainsString('categoria=servicios', $first['listings']->nextPageUrl());
        $this->assertStringNotContainsString('tracking', $first['listings']->nextPageUrl());
        $this->get('/playa-del-carmen/negocios?page=3')->assertNotFound();
        $this->get('/@'.$missing->slug)->assertNotFound();
    }

    public function test_publication_date_precedes_id_in_organic_order(): void
    {
        $this->freezeTime();
        $city = City::factory()->create();
        $newer = Listing::factory()->for($city)->published()->create();
        $older = Listing::factory()->for($city)->published()->create();
        $older->forceFill(['published_at' => now()->subDay()])->save();

        $response = $this->get('/playa-del-carmen/negocios')->assertOk();

        $this->assertSame([$newer->id, $older->id], $response['listings']->pluck('id')->all());
    }

    public function test_filter_forms_submit_get_without_page_and_clear_to_the_current_context(): void
    {
        $city = City::factory()->create();
        $category = Category::factory()->create(['slug' => 'servicios']);
        $area = Area::factory()->for($city)->create(['slug' => 'centro']);
        Listing::factory()->count(13)->for($city)->for($category, 'primaryCategory')->published()->create(['area_id' => $area->id]);

        foreach (['/playa-del-carmen/servicios' => 'zona=centro', '/playa-del-carmen/zonas/centro' => 'categoria=servicios'] as $base => $filter) {
            $response = $this->get($base.'?'.$filter.'&page=2')->assertOk()->assertSee('method="GET"', false)->assertSee('action="'.url($base).'"', false)->assertSee('Aplicar')->assertDontSee('name="page"', false);
            $document = new \DOMDocument;
            @$document->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
            $links = (new \DOMXPath($document))->query('//a[normalize-space(.)="Limpiar filtros"]');
            $this->assertGreaterThan(0, $links->length);
            foreach ($links as $link) {
                $this->assertSame(url($base), $link->getAttribute('href'));
            }
            $this->assertStringContainsString($filter, $response['listings']->previousPageUrl());
            $reset = $this->get($base)->assertOk();
            $this->assertSame(1, $reset['listings']->currentPage());
            $this->assertSame(13, $reset['listings']->total());
        }
    }

    public function test_inactive_primary_category_hides_a_business_even_with_an_active_secondary_category(): void
    {
        $city = City::factory()->create();
        $primary = Category::factory()->create();
        $secondary = Category::factory()->create(['slug' => 'servicios']);
        $listing = Listing::factory()->for($city)->for($primary, 'primaryCategory')->published()->create();
        $listing->categories()->syncWithoutDetaching([$secondary->id]);
        $primary->update(['is_active' => false]);

        $this->get('/playa-del-carmen')->assertOk()->assertDontSee($listing->name);
        $this->get('/playa-del-carmen/negocios')->assertOk()->assertDontSee($listing->name);
        $this->get('/playa-del-carmen/servicios')->assertOk()->assertDontSee($listing->name);
        $this->get('/@'.$listing->slug)->assertNotFound();
    }

    #[DataProvider('ineligibleListingAttributes')]
    public function test_ineligible_business_is_absent_from_every_discovery_surface(array $attributes): void
    {
        $this->travelTo(now()->setDate(2026, 10, 22)->setTime(12, 0));
        $city = City::factory()->create();
        $category = Category::factory()->create(['slug' => 'servicios']);
        $area = Area::factory()->for($city)->create(['slug' => 'centro']);
        $listing = Listing::factory()->for($city)->for($category, 'primaryCategory')->published()->create(['area_id' => $area->id]);
        $listing->forceFill($attributes)->save();

        foreach (['/playa-del-carmen', '/playa-del-carmen/negocios', '/playa-del-carmen/servicios', '/playa-del-carmen/zonas/centro'] as $url) {
            $this->get($url)->assertOk()->assertDontSee($listing->name);
        }
    }

    public static function ineligibleListingAttributes(): array
    {
        return [
            'borrador' => [['status' => 'draft']],
            'pausado' => [['status' => 'paused']],
            'archivado' => [['status' => 'archived']],
            'contenido sin aprobar' => [['content_approved' => false]],
            'lugar' => [['type' => 'place']],
            'logo sin autorización' => [['logo_approved' => false]],
            'logo sin alt' => [['logo_alt' => null]],
            'logo sin fecha' => [['logo_reviewed_at' => null]],
            'publicación futura' => [['published_at' => '2030-01-01 00:00:00']],
            'logo con fecha futura' => [['logo_reviewed_at' => '2099-01-01']],
        ];
    }

    public function test_home_keeps_manual_order_limits_each_section_and_never_repeats_selected_items(): void
    {
        $this->freezeTime();
        $city = City::factory()->create();
        $listings = Listing::factory()->count(14)->for($city)->published()->create();
        foreach ($listings->take(6) as $position => $listing) {
            HomeSelection::factory()->create(['city_id' => $city->id, 'listing_id' => $listing->id, 'position' => $position + 1]);
        }

        $response = $this->get('/playa-del-carmen')->assertOk();

        $this->assertSame($listings->take(6)->pluck('id')->all(), $response['featured']->pluck('id')->all());
        $this->assertSame([$listings[13]->id, $listings[12]->id, $listings[11]->id, $listings[10]->id, $listings[9]->id, $listings[8]->id], $response['recent']->pluck('id')->all());
    }

    public function test_permanent_closure_is_informative_in_organic_results_but_absent_from_home(): void
    {
        $city = City::factory()->create();
        $closed = Listing::factory()->for($city)->published()->create(['operating_status' => 'permanently_closed']);
        $temporary = Listing::factory()->for($city)->published()->create(['operating_status' => 'temporarily_closed']);
        HomeSelection::factory()->create(['city_id' => $city->id, 'listing_id' => $closed->id, 'position' => 1]);

        $home = $this->get('/playa-del-carmen')->assertOk()->assertDontSee($closed->name)->assertSee($temporary->name);
        $this->assertCount(0, $home['featured']);
        $this->get('/playa-del-carmen/negocios')->assertOk()->assertSee($closed->name)->assertSee('Cerrado permanentemente')->assertSee('Cerrado temporalmente');
        $this->assertDatabaseHas('home_selections', ['city_id' => $city->id, 'listing_id' => $closed->id]);
    }

    public function test_losing_eligibility_removes_a_selected_business_without_deleting_selection(): void
    {
        $city = City::factory()->create();
        $listing = Listing::factory()->for($city)->published()->create();
        HomeSelection::factory()->create(['city_id' => $city->id, 'listing_id' => $listing->id, 'position' => 1]);
        $this->get('/playa-del-carmen')->assertOk()->assertSee($listing->name);
        $listing->forceFill(['status' => 'paused'])->save();

        $response = $this->get('/playa-del-carmen')->assertOk()->assertDontSee($listing->name);

        $this->assertCount(0, $response['featured']);
        $this->assertDatabaseHas('home_selections', ['listing_id' => $listing->id]);
    }

    public function test_home_only_promotes_categories_and_areas_with_eligible_results(): void
    {
        $city = City::factory()->create();
        $emptyCategory = Category::factory()->create(['slug' => 'vacia']);
        $usedCategory = Category::factory()->create(['slug' => 'servicios']);
        $emptyArea = Area::factory()->for($city)->create(['slug' => 'vacia']);
        $usedArea = Area::factory()->for($city)->create(['slug' => 'centro']);
        Listing::factory()->for($city)->for($usedCategory, 'primaryCategory')->published()->create(['area_id' => $usedArea->id]);
        $missingLogo = Listing::factory()->for($city)->for($emptyCategory, 'primaryCategory')->published()->create(['area_id' => $emptyArea->id]);
        Storage::disk('listing_media')->delete($missingLogo->getFirstMedia('logo')->getPathRelativeToRoot());

        $home = $this->get('/playa-del-carmen')->assertOk();

        $this->assertSame([$usedCategory->id], $home['categories']->pluck('id')->all());
        $this->assertSame([$usedArea->id], $home['areas']->pluck('id')->all());
        $this->get('/playa-del-carmen/vacia')->assertOk()->assertSee('noindex');
        $this->get('/playa-del-carmen/zonas/vacia')->assertOk()->assertSee('noindex');
    }

    public function test_demo_only_pages_are_noindex_and_demo_is_excluded_outside_local_and_testing(): void
    {
        $city = City::factory()->create();
        $demo = Listing::factory()->for($city)->published()->create(['is_demo' => true]);
        $this->get('/playa-del-carmen')->assertOk()->assertSee('noindex')->assertSee($demo->name);
        $this->get('/playa-del-carmen/negocios')->assertOk()->assertSee('noindex')->assertSee($demo->name);

        $this->app->detectEnvironment(fn (): string => 'production');

        $this->get('/playa-del-carmen')->assertOk()->assertDontSee($demo->name);
        $this->get('/playa-del-carmen/negocios')->assertOk()->assertDontSee($demo->name);
        $this->get('/@'.$demo->slug)->assertNotFound();
    }

    public function test_canonical_distinguishes_plain_pagination_from_filtered_results(): void
    {
        $city = City::factory()->create();
        $category = Category::factory()->create(['slug' => 'servicios']);
        Listing::factory()->count(13)->for($city)->for($category, 'primaryCategory')->published()->create();

        $this->get('/playa-del-carmen/negocios?page=2')->assertOk()->assertSee('rel="canonical" href="'.url('/playa-del-carmen/negocios').'?page=2"', false)->assertDontSee('noindex');
        $this->get('/playa-del-carmen/negocios?categoria=servicios&page=2')->assertOk()->assertSee('rel="canonical" href="'.url('/playa-del-carmen/negocios').'"', false)->assertSee('noindex');
        $this->get('/playa-del-carmen/servicios')->assertOk()->assertSee('rel="canonical" href="'.url('/playa-del-carmen/servicios').'"', false)->assertDontSee('noindex');
    }

    public function test_seeded_demo_identity_is_visible_locally_but_absent_from_production_body_and_metadata(): void
    {
        $city = City::factory()->create(['demo_identity_seeded' => true, 'tagline' => DemoTaxonomyIdentity::CITY_TAGLINE, 'description' => DemoTaxonomyIdentity::DESCRIPTION]);
        $category = Category::factory()->create(['slug' => 'servicios', 'demo_identity_seeded' => true, 'tagline' => DemoTaxonomyIdentity::CATEGORY_TAGLINE, 'description' => DemoTaxonomyIdentity::DESCRIPTION]);
        $this->get('/playa-del-carmen')->assertOk()->assertSee('DEMO · Identidad de ciudad para revisión local')->assertSee('<h2>Identidad DEMO</h2>', false);
        $this->get('/playa-del-carmen/servicios')->assertOk()->assertSee('DEMO · Contenido de categoría')->assertSee('<h2>Identidad DEMO</h2>', false);

        $this->app->detectEnvironment(fn (): string => 'production');

        foreach (['/playa-del-carmen', '/playa-del-carmen/negocios', '/playa-del-carmen/servicios'] as $url) {
            $response = $this->get($url)->assertOk()->assertDontSee('DEMO')->assertSee('<meta name="description"', false)->assertSee('<meta property="og:description"', false);
            $this->assertSame('', $response['identity']['description']);
            $this->assertStringNotContainsString('DEMO', $response['seo']['description']);
        }
        $this->assertDatabaseHas('cities', ['id' => $city->id, 'tagline' => DemoTaxonomyIdentity::CITY_TAGLINE, 'description' => DemoTaxonomyIdentity::DESCRIPTION]);
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'tagline' => DemoTaxonomyIdentity::CATEGORY_TAGLINE, 'description' => DemoTaxonomyIdentity::DESCRIPTION]);
    }

    public function test_seed_marker_preserves_real_identity_text_in_production(): void
    {
        City::factory()->create(['demo_identity_seeded' => true, 'tagline' => 'Negocios de Playa del Carmen', 'description' => '<p>Información <strong>local</strong> revisada.</p>']);
        Category::factory()->create(['slug' => 'servicios', 'demo_identity_seeded' => true, 'tagline' => 'Servicios de la ciudad', 'description' => '<p>Encuentra atención profesional.</p>']);
        $this->app->detectEnvironment(fn (): string => 'production');

        $city = $this->get('/playa-del-carmen')->assertOk()->assertSee('Negocios de Playa del Carmen')->assertSee('<strong>local</strong>', false);
        $category = $this->get('/playa-del-carmen/servicios')->assertOk()->assertSee('Servicios de la ciudad')->assertSee('<p>Encuentra atención profesional.</p>', false);

        $this->assertSame('Negocios de Playa del Carmen', $city['seo']['description']);
        $this->assertSame('Servicios de la ciudad', $category['seo']['description']);
    }

    public function test_demo_and_real_identity_fields_are_filtered_independently(): void
    {
        City::factory()->create(['demo_identity_seeded' => true, 'tagline' => DemoTaxonomyIdentity::CITY_TAGLINE, 'description' => '<p>Descripción real de la ciudad.</p>']);
        Category::factory()->create(['slug' => 'servicios', 'demo_identity_seeded' => true, 'tagline' => 'Servicios de la ciudad', 'description' => DemoTaxonomyIdentity::DESCRIPTION]);
        $this->app->detectEnvironment(fn (): string => 'production');

        $city = $this->get('/playa-del-carmen')->assertOk()->assertSee('Descripción real de la ciudad.')->assertDontSee('DEMO');
        $category = $this->get('/playa-del-carmen/servicios')->assertOk()->assertSee('Servicios de la ciudad')->assertDontSee('DEMO');

        $this->assertNull($city['identity']['tagline']);
        $this->assertSame('Descripción real de la ciudad.', $city['seo']['description']);
        $this->assertSame('', $category['identity']['description']);
        $this->assertSame('Servicios de la ciudad', $category['seo']['description']);
    }

    public function test_unmarked_identity_is_not_classified_by_words_alone(): void
    {
        City::factory()->create(['tagline' => DemoTaxonomyIdentity::CITY_TAGLINE, 'demo_identity_seeded' => false]);
        Category::factory()->create(['slug' => 'servicios', 'demo_identity_seeded' => true, 'tagline' => 'DEMO · Texto editado por el propietario']);
        $this->app->detectEnvironment(fn (): string => 'production');

        $this->get('/playa-del-carmen')->assertOk()->assertSee('DEMO · Identidad de ciudad para revisión local');
        $this->get('/playa-del-carmen/servicios')->assertOk()->assertSee('DEMO · Texto editado por el propietario');
    }

    public function test_public_identity_is_sanitized_cards_are_escaped_and_private_values_are_absent(): void
    {
        $city = City::factory()->create(['name' => 'Playa 👩🏽‍🎨 <script>bad()</script>', 'tagline' => 'Tagline <img src=x>', 'description' => '<p>Texto <strong>válido</strong></p><script>bad()</script><a href="javascript:bad()">Enlace</a>']);
        Listing::factory()->for($city)->published()->create(['name' => 'Negocio <script>bad()</script>', 'summary' => 'Resumen <img src=x>', 'private_notes' => 'NOTA-PRIVADA', 'source_url' => 'https://example.org/FUENTE-PRIVADA']);

        $this->get('/playa-del-carmen')->assertOk()->assertSee('Playa 👩🏽‍🎨 &lt;script&gt;', false)->assertSee('<strong>válido</strong>', false)->assertDontSee('<script>bad()</script>', false)->assertDontSee('javascript:bad()', false)->assertDontSee('<img src=x>', false)->assertDontSee('NOTA-PRIVADA')->assertDontSee('FUENTE-PRIVADA');
        $this->get('/playa-del-carmen/negocios')->assertOk()->assertSee('Negocio &lt;script&gt;', false)->assertDontSee('<script>bad()</script>', false)->assertDontSee('<img src=x>', false)->assertDontSee('NOTA-PRIVADA');
    }

    public function test_filtered_views_deduplicate_for_sixty_seconds_and_store_only_normalized_context(): void
    {
        $this->freezeTime();
        $city = City::factory()->create();
        $category = Category::factory()->create(['slug' => 'servicios']);
        $area = Area::factory()->for($city)->create(['slug' => 'centro']);
        $url = '/playa-del-carmen/negocios?categoria=servicios&zona=centro';

        $this->get($url.'&email=private@example.org')->assertOk();
        $this->get($url.'&tracking=PRIVATE-TRACKING')->assertOk();
        $this->assertDatabaseCount('interaction_events', 1);
        $event = InteractionEvent::query()->sole();
        $this->assertSame($city->id, $event->city_id);
        $this->assertSame($category->id, $event->category_id);
        $this->assertSame($area->id, $event->area_id);
        $this->assertSame(1, $event->page);
        $this->assertNull($event->listing_id);
        foreach (['private@example.org', 'PRIVATE-TRACKING', '127.0.0.1', '?categoria='] as $private) {
            $this->assertStringNotContainsString($private, $event->toJson());
        }
        $this->travel(59)->seconds();
        $this->get($url)->assertOk();
        $this->assertDatabaseCount('interaction_events', 1);
        $this->travel(2)->seconds();
        $this->get($url)->assertOk();
        $this->assertDatabaseCount('interaction_events', 2);
        $this->get('/playa-del-carmen/negocios')->assertOk();
        $this->assertDatabaseCount('interaction_events', 3);
    }

    public function test_page_context_has_its_own_view_event(): void
    {
        $city = City::factory()->create();
        Listing::factory()->count(13)->for($city)->published()->create();

        $this->get('/playa-del-carmen/negocios')->assertOk();
        $this->get('/playa-del-carmen/negocios?page=2')->assertOk();

        $this->assertDatabaseHas('interaction_events', ['type' => 'businesses.view', 'city_id' => $city->id, 'page' => 1]);
        $this->assertDatabaseHas('interaction_events', ['type' => 'businesses.view', 'city_id' => $city->id, 'page' => 2]);
        $this->assertDatabaseCount('interaction_events', 2);
    }

    public function test_more_cards_do_not_add_per_listing_database_queries(): void
    {
        $city = City::factory()->create();
        Listing::factory()->for($city)->published()->create();
        DB::enableQueryLog();
        $this->get('/playa-del-carmen/negocios')->assertOk();
        $firstCount = collect(DB::getQueryLog())->filter(fn (array $query): bool => str_starts_with(strtolower($query['query']), 'select'))->count();
        DB::disableQueryLog();
        Listing::factory()->count(11)->for($city)->published()->create();
        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->get('/playa-del-carmen/negocios')->assertOk();

        $secondCount = collect(DB::getQueryLog())->filter(fn (array $query): bool => str_starts_with(strtolower($query['query']), 'select'))->count();
        DB::disableQueryLog();
        $this->assertSame(12, $response['listings']->count());
        $this->assertLessThanOrEqual($firstCount + 3, $secondCount);
    }
}
