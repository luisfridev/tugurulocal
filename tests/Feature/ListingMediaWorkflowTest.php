<?php

namespace Tests\Feature;

use App\Actions\Listings\SaveListing;
use App\Actions\Listings\SaveTaxonomy;
use App\Filament\Resources\Areas\Pages\ManageAreas;
use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Filament\Resources\Cities\Pages\ManageCities;
use App\Filament\Resources\Listings\Pages\EditListing;
use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\User;
use Carbon\CarbonImmutable;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class ListingMediaWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('listing_media');
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
        $this->travelTo(CarbonImmutable::parse('2026-10-22 12:00:00', 'UTC'));
    }

    public function test_listing_form_retires_source_and_license_without_overwriting_historical_values(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $this->actingAs($actor);
        $listing = Listing::factory()->withAuthorizedLogo()->create();
        $historical = ['logo_source' => 'Origen anterior', 'logo_permission' => 'Permiso anterior', 'media_source' => 'Origen de archivos anterior', 'media_permission' => 'Licencia anterior'];
        $listing->forceFill($historical)->save();
        $form = Livewire::test(EditListing::class, ['record' => $listing->id])
            ->assertFormFieldExists('logo_reviewed_at')->assertFormFieldExists('media_reviewed_at')
            ->assertFormFieldExists('source_url')->assertFormFieldExists('reviewed_at')->assertFormFieldExists('google_reviewed_at');
        foreach (array_keys($historical) as $field) {
            $form->assertFormFieldDoesNotExist($field);
        }

        $form->fillForm(['logo_approved' => true, 'logo_reviewed_at' => '2026-10-20', 'media_approved' => true, 'media_reviewed_at' => '2026-10-21'])
            ->call('save')->assertHasNoFormErrors()->callAction('published');

        $listing->refresh();
        $this->assertSame('published', $listing->status);
        $this->assertSame('2026-10-20', $listing->logo_reviewed_at->toDateString());
        $this->assertSame('2026-10-21', $listing->media_reviewed_at->toDateString());
        foreach ($historical as $field => $value) {
            $this->assertSame($value, $listing->{$field});
            $this->assertArrayNotHasKey($field, $listing->toArray());
            $this->assertArrayNotHasKey($field, $listing->revisions()->latest('id')->first()->snapshot);
        }
        $data = $listing->only(Listing::FIELDS) + ['category_ids' => [$listing->primary_category_id]];
        $saved = app(SaveListing::class)->handle($actor, array_replace($data, array_fill_keys(array_keys($historical), 'Valor enviado fuera del formulario')), $listing);
        foreach ($historical as $field => $value) {
            $this->assertSame($value, $saved->{$field});
        }
        $this->get('/@'.$listing->slug)->assertOk()->assertDontSee('Permiso anterior')->assertDontSee('Origen anterior');
    }

    public function test_draft_authorization_requires_manual_dates_and_rejects_the_next_cancun_calendar_day(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-23 01:00:00', 'UTC'));
        $this->actingAs(User::factory()->superAdmin()->withAppAuthentication()->create());
        $listing = Listing::factory()->withAuthorizedLogo()->create(['reviewed_at' => '2026-10-22']);

        foreach (['logo' => 'logo_approved', 'media' => 'media_approved'] as $prefix => $approval) {
            $field = $prefix.'_reviewed_at';
            foreach ([null, '2026-10-23'] as $invalidDate) {
                Livewire::test(EditListing::class, ['record' => $listing->id])
                    ->fillForm([$approval => true, $field => $invalidDate])->call('save')->assertHasFormErrors([$field]);
                $this->assertSame('draft', $listing->fresh()->status);
            }
        }
        Livewire::test(EditListing::class, ['record' => $listing->id])
            ->fillForm(['logo_approved' => true, 'logo_reviewed_at' => '2026-10-22', 'media_approved' => true, 'media_reviewed_at' => '2026-10-21'])
            ->call('save')->assertHasNoFormErrors();

        $this->assertSame('2026-10-22', $listing->fresh()->logo_reviewed_at->toDateString());
        $this->assertSame('2026-10-21', $listing->fresh()->media_reviewed_at->toDateString());
        $this->assertTrue($listing->fresh()->hasAuthorizedMedia());
    }

    public function test_taxonomy_forms_keep_manual_dates_and_retire_historical_source_and_license_fields(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $this->actingAs($actor);
        $city = City::factory()->create();
        $records = [
            [$city, ManageCities::class],
            [Category::factory()->create(), ManageCategories::class],
            [Area::factory()->for($city)->create(), ManageAreas::class],
        ];
        foreach ($records as [$record, $page]) {
            $historical = ['og_image_source' => 'Origen OG anterior', 'og_image_permission' => 'Permiso OG anterior', 'icon_source' => 'Origen de icono anterior', 'icon_permission' => 'Permiso de icono anterior'];
            $record->forceFill($historical)->save();
            $form = Livewire::test($page)->mountAction(TestAction::make('edit')->table($record));
            foreach (array_keys($historical) as $field) {
                $form->assertFormFieldDoesNotExist($field, 'mountedActionSchema0');
            }
            foreach (['og_image', 'icon'] as $collection) {
                $form->assertFormFieldExists($collection.'_reviewed_at', 'mountedActionSchema0');
            }
            $data = $record->getAttributes();
            foreach (['og_image', 'icon'] as $collection) {
                $data[$collection.'_approved'] = true;
                $data[$collection.'_alt'] = 'Texto alternativo';
                $data[$collection.'_reviewed_at'] = '2026-10-20';
            }
            $form->fillForm($data, 'mountedActionSchema0')->callMountedAction()->assertHasNoActionErrors();

            $record->refresh();
            foreach ($historical as $field => $value) {
                $this->assertSame($value, $record->{$field});
                $this->assertArrayNotHasKey($field, $record->toArray());
            }
            foreach (['og_image', 'icon'] as $collection) {
                $this->assertTrue($record->{$collection.'_approved'});
                $this->assertSame('2026-10-20', $record->{$collection.'_reviewed_at'}->toDateString());
                foreach ([null, '2026-10-23'] as $invalidDate) {
                    try {
                        app(SaveTaxonomy::class)->handle($actor, $record, array_replace($data, [$collection.'_reviewed_at' => $invalidDate]));
                        $this->fail('Autorizó medio sin fecha válida.');
                    } catch (ValidationException $exception) {
                        $this->assertArrayHasKey($collection.'_reviewed_at', $exception->errors());
                    }
                }
            }
        }
    }

    public function test_public_media_require_current_authorization_dates_and_never_require_written_source_or_license(): void
    {
        $listing = Listing::factory()->published()->create(['media_approved' => true, 'media_reviewed_at' => '2026-10-21']);
        $cover = $listing->addMedia(UploadedFile::fake()->image('portada.png'))->toMediaCollection('cover');
        $pdf = $listing->addMedia(UploadedFile::fake()->createWithContent('menu.pdf', "%PDF-1.4\n%%EOF"))->toMediaCollection('document');
        $this->assertNull($listing->logo_source);
        $this->assertNull($listing->logo_permission);
        $this->assertNull($listing->media_source);
        $this->assertNull($listing->media_permission);
        $this->get('/@'.$listing->slug)->assertOk()->assertSee($cover->getUrl(), false)->assertSee('Descargar menú o folleto');
        $this->get($pdf->getUrl())->assertOk();

        foreach ([null, '2026-10-23'] as $invalidDate) {
            $listing->forceFill(['media_reviewed_at' => $invalidDate])->save();
            $this->get('/@'.$listing->slug)->assertOk()->assertDontSee($cover->getUrl(), false)->assertDontSee('Descargar menú o folleto');
            $this->get($pdf->getUrl())->assertNotFound();
            $this->get(route('listings.contact', ['listing' => $listing->slug, 'kind' => 'document']))->assertNotFound();
        }
        $listing->forceFill(['logo_reviewed_at' => '2026-10-23'])->save();
        $this->get('/@'.$listing->slug)->assertNotFound();
        $this->get($listing->getFirstMedia('logo')->getUrl())->assertNotFound();
    }
}
