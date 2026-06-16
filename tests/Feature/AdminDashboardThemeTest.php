<?php

namespace Tests\Feature;

use App\Models\Schedule;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminDashboardThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_uses_custom_theme_widget(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::where('email', 'admin@smk.sch.id')->firstOrFail();

        $this
            ->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard SIAKAD')
            ->assertSee('css/filament-admin-dashboard.css', false);
    }

    public function test_core_database_is_lightweight_and_schedule_is_available(): void
    {
        $this->assertDatabaseCount('siswa', 5);
        $this->assertGreaterThan(0, Schedule::count());
        $this->assertDatabaseMissing('migrations', ['migration' => '2026_06_12_000003_create_blogs_table']);
        $this->assertTrue(DB::table('fblog_share_snippets')->where('active', true)->exists());
    }

    public function test_student_dashboard_hides_admin_only_student_list(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::where('email', 'aditya.siswa@smk.sch.id')->firstOrFail();

        $this
            ->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Jadwal Pelajaran')
            ->assertDontSee('Siswa Terbaru');
    }

    public function test_public_blog_has_admin_login_access(): void
    {
        $this
            ->get('/')
            ->assertOk()
            ->assertSee('Login Admin');
    }
}
