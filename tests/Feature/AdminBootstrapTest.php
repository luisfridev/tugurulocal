<?php

namespace Tests\Feature;

use App\Actions\Admin\BootstrapSuperAdmin;
use App\Models\AuditEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminBootstrapTest extends TestCase
{
    use RefreshDatabase;

    public function test_interactive_bootstrap_creates_only_the_first_admin_without_exposing_the_password(): void
    {
        $this->artisan('app:bootstrap-super-admin')
            ->expectsQuestion('Nombre de la primera cuenta administrativa', 'Administración de pruebas')
            ->expectsQuestion('Correo administrativo', 'admin@example.test')
            ->expectsQuestion('Contraseña (12 caracteres, mayúsculas, minúsculas y números)', 'ClaveSegura12345')
            ->expectsQuestion('Repite la contraseña', 'ClaveSegura12345')
            ->assertSuccessful();

        $admin = User::query()->sole();
        $this->assertTrue($admin->is_super_admin);
        $this->assertTrue(Hash::check('ClaveSegura12345', $admin->password));
        $this->assertNull($admin->getAppAuthenticationSecret());
        $this->assertDatabaseHas('audit_events', ['action' => 'admin.bootstrapped', 'actor_id' => $admin->id, 'result' => 'success']);
        $this->assertStringNotContainsString('ClaveSegura12345', AuditEvent::query()->get()->toJson());
    }

    public function test_bootstrap_refuses_a_second_admin(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->artisan('app:bootstrap-super-admin')
            ->expectsOutput('La primera cuenta administrativa ya existe. No se modificó ninguna cuenta.')
            ->assertFailed();

        $this->assertDatabaseCount('users', 1);
        $this->assertTrue($admin->fresh()->is_super_admin);
    }

    public function test_noninteractive_bootstrap_is_refused(): void
    {
        $this->artisan('app:bootstrap-super-admin', ['--no-interaction' => true])
            ->expectsOutput('Este comando requiere una terminal interactiva y una contraseña oculta.')
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_mismatched_confirmation_creates_nothing(): void
    {
        $this->artisan('app:bootstrap-super-admin')
            ->expectsQuestion('Nombre de la primera cuenta administrativa', 'Prueba')
            ->expectsQuestion('Correo administrativo', 'admin@example.test')
            ->expectsQuestion('Contraseña (12 caracteres, mayúsculas, minúsculas y números)', 'ClaveSegura12345')
            ->expectsQuestion('Repite la contraseña', 'OtraClave12345')
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('audit_events', 0);
    }

    public function test_existing_ordinary_account_is_never_promoted_by_bootstrap(): void
    {
        $user = User::factory()->create(['email' => 'ordinary@example.test']);

        try {
            app(BootstrapSuperAdmin::class)->handle('Prueba', $user->email, 'ClaveSegura12345');
            $this->fail('Se esperaba rechazar el correo existente.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('email', $exception->errors());
        }

        $this->assertFalse($user->fresh()->is_super_admin);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('audit_events', 0);
    }

    #[DataProvider('invalidCredentials')]
    public function test_invalid_bootstrap_credentials_do_not_create_accounts(string $email, string $password, string $error): void
    {
        try {
            app(BootstrapSuperAdmin::class)->handle('Prueba', $email, $password);
            $this->fail('Se esperaba validación.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey($error, $exception->errors());
        }

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('audit_events', 0);
    }

    /** @return array<string, array{string, string, string}> */
    public static function invalidCredentials(): array
    {
        return [
            'correo inválido' => ['correo-invalido', 'ClaveSegura12345', 'email'],
            'clave corta' => ['admin@example.test', 'Corta123', 'password'],
            'sin mayúsculas' => ['admin@example.test', 'clavesegura12345', 'password'],
            'sin números' => ['admin@example.test', 'ClaveSeguraLarga', 'password'],
        ];
    }

    public function test_mass_assignment_cannot_grant_privileges_or_install_a_second_factor(): void
    {
        $user = User::query()->create([
            'name' => 'Prueba', 'email' => 'ordinary@example.test', 'password' => 'ClaveSegura12345',
            'is_super_admin' => true, 'app_authentication_secret' => 'INJECTEDSECRET',
            'app_authentication_recovery_codes' => ['injected'],
        ]);

        $this->assertFalse($user->fresh()->is_super_admin);
        $this->assertNull($user->fresh()->getAppAuthenticationSecret());
        $this->assertNull($user->fresh()->getAppAuthenticationRecoveryCodes());
    }

    public function test_bootstrap_validation_errors_are_readable_in_spanish(): void
    {
        try {
            app(BootstrapSuperAdmin::class)->handle('Prueba', 'admin@example.test', 'Corta123');
            $this->fail('Se esperaba rechazar la contraseña corta.');
        } catch (ValidationException $exception) {
            $this->assertContains('El campo contraseña debe tener al menos 12 caracteres.', $exception->errors()['password']);
        }

        $this->assertDatabaseCount('users', 0);
    }
}
