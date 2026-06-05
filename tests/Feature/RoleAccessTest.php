<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_role_is_redirected_to_its_own_dashboard_on_login(): void
    {
        $map = [
            'admin'   => '/admin',
            'teacher' => '/teacher',
            'parent'  => '/parent',
            'student' => '/student',
        ];

        foreach ($map as $role => $home) {
            $user = User::factory()->create([
                'role'      => $role,
                'is_active' => true,
                'password'  => bcrypt('secret123'),
            ]);

            $this->post('/login', ['email' => $user->email, 'password' => 'secret123'])
                ->assertRedirect($home);

            $this->post('/logout');
        }
    }

    public function test_student_is_blocked_from_admin_routes(): void
    {
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);

        $this->actingAs($student)->get('/admin')->assertForbidden();
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)->get('/admin')->assertOk();
    }
}
