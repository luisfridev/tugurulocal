<?php

namespace Tests\Feature;

use App\Models\InteractionEvent;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ListingPublicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('listing_media');
    }

    public function test_public_listing_renders_real_data_seo_faqs_and_no_private_or_invented_values(): void
    {
        $listing = Listing::factory()->published()->create(['name' => 'Negocio <script>alert(1)</script>', 'private_notes' => 'NOTA-SECRETA', 'faqs' => [['question' => '¿Aceptan visitas?', 'answer' => 'Consulta el horario.']]]);
        $this->get('/@'.$listing->slug)->assertOk()->assertSee('Negocio &lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false)->assertSee('application/ld+json', false)->assertSee('rel="canonical"', false)->assertSee('Consulta el horario.')->assertDontSee('NOTA-SECRETA')->assertDontSee('0 reseñas')->assertDontSee('Cómo llegar');
        $this->assertDatabaseHas('interaction_events', ['listing_id' => $listing->id, 'type' => 'listing.view']);
        $this->get('/@'.$listing->slug)->assertOk();
        $this->assertSame(1, InteractionEvent::query()->where('type', 'listing.view')->count());
    }

    public function test_all_private_states_future_dates_unapproved_content_and_places_return_404(): void
    {
        $listing = Listing::factory()->published()->create();
        foreach ([['status' => 'draft'], ['status' => 'paused'], ['status' => 'archived'], ['status' => 'published', 'published_at' => now()->addHour()], ['published_at' => now(), 'content_approved' => false], ['content_approved' => true, 'type' => 'place']] as $attributes) {
            $listing->forceFill($attributes)->save();
            $this->get('/@'.$listing->slug)->assertNotFound();
        }
    }

    public function test_permanent_closure_keeps_informative_detail(): void
    {
        $listing = Listing::factory()->published()->create(['operating_status' => 'permanently_closed']);
        $this->get('/@'.$listing->slug)->assertOk()->assertSee('Cerrado permanentemente');
    }

    public function test_preview_requires_admin_with_mfa_and_never_records_public_interactions(): void
    {
        $listing = Listing::factory()->create();
        $url = route('listings.preview', $listing);
        $this->get($url)->assertRedirect('/dashboard/login');
        $this->actingAs(User::factory()->create())->get($url)->assertForbidden();
        $this->actingAs(User::factory()->superAdmin()->create())->get($url)->assertRedirect(route('filament.admin.auth.multi-factor-authentication.set-up-required'));
        $this->actingAs(User::factory()->superAdmin()->withAppAuthentication()->create())->get($url)->assertOk()->assertSee('Previsualización privada')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->assertDatabaseCount('interaction_events', 0);
    }

    public function test_pdf_button_label_download_authorization_and_unpublish_protect_the_file(): void
    {
        Storage::fake('listing_media');
        $listing = Listing::factory()->published()->create(['document_button_label' => 'Consultar nuestra carta', 'media_approved' => true, 'media_reviewed_at' => today('America/Cancun')]);
        $file = $listing->addMedia(UploadedFile::fake()->createWithContent('carta.pdf', "%PDF-1.4\n1 0 obj <</Type/Catalog>> endobj\n%%EOF"))->toMediaCollection('document');
        $this->get('/@'.$listing->slug)->assertOk()->assertSee('Consultar nuestra carta');
        $this->get(route('listings.media', ['listing' => $listing->slug, 'media' => $file->id]))->assertOk()->assertDownload('menu-o-folleto.pdf');
        $listing->forceFill(['status' => 'paused'])->save();
        $this->get($file->getUrl())->assertNotFound();
        $this->actingAs(User::factory()->superAdmin()->withAppAuthentication()->create())->get($file->getUrl())->assertOk();
        $other = Listing::factory()->recycle($listing->city)->published()->create(['media_approved' => true, 'media_reviewed_at' => today('America/Cancun')]);
        $this->get(route('listings.media', ['listing' => $other->slug, 'media' => $file->id]))->assertNotFound();
    }

    public function test_unauthorized_media_and_missing_pdf_never_create_a_button_or_download(): void
    {
        Storage::fake('listing_media');
        $listing = Listing::factory()->published()->create(['media_approved' => false]);
        $file = $listing->addMedia(UploadedFile::fake()->createWithContent('menu.pdf', '%PDF-1.4'))->toMediaCollection('document');
        $this->get('/@'.$listing->slug)->assertOk()->assertDontSee('Descargar menú o folleto');
        $this->get($file->getUrl())->assertNotFound();
        $this->get(route('listings.contact', ['listing' => $listing->slug, 'kind' => 'document']))->assertNotFound();
    }

    public function test_contacts_use_saved_destinations_and_events_store_no_contact_data(): void
    {
        $listing = Listing::factory()->published()->create(['maps_url' => 'https://maps.google.com/?q=ubicacion', 'phone' => '+52 984 000 0000']);
        $this->get(route('listings.contact', ['listing' => $listing->slug, 'kind' => 'maps']))->assertRedirect('https://maps.google.com/?q=ubicacion');
        $this->get(route('listings.contact', ['listing' => $listing->slug, 'kind' => 'unknown']))->assertNotFound();
        $event = InteractionEvent::query()->sole();
        $this->assertSame('contact.maps', $event->type);
        $this->assertStringNotContainsString('ubicacion', $event->toJson());
    }

    public function test_authorized_image_upload_generates_an_optimized_conversion_and_public_alt_text(): void
    {
        Storage::fake('listing_media');
        $listing = Listing::factory()->published()->create(['media_approved' => true, 'media_reviewed_at' => today('America/Cancun'), 'image_alt' => 'Imagen sintética de comprobación']);
        $media = $listing->addMedia(UploadedFile::fake()->image('portada.jpg', 100, 80))->toMediaCollection('cover');
        $this->assertTrue($media->hasGeneratedConversion('card'));
        Storage::disk('listing_media')->assertExists($media->getPathRelativeToRoot('card'));
        $this->get('/@'.$listing->slug)->assertOk()->assertSee('alt="Imagen sintética de comprobación"', false);
        $this->get($media->getUrl('card'))->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
    }
}
