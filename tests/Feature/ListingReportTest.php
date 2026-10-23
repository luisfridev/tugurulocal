<?php

namespace Tests\Feature;

use App\Actions\Listings\ResolveListingReport;
use App\Models\Listing;
use App\Models\ListingReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ListingReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('listing_media');
    }

    private function payload(Listing $listing): array
    {
        $this->get(route('listings.report', ['listing' => $listing->slug]))->assertOk();

        $this->withCookie(config('session.cookie'), session()->getId());

        return ['field' => 'opening_hours', 'explanation' => 'El horario del sábado debe revisarse.', 'form_token' => Crypt::encryptString($listing->id.'|'.now()->subSeconds(5)->timestamp.'|'.session()->getId())];
    }

    public function test_report_stays_pending_and_admin_resolution_never_changes_listing_content(): void
    {
        $listing = Listing::factory()->published()->create();
        $name = $listing->name;
        $this->post(route('listings.report.store', ['listing' => $listing->slug]), $this->payload($listing) + ['status' => 'resolved', 'resolved_by' => 99])->assertRedirect();
        $report = ListingReport::query()->sole();
        $this->assertSame('pending', $report->status);
        $this->assertNull($report->resolved_by);
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        app(ResolveListingReport::class)->handle($actor, $report, 'resolved');
        $this->assertSame('resolved', $report->fresh()->status);
        $this->assertSame($name, $listing->fresh()->name);
        $this->assertDatabaseHas('audit_events', ['action' => 'report.resolved']);
        $this->get('/@'.$listing->slug)->assertDontSee($report->explanation);
    }

    public function test_honeypot_timing_invalid_tokens_and_private_listings_reject_reports(): void
    {
        $listing = Listing::factory()->published()->create();
        $payload = $this->payload($listing);
        $url = route('listings.report.store', ['listing' => $listing->slug]);
        $this->post($url, $payload + ['website' => 'spam'])->assertSessionHasErrors('website');
        $this->post($url, array_replace($payload, ['form_token' => Crypt::encryptString($listing->id.'|'.now()->timestamp.'|'.session()->getId())]))->assertSessionHasErrors('form_token');
        $this->post($url, array_replace($payload, ['form_token' => 'invalid']))->assertSessionHasErrors('form_token');
        $listing->forceFill(['status' => 'draft'])->save();
        $this->get(route('listings.report', ['listing' => $listing->slug]))->assertNotFound();
        $this->assertDatabaseCount('listing_reports', 0);
    }

    public function test_fourth_report_per_hour_is_rate_limited(): void
    {
        $listing = Listing::factory()->published()->create();
        $url = route('listings.report.store', ['listing' => $listing->slug]);
        for ($i = 0; $i < 3; $i++) {
            $this->post($url, $this->payload($listing))->assertRedirect();
        }
        $this->post($url, $this->payload($listing))->assertStatus(429);
        $this->assertDatabaseCount('listing_reports', 3);
    }

    public function test_daily_limit_remains_after_hourly_windows_expire(): void
    {
        $this->freezeTime();
        $listing = Listing::factory()->published()->create();
        $url = route('listings.report.store', ['listing' => $listing->slug]);
        for ($i = 0; $i < 10; $i++) {
            if ($i > 0 && $i % 3 === 0) {
                $this->travel(61)->minutes();
            } $this->post($url, $this->payload($listing))->assertRedirect();
        }
        $this->post($url, $this->payload($listing))->assertStatus(429);
        $this->assertDatabaseCount('listing_reports', 10);
    }

    public function test_production_collection_accepts_reports_without_privacy_approval_or_route_and_keeps_csrf(): void
    {
        $listing = Listing::factory()->published()->create();
        $payload = $this->payload($listing);
        app()->instance('env', 'production');
        $this->assertFalse(Route::has('privacy'));
        $this->get('/aviso-de-privacidad')->assertNotFound();
        $this->get('/@'.$listing->slug)->assertOk()->assertSee('Reportar datos incorrectos');
        $this->get(route('listings.report', ['listing' => $listing->slug]))->assertOk()->assertDontSee('/aviso-de-privacidad');
        $this->post(route('listings.report.store', ['listing' => $listing->slug]), $payload)->assertStatus(419);
        $this->post(route('listings.report.store', ['listing' => $listing->slug]), $payload + ['_token' => session()->token()])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseCount('listing_reports', 1);
        $this->get(route('listings.report', ['listing' => $listing->slug]))->assertOk()->assertSee('Recibimos tu reporte.');
    }
}
