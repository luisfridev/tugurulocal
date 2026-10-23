<?php

namespace Tests\Feature;

use App\Actions\Listings\RestoreListingRevision;
use App\Actions\Listings\SaveListing;
use App\Actions\Listings\TransitionListing;
use App\Filament\Resources\Listings\Pages\EditListing;
use App\Models\Listing;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SlugRedirectTest extends TestCase
{
    use RefreshDatabase;

    private User $actor;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('listing_media');
        $this->actor = User::factory()->superAdmin()->withAppAuthentication()->create();
    }

    public function test_successive_changes_redirect_to_current_url_without_recording_events(): void
    {
        $listing = Listing::factory()->published()->create(['slug' => 'origen']);
        $listing = $this->rename($listing, 'intermedio');
        $listing = $this->rename($listing, 'vigente');
        foreach (['origen', 'intermedio'] as $slug) {
            $this->get('/@'.$slug.'?tracking=privado')->assertStatus(301)->assertRedirect(url('/@vigente'))->assertHeader('Cache-Control', 'no-store, private');
            $this->head('/@'.$slug)->assertStatus(301)->assertRedirect(url('/@vigente'));
        }
        $this->assertDatabaseCount('interaction_events', 0);
        $this->get('/@vigente')->assertOk()->assertSee('href="'.url('/@vigente').'"', false);
        $this->assertDatabaseCount('interaction_events', 1);
        $this->assertDatabaseCount('slug_redirects', 2);
        $this->assertSame(2, $listing->revisions()->count());
        $this->assertDatabaseHas('audit_events', ['action' => 'content.changed', 'subject_id' => $listing->id]);
        $this->get('/sitemap.xml')->assertSee(url('/@vigente'))->assertDontSee('/@origen')->assertDontSee('/@intermedio');
        $this->get('/playa-del-carmen/negocios')->assertSee(url('/@vigente'))->assertDontSee('/@origen');
    }

    #[DataProvider('privateStates')]
    public function test_private_destination_is_hidden_and_reservation_survives(string $status): void
    {
        $listing = Listing::factory()->published()->create(['slug' => 'anterior']);
        $listing = $this->rename($listing, 'actual');
        app(TransitionListing::class)->handle($this->actor, $listing, $status);
        $this->get('/@anterior')->assertNotFound()->assertHeaderMissing('Location')->assertDontSee('/@actual');
        $this->get('/sitemap.xml')->assertDontSee('/@actual');
        $this->assertDatabaseHas('slug_redirects', ['source_path' => '/@anterior']);
    }

    public static function privateStates(): array
    {
        return [['draft'], ['paused'], ['archived']];
    }

    public function test_renaming_paused_content_and_republishing_reactivates_redirects(): void
    {
        $listing = Listing::factory()->published()->create(['slug' => 'anterior']);
        $listing = app(TransitionListing::class)->handle($this->actor, $listing, 'paused');
        $listing = $this->rename($listing, 'actual');
        $this->get('/@anterior')->assertNotFound();
        app(TransitionListing::class)->handle($this->actor, $listing, 'published');
        $this->get('/@anterior')->assertStatus(301)->assertRedirect(url('/@actual'));
    }

    public function test_missing_logo_or_disabled_city_revokes_redirect_access(): void
    {
        $listing = Listing::factory()->published()->create(['slug' => 'anterior']);
        $listing = $this->rename($listing, 'actual');
        $listing->city->update(['is_public' => false]);
        $this->get('/@anterior')->assertNotFound();
        $listing->city->update(['is_public' => true]);
        Storage::disk('listing_media')->delete($listing->getFirstMedia('logo')->getPathRelativeToRoot());
        $this->get('/@anterior')->assertNotFound();
        $this->get('/@actual')->assertNotFound();
    }

    public function test_origin_cannot_be_reused_by_owner_or_new_content(): void
    {
        $listing = Listing::factory()->published()->create(['slug' => 'reservado']);
        $listing = $this->rename($listing, 'actual');
        foreach ([$listing, null] as $target) {
            try {
                app(SaveListing::class)->handle($this->actor, $this->data($listing, 'reservado'), $target);
                $this->fail('Aceptó un origen reservado');
            } catch (ValidationException $exception) {
                $this->assertSame(['Este slug está reservado por una URL anterior y no se puede reutilizar.'], $exception->errors()['slug']);
            }
        }
        $this->assertSame('actual', $listing->fresh()->slug);
        $this->assertDatabaseCount('listings', 1);
    }

    public function test_rollback_refuses_to_remove_reserved_urls(): void
    {
        $listing = Listing::factory()->published()->create(['slug' => 'origen-reservado']);
        $listing = $this->rename($listing, 'destino-vigente');
        $migration = require database_path('migrations/2026_10_23_072127_create_slug_redirects_table.php');

        try {
            $migration->down();
            $this->fail('Eliminó las reservas de URLs existentes.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('No se pueden retirar las reservas de URLs existentes. Usa una migración correctiva.', $exception->getMessage());
        }

        $this->assertDatabaseHas('slug_redirects', ['source_path' => '/@origen-reservado', 'listing_id' => $listing->id]);
        $this->get('/@origen-reservado')->assertStatus(301)->assertRedirect(url('/@destino-vigente'));
    }

    public function test_failed_validation_rolls_back_slug_and_reservation(): void
    {
        $listing = Listing::factory()->published()->create(['slug' => 'anterior']);
        try {
            app(SaveListing::class)->handle($this->actor, array_replace($this->data($listing, 'actual'), ['summary' => null]), $listing);
            $this->fail('Aceptó publicación incompleta');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('summary', $exception->errors());
        }
        $this->assertSame('anterior', $listing->fresh()->slug);
        $this->assertDatabaseCount('slug_redirects', 0);
        $this->assertDatabaseCount('content_revisions', 0);
    }

    public function test_drafts_create_no_history_and_auxiliary_and_unknown_paths_do_not_redirect(): void
    {
        $draft = Listing::factory()->create(['slug' => 'borrador']);
        $this->rename($draft, 'actual');
        $this->assertDatabaseCount('slug_redirects', 0);
        $listing = Listing::factory()->recycle($draft->city)->published()->create(['slug' => 'anterior']);
        $this->rename($listing, 'publico');
        foreach (['/@desconocido', '/@anterior/contacto/maps', '/@anterior/medios/1', '/@anterior/reportar', '/negocio-antiguo'] as $path) {
            $this->get($path)->assertNotFound()->assertHeaderMissing('Location');
        }
        $this->post('/@anterior/reportar', [])->assertNotFound()->assertHeaderMissing('Location');
        $this->post('/@anterior')->assertStatus(405)->assertHeaderMissing('Location');
    }

    public function test_revision_restore_keeps_current_slug_and_reservations(): void
    {
        $listing = Listing::factory()->published()->create(['slug' => 'original']);
        app(SaveListing::class)->handle($this->actor, $this->data($listing, 'original'), $listing);
        $revision = $listing->revisions()->firstOrFail();
        $listing = $this->rename($listing, 'actual');
        $restored = app(RestoreListingRevision::class)->handle($this->actor, $listing, $revision);
        $this->assertSame('actual', $restored->slug);
        $this->assertSame('draft', $restored->status);
        $this->assertDatabaseCount('slug_redirects', 1);
        $this->get('/@original')->assertNotFound();
    }

    public function test_slug_mutations_require_super_admin_and_second_factor(): void
    {
        $listing = Listing::factory()->published()->create(['slug' => 'original']);
        foreach ([User::factory()->create(), User::factory()->superAdmin()->create()] as $actor) {
            try {
                app(SaveListing::class)->handle($actor, $this->data($listing, 'actual'), $listing);
                $this->fail('Cambió el slug sin autorización');
            } catch (AuthorizationException) {
                $this->assertSame('original', $listing->fresh()->slug);
                $this->assertDatabaseCount('slug_redirects', 0);
            }
        }
    }

    public function test_admin_form_saves_public_slug_change_and_shows_reserved_slug_errors(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
        $this->actingAs($this->actor);
        $listing = Listing::factory()->published()->create(['slug' => 'original']);
        Livewire::test(EditListing::class, ['record' => $listing->id])->fillForm(['slug' => 'actual'])->call('save')->assertHasNoFormErrors();
        $this->get('/@original')->assertStatus(301)->assertRedirect(url('/@actual'));
        Livewire::test(EditListing::class, ['record' => $listing->id])->fillForm(['slug' => 'original'])->call('save')->assertHasFormErrors(['slug']);
        $this->assertSame('actual', $listing->fresh()->slug);
    }

    private function rename(Listing $listing, string $slug): Listing
    {
        return app(SaveListing::class)->handle($this->actor, $this->data($listing, $slug), $listing);
    }

    private function data(Listing $listing, string $slug): array
    {
        return array_replace($listing->only(Listing::FIELDS), ['slug' => $slug, 'category_ids' => $listing->categories()->pluck('categories.id')->all()]);
    }
}
