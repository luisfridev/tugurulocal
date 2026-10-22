<?php

namespace Tests\Feature;

use App\Actions\Listings\SaveHomeSelection;
use App\Enums\AuditAction;
use App\Filament\Resources\Cities\Pages\ManageCities;
use App\Models\AuditEvent;
use App\Models\City;
use App\Models\HomeSelection;
use App\Models\Listing;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HomeSelectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('listing_media');
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
    }

    public function test_admin_can_save_six_businesses_reorder_remove_and_clear_the_selection_with_private_audit(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $city = City::factory()->create();
        $listings = Listing::factory()->recycle($city)->published()->count(6)->create(['private_notes' => 'Nota privada que no debe ir a auditoría']);
        $action = app(SaveHomeSelection::class);

        $action->handle($actor, $city, $listings->modelKeys());

        $this->assertSame($listings->modelKeys(), $city->homeSelections()->pluck('listing_id')->all());
        $this->assertSame([1, 2, 3, 4, 5, 6], $city->homeSelections()->pluck('position')->all());
        $event = AuditEvent::query()->sole();
        $this->assertSame(AuditAction::HomeSelectionChanged, $event->action);
        $this->assertSame($city->id, $event->subject_id);
        $this->assertSame($actor->id, $event->actor_id);
        $this->assertSame(['home_selections'], $event->changed_fields);
        $this->assertStringNotContainsString('Nota privada', $event->toJson());
        $this->assertStringNotContainsString($listings[0]->name, $event->toJson());

        $action->handle($actor, $city, [$listings[5]->id, $listings[0]->id]);
        $this->assertSame([$listings[5]->id, $listings[0]->id], $city->homeSelections()->pluck('listing_id')->all());
        $this->assertSame([1, 2], $city->homeSelections()->pluck('position')->all());

        $action->handle($actor, $city, []);
        $this->assertDatabaseCount('home_selections', 0);
        $this->assertDatabaseCount('listings', 6);
        $this->assertDatabaseCount('audit_events', 3);
    }

    public function test_panel_saves_the_selected_order_and_keeps_validation_errors_visible(): void
    {
        $this->actingAs(User::factory()->superAdmin()->withAppAuthentication()->create());
        $city = City::factory()->create();
        $listings = Listing::factory()->recycle($city)->published()->count(2)->create();

        Livewire::test(ManageCities::class)
            ->callAction(TestAction::make('homeSelection')->table($city), data: [
                'selections' => [['listing_id' => $listings[1]->id], ['listing_id' => $listings[0]->id]],
            ])
            ->assertHasNoActionErrors()
            ->assertNotified('Selección de Inicio guardada');

        $this->assertSame([$listings[1]->id, $listings[0]->id], $city->homeSelections()->pluck('listing_id')->all());

        Livewire::test(ManageCities::class)
            ->callAction(TestAction::make('homeSelection')->table($city), data: [
                'selections' => [['listing_id' => $listings[0]->id], ['listing_id' => $listings[0]->id]],
            ])
            ->assertHasActionErrors();
        $this->assertSame([$listings[1]->id, $listings[0]->id], $city->homeSelections()->pluck('listing_id')->all());
        $this->assertDatabaseCount('audit_events', 1);
    }

    public function test_more_than_six_and_duplicate_businesses_leave_the_current_selection_unchanged(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $city = City::factory()->create();
        $listings = Listing::factory()->recycle($city)->published()->count(7)->create();
        HomeSelection::factory()->for($city)->for($listings[0])->create();

        foreach ([$listings->modelKeys(), [$listings[0]->id, (string) $listings[0]->id]] as $ids) {
            try {
                app(SaveHomeSelection::class)->handle($actor, $city, $ids);
                $this->fail('Aceptó una selección con exceso de cupo o duplicados.');
            } catch (ValidationException $exception) {
                $this->assertStringContainsString($ids === $listings->modelKeys() ? 'seis' : 'una vez', collect($exception->errors())->flatten()->first());
            }
        }

        $this->assertSame([$listings[0]->id], $city->homeSelections()->pluck('listing_id')->all());
        $this->assertDatabaseCount('audit_events', 0);
    }

    public function test_panel_buttons_move_businesses_up_down_and_remove_them_before_saving(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $this->actingAs($actor);
        $city = City::factory()->create();
        $listings = Listing::factory()->recycle($city)->published()->count(3)->create();
        app(SaveHomeSelection::class)->handle($actor, $city, $listings->modelKeys());
        $component = Livewire::test(ManageCities::class)->mountAction(TestAction::make('homeSelection')->table($city));
        $keys = array_keys($component->get('mountedActions.0.data.selections'));

        $component->callAction(TestAction::make('moveUp')->schemaComponent('selections')->arguments(['item' => $keys[2]]));
        $this->assertSame([$listings[0]->id, $listings[2]->id, $listings[1]->id], array_map(intval(...), array_column($component->get('mountedActions.0.data.selections'), 'listing_id')));

        $component->callAction(TestAction::make('moveDown')->schemaComponent('selections')->arguments(['item' => $keys[0]]));
        $this->assertSame([$listings[2]->id, $listings[0]->id, $listings[1]->id], array_map(intval(...), array_column($component->get('mountedActions.0.data.selections'), 'listing_id')));

        $component->callAction(TestAction::make('delete')->schemaComponent('selections')->arguments(['item' => $keys[1]]))
            ->callMountedAction()->assertHasNoActionErrors();

        $this->assertSame([$listings[2]->id, $listings[0]->id], $city->homeSelections()->pluck('listing_id')->all());
        $this->assertDatabaseCount('listings', 3);
    }

    public function test_unknown_businesses_and_businesses_from_another_city_are_rejected(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $selection = HomeSelection::factory()->create();
        $otherCity = City::factory()->create(['name' => 'Ciudad privada', 'slug' => 'ciudad-privada', 'is_public' => false]);
        $otherListing = Listing::factory()->for($otherCity)->published()->create();

        foreach ([$otherListing->id, $otherListing->id + 100] as $id) {
            try {
                app(SaveHomeSelection::class)->handle($actor, $selection->city, [$selection->listing_id, $id]);
                $this->fail('Aceptó una ficha ajena o inexistente.');
            } catch (ValidationException $exception) {
                $this->assertSame(['Selecciona únicamente negocios de esta ciudad.'], $exception->errors()['selections']);
            }
        }

        $this->assertDatabaseCount('home_selections', 1);
        $this->assertModelExists($selection);
        $this->assertDatabaseCount('audit_events', 0);
    }

    /** @return array<string, array{array<string, mixed>}> */
    public static function unavailableListings(): array
    {
        return [
            'borrador' => [['status' => 'draft']],
            'pausado' => [['status' => 'paused']],
            'archivado' => [['status' => 'archived']],
            'contenido sin aprobación' => [['content_approved' => false]],
            'logo sin autorización' => [['logo_approved' => false]],
            'cerrado permanentemente' => [['operating_status' => 'permanently_closed']],
        ];
    }

    #[DataProvider('unavailableListings')]
    public function test_new_selections_reject_businesses_that_are_not_eligible(array $changes): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $selection = HomeSelection::factory()->create();
        $listing = Listing::factory()->for($selection->city)->published()->create();
        $listing->forceFill($changes)->save();

        try {
            app(SaveHomeSelection::class)->handle($actor, $selection->city, [$selection->listing_id, $listing->id]);
            $this->fail('Añadió una ficha que no debe destacarse.');
        } catch (ValidationException $exception) {
            $this->assertSame(['Solo puedes añadir negocios públicos con logo autorizado que no estén cerrados permanentemente.'], $exception->errors()['selections']);
        }

        $this->assertModelExists($selection);
        $this->assertDatabaseCount('home_selections', 1);
        $this->assertDatabaseCount('audit_events', 0);
    }

    public function test_a_missing_logo_file_cannot_be_selected_even_when_media_metadata_remains(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $listing = Listing::factory()->published()->create();
        Storage::disk('listing_media')->delete($listing->getFirstMedia('logo')->getPathRelativeToRoot());

        try {
            app(SaveHomeSelection::class)->handle($actor, $listing->city, [$listing->id]);
            $this->fail('Añadió una ficha sin archivo de logo.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('selections', $exception->errors());
        }

        $this->assertSame([], app(SaveHomeSelection::class)->options($listing->city));
        $this->assertDatabaseCount('home_selections', 0);
    }

    public function test_existing_ineligible_selections_remain_identified_can_be_reordered_and_can_be_removed(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $selection = HomeSelection::factory()->create();
        $listing = $selection->listing;
        $listing->forceFill(['status' => 'paused'])->save();
        $newListing = Listing::factory()->for($selection->city)->published()->create(['operating_status' => 'temporarily_closed']);

        app(SaveHomeSelection::class)->handle($actor, $selection->city, [$newListing->id, $listing->id]);

        $this->assertSame([$newListing->id, $listing->id], $selection->city->homeSelections()->pluck('listing_id')->all());
        $this->assertSame($listing->name.' (no visible en Inicio)', app(SaveHomeSelection::class)->options($selection->city)[$listing->id]);
        $this->assertSame($newListing->name, app(SaveHomeSelection::class)->options($selection->city)[$newListing->id]);

        app(SaveHomeSelection::class)->handle($actor, $selection->city, [$newListing->id]);
        $this->assertDatabaseMissing('home_selections', ['listing_id' => $listing->id]);
        $this->assertArrayNotHasKey($listing->id, app(SaveHomeSelection::class)->options($selection->city));
        $this->assertSame('paused', $listing->fresh()->status);
    }

    public function test_selection_mutation_requires_super_admin_with_second_factor(): void
    {
        $selection = HomeSelection::factory()->create();
        foreach ([User::factory()->withAppAuthentication()->create(), User::factory()->superAdmin()->create()] as $actor) {
            try {
                app(SaveHomeSelection::class)->handle($actor, $selection->city, []);
                $this->fail('Modificó la selección sin permisos completos.');
            } catch (AuthorizationException) {
                $this->assertModelExists($selection);
            }
        }

        $this->assertDatabaseCount('audit_events', 0);
    }

    public function test_livewire_rechecks_permissions_when_an_admin_loses_access(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $this->actingAs($actor);
        $selection = HomeSelection::factory()->create();
        $component = Livewire::test(ManageCities::class)->mountAction(TestAction::make('homeSelection')->table($selection->city));
        $actor->forceFill(['is_super_admin' => false])->save();

        $component->call('callMountedAction')->assertForbidden();

        $this->assertModelExists($selection);
        $this->assertDatabaseMissing('audit_events', ['action' => AuditAction::HomeSelectionChanged->value]);
    }
}
