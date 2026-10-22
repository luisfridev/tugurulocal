<?php

namespace Tests\Feature;

use App\Actions\Listings\SaveListing;
use App\Actions\Listings\SaveTaxonomy;
use App\Actions\Listings\TransitionListing;
use App\Filament\Resources\Areas\Pages\ManageAreas;
use App\Filament\Resources\Listings\Pages\EditListing;
use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\User;
use App\Support\IdentityColor;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class ListingIdentityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('listing_media');
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
    }

    public function test_draft_saves_without_logo_but_publication_rejects_it(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $listing = Listing::factory()->create();

        $saved = app(SaveListing::class)->handle($actor, $listing->only(Listing::FIELDS) + ['category_ids' => [$listing->primary_category_id]], $listing);
        $this->assertSame('draft', $saved->status);
        try {
            app(TransitionListing::class)->handle($actor, $listing, 'published');
            $this->fail('Publicó sin logo.');
        } catch (ValidationException $exception) {
            $this->assertSame('Carga y guarda un logo antes de publicar.', $exception->errors()['logo'][0]);
        }
        $this->assertSame('draft', $listing->fresh()->status);
        $listing->forceFill(['status' => 'published', 'published_at' => now(), 'logo_approved' => true, 'logo_alt' => 'Alt', 'logo_reviewed_at' => today('America/Cancun')])->save();
        $this->get('/@'.$listing->slug)->assertNotFound();
    }

    public function test_authorized_logo_publishes_independently_and_is_social_image_without_cover(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $listing = Listing::factory()->withAuthorizedLogo()->create(['media_approved' => false]);

        $listing = app(TransitionListing::class)->handle($actor, $listing, 'published');

        $logo = $listing->getFirstMedia('logo');
        $this->get('/@'.$listing->slug)->assertOk()->assertSee('alt="Logo sintético de prueba"', false)->assertSee('<meta property="og:image" content="'.$logo->getUrl().'"', false);
        $this->get('/@'.$listing->slug)->assertSee('"logo":"'.$logo->getUrl().'"', false);
        $this->get($logo->getUrl())->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertFalse($listing->hasMedia('cover'));
        $this->assertFalse($listing->hasMedia('document'));
        $this->assertFalse($listing->media_approved);
    }

    public function test_missing_authorization_evidence_or_file_blocks_publication_and_direct_url(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $listing = Listing::factory()->withAuthorizedLogo()->create();
        foreach (['logo_approved' => false, 'logo_alt' => null, 'logo_reviewed_at' => null] as $field => $value) {
            $valid = $listing->{$field};
            $listing->forceFill([$field => $value])->save();
            try {
                app(TransitionListing::class)->handle($actor, $listing, 'published');
                $this->fail('Publicó sin evidencia del logo.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('logo_approved', $exception->errors());
            }
            $listing->forceFill(['status' => 'published', 'published_at' => now()])->save();
            $this->get('/@'.$listing->slug)->assertNotFound();
            $listing->forceFill(['status' => 'draft', $field => $valid])->save();
        }
        $logo = $listing->getFirstMedia('logo');
        Storage::disk('listing_media')->delete($logo->getPathRelativeToRoot());
        $listing->forceFill(['status' => 'published', 'published_at' => now()])->save();
        $this->get('/@'.$listing->slug)->assertNotFound();
        $this->get($logo->getUrl())->assertNotFound();
    }

    public function test_published_logo_cannot_be_removed_replaced_or_moved_until_unpublished(): void
    {
        $listing = Listing::factory()->published()->create();
        $logo = $listing->getFirstMedia('logo');
        try {
            $logo->delete();
            $this->fail('Retiró logo publicado.');
        } catch (ValidationException $exception) {
            $this->assertSame('Despublica la ficha antes de quitar o sustituir su logo.', $exception->errors()['logo'][0]);
        }
        try {
            $listing->addMedia(UploadedFile::fake()->image('nuevo.png'))->toMediaCollection('logo');
            $this->fail('Reemplazó logo publicado.');
        } catch (ValidationException) {
            $this->assertSame($logo->id, $listing->fresh()->getFirstMedia('logo')->id);
        }
        try {
            $logo->collection_name = 'cover';
            $logo->save();
            $this->fail('Movió logo publicado.');
        } catch (ValidationException) {
            $this->assertSame('logo', $logo->fresh()->collection_name);
        }
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $listing = app(TransitionListing::class)->handle($actor, $listing, 'paused');
        $this->get($logo->getUrl())->assertNotFound();

        $logo->refresh()->delete();

        $this->assertFalse($listing->fresh()->hasMedia('logo'));
        $this->assertFalse($listing->fresh()->logo_approved);
        Storage::disk('listing_media')->assertMissing($logo->getPathRelativeToRoot());
    }

    public function test_published_edit_rejects_revoking_logo_approval(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $this->actingAs($actor);
        $listing = Listing::factory()->published()->create();

        Livewire::test(EditListing::class, ['record' => $listing->id])->fillForm(['logo_approved' => false])->call('save')->assertHasFormErrors(['logo_approved']);

        $this->assertTrue($listing->fresh()->logo_approved);
        $this->get('/@'.$listing->slug)->assertOk();
    }

    public function test_cover_has_og_priority_and_other_media_remain_unavailable_without_own_approval(): void
    {
        $listing = Listing::factory()->published()->create(['media_approved' => true, 'media_reviewed_at' => today('America/Cancun')]);
        $cover = $listing->addMedia(UploadedFile::fake()->image('cover.jpg'))->toMediaCollection('cover');
        $this->get('/@'.$listing->slug)->assertOk()->assertSee('<meta property="og:image" content="'.$cover->getUrl().'"', false);
        $listing->forceFill(['media_approved' => false])->save();

        $this->get($cover->getUrl())->assertNotFound();
        $this->get('/@'.$listing->slug)->assertOk()->assertSee('<meta property="og:image" content="'.$listing->getFirstMedia('logo')->getUrl().'"', false)->assertDontSee($cover->getUrl(), false);
    }

    public function test_taxonomies_preserve_compound_emojis_and_sanitize_rich_html_and_hex_values(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $city = City::factory()->create();
        $records = [$city, Category::factory()->create(), Area::factory()->for($city)->create()];
        foreach ($records as $record) {
            $data = $record->getAttributes();
            $data = array_replace($data, ['name' => 'DEMO 👩🏽‍🎨 👨‍👩‍👧‍👦 🏳️‍🌈', 'primary_color' => '#74DDCF', 'tagline' => 'Identidad de prueba', 'description' => '<h2>Título</h2><p style="position:fixed" onclick="bad()">Texto <strong>útil</strong> <em>local</em></p><ul><li>Idea</li></ul><blockquote>Cita</blockquote><a href="https://example.org">Enlace</a><script>alert(1)</script><img src="https://example.org/tracker"><a href="javascript:bad()">Malo</a>']);

            $saved = app(SaveTaxonomy::class)->handle($actor, $record, $data)->fresh();

            $this->assertSame('DEMO 👩🏽‍🎨 👨‍👩‍👧‍👦 🏳️‍🌈', $saved->name);
            $this->assertSame('#74DDCF', $saved->primary_color);
            $this->assertSame('Identidad de prueba', $saved->tagline);
            $html = $saved->sanitizedDescription();
            $this->assertStringContainsString('<h2>Título</h2>', $html);
            $this->assertStringContainsString('<strong>útil</strong>', $html);
            $this->assertStringContainsString('href="https://example.org"', $html);
            foreach (['<script', 'javascript:', 'onclick', 'style=', '<img'] as $unsafe) {
                $this->assertStringNotContainsString($unsafe, $html);
            }
            foreach (['red', '#FFF', '#12345678', 'url(evil)'] as $invalid) {
                try {
                    app(SaveTaxonomy::class)->handle($actor, $saved, array_replace($data, ['primary_color' => $invalid]));
                    $this->fail('Aceptó color inválido.');
                } catch (ValidationException $exception) {
                    $this->assertArrayHasKey('primary_color', $exception->errors());
                }
            }
            try {
                app(SaveTaxonomy::class)->handle($actor, $saved, array_replace($data, ['slug' => 'emoji-🌴']));
                $this->fail('Aceptó emoji en slug.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('slug', $exception->errors());
            }
        }
        $this->assertSame('#000000', IdentityColor::foreground('#74DDCF'));
        $this->assertSame('#FFFFFF', IdentityColor::foreground('#2C254E'));
        $this->assertSame('#2C254E', IdentityColor::background(null));
    }

    public function test_taxonomy_plugin_keeps_icon_and_og_separate_private_and_authorized_by_role(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        $this->actingAs($admin);
        $area = Area::factory()->create();
        $icon = UploadedFile::fake()->image('icon.png');
        $og = UploadedFile::fake()->image('og.webp');
        Livewire::test(ManageAreas::class)->callAction(TestAction::make('edit')->table($area), data: [
            'name' => 'Zona 👩🏽‍🎨', 'slug' => $area->slug, 'city_id' => $area->city_id, 'is_active' => true,
            'primary_color' => '#F4C95D', 'tagline' => 'Texto breve', 'description' => '<p>Descripción <strong>enriquecida</strong></p>', 'icon' => [$icon], 'og_image' => [$og],
        ])->assertHasNoActionErrors();
        $area->refresh();
        $this->assertSame('Zona 👩🏽‍🎨', $area->name);
        $this->assertSame('<p>Descripción <strong>enriquecida</strong></p>', $area->description);
        $this->assertNotNull($area->getFirstMedia('icon'));
        $this->assertNotNull($area->getFirstMedia('og_image'));
        $this->assertNotSame($area->getFirstMedia('icon')->id, $area->getFirstMedia('og_image')->id);
        $url = $area->getFirstMedia('icon')->getUrl();
        $this->get($url)->assertOk();
        $this->actingAs(User::factory()->create())->get($url)->assertForbidden();
        $this->actingAs(User::factory()->superAdmin()->create())->get($url)->assertRedirect(route('filament.admin.auth.multi-factor-authentication.set-up-required'));
        $this->app['auth']->forgetGuards();
        $this->get($url)->assertRedirect('/dashboard/login');
        $this->assertFalse($area->hasMedia('cover'));
    }

    public function test_admin_uploads_logo_with_plugin_then_approves_and_publishes_without_optional_media(): void
    {
        $this->actingAs(User::factory()->superAdmin()->withAppAuthentication()->create());
        $listing = Listing::factory()->create();
        Livewire::test(EditListing::class, ['record' => $listing->id])->fillForm([
            'logo' => [UploadedFile::fake()->image('logo.png')],
            'logo_alt' => 'Logo DEMO',
            'logo_reviewed_at' => today('America/Cancun')->toDateString(),
        ])->call('save')->assertHasNoFormErrors();
        $this->assertNotNull($listing->fresh()->getFirstMedia('logo'));
        $this->assertFalse($listing->fresh()->logo_approved);

        Livewire::test(EditListing::class, ['record' => $listing->id])->fillForm(['logo_approved' => true])->call('save')->assertHasNoFormErrors()->callAction('published');

        $this->assertSame('published', $listing->fresh()->status);
        $this->assertFalse($listing->fresh()->media_approved);
        $this->get('/@'.$listing->slug)->assertOk()->assertSee('alt="Logo DEMO"', false);
    }

    public function test_replacing_draft_logo_clears_its_previous_approval(): void
    {
        $listing = Listing::factory()->withAuthorizedLogo()->create();

        $listing->addMedia(UploadedFile::fake()->image('replacement.png'))->toMediaCollection('logo');

        $this->assertFalse($listing->fresh()->logo_approved);
        $this->assertSame(1, $listing->media()->where('collection_name', 'logo')->count());
    }
}
