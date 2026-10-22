<?php

namespace Tests\Feature;

use App\Filament\Auth\EditProfile;
use App\Models\AuditEvent;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSessionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
    }

    public function test_livewire_request_rechecks_privileges_after_role_revocation(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        auth()->login($admin);
        $snapshot = $this->pageSnapshot('/dashboard/auditoria');
        DB::table('users')->where('id', $admin->id)->update(['is_super_admin' => false]);
        auth()->forgetGuards();

        $this->updateComponent($snapshot, '$refresh')->assertForbidden();

        $this->assertDatabaseHas('audit_events', ['action' => 'auth.access_denied', 'actor_id' => $admin->id]);
    }

    public function test_livewire_request_rechecks_required_mfa_after_it_is_disabled(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        auth()->login($admin);
        $snapshot = $this->pageSnapshot('/dashboard/auditoria');
        DB::table('users')->where('id', $admin->id)->update(['app_authentication_secret' => null]);
        auth()->forgetGuards();

        $this->updateComponent($snapshot, '$refresh')->assertRedirect(Filament::getSetUpRequiredMultiFactorAuthenticationUrl());
    }

    public function test_forged_delete_and_bulk_delete_requests_cannot_change_audit_history(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        $event = AuditEvent::factory()->create();
        auth()->login($admin);
        $snapshot = $this->pageSnapshot('/dashboard/auditoria');
        $count = AuditEvent::query()->count();

        $this->updateComponent($snapshot, 'mountAction', ['delete', ['record' => $event->getKey()], ['table' => true]])->assertOk();
        $this->updateComponent($snapshot, 'mountAction', ['delete', [], ['table' => true, 'bulk' => true]], ['selectedTableRecords' => [$event->getKey()]])->assertOk();

        $this->assertModelExists($event);
        $this->assertDatabaseCount('audit_events', $count);
    }

    public function test_profile_password_change_requires_current_password_and_is_audited(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        $this->actingAs($admin);

        Livewire::test(EditProfile::class)->fillForm([
            'password' => 'OtraClave123456', 'passwordConfirmation' => 'OtraClave123456', 'currentPassword' => 'incorrecta',
        ])->call('save')->assertHasFormErrors(['currentPassword']);
        $this->assertTrue(Hash::check('password', $admin->fresh()->password));
        $this->assertDatabaseMissing('audit_events', ['action' => 'account.updated']);

        Livewire::test(EditProfile::class)->fillForm([
            'password' => 'OtraClave123456', 'passwordConfirmation' => 'OtraClave123456', 'currentPassword' => 'password',
        ])->call('save')->assertHasNoFormErrors();

        $this->assertTrue(Hash::check('OtraClave123456', $admin->fresh()->password));
        $this->assertDatabaseHas('audit_events', ['action' => 'account.updated', 'actor_id' => $admin->id]);
    }

    public function test_password_change_invalidates_an_existing_session_with_the_old_password_hash(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        auth()->login($admin);
        $oldHash = $admin->password;
        $this->get('/dashboard')->assertOk();
        $admin->password = 'OtraClave123456';
        $admin->save();
        auth()->forgetGuards();

        $this->withSession(['password_hash_web' => $oldHash])->get('/dashboard')->assertRedirect('/dashboard/login');

        $this->assertGuest();
    }

    public function test_logout_ends_the_session_and_audits_the_actor(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        auth()->login($admin);
        $this->get('/dashboard')->assertOk();

        $this->post('/dashboard/logout')->assertRedirect('/dashboard/login');

        $this->assertGuest();
        $this->get('/dashboard/auditoria')->assertRedirect('/dashboard/login');
        $this->assertDatabaseHas('audit_events', ['action' => 'auth.logout', 'actor_id' => $admin->id, 'result' => 'success']);
    }

    public function test_session_cookie_is_http_only_same_site_and_expires_when_browser_closes(): void
    {
        $response = $this->get('/dashboard/login')->assertOk();
        $cookies = collect($response->headers->getCookies());
        $cookie = $cookies->first(fn ($cookie): bool => $cookie->getName() === config('session.cookie'));

        $this->assertNotNull($cookie);
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertSame('lax', $cookie->getSameSite());
        $this->assertSame(0, $cookie->getExpiresTime());
        $this->assertTrue(config('session.encrypt'));
        $this->assertSame(30, config('session.lifetime'));
    }

    public function test_audit_history_rejects_model_updates_and_deletes(): void
    {
        $event = AuditEvent::factory()->create();
        try {
            $event->source = 'console';
            $event->save();
            $this->fail('No debe permitirse editar la auditoría.');
        } catch (\LogicException $exception) {
            $this->assertSame('Los eventos de auditoría no se modifican.', $exception->getMessage());
        }
        try {
            $event->delete();
            $this->fail('No debe permitirse borrar la auditoría.');
        } catch (\LogicException $exception) {
            $this->assertSame('Los eventos de auditoría no se eliminan.', $exception->getMessage());
        }

        $this->assertModelExists($event);
        $this->assertSame('web', $event->fresh()->source);
    }

    private function pageSnapshot(string $url): string
    {
        $html = $this->get($url)->assertOk()->getContent();
        preg_match_all('/wire:snapshot="([^"]+)"/', $html, $matches);
        foreach ($matches[1] as $encoded) {
            $snapshot = html_entity_decode($encoded, ENT_QUOTES);
            if (str_contains(json_decode($snapshot, true)['memo']['name'], 'ManageAuditEvents')) {
                return $snapshot;
            }
        }
        $this->fail('No se encontró el componente de auditoría.');
    }

    /** @param array<mixed> $params
     * @param  array<string, mixed>  $updates
     */
    private function updateComponent(string $snapshot, string $method, array $params = [], array $updates = []): TestResponse
    {
        return $this->withHeaders(['X-Livewire' => 'true'])->postJson(app('livewire')->getUpdateUri(), [
            'components' => [[
                'snapshot' => $snapshot, 'updates' => $updates,
                'calls' => [['path' => '', 'method' => $method, 'params' => $params]],
            ]],
        ]);
    }
}
