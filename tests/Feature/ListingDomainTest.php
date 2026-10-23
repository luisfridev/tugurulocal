<?php

namespace Tests\Feature;

use App\Actions\Listings\RestoreListingRevision;
use App\Actions\Listings\SaveListing;
use App\Actions\Listings\SaveTaxonomy;
use App\Actions\Listings\TransitionListing;
use App\Models\Area;
use App\Models\AuditEvent;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ListingDomainTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('listing_media');
    }

    public function test_structural_seeding_is_repeatable_and_never_creates_a_sample_account_or_listing(): void
    {
        $this->seed();
        $this->seed();
        $this->assertDatabaseCount('cities', 1);
        $this->assertDatabaseCount('categories', 5);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('listings', 0);
    }

    public function test_admin_saves_publishes_pauses_archives_and_restores_a_listing_with_history(): void
    {
        $this->freezeTime();
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $listing = Listing::factory()->withAuthorizedLogo()->create();
        $data = $listing->only(Listing::FIELDS) + ['category_ids' => [$listing->primary_category_id]];
        $data['name'] = 'Nombre revisado';
        $listing = app(SaveListing::class)->handle($actor, $data, $listing);
        $revision = $listing->revisions()->first();
        $listing = app(TransitionListing::class)->handle($actor, $listing, 'published');
        $this->assertSame('published', $listing->status);
        $this->assertTrue($listing->was_published);
        $this->assertSame(now()->timestamp, $listing->published_at->timestamp);
        app(TransitionListing::class)->handle($actor, $listing, 'paused');
        $this->assertSame('paused', $listing->fresh()->status);
        app(TransitionListing::class)->handle($actor, $listing, 'archived');
        $this->assertSame('archived', $listing->fresh()->status);
        $restored = app(RestoreListingRevision::class)->handle($actor, $listing, $revision);
        $this->assertSame('draft', $restored->status);
        $this->assertFalse($restored->content_approved);
        $this->assertFalse($restored->media_approved);
        $this->assertSame('Nombre revisado', $restored->name);
        $this->assertGreaterThanOrEqual(6, $listing->revisions()->count());
        $this->assertDatabaseHas('audit_events', ['action' => 'content.changed', 'actor_id' => $actor->id]);
    }

    public function test_publication_requires_verification_and_places_wait_for_verified_children(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $listing = Listing::factory()->create(['content_approved' => false]);
        try {
            app(TransitionListing::class)->handle($actor, $listing, 'published');
            $this->fail('Publicó sin aprobación');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('content_approved', $e->errors());
        }
        $listing->forceFill(['type' => 'place', 'content_approved' => true])->save();
        try {
            app(TransitionListing::class)->handle($actor, $listing, 'published');
            $this->fail('Publicó un Lugar sin hijos');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('type', $e->errors());
        }
        $this->assertSame('draft', $listing->fresh()->status);
    }

    public function test_listing_rejects_cross_city_area_missing_primary_category_wrong_type_and_duplicate_slug(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $listing = Listing::factory()->create();
        $otherCity = City::factory()->create(['name' => 'Otra ciudad', 'slug' => 'otra-ciudad', 'is_public' => false]);
        $area = Area::factory()->for($otherCity)->create();
        $place = Category::factory()->create(['listing_type' => 'place']);
        $other = Listing::factory()->recycle($listing->city)->create();
        $base = $listing->only(Listing::FIELDS) + ['category_ids' => [$listing->primary_category_id]];
        foreach ([['area_id' => $area->id], ['category_ids' => [$place->id]], ['primary_category_id' => $place->id, 'category_ids' => [$place->id]], ['type' => 'place', 'primary_category_id' => $place->id, 'category_ids' => [$place->id]], ['slug' => $other->slug], ['slug' => 'blog']] as $changes) {
            try {
                app(SaveListing::class)->handle($actor, array_replace($base, $changes), $listing);
                $this->fail('Aceptó clasificación inválida');
            } catch (ValidationException $e) {
                $this->assertNotEmpty($e->errors());
            }
        }
        $this->assertSame($base['slug'], $listing->fresh()->slug);
    }

    public function test_slug_of_an_ever_published_listing_reserves_its_previous_url(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $listing = Listing::factory()->published()->create();
        $data = $listing->only(Listing::FIELDS) + ['category_ids' => [$listing->primary_category_id]];
        $data['slug'] = 'nuevo-slug';
        app(SaveListing::class)->handle($actor, $data, $listing);
        $this->assertSame('nuevo-slug', $listing->fresh()->slug);
        $this->assertDatabaseHas('slug_redirects', ['source_path' => '/@'.$listing->slug, 'listing_id' => $listing->id]);
    }

    public function test_unprivileged_and_mfa_missing_accounts_cannot_execute_domain_mutations(): void
    {
        $listing = Listing::factory()->create();
        foreach ([User::factory()->create(), User::factory()->superAdmin()->create()] as $actor) {
            try {
                app(TransitionListing::class)->handle($actor, $listing, 'published');
                $this->fail('Publicó sin permiso');
            } catch (AuthorizationException) {
                $this->assertSame('draft', $listing->fresh()->status);
            }
            try {
                app(SaveTaxonomy::class)->handle($actor, new City, ['name' => 'No', 'slug' => 'no', 'timezone' => 'UTC', 'is_public' => false]);
                $this->fail('Creó ciudad sin permiso');
            } catch (AuthorizationException) {
                $this->assertDatabaseMissing('cities', ['slug' => 'no']);
            }
        }
    }

    public function test_taxonomy_admin_cannot_expose_another_city_or_break_used_relations(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $listing = Listing::factory()->create();
        try {
            app(SaveTaxonomy::class)->handle($actor, new City, ['name' => 'Otra', 'slug' => 'otra', 'timezone' => 'America/Cancun', 'is_public' => true]);
            $this->fail('Expuso otra ciudad');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('is_public', $e->errors());
        }
        try {
            app(SaveTaxonomy::class)->handle($actor, $listing->primaryCategory, ['name' => 'Cambio', 'slug' => 'nuevo', 'description' => null, 'listing_type' => 'business', 'sort_order' => 0, 'is_active' => true]);
            $this->fail('Cambió slug usado');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('slug', $e->errors());
        }
        $city = app(SaveTaxonomy::class)->handle($actor, $listing->city, ['name' => 'Playa del Carmen', 'slug' => 'playa-del-carmen', 'timezone' => 'America/Cancun', 'is_public' => false]);
        $this->get('/@'.$listing->slug)->assertNotFound();
        $this->assertFalse($city->is_public);
    }

    public function test_private_notes_and_permission_values_never_appear_in_audit_payloads(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $listing = Listing::factory()->create();
        $data = $listing->only(Listing::FIELDS) + ['category_ids' => [$listing->primary_category_id]];
        $data['private_notes'] = 'EVIDENCIA-PRIVADA-DE-PRUEBA';
        app(SaveListing::class)->handle($actor, $data, $listing);
        $this->assertStringNotContainsString('EVIDENCIA-PRIVADA-DE-PRUEBA', AuditEvent::query()->get()->toJson());
    }

    public function test_publication_rejects_unverified_media_and_google_values_without_review_date(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $listing = Listing::factory()->create(['media_approved' => true]);
        try {
            app(TransitionListing::class)->handle($actor, $listing, 'published');
            $this->fail('Aceptó medios sin fecha de verificación');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('media_reviewed_at', $e->errors());
        }
        $data = $listing->only(Listing::FIELDS) + ['category_ids' => [$listing->primary_category_id]];
        $data['media_reviewed_at'] = today('America/Cancun')->toDateString();
        $data['google_rating'] = 4.5;
        try {
            app(SaveListing::class)->handle($actor, $data, $listing);
            $this->fail('Aceptó cifra sin revisar');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('google_reviewed_at', $e->errors());
        }
    }

    public function test_review_date_uses_cancun_calendar_at_the_utc_day_boundary(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-01-02 01:00:00', 'UTC'));
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $listing = Listing::factory()->create();
        $data = $listing->only(Listing::FIELDS) + ['category_ids' => [$listing->primary_category_id]];
        try {
            app(SaveListing::class)->handle($actor, $data, $listing);
            $this->fail('Aceptó una revisión futura en Cancún');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('reviewed_at', $e->errors());
        }
        $data['reviewed_at'] = '2026-01-01';
        $saved = app(SaveListing::class)->handle($actor, $data, $listing);
        $this->assertSame('2026-01-01', $saved->reviewed_at->toDateString());
    }
}
