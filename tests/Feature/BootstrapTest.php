<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BootstrapTest extends TestCase
{
    use RefreshDatabase;

    public function test_mysql_schema_is_available_without_an_admin_account(): void
    {
        $this->assertSame('mysql', DB::getDriverName());
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertSame(0, User::query()->count());
    }

    public function test_bootstrap_page_is_in_spanish_and_not_indexable(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('lang="es"', false)
            ->assertSee('noindex,nofollow')
            ->assertSee('Tu Gurú Local');
    }

    public function test_public_registration_and_commercial_page_are_not_available(): void
    {
        $this->get('/register')->assertNotFound();
        $this->get('/premium')->assertNotFound();
    }

    public function test_a_new_user_cannot_access_the_admin_panel(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->is_super_admin);
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('admin')));

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_business_time_is_displayed_in_cancun_while_application_uses_utc(): void
    {
        $this->assertSame('UTC', config('app.timezone'));
        $this->assertSame('America/Cancun', config('business.timezone'));
    }
}
