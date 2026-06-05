<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Path;
use App\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PathController extends Controller
{
    public function __construct(
        private readonly StudentRepositoryInterface $students,
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Student/Paths', [
            'paths' => $this->students->browsePaths($request->user()),
        ]);
    }

    public function enroll(Request $request, Path $path): RedirectResponse
    {
        $data = $request->validate([
            'grade_level' => 'required|in:primary,middle,high',
        ]);

        $enrollment = $this->students->enroll($request->user(), $path, $data['grade_level']);

        return redirect()->route('student.journey', $enrollment->id);
    }

    public function journey(Request $request, int $enrollment): Response
    {
        $student = $request->user();
        $model = $student->enrollments()->with('path')->findOrFail($enrollment);

        return Inertia::render('Student/Journey', [
            'journey' => $this->students->pathJourney($student, $model),
        ]);
    }
}
