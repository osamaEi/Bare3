<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function parent(): User
    {
        return User::factory()->create(['role' => 'parent', 'is_active' => true]);
    }

    public function test_parent_dashboard_renders(): void
    {
        $this->actingAs($this->parent())->get('/parent')->assertOk();
    }

    public function test_parent_can_add_a_child(): void
    {
        $parent = $this->parent();

        $this->actingAs($parent)->post(route('parent.children.store'), [
            'name' => 'طفل تجريبي',
            'email' => 'child@bare3.sa',
            'password' => 'password123',
            'gender' => 'male',
        ])->assertRedirect();

        $child = User::where('email', 'child@bare3.sa')->first();
        $this->assertNotNull($child);
        $this->assertSame('student', $child->role);
        $this->assertDatabaseHas('parent_children', [
            'parent_id' => $parent->id, 'child_id' => $child->id,
        ]);
    }

    public function test_parent_can_view_child_report(): void
    {
        $parent = $this->parent();
        $this->actingAs($parent)->post(route('parent.children.store'), [
            'name' => 'طفل', 'email' => 'c2@bare3.sa', 'password' => 'password123',
        ]);
        $child = User::where('email', 'c2@bare3.sa')->first();

        $this->actingAs($parent)->get(route('parent.children.report', $child->id))->assertOk();
    }

    public function test_parent_cannot_view_other_parents_child(): void
    {
        $parentA = $this->parent();
        $parentB = $this->parent();
        $this->actingAs($parentB)->post(route('parent.children.store'), [
            'name' => 'طفل ب', 'email' => 'cb@bare3.sa', 'password' => 'password123',
        ]);
        $childB = User::where('email', 'cb@bare3.sa')->first();

        $this->actingAs($parentA)->get(route('parent.children.report', $childB->id))->assertNotFound();
    }

    public function test_billing_page_renders(): void
    {
        $this->actingAs($this->parent())->get('/parent/billing')->assertOk();
    }

    public function test_student_cannot_access_parent_portal(): void
    {
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $this->actingAs($student)->get('/parent')->assertForbidden();
    }
}
