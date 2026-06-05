<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Path;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentLearningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        Storage::fake('public');
    }

    private function student(): User
    {
        return User::factory()->create(['role' => 'student', 'is_active' => true]);
    }

    public function test_student_can_enroll_and_first_lesson_unlocks(): void
    {
        $student = $this->student();
        $path = Path::first();

        $this->actingAs($student)
            ->post(route('student.paths.enroll', $path->id), ['grade_level' => 'primary'])
            ->assertRedirect();

        $enrollment = Enrollment::where('student_id', $student->id)->where('path_id', $path->id)->first();
        $this->assertNotNull($enrollment);

        $lessons = $path->lessons()->orderBy('sort_order')->get();
        $first = LessonProgress::where('student_id', $student->id)->where('lesson_id', $lessons[0]->id)->first();
        $second = LessonProgress::where('student_id', $student->id)->where('lesson_id', $lessons[1]->id)->first();

        $this->assertSame('in_progress', $first->status);
        $this->assertSame('locked', $second->status);
    }

    public function test_locked_lesson_is_forbidden(): void
    {
        $student = $this->student();
        $path = Path::first();

        $this->actingAs($student)->post(route('student.paths.enroll', $path->id), ['grade_level' => 'primary']);

        $lessons = $path->lessons()->orderBy('sort_order')->get();

        $this->actingAs($student)->get(route('student.lesson', $lessons[1]->id))->assertForbidden();
        $this->actingAs($student)->get(route('student.lesson', $lessons[0]->id))->assertOk();
    }

    public function test_completing_all_lessons_awards_badge_and_certificate(): void
    {
        $student = $this->student();
        $path = Path::with('lessons.video', 'lessons.scormPackage', 'lessons.quiz.questions.options')->first();

        $this->actingAs($student)->post(route('student.paths.enroll', $path->id), ['grade_level' => 'primary']);

        foreach ($path->lessons()->orderBy('sort_order')->get() as $lesson) {
            $this->completeLesson($student, $lesson);
        }

        $enrollment = Enrollment::where('student_id', $student->id)->where('path_id', $path->id)->first();
        $this->assertSame('completed', $enrollment->fresh()->status);

        // Badge awarded
        $badge = Badge::where('path_id', $path->id)->where('type', 'path')->first();
        $this->assertDatabaseHas('student_badges', ['student_id' => $student->id, 'badge_id' => $badge->id]);

        // Certificate issued with PDF
        $cert = Certificate::where('student_id', $student->id)->where('path_id', $path->id)->first();
        $this->assertNotNull($cert);
        $this->assertStringStartsWith('BARE3-', $cert->cert_number);
        $this->assertTrue(Storage::disk('public')->exists($cert->pdf_path));
    }

    public function test_public_certificate_verification_works(): void
    {
        $student = $this->student();
        $path = Path::with('lessons.video', 'lessons.scormPackage', 'lessons.quiz.questions.options')->first();

        $this->actingAs($student)->post(route('student.paths.enroll', $path->id), ['grade_level' => 'primary']);
        foreach ($path->lessons()->orderBy('sort_order')->get() as $lesson) {
            $this->completeLesson($student, $lesson);
        }

        $cert = Certificate::where('student_id', $student->id)->first();

        $this->get(route('certificates.verify', $cert->cert_number))
            ->assertOk()
            ->assertInertia(fn ($p) => $p->where('valid', true)->where('certificate.cert_number', $cert->cert_number));

        $this->get(route('certificates.verify', 'BARE3-9999-99999'))
            ->assertOk()
            ->assertInertia(fn ($p) => $p->where('valid', false));
    }

    private function completeLesson(User $student, Lesson $lesson): void
    {
        // Video
        $this->actingAs($student)->postJson(route('student.lesson.video'), [
            'video_id'      => $lesson->video->id,
            'watch_percent' => 100,
            'position'      => 300,
        ])->assertOk()->assertJson(['video_completed' => true]);

        // SCORM
        $this->actingAs($student)->postJson(route('student.lesson.scorm'), [
            'scorm_id' => $lesson->scormPackage->id,
        ])->assertOk()->assertJson(['scorm_completed' => true]);

        // Quiz — answer every question correctly
        $answers = [];
        foreach ($lesson->quiz->questions as $q) {
            $answers[$q->id] = $q->options->firstWhere('is_correct', true)->id;
        }

        $this->actingAs($student)->postJson(route('student.lesson.quiz'), [
            'quiz_id' => $lesson->quiz->id,
            'answers' => $answers,
        ])->assertOk()->assertJson(['passed' => true]);
    }
}
