<?php

namespace Tests\Feature;

use App\Filament\Auth\EditProfile;
use App\Filament\Auth\Login;
use App\Filament\Resources\AuditEvents\Pages\ManageAuditEvents;
use App\Models\AuditEvent;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Pages\SetUpRequiredMultiFactorAuthentication;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
    }

    public function test_guests_are_redirected_and_registration_is_absent(): void
    {
        foreach (['/dashboard', '/dashboard/auditoria', '/dashboard/profile'] as $url) {
            $this->get($url)->assertRedirect('/dashboard/login');
        }

        $this->get('/dashboard/login')->assertOk()->assertSee('Acceso')->assertDontSee('Recordarme');
        $this->get('/dashboard/register')->assertNotFound();
        $this->get('/register')->assertNotFound();
    }

    public function test_login_discards_an_obsolete_admin_destination_saved_in_the_session(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        $this->withSession(['url.intended' => url('/admin')]);

        Livewire::test(Login::class)->fillForm(['email' => $admin->email, 'password' => 'password'])
            ->call('authenticate')->set('data.multiFactor.app.code', $this->provider()->getCurrentCode($admin))
            ->call('authenticate')->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($admin);
    }

    public function test_an_open_login_form_discards_an_obsolete_destination_before_authentication(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $login = Livewire::test(Login::class)->fillForm(['email' => $admin->email, 'password' => 'password']);
        session()->put('url.intended', url('/admin/auditoria'));

        $login->call('authenticate')->assertRedirect('/dashboard');

        $this->get('/dashboard')->assertRedirect(Filament::getSetUpRequiredMultiFactorAuthenticationUrl());
    }

    public function test_login_preserves_a_current_dashboard_destination(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        $this->withSession(['url.intended' => url('/dashboard/auditoria')]);

        Livewire::test(Login::class)->fillForm(['email' => $admin->email, 'password' => 'password'])
            ->call('authenticate')->set('data.multiFactor.app.code', $this->provider()->getCurrentCode($admin))
            ->call('authenticate')->assertRedirect('/dashboard/auditoria');

        $this->assertAuthenticatedAs($admin);
    }

    public function test_authenticated_login_visit_discards_an_obsolete_admin_destination(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();

        $this->actingAs($admin)->withSession(['url.intended' => url('/admin')])
            ->get('/dashboard/login')->assertRedirect('/dashboard');
    }

    public function test_previous_admin_routes_are_not_available(): void
    {
        foreach (['/admin', '/admin/login', '/admin/profile', '/admin/auditoria', '/admin/multi-factor-authentication/set-up'] as $url) {
            $this->get($url)->assertNotFound();
        }

        $this->post('/admin/logout')->assertNotFound();
    }

    public function test_ordinary_users_cannot_access_any_admin_surface(): void
    {
        $user = User::factory()->withAppAuthentication()->create();
        $this->actingAs($user);

        foreach (['/dashboard', '/dashboard/auditoria', '/dashboard/profile', Filament::getSetUpRequiredMultiFactorAuthenticationUrl()] as $url) {
            $this->get($url)->assertForbidden();
        }

        $this->assertDatabaseHas('audit_events', ['action' => 'auth.access_denied', 'actor_id' => $user->id, 'result' => 'denied']);
    }

    public function test_admin_must_enable_mfa_before_dashboard_audit_or_profile_changes(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $this->actingAs($admin);

        $this->get('/dashboard')->assertRedirect(Filament::getSetUpRequiredMultiFactorAuthenticationUrl());
        $this->get('/dashboard/auditoria')->assertRedirect(Filament::getSetUpRequiredMultiFactorAuthenticationUrl());
        $this->get(Filament::getSetUpRequiredMultiFactorAuthenticationUrl())->assertOk();
        Livewire::test(EditProfile::class)->fillForm(['name' => 'Alterado'])->call('save')->assertForbidden();
        $this->assertNotSame('Alterado', $admin->fresh()->name);
    }

    public function test_correct_password_of_ordinary_user_still_fails_without_showing_mfa(): void
    {
        $user = User::factory()->withAppAuthentication()->create();

        Livewire::test(Login::class)->fillForm(['email' => $user->email, 'password' => 'password'])
            ->call('authenticate')->assertHasFormErrors(['email'])->assertSet('userUndertakingMultiFactorAuthentication', null);

        $this->assertGuest();
        $this->assertDatabaseHas('audit_events', ['action' => 'auth.login_failed', 'result' => 'failed', 'actor_id' => null]);
    }

    public function test_password_alone_does_not_authenticate_an_admin_with_mfa(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();

        Livewire::test(Login::class)->fillForm(['email' => $admin->email, 'password' => 'password'])
            ->call('authenticate')->assertHasNoFormErrors()->assertSee('6 dígitos');

        $this->assertGuest();
        $this->assertDatabaseMissing('audit_events', ['action' => 'auth.login']);
    }

    public function test_invalid_mfa_is_audited_then_valid_code_authenticates_and_rotates_session(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        session()->start();
        $sessionId = session()->getId();
        $login = Livewire::test(Login::class)->fillForm(['email' => $admin->email, 'password' => 'password'])->call('authenticate');

        $login->set('data.multiFactor.app.code', 'invalid')->call('authenticate')
            ->assertHasErrors(['data.multiFactor.app.code']);
        $this->assertGuest();
        $this->assertDatabaseHas('audit_events', ['action' => 'auth.mfa_failed', 'result' => 'failed', 'subject_id' => $admin->id]);

        $login->set('data.multiFactor.app.code', $this->provider()->getCurrentCode($admin))->call('authenticate')->assertHasNoErrors()->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($admin);
        $this->assertNotSame($sessionId, session()->getId());
        $this->assertDatabaseHas('audit_events', ['action' => 'auth.login', 'actor_id' => $admin->id, 'result' => 'success']);
        $this->get('/dashboard')->assertOk();
    }

    public function test_recovery_code_is_hashed_encrypted_consumed_and_cannot_be_reused(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        $this->provider()->saveRecoveryCodes($admin, ['recovery-code-for-test']);
        $raw = DB::table('users')->where('id', $admin->id)->first();
        $this->assertStringNotContainsString('recovery-code-for-test', $raw->app_authentication_recovery_codes);

        $login = Livewire::test(Login::class)->fillForm(['email' => $admin->email, 'password' => 'password'])->call('authenticate');
        $login->set('data.multiFactor.app.useRecoveryCode', true)->set('data.multiFactor.app.recoveryCode', 'recovery-code-for-test')
            ->call('authenticate')->assertHasNoErrors()->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($admin);
        $this->assertSame([], $admin->fresh()->getAppAuthenticationRecoveryCodes());
        $this->post('/dashboard/logout')->assertRedirect('/dashboard/login');
        $this->assertGuest();

        Livewire::test(Login::class)->fillForm(['email' => $admin->email, 'password' => 'password'])->call('authenticate')
            ->set('data.multiFactor.app.useRecoveryCode', true)->set('data.multiFactor.app.recoveryCode', 'recovery-code-for-test')
            ->call('authenticate')->assertHasErrors(['data.multiFactor.app.recoveryCode']);
        $this->assertGuest();
    }

    public function test_login_limit_blocks_the_sixth_attempt_and_expires(): void
    {
        $admin = User::factory()->superAdmin()->create();
        for ($attempt = 0; $attempt < 5; $attempt++) {
            Livewire::test(Login::class)->fillForm(['email' => $admin->email, 'password' => 'incorrecta'])->call('authenticate')->assertHasFormErrors(['email']);
        }

        Livewire::test(Login::class)->fillForm(['email' => $admin->email, 'password' => 'password'])->call('authenticate');

        $this->assertGuest();
        $this->assertDatabaseHas('audit_events', ['action' => 'auth.login_throttled', 'result' => 'throttled']);
        $this->travel(61)->seconds();
        Livewire::test(Login::class)->fillForm(['email' => $admin->email, 'password' => 'password'])->call('authenticate')->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($admin);
        $this->get('/dashboard')->assertRedirect(Filament::getSetUpRequiredMultiFactorAuthenticationUrl());
    }

    public function test_remember_me_cannot_be_injected_to_create_a_persistent_login(): void
    {
        $admin = User::factory()->superAdmin()->create(['remember_token' => null]);

        Livewire::test(Login::class)->fillForm(['email' => $admin->email, 'password' => 'password'])
            ->set('data.remember', true)->call('authenticate')->assertRedirect('/dashboard');

        $this->assertNull($admin->fresh()->remember_token);
    }

    public function test_audit_is_read_only_in_the_ui_and_policy_including_bulk_operations(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        $event = AuditEvent::factory()->create();
        $this->actingAs($admin);

        $this->get('/dashboard/auditoria')->assertOk()->assertSee('Auditoría');
        Livewire::test(ManageAuditEvents::class)->assertCanSeeTableRecords([$event])
            ->assertActionDoesNotExist('create')->assertTableActionDoesNotExist('edit')->assertTableActionDoesNotExist('delete')
            ->assertTableBulkActionDoesNotExist('delete');
        foreach (['create', 'deleteAny', 'restoreAny', 'forceDeleteAny'] as $ability) {
            $this->assertFalse(Gate::allows($ability, AuditEvent::class));
        }
        foreach (['update', 'delete', 'restore', 'forceDelete'] as $ability) {
            $this->assertFalse(Gate::allows($ability, $event));
        }
    }

    public function test_revoked_admin_loses_access_and_cannot_change_other_accounts(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        $other = User::factory()->create();
        $this->actingAs($admin);
        $this->assertFalse(Gate::allows('update', $other));

        $admin->is_super_admin = false;
        $admin->save();

        $this->get('/dashboard/auditoria')->assertForbidden();
        $this->assertDatabaseHas('audit_events', ['action' => 'account.updated', 'actor_id' => $admin->id, 'subject_id' => $admin->id]);
    }

    public function test_mfa_storage_and_account_audit_never_serialize_sensitive_values(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $this->actingAs($admin);
        $secret = $this->provider()->generateSecret();

        $admin->saveAppAuthenticationSecret($secret);
        $this->provider()->saveRecoveryCodes($admin, ['test-recovery-value']);
        $admin->password = 'NuevaClave123456';
        $admin->save();

        $serialized = $admin->fresh()->toArray();
        $this->assertArrayNotHasKey('password', $serialized);
        $this->assertArrayNotHasKey('app_authentication_secret', $serialized);
        $this->assertArrayNotHasKey('app_authentication_recovery_codes', $serialized);
        $this->assertNotSame($secret, DB::table('users')->where('id', $admin->id)->value('app_authentication_secret'));
        $audit = AuditEvent::query()->get()->toJson();
        foreach ([$secret, 'test-recovery-value', 'NuevaClave123456', $admin->email] as $privateValue) {
            $this->assertStringNotContainsString($privateValue, $audit);
        }
        $this->assertDatabaseHas('audit_events', ['action' => 'account.mfa_enabled', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('audit_events', ['action' => 'account.recovery_codes_changed', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('audit_events', ['action' => 'account.updated', 'actor_id' => $admin->id]);
    }

    private function provider(): AppAuthentication
    {
        return Filament::getMultiFactorAuthenticationProviders()['app'];
    }

    public function test_mfa_enrollment_requires_current_password_and_valid_code_before_saving(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $this->actingAs($admin);
        $setup = Livewire::test(SetUpRequiredMultiFactorAuthentication::class)
            ->mountAction(TestAction::make('setUpAppAuthentication')->schemaComponent(true, 'content'));
        $arguments = decrypt($setup->instance()->mountedActions[0]['arguments']['encrypted']);
        $code = $this->provider()->getCurrentCode($admin, $arguments['secret']);

        $setup->fillForm(['code' => $code, 'password' => 'incorrecta'])->callMountedAction()->assertHasActionErrors(['password']);
        $this->assertNull($admin->fresh()->getAppAuthenticationSecret());
        $setup->fillForm(['code' => 'invalid', 'password' => 'password'])->callMountedAction()->assertHasActionErrors(['code']);
        $this->assertNull($admin->fresh()->getAppAuthenticationSecret());

        $setup->fillForm(['code' => $code, 'password' => 'password'])->callMountedAction()->assertHasNoActionErrors();

        $this->assertSame($arguments['secret'], $admin->fresh()->getAppAuthenticationSecret());
        $this->assertCount(8, $admin->fresh()->getAppAuthenticationRecoveryCodes());
        $this->assertDatabaseHas('audit_events', ['action' => 'account.mfa_enabled', 'actor_id' => $admin->id]);
        $this->get('/dashboard')->assertOk();
    }

    public function test_a_totp_used_for_login_cannot_be_replayed_in_another_login(): void
    {
        $admin = User::factory()->superAdmin()->withAppAuthentication()->create();
        $code = $this->provider()->getCurrentCode($admin);

        Livewire::test(Login::class)->fillForm(['email' => $admin->email, 'password' => 'password'])->call('authenticate')
            ->set('data.multiFactor.app.code', $code)->call('authenticate')->assertRedirect('/dashboard');
        $this->post('/dashboard/logout')->assertRedirect('/dashboard/login');

        Livewire::test(Login::class)->fillForm(['email' => $admin->email, 'password' => 'password'])->call('authenticate')
            ->set('data.multiFactor.app.code', $code)->call('authenticate')->assertHasErrors(['data.multiFactor.app.code']);

        $this->assertGuest();
    }
}
