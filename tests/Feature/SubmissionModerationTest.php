<?php

namespace Tests\Feature;

use App\Actions\Submissions\ModerateSubmission;
use App\Actions\Submissions\SelectSubmissionCredit;
use App\Actions\Submissions\SubmitSubmission;
use App\Filament\Resources\Submissions\Pages\ManageSubmissions;
use App\Models\AuditEvent;
use App\Models\Category;
use App\Models\City;
use App\Models\InteractionEvent;
use App\Models\Listing;
use App\Models\Submission;
use App\Models\User;
use App\Queries\SubmissionMatches;
use App\Support\ListingPageData;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SubmissionModerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
    }

    private function creation(Submission $submission): array
    {
        return ['status' => 'approved', 'mode' => 'create', 'name' => 'Nombre elegido', 'slug' => 'borrador-elegido', 'primary_category_id' => $submission->category_id, 'address' => 'Ubicación elegida', 'moderation_reason' => 'Revisión privada de prueba'];
    }

    public function test_approval_creates_only_a_draft_from_selected_fields_and_preserves_the_original_private_proposal(): void
    {
        $this->freezeTime();
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $submission = Submission::factory()->withCreditConsent()->create(['email' => 'contacto@example.org', 'whatsapp' => '+529841234567', 'evidence_url' => 'https://example.org/evidencia-privada']);
        $original = $submission->fresh()->getAttributes();

        $result = app(ModerateSubmission::class)->handle($actor, $submission, $this->creation($submission) + ['select_credit' => true, 'status_injected' => 'published', 'source_url' => $submission->evidence_url, 'phone' => '9999999999']);

        $listing = Listing::query()->sole();
        $this->assertSame('approved', $result->status);
        $this->assertSame($actor->id, $result->moderated_by);
        $this->assertSame(now()->timestamp, $result->moderated_at->timestamp);
        $this->assertSame($listing->id, $result->listing_id);
        $this->assertSame('draft', $listing->status);
        $this->assertFalse($listing->content_approved);
        $this->assertNull($listing->published_at);
        $this->assertSame('Nombre elegido', $listing->name);
        $this->assertSame('Ubicación elegida', $listing->address);
        foreach (['description', 'reasons', 'phone', 'whatsapp', 'source_url', 'private_notes', 'summary'] as $field) {
            $this->assertNull($listing->{$field});
        }
        $this->assertSame($submission->id, $listing->credit_submission_id);
        foreach (['name', 'reason', 'address', 'email', 'whatsapp', 'evidence_url', 'instagram', 'credit_consent'] as $field) {
            $this->assertSame($original[$field], $submission->fresh()->getRawOriginal($field));
        }
        $this->get('/@'.$listing->slug)->assertNotFound();
        $events = AuditEvent::all()->toJson().InteractionEvent::all()->toJson();
        foreach ([$submission->email, $submission->whatsapp, $submission->evidence_url, $submission->reason, $result->moderation_reason, $submission->instagram] as $private) {
            $this->assertStringNotContainsString($private, $events);
        }
        $this->assertDatabaseHas('interaction_events', ['type' => 'submission.approved', 'listing_id' => $listing->id]);
    }

    public function test_linking_a_compatible_draft_preserves_every_field_and_category(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $listing = Listing::factory()->create();
        $before = $listing->fresh()->getAttributes();
        $submission = Submission::factory()->recycle($listing->city)->create();

        app(ModerateSubmission::class)->handle($actor, $submission, ['status' => 'approved', 'mode' => 'link', 'listing_id' => $listing->id, 'name' => 'Sobrescribir', 'moderation_reason' => 'Compatible']);

        $this->assertSame($before, $listing->fresh()->getAttributes());
        $this->assertSame('approved', $submission->fresh()->status);
        $this->assertSame($listing->id, $submission->fresh()->listing_id);
        $this->assertDatabaseCount('listings', 1);
    }

    public static function decisions(): array
    {
        return ['reject' => ['rejected'], 'duplicate' => ['duplicate'], 'spam' => ['spam']];
    }

    #[DataProvider('decisions')]
    public function test_terminal_decisions_require_a_reason_and_prevent_repeated_decisions(string $status): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $submission = Submission::factory()->create();
        try {
            app(ModerateSubmission::class)->handle($actor, $submission, ['status' => $status]);
            $this->fail('Falta motivo');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('moderation_reason', $exception->errors());
        }
        app(ModerateSubmission::class)->handle($actor, $submission, ['status' => $status, 'moderation_reason' => 'Decisión comprobada']);
        $this->assertSame($status, $submission->fresh()->status);
        $this->assertSame($actor->id, $submission->fresh()->moderated_by);
        $this->assertDatabaseCount('listings', 0);
        try {
            app(ModerateSubmission::class)->handle($actor, $submission, $this->creation($submission));
            $this->fail('Una decisión repetida debe rechazarse');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('status', $exception->errors());
        }
        $this->assertSame($status, $submission->fresh()->status);
        $this->assertDatabaseCount('listings', 0);
    }

    public function test_published_duplicate_reference_does_not_mutate_the_listing_and_cannot_be_approved_as_a_draft(): void
    {
        Storage::fake('listing_media');
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $listing = Listing::factory()->published()->create();
        $before = $listing->fresh()->getAttributes();
        $submission = Submission::factory()->recycle($listing->city)->withCreditConsent()->create();
        try {
            app(ModerateSubmission::class)->handle($actor, $submission, ['status' => 'approved', 'mode' => 'link', 'listing_id' => $listing->id, 'moderation_reason' => 'Intento']);
            $this->fail('Publicada no es borrador');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('listing_id', $exception->errors());
        }
        app(ModerateSubmission::class)->handle($actor, $submission, ['status' => 'duplicate', 'listing_id' => $listing->id, 'moderation_reason' => 'Ya publicada']);
        $this->assertSame($before, $listing->fresh()->getAttributes());
        $this->assertSame('duplicate', $submission->fresh()->status);
        $this->assertSame($listing->id, $submission->fresh()->listing_id);
        $this->assertDatabaseCount('content_revisions', 0);
    }

    public function test_incompatible_city_type_or_non_draft_targets_are_rejected(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $submission = Submission::factory()->create();
        $otherCity = City::factory()->create(['slug' => 'otra-ciudad']);
        $targets = [
            Listing::factory()->recycle($otherCity)->create(),
            Listing::factory()->recycle($submission->city)->create(['type' => 'place']),
            Listing::factory()->recycle($submission->city)->archived()->create(),
        ];
        foreach ($targets as $listing) {
            try {
                app(ModerateSubmission::class)->handle($actor, $submission, ['status' => 'approved', 'mode' => 'link', 'listing_id' => $listing->id, 'moderation_reason' => 'Intento']);
                $this->fail('Destino incompatible');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('listing_id', $exception->errors());
            }
        }
        $this->assertSame('pending', $submission->fresh()->status);
    }

    public function test_credit_failure_rolls_back_draft_and_moderation_atomically(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $submission = Submission::factory()->create(['instagram' => 'sin_consentimiento']);
        try {
            app(ModerateSubmission::class)->handle($actor, $submission, $this->creation($submission) + ['select_credit' => true]);
            $this->fail('Falta consentimiento');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('credit', $exception->errors());
        }
        $this->assertSame('pending', $submission->fresh()->status);
        $this->assertDatabaseCount('listings', 0);
        $this->assertDatabaseCount('content_revisions', 0);
        $this->assertDatabaseCount('audit_events', 0);
    }

    public function test_only_one_selected_consented_credit_is_public_and_it_disappears_when_invalid_or_unpublished(): void
    {
        Storage::fake('listing_media');
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $listing = Listing::factory()->published()->create();
        $first = Submission::factory()->recycle($listing->city)->withCreditConsent()->create(['instagram' => 'primera_persona', 'status' => 'approved', 'listing_id' => $listing->id]);
        $second = Submission::factory()->recycle($listing->city)->withCreditConsent()->create(['instagram' => 'segunda_persona', 'status' => 'approved', 'listing_id' => $listing->id]);
        app(SelectSubmissionCredit::class)->handle($actor, $first, true);
        app(SelectSubmissionCredit::class)->handle($actor, $second, true);
        $this->get('/@'.$listing->slug)->assertOk()->assertSee('@segunda_persona')->assertDontSee('@primera_persona')->assertDontSee($second->reason);
        $this->assertNull(app(ListingPageData::class)->make($listing->fresh(), true)['credit']);
        foreach ([['credit_consent' => false], ['instagram' => '<script>'], ['status' => 'duplicate']] as $invalid) {
            $second->forceFill($invalid)->save();
            $this->get('/@'.$listing->slug)->assertOk()->assertDontSee('Recomendación de');
            $second->forceFill(['credit_consent' => true, 'instagram' => 'segunda_persona', 'status' => 'approved'])->save();
        }
        app(SelectSubmissionCredit::class)->handle($actor, $second, false);
        $this->get('/@'.$listing->slug)->assertOk()->assertDontSee('@segunda_persona');
        app(SelectSubmissionCredit::class)->handle($actor, $first, true);
        $listing->forceFill(['status' => 'draft'])->save();
        $this->get('/@'.$listing->slug)->assertNotFound();
    }

    public function test_normalized_name_location_and_url_matches_are_private_and_never_block_a_second_proposal(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $city = City::factory()->create();
        $category = Category::factory()->create();
        $first = app(SubmitSubmission::class)->handle(['city_id' => $city->id, 'category_id' => $category->id, 'type' => 'business', 'name' => 'Café de Prueba', 'address' => 'Calle 10, Centro', 'maps_url' => 'https://www.example.org/maps/?b=2&a=1&utm_source=uno', 'reason' => 'Motivo privado original']);
        $second = app(SubmitSubmission::class)->handle(['city_id' => $city->id, 'category_id' => $category->id, 'type' => 'business', 'name' => 'CAFE DE PRUEBA', 'address' => 'otra referencia', 'reason' => 'Motivo privado segundo']);
        $byUrl = app(SubmitSubmission::class)->handle(['city_id' => $city->id, 'category_id' => $category->id, 'type' => 'business', 'name' => 'Otro nombre por enlace', 'address' => 'otra dirección', 'maps_url' => 'http://example.org/maps?a=1&b=2', 'reason' => 'Motivo privado por enlace']);
        $listing = Listing::factory()->recycle($city)->create(['name' => 'Nombre distinto', 'address' => 'CALLE 10 CENTRO']);
        $urlListing = Listing::factory()->recycle($city)->create(['maps_url' => 'http://example.org/maps?a=1&b=2']);
        $matches = app(SubmissionMatches::class)->handle($actor, $first);
        $this->assertSame([$listing->id, $urlListing->id], array_column($matches['listings'], 'id'));
        $this->assertSame([$byUrl->id, $second->id], array_column($matches['submissions'], 'id'));
        $this->assertSame($first->maps_key, $byUrl->maps_key);
        $this->assertSame('pending', $first->status);
        $this->assertSame('pending', $second->status);
        $this->assertDatabaseCount('submissions', 3);
    }

    public function test_panel_can_link_a_draft_without_requiring_the_hidden_creation_fields(): void
    {
        $this->actingAs(User::factory()->superAdmin()->withAppAuthentication()->create());
        $listing = Listing::factory()->create();
        $submission = Submission::factory()->recycle($listing->city)->create();
        Livewire::test(ManageSubmissions::class)->callAction(TestAction::make('approve')->table($submission), data: ['mode' => 'link', 'listing_id' => $listing->id, 'moderation_reason' => 'Vínculo comprobado'])->assertHasNoActionErrors();
        $this->assertSame('approved', $submission->fresh()->status);
        $this->assertSame($listing->id, $submission->fresh()->listing_id);
        $this->assertSame('draft', $listing->fresh()->status);
        $this->assertDatabaseCount('listings', 1);
    }

    public function test_panel_filters_details_escape_and_moderation_actions_work_without_bulk_actions(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $this->actingAs($actor);
        $submission = Submission::factory()->create(['reason' => '<script>alert("privado")</script>']);
        $component = Livewire::test(ManageSubmissions::class)->assertCanSeeTableRecords([$submission])->assertTableBulkActionDoesNotExist('delete')
            ->mountAction(TestAction::make('details')->table($submission))
            ->assertMountedActionModalSee($submission->reason)->assertMountedActionModalDontSeeHtml($submission->reason);
        $component->unmountAction();
        $component->filterTable('status', 'approved')->assertCanNotSeeTableRecords([$submission])->resetTableFilters();
        $component->callAction(TestAction::make('approve')->table($submission), data: ['mode' => 'create', 'name' => 'Borrador desde panel', 'slug' => 'borrador-desde-panel', 'primary_category_id' => $submission->category_id, 'moderation_reason' => 'Comprobación administrativa'])
            ->assertHasNoActionErrors();
        $this->assertSame('approved', $submission->fresh()->status);
        $this->assertSame('draft', $submission->fresh()->listing->status);
        $component->assertActionHidden(TestAction::make('approve')->table($submission));
    }

    public function test_guests_ordinary_users_and_admins_without_2fa_cannot_access_or_mutate_submissions(): void
    {
        $submission = Submission::factory()->create();
        $this->get('/dashboard/recomendaciones')->assertRedirect('/dashboard/login');
        foreach ([User::factory()->withAppAuthentication()->create(), User::factory()->superAdmin()->create()] as $actor) {
            foreach (['viewAny', 'create', 'deleteAny', 'restoreAny', 'forceDeleteAny'] as $ability) {
                $this->assertFalse(Gate::forUser($actor)->allows($ability, Submission::class));
            }
            foreach (['view', 'update', 'delete', 'restore', 'forceDelete'] as $ability) {
                $this->assertFalse(Gate::forUser($actor)->allows($ability, $submission));
            }
            try {
                app(ModerateSubmission::class)->handle($actor, $submission, $this->creation($submission));
                $this->fail('Acceso indebido');
            } catch (AuthorizationException) {
                $this->assertSame('pending', $submission->fresh()->status);
            }
        }
        $this->actingAs(User::factory()->withAppAuthentication()->create())->get('/dashboard/recomendaciones')->assertForbidden();
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        $this->actingAs($admin);
        $component = Livewire::test(ManageSubmissions::class)->mountAction(TestAction::make('spam')->table($submission));
        $admin->is_super_admin = false;
        $admin->save();
        $component->call('callMountedAction')->assertForbidden();
        $this->assertSame('pending', $submission->fresh()->status);
        $this->assertDatabaseCount('listings', 0);
    }
}
