<?php

namespace Tests\Feature;

use App\Filament\Resources\Areas\Pages\ManageAreas;
use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Filament\Resources\Cities\Pages\ManageCities;
use App\Filament\Resources\Listings\Pages\CreateListing;
use App\Filament\Resources\Listings\Pages\EditListing;
use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ListingAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('listing_media');
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
    }

    public function test_guest_and_ordinary_user_cannot_reach_domain_resources(): void
    {
        foreach (['ciudades', 'zonas', 'categorias', 'fichas', 'reportes'] as $path) {
            $this->get('/dashboard/'.$path)->assertRedirect('/dashboard/login');
        }
        $this->actingAs(User::factory()->withAppAuthentication()->create());
        foreach (['ciudades', 'zonas', 'categorias', 'fichas', 'reportes'] as $path) {
            $this->get('/dashboard/'.$path)->assertForbidden();
        }
    }

    public function test_admin_can_create_and_edit_taxonomy_from_the_panel_without_delete_actions(): void
    {
        $this->actingAs(User::factory()->superAdmin()->withAppAuthentication()->create());
        Livewire::test(ManageCities::class)->callAction('create', data: ['name' => 'Playa del Carmen', 'slug' => 'playa-del-carmen', 'timezone' => 'America/Cancun', 'is_public' => true])->assertHasNoActionErrors();
        $city = City::query()->sole();
        Livewire::test(ManageAreas::class)->callAction('create', data: ['name' => 'Centro', 'slug' => 'centro', 'city_id' => $city->id, 'is_active' => true])->assertHasNoActionErrors();
        Livewire::test(ManageCategories::class)->callAction('create', data: ['name' => 'Servicios', 'slug' => 'servicios', 'listing_type' => 'business', 'sort_order' => 2, 'is_active' => true])->assertHasNoActionErrors();
        $area = Area::query()->sole();
        Livewire::test(ManageAreas::class)->callAction(TestAction::make('edit')->table($area), data: ['name' => 'Centro revisado', 'slug' => 'centro', 'city_id' => $city->id, 'is_active' => true])->assertHasNoActionErrors()->assertTableActionDoesNotExist('delete')->assertTableBulkActionDoesNotExist('delete');
        $this->assertSame('Centro revisado', $area->fresh()->name);
        $this->assertDatabaseHas('audit_events', ['action' => 'taxonomy.changed']);
    }

    public function test_admin_creates_a_draft_edits_pdf_label_and_publishes_from_the_panel(): void
    {
        $this->actingAs(User::factory()->superAdmin()->withAppAuthentication()->create());
        $city = City::factory()->create();
        $category = Category::factory()->create();
        $component = Livewire::test(CreateListing::class)->fillForm(['name' => 'Negocio de prueba', 'slug' => 'negocio-de-prueba', 'city_id' => $city->id, 'primary_category_id' => $category->id, 'category_ids' => [$category->id], 'summary' => 'Resumen verificado', 'description' => 'Descripción aprobada', 'source_url' => 'https://example.org', 'reviewed_at' => today()->toDateString(), 'content_approved' => true])->call('create')->assertHasNoFormErrors();
        $listing = Listing::query()->sole();
        $this->assertSame('draft', $listing->status);
        Livewire::test(EditListing::class, ['record' => $listing->id])->fillForm(['document_button_label' => 'Descargar folleto actualizado'])->call('save')->assertHasNoFormErrors();
        $listing->addMedia(UploadedFile::fake()->image('logo.png'))->toMediaCollection('logo');
        $listing->forceFill(['logo_approved' => true, 'logo_alt' => 'Logo de prueba', 'logo_reviewed_at' => today('America/Cancun')])->save();
        Livewire::test(EditListing::class, ['record' => $listing->id])->callAction('published')->assertHasNoErrors();
        $this->assertSame('Descargar folleto actualizado', $listing->fresh()->document_button_label);
        $this->assertSame('published', $listing->fresh()->status);
    }

    public function test_plugin_uploads_pdf_to_private_collection_and_rejects_fake_pdf_content(): void
    {
        Storage::fake('listing_media');
        $this->actingAs(User::factory()->superAdmin()->withAppAuthentication()->create());
        $listing = Listing::factory()->create();
        $pdf = UploadedFile::fake()->createWithContent('menu.pdf', "%PDF-1.4\n1 0 obj <</Type/Catalog>> endobj\n%%EOF");
        Livewire::test(EditListing::class, ['record' => $listing->id])->fillForm(['document' => [$pdf]])->call('save')->assertHasNoFormErrors();
        $media = $listing->fresh()->getFirstMedia('document');
        $this->assertNotNull($media);
        $this->assertSame('listing_media', $media->disk);
        Storage::disk('listing_media')->assertExists($media->getPathRelativeToRoot());
        Livewire::test(EditListing::class, ['record' => $listing->id])->fillForm(['document' => [UploadedFile::fake()->createWithContent('falso.pdf', '<script>no es pdf</script>')]])->call('save')->assertHasFormErrors(['document']);
    }

    public function test_revoked_access_is_checked_on_livewire_updates_and_delete_policies_remain_denied(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        $this->actingAs($admin);
        $listing = Listing::factory()->create();
        $component = Livewire::test(EditListing::class, ['record' => $listing->id]);
        $admin->is_super_admin = false;
        $admin->save();
        $component->call('save')->assertForbidden();
        $this->assertNotSame('No autorizado', $listing->fresh()->name);
        $admin->is_super_admin = true;
        $admin->save();
        foreach ([City::class, Area::class, Category::class, Listing::class] as $model) {
            $this->assertFalse(Gate::allows('deleteAny', $model));
        }
    }

    public function test_domain_validation_errors_are_attached_to_the_edit_form(): void
    {
        $this->actingAs(User::factory()->superAdmin()->withAppAuthentication()->create());
        $listing = Listing::factory()->create();
        Livewire::test(EditListing::class, ['record' => $listing->id])->fillForm(['slug' => 'blog'])->call('save')->assertHasFormErrors(['slug']);
        $this->assertNotSame('blog', $listing->fresh()->slug);
    }

    public function test_published_form_preserves_private_verification_fields_and_rejects_incomplete_publication(): void
    {
        $this->actingAs(User::factory()->superAdmin()->withAppAuthentication()->create());
        $listing = Listing::factory()->withAuthorizedLogo()->create(['private_notes' => 'Nota solo administrativa', 'media_permission' => 'Permiso verificado']);
        Livewire::test(EditListing::class, ['record' => $listing->id])->assertSchemaStateSet(['source_url' => 'https://example.org/fuente', 'private_notes' => 'Nota solo administrativa'])->assertFormFieldDoesNotExist('media_permission')->fillForm(['source_url' => null])->call('save')->assertHasNoFormErrors();
        Livewire::test(EditListing::class, ['record' => $listing->id])->callAction('published')->assertNotified(Notification::make()->title('No se pudo cambiar el estado')->body('Completa y verifica este dato antes de publicar.')->danger()->persistent());
        $this->assertSame('draft', $listing->fresh()->status);
        $this->assertSame('Permiso verificado', $listing->fresh()->media_permission);
    }

    public function test_http_livewire_request_rechecks_revoked_role_and_mfa(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        $this->actingAs($admin);
        $listing = Listing::factory()->create();
        $html = $this->get('/dashboard/fichas/'.$listing->id.'/edit')->assertOk()->getContent();
        preg_match_all('/wire:snapshot="([^"]+)"/', $html, $matches);
        $snapshot = null;
        foreach ($matches[1] as $encoded) {
            $decoded = html_entity_decode($encoded, ENT_QUOTES);
            if (str_contains(json_decode($decoded, true)['memo']['name'], 'EditListing')) {
                $snapshot = $decoded;
                break;
            }
        }
        $this->assertNotNull($snapshot);
        $admin->is_super_admin = false;
        $admin->save();
        $this->withHeaders(['X-Livewire' => 'true'])->postJson(app('livewire')->getUpdateUri(), ['components' => [['snapshot' => $snapshot, 'updates' => [], 'calls' => [['path' => '', 'method' => 'save', 'params' => []]]]]])->assertForbidden();
        $this->assertSame('draft', $listing->fresh()->status);
    }
}
