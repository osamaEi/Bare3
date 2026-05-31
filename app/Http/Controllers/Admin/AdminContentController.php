<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ContentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminContentController extends Controller
{
    public function __construct(
        private readonly ContentRepositoryInterface $content,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Content', [
            'paths'       => $this->content->allPaths(),
            'scorm_files' => $this->content->allScorm(),
        ]);
    }

    // ── Paths ──────────────────────────────────────────────
    public function storePath(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'slug'        => 'required|string|unique:paths,slug',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string',
            'color'       => 'nullable|string|max:10',
            'sort_order'  => 'integer|min:0',
        ]);

        $this->content->createPath($data);

        return back()->with('success', 'تم إضافة المسار');
    }

    public function updatePath(Request $request, int $id): RedirectResponse
    {
        $this->content->updatePath($id, $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string',
            'color'       => 'nullable|string|max:10',
            'is_active'   => 'boolean',
        ]));

        return back()->with('success', 'تم تعديل المسار');
    }

    // ── Lessons ────────────────────────────────────────────
    public function storeLesson(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'path_id'     => 'required|exists:paths,id',
            'title'       => 'required|string|max:255',
            'grade_level' => 'required|in:primary,middle,high,all',
            'sort_order'  => 'integer|min:0',
        ]);

        $this->content->createLesson($data);

        return back()->with('success', 'تم إضافة الدرس');
    }

    public function updateLesson(Request $request, int $id): RedirectResponse
    {
        $this->content->updateLesson($id, $request->validate([
            'title'       => 'required|string|max:255',
            'grade_level' => 'required|in:primary,middle,high,all',
            'is_active'   => 'boolean',
        ]));

        return back()->with('success', 'تم تعديل الدرس');
    }

    public function destroyLesson(int $id): RedirectResponse
    {
        $this->content->deleteLesson($id);

        return back()->with('success', 'تم حذف الدرس');
    }

    // ── Videos ─────────────────────────────────────────────
    public function storeVideo(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'lesson_id'       => 'required|exists:lessons,id',
            'title'           => 'required|string|max:255',
            'url'             => 'nullable|url',
            'duration_seconds'=> 'integer|min:0',
            'watch_threshold' => 'integer|min:50|max:100',
        ]);

        if ($request->hasFile('video_file')) {
            $data['file_path'] = $request->file('video_file')->store('videos', 'public');
        }

        $this->content->createVideo($data);

        return back()->with('success', 'تم رفع الفيديو');
    }

    // ── SCORM ──────────────────────────────────────────────
    public function storeScorm(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'lesson_id'  => 'required|exists:lessons,id',
            'title'      => 'required|string|max:255',
            'version'    => 'required|in:1.2,2004',
            'entry_point'=> 'nullable|string',
        ]);

        if ($request->hasFile('scorm_file')) {
            $file = $request->file('scorm_file');
            $data['package_path'] = $file->store('scorm', 'public');
            $data['file_size_kb'] = (int) ($file->getSize() / 1024);
        }

        $this->content->createScorm($data);

        return back()->with('success', 'تم رفع ملف SCORM');
    }

    public function destroyScorm(int $id): RedirectResponse
    {
        $this->content->deleteScorm($id);

        return back()->with('success', 'تم حذف ملف SCORM');
    }

    // ── Quizzes ────────────────────────────────────────────
    public function storeQuiz(Request $request): RedirectResponse
    {
        $request->validate([
            'lesson_id'         => 'required|exists:lessons,id',
            'title'             => 'required|string|max:255',
            'pass_mark'         => 'integer|min:50|max:100',
            'max_attempts'      => 'integer|min:1|max:10',
            'time_limit_minutes'=> 'nullable|integer|min:5',
        ]);

        $quiz = $this->content->createQuiz($request->only(['lesson_id', 'title', 'pass_mark', 'max_attempts', 'time_limit_minutes']));

        foreach ($request->questions ?? [] as $q) {
            $this->content->addQuestion($quiz->id, $q);
        }

        return back()->with('success', 'تم حفظ الاختبار');
    }

    public function storeQuestion(Request $request, int $quizId): RedirectResponse
    {
        $this->content->addQuestion($quizId, $request->validate([
            'text'    => 'required|string',
            'options' => 'required|array|min:2',
            'correct' => 'required|integer|min:0',
        ]));

        return back()->with('success', 'تم إضافة السؤال');
    }

    public function destroyQuestion(int $id): RedirectResponse
    {
        $this->content->removeQuestion($id);

        return back()->with('success', 'تم حذف السؤال');
    }
}
