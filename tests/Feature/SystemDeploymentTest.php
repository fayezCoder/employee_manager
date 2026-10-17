<?php

namespace Tests\Feature;

use App\Models\OrganizationalUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemDeploymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_without_key_is_forbidden(): void
    {
        $response = $this->get('/system/deploy-migrate');
        $response->assertStatus(403);
    }

    public function test_guest_with_wrong_key_is_forbidden(): void
    {
        $response = $this->get('/system/deploy-migrate?key=wrong-key');
        $response->assertStatus(403);
    }

    public function test_guest_with_valid_deploy_key_can_run_migrations(): void
    {
        $response = $this->get('/system/deploy-migrate?key=deploy2026');
        $response->assertStatus(200);
        $response->assertSee('أداة ترحيل وتحديث النظام');
        $response->assertSee('تم تنفيذ التحديثات بنجاح تام');
    }

    public function test_authenticated_super_admin_can_access_without_key(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/system/deploy-migrate');
        $response->assertStatus(200);
        $response->assertSee('أداة ترحيل وتحديث النظام');
    }

    public function test_seeding_structure_creates_default_hierarchy(): void
    {
        $this->assertEquals(0, OrganizationalUnit::count());

        $response = $this->get('/system/deploy-migrate?key=deploy2026&seed_structure=1');
        $response->assertStatus(200);
        $response->assertSee('تم إنشاء الهيكل التنظيمي المعتمد');

        $this->assertDatabaseHas('organizational_units', [
            'code' => 'HQ-MAIN',
            'name' => 'القيادة الرئيسية',
        ]);
        $this->assertDatabaseHas('organizational_units', [
            'code' => 'SUB-01',
            'name' => 'القيادة الفرعية الأولى',
        ]);
        $this->assertDatabaseHas('organizational_units', [
            'code' => 'CTR-01',
            'name' => 'المركز الأول',
        ]);
    }
}
