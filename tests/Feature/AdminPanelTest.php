<?php

namespace Tests\Feature;

use App\Models\Path;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function admin(): User
    {
        return User::where('role', 'admin')->first();
    }

    public function test_all_admin_pages_render(): void
    {
        $admin = $this->admin();

        foreach (['/admin', '/admin/users', '/admin/content', '/admin/payments', '/admin/blog', '/admin/settings'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_admin_can_create_and_delete_user(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'مستخدم اختبار', 'email' => 'newuser@bare3.sa',
            'password' => 'password123', 'role' => 'teacher',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'newuser@bare3.sa', 'role' => 'teacher']);

        $user = User::where('email', 'newuser@bare3.sa')->first();
        $this->actingAs($admin)->delete(route('admin.users.destroy', $user->id))->assertRedirect();
        $this->assertDatabaseMissing('users', ['email' => 'newuser@bare3.sa']);
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $admin = $this->admin();
        $user = User::where('role', 'student')->first();
        $before = $user->is_active;

        $this->actingAs($admin)->patch(route('admin.users.toggle-status', $user->id))->assertRedirect();
        $this->assertSame(! $before, (bool) $user->fresh()->is_active);
    }

    public function test_settings_persist(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.settings.update'), [
            'platform_name' => 'بارع المعدّلة',
            'pass_mark_default' => 75,
            'video_threshold' => 85,
            'max_quiz_attempts' => 4,
            'notification_email' => true,
            'notification_in_app' => false,
        ])->assertRedirect();

        $this->assertSame('بارع المعدّلة', Setting::get('platform_name'));
        $this->assertSame('75', Setting::get('pass_mark_default'));
        $this->assertSame('0', Setting::get('notification_in_app'));
    }

    public function test_admin_can_create_path_lesson_and_quiz(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.content.paths.store'), [
            'title' => 'مسار اختبار', 'slug' => 'test-path', 'sort_order' => 9,
        ])->assertRedirect();
        $path = Path::where('slug', 'test-path')->first();
        $this->assertNotNull($path);

        $this->actingAs($admin)->post(route('admin.content.lessons.store'), [
            'path_id' => $path->id, 'title' => 'درس اختبار', 'grade_level' => 'all', 'sort_order' => 1,
        ])->assertRedirect();
        $lesson = $path->lessons()->first();
        $this->assertNotNull($lesson);

        $this->actingAs($admin)->post(route('admin.content.quizzes.store'), [
            'lesson_id' => $lesson->id, 'title' => 'اختبار', 'pass_mark' => 70, 'max_attempts' => 3,
            'questions' => [
                ['text' => 'سؤال؟', 'options' => ['أ', 'ب', 'ج'], 'correct' => 1],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('quizzes', ['lesson_id' => $lesson->id]);
        $this->assertDatabaseHas('quiz_questions', ['text' => 'سؤال؟']);
    }

    public function test_admin_can_create_blog_post_with_tags(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $category = \App\Models\BlogCategory::first();

        $this->actingAs($admin)->post(route('admin.blog.store'), [
            'title' => 'مقالة اختبار', 'category_id' => $category->id,
            'content' => '<p>محتوى</p>', 'status' => 'published',
            'tags' => ['وسم جديد', 'تعليم'],
        ])->assertRedirect();

        $this->assertDatabaseHas('blog_posts', ['title' => 'مقالة اختبار', 'status' => 'published']);
        $this->assertDatabaseHas('blog_tags', ['name' => 'وسم جديد']);
    }
}
