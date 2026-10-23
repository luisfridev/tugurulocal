<?php

namespace Tests\Feature;

use App\Models\AuditEvent;
use App\Models\Category;
use App\Models\City;
use App\Models\InteractionEvent;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SubmissionPublicTest extends TestCase
{
    use RefreshDatabase;

    private function payload(): array
    {
        City::query()->first() ?? City::factory()->create();
        $category = Category::query()->where('listing_type', 'business')->first() ?? Category::factory()->create();
        $this->get('/recomendar')->assertOk();
        $this->withCookie(config('session.cookie'), session()->getId());

        return ['name' => 'Propuesta de prueba', 'type' => 'business', 'category_id' => $category->id, 'address' => 'Referencia de ubicación de prueba', 'reason' => 'Motivo privado para revisión del equipo.', 'form_token' => Crypt::encryptString('submission|'.now()->subSeconds(5)->timestamp.'|'.session()->getId())];
    }

    public function test_public_submission_is_pending_and_ignores_administrative_fields_without_sending_messages_or_fetching_urls(): void
    {
        Mail::fake();
        Notification::fake();
        Http::preventStrayRequests();
        Http::fake();
        $data = $this->payload() + ['email' => 'privado@example.org', 'whatsapp' => '+529841234567', 'evidence_url' => 'https://example.org/evidencia-privada', 'instagram' => '@Persona_Prueba', 'credit_consent' => '1', 'status' => 'approved', 'moderated_by' => 1, 'listing_id' => 55, 'city_id' => 999];

        $this->post('/recomendar', $data)->assertRedirect('/recomendar')->assertSessionHasNoErrors()->assertSessionHas('submission_sent', true)->assertHeader('X-Robots-Tag', 'noindex');

        $submission = Submission::query()->sole();
        $this->assertSame('pending', $submission->status);
        $this->assertNull($submission->moderated_by);
        $this->assertNull($submission->listing_id);
        $this->assertSame('persona_prueba', $submission->instagram);
        $this->assertSame('+529841234567', $submission->whatsapp);
        $this->assertTrue($submission->credit_consent);
        $this->assertSame('privado@example.org', $submission->email);
        $this->assertNotSame('privado@example.org', DB::table('submissions')->value('email'));
        $this->assertDatabaseCount('listings', 0);
        $response = $this->get('/recomendar')->assertOk()->assertSee('Recibimos tu recomendación')->assertDontSee($data['email'])->assertDontSee($data['evidence_url'])->assertDontSee($data['reason'])->assertDontSee('name="email"', false);
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        foreach (['email', 'whatsapp', 'evidence_url', 'reason', 'moderation_reason', 'instagram'] as $field) {
            $this->assertArrayNotHasKey($field, $submission->toArray());
        }
        $events = InteractionEvent::all()->toJson().AuditEvent::all()->toJson();
        foreach ([$data['email'], $data['whatsapp'], $data['evidence_url'], $data['reason'], 'persona_prueba', '127.0.0.1'] as $private) {
            $this->assertStringNotContainsString($private, $events);
        }
        $this->assertDatabaseHas('interaction_events', ['type' => 'submission.sent', 'listing_id' => null]);
        $this->assertDatabaseHas('audit_events', ['action' => 'submission.sent', 'subject_id' => $submission->id]);
        Mail::assertNothingSent();
        Notification::assertNothingSent();
        Http::assertNothingSent();
    }

    public function test_required_fields_are_explained_and_valid_values_are_preserved_escaped(): void
    {
        $data = $this->payload();
        $data['name'] = '<script>alert("nombre")</script>';
        unset($data['reason'], $data['address'], $data['category_id'], $data['type']);
        $this->from('/recomendar')->post('/recomendar', $data)->assertSessionHasErrors([
            'reason' => 'Cuéntanos por qué lo recomiendas.', 'address' => 'Escribe una dirección o referencia de ubicación.', 'category_id' => 'Elige una categoría.', 'type' => 'Elige si recomiendas un negocio o un Lugar.',
        ]);
        $this->get('/recomendar')->assertOk()->assertSee('role="alert"', false)->assertSee('aria-invalid="true"', false)->assertSee($data['name'])->assertDontSee($data['name'], false);
        $this->assertDatabaseCount('submissions', 0);
    }

    public static function invalidValues(): array
    {
        return [
            'email' => ['email', 'invalido', 'Revisa el formato del correo.'],
            'whatsapp' => ['whatsapp', 'abc123', 'Usa de 8 a 15 dígitos con código de país, con + opcional.'],
            'maps javascript' => ['maps_url', 'javascript:alert(1)', 'Usa un enlace completo de Maps con http o https.'],
            'evidence file' => ['evidence_url', 'file:///etc/passwd', 'Usa un enlace de evidencia con http o https.'],
            'evidence data' => ['evidence_url', 'data:text/html,hello', 'Usa un enlace de evidencia con http o https.'],
            'reason short' => ['reason', 'corto', 'Escribe al menos 10 caracteres en el motivo.'],
            'instagram url' => ['instagram', 'https://instagram.com/persona', 'Usa hasta 30 letras, números, puntos o guiones bajos, sin puntos seguidos ni en los extremos.'],
            'instagram consecutive dots' => ['instagram', 'persona..prueba', 'Usa hasta 30 letras, números, puntos o guiones bajos, sin puntos seguidos ni en los extremos.'],
            'instagram trailing dot' => ['instagram', 'persona.', 'Usa hasta 30 letras, números, puntos o guiones bajos, sin puntos seguidos ni en los extremos.'],
            'instagram repeated at' => ['instagram', '@@persona', 'Usa hasta 30 letras, números, puntos o guiones bajos, sin puntos seguidos ni en los extremos.'],
            'honeypot' => ['website', 'bot', 'No fue posible validar el envío.'],
        ];
    }

    #[DataProvider('invalidValues')]
    public function test_invalid_fields_are_rejected(string $field, mixed $value, string $message): void
    {
        $this->post('/recomendar', array_replace($this->payload(), [$field => $value]))->assertSessionHasErrors([$field => $message]);
        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_files_are_rejected_even_when_hidden_under_unexpected_nested_keys(): void
    {
        $this->post('/recomendar', $this->payload() + ['unexpected' => ['upload' => UploadedFile::fake()->create('evidencia.pdf', 1)]])->assertSessionHasErrors(['attachments' => 'Envía la evidencia mediante un enlace. No se admiten archivos.']);
        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_array_inputs_are_validation_errors_and_cannot_crash_or_mutate_the_submission(): void
    {
        $data = array_replace($this->payload(), ['type' => ['business'], 'name' => ['nombre'], 'instagram' => ['persona'], 'reason' => ['motivo']]);
        $this->post('/recomendar', $data)->assertSessionHasErrors(['type', 'name', 'instagram', 'reason']);
        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_initial_form_and_discovery_links_have_no_privacy_dependency_or_preselected_consent(): void
    {
        City::factory()->create();
        Category::factory()->create();
        $response = $this->get('/recomendar')->assertOk()->assertHeader('X-Robots-Tag', 'noindex')->assertSee('noindex, nofollow')->assertDontSee('/aviso-de-privacidad')->assertDontSee('checked', false);
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->get('/playa-del-carmen')->assertOk()->assertSee('Recomendar un negocio o Lugar')->assertSee('href="'.route('submissions.create').'"', false);
        $this->get('/sitemap.xml')->assertOk()->assertDontSee('/recomendar')->assertDontSee('/aviso-de-privacidad');
    }

    public function test_category_must_be_active_and_compatible_and_consent_needs_instagram(): void
    {
        $data = $this->payload();
        $place = Category::factory()->create(['listing_type' => 'place', 'slug' => 'lugares-publicos']);
        $this->post('/recomendar', array_replace($data, ['category_id' => $place->id]))->assertSessionHasErrors(['category_id' => 'Elige una categoría activa compatible con el tipo.']);
        $place->update(['is_active' => false]);
        $this->post('/recomendar', array_replace($data, ['type' => 'place', 'category_id' => $place->id]))->assertSessionHasErrors('category_id');
        $this->post('/recomendar', $data + ['credit_consent' => '1'])->assertSessionHasErrors(['instagram' => 'Indica tu usuario de Instagram para autorizar el crédito.']);
        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_place_submission_and_optional_empty_contacts_are_accepted_without_credit(): void
    {
        $data = $this->payload();
        $place = Category::factory()->create(['listing_type' => 'place', 'slug' => 'lugares-publicos']);
        $this->post('/recomendar', array_replace($data, ['type' => 'place', 'category_id' => $place->id, 'instagram' => 'persona.prueba']))->assertSessionHasNoErrors();
        $submission = Submission::query()->sole();
        $this->assertSame('place', $submission->type);
        $this->assertFalse($submission->credit_consent);
        $this->assertNull($submission->email);
        $this->assertNull($submission->maps_url);
        $this->assertDatabaseCount('listings', 0);
    }

    public static function invalidTokens(): array
    {
        return ['too fast' => [2, 'submission', null], 'expired' => [7201, 'submission', null], 'future' => [-30, 'submission', null], 'wrong form' => [5, 'another', null], 'wrong session' => [5, 'submission', 'different-session']];
    }

    #[DataProvider('invalidTokens')]
    public function test_timing_and_session_boundaries_reject_tokens(int $age, string $scope, ?string $session): void
    {
        $this->freezeTime();
        $data = $this->payload();
        $data['form_token'] = Crypt::encryptString($scope.'|'.now()->subSeconds($age)->timestamp.'|'.($session ?? session()->getId()));
        $this->post('/recomendar', $data)->assertSessionHasErrors('form_token');
        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_tampered_token_is_rejected_and_exact_time_boundaries_are_accepted(): void
    {
        $this->freezeTime();
        $data = $this->payload();
        $this->post('/recomendar', array_replace($data, ['form_token' => 'invalid']))->assertSessionHasErrors('form_token');
        foreach ([3, 7200] as $age) {
            $data['form_token'] = Crypt::encryptString('submission|'.now()->subSeconds($age)->timestamp.'|'.session()->getId());
            $this->post('/recomendar', $data)->assertSessionHasNoErrors();
        }
        $this->assertDatabaseCount('submissions', 2);
    }

    public function test_fourth_submission_in_an_hour_is_limited(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->post('/recomendar', $this->payload())->assertSessionHasNoErrors();
        }
        $response = $this->post('/recomendar', $this->payload())->assertStatus(429)->assertHeader('X-Robots-Tag', 'noindex')->assertSee('Se alcanzó el límite de intentos');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertDatabaseCount('submissions', 3);
    }

    public function test_eleventh_submission_is_limited_within_24_hours_even_across_midnight_and_new_hourly_windows(): void
    {
        $this->travelTo(now()->setTime(23, 30));
        for ($i = 0; $i < 10; $i++) {
            if ($i > 0 && $i % 3 === 0) {
                $this->travel(61)->minutes();
            }
            $this->post('/recomendar', $this->payload())->assertSessionHasNoErrors();
        }
        $this->post('/recomendar', $this->payload())->assertStatus(429);
        $this->assertDatabaseCount('submissions', 10);
        $this->travel(25)->hours();
        $this->post('/recomendar', $this->payload())->assertSessionHasNoErrors();
        $this->assertDatabaseCount('submissions', 11);
    }

    public function test_production_receives_recommendations_with_csrf_without_privacy_route_or_approval(): void
    {
        $data = $this->payload();
        app()->instance('env', 'production');
        $this->assertFalse(Route::has('privacy'));
        $this->get('/aviso-de-privacidad')->assertNotFound();
        $this->get('/recomendar')->assertOk()->assertDontSee('/aviso-de-privacidad')->assertSee('name="_token"', false);
        $this->post('/recomendar', $data)->assertStatus(419)->assertHeader('X-Robots-Tag', 'noindex')->assertSee('Tu sesión venció');
        $this->post('/recomendar', $data + ['_token' => session()->token()])->assertRedirect('/recomendar')->assertSessionHasNoErrors();
        $this->get('/recomendar')->assertOk()->assertSee('Recibimos tu recomendación')->assertDontSee('/aviso-de-privacidad');
        $this->assertDatabaseCount('submissions', 1);
    }
}
