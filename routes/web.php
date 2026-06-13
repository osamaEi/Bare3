<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminContentController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\PathController as StudentPathController;
use App\Http\Controllers\Student\LessonController as StudentLessonController;
use App\Http\Controllers\Student\BadgeController as StudentBadgeController;
use App\Http\Controllers\Student\CertificateController as StudentCertificateController;
use App\Http\Controllers\Parent\ParentDashboardController;
use App\Http\Controllers\Parent\ChildController as ParentChildController;
use App\Http\Controllers\Parent\BillingController as ParentBillingController;
use App\Http\Controllers\Parent\TicketController as ParentTicketController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\AdminStudentProgressController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'    => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ── Admin Routes ───────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/',               [AdminUserController::class, 'index'])->name('index');
        Route::post('/',              [AdminUserController::class, 'store'])->name('store');
        Route::put('/{id}',           [AdminUserController::class, 'update'])->name('update');
        Route::delete('/{id}',        [AdminUserController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/status',  [AdminUserController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Content (Paths / Lessons / Videos / SCORM / Quizzes)
    Route::prefix('content')->name('content.')->group(function () {
        Route::get('/',                                [AdminContentController::class, 'index'])->name('index');

        Route::post('/paths',                          [AdminContentController::class, 'storePath'])->name('paths.store');
        Route::put('/paths/{id}',                      [AdminContentController::class, 'updatePath'])->name('paths.update');

        Route::post('/lessons',                        [AdminContentController::class, 'storeLesson'])->name('lessons.store');
        Route::put('/lessons/{id}',                    [AdminContentController::class, 'updateLesson'])->name('lessons.update');
        Route::delete('/lessons/{id}',                 [AdminContentController::class, 'destroyLesson'])->name('lessons.destroy');

        Route::post('/videos',                         [AdminContentController::class, 'storeVideo'])->name('videos.store');
        Route::put('/videos/{id}',                     [AdminContentController::class, 'updateVideo'])->name('videos.update');
        Route::delete('/videos/{id}',                  [AdminContentController::class, 'destroyVideo'])->name('videos.destroy');

        Route::post('/scorm',                          [AdminContentController::class, 'storeScorm'])->name('scorm.store');
        Route::delete('/scorm/{id}',                   [AdminContentController::class, 'destroyScorm'])->name('scorm.destroy');

        Route::post('/quizzes',                        [AdminContentController::class, 'storeQuiz'])->name('quizzes.store');
        Route::put('/quizzes/{id}',                    [AdminContentController::class, 'updateQuiz'])->name('quizzes.update');
        Route::delete('/quizzes/{id}',                 [AdminContentController::class, 'destroyQuiz'])->name('quizzes.destroy');
        Route::post('/quizzes/{quizId}/questions',     [AdminContentController::class, 'storeQuestion'])->name('questions.store');
        Route::delete('/questions/{id}',               [AdminContentController::class, 'destroyQuestion'])->name('questions.destroy');
    });

    // Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/',              [AdminPaymentController::class, 'index'])->name('index');
        Route::get('/{id}',          [AdminPaymentController::class, 'show'])->name('show');
        Route::patch('/{id}/refund', [AdminPaymentController::class, 'refund'])->name('refund');
    });

    // Blog
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/',                   [AdminBlogController::class, 'index'])->name('index');
        Route::post('/',                  [AdminBlogController::class, 'store'])->name('store');
        Route::put('/{id}',               [AdminBlogController::class, 'update'])->name('update');
        Route::delete('/{id}',            [AdminBlogController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/publish',     [AdminBlogController::class, 'publish'])->name('publish');
        Route::patch('/{id}/unpublish',   [AdminBlogController::class, 'unpublish'])->name('unpublish');
    });

    // Settings
    Route::get('/settings',  [AdminSettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');

    // Student Progress
    Route::get('/students/progress', [AdminStudentProgressController::class, 'index'])->name('students.progress');
    Route::get('/students/{student}/progress', [AdminStudentProgressController::class, 'show'])->name('students.progress.show');

    // Tickets
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets');
    Route::post('/tickets/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('tickets.reply');
    Route::patch('/tickets/{ticket}/close', [AdminTicketController::class, 'close'])->name('tickets.close');

    // Legacy aliases (keep sidebar links working)
    Route::get('/users',    [AdminUserController::class,    'index'])->name('users');
    Route::get('/paths',    [AdminContentController::class, 'index'])->name('paths');
    Route::get('/content',  [AdminContentController::class, 'index'])->name('content');
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments');
    Route::get('/blog',     [AdminBlogController::class,    'index'])->name('blog');
});

// ── Student Routes ─────────────────────────────────────────────
Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/', [StudentDashboardController::class, 'index'])->name('dashboard');

    // Paths & enrollment
    Route::get('/paths',                       [StudentPathController::class, 'index'])->name('paths');
    Route::post('/paths/{path}/enroll',        [StudentPathController::class, 'enroll'])->name('paths.enroll');
    Route::get('/journey/{enrollment}',        [StudentPathController::class, 'journey'])->name('journey');

    // Lesson journey
    Route::get('/lessons/{lesson}',            [StudentLessonController::class, 'show'])->name('lesson');
    Route::post('/lessons/video-progress',     [StudentLessonController::class, 'videoProgress'])->name('lesson.video');
    Route::post('/lessons/complete-scorm',     [StudentLessonController::class, 'completeScorm'])->name('lesson.scorm');
    Route::post('/lessons/submit-quiz',        [StudentLessonController::class, 'submitQuiz'])->name('lesson.quiz');

    // Achievements
    Route::get('/badges',                      [StudentBadgeController::class, 'index'])->name('badges');
    Route::get('/certificates',                [StudentCertificateController::class, 'index'])->name('certificates');
    Route::get('/certificates/{certificate}/download', [StudentCertificateController::class, 'download'])->name('certificates.download');
});

// Public certificate verification (QR target)
Route::get('/verify/{certNumber}', [StudentCertificateController::class, 'verify'])->name('certificates.verify');

// ── Parent Routes ──────────────────────────────────────────────
Route::prefix('parent')->name('parent.')->middleware(['auth', 'role:parent'])->group(function () {
    Route::get('/', [ParentDashboardController::class, 'index'])->name('dashboard');
    Route::post('/children', [ParentChildController::class, 'store'])->name('children.store');
    Route::get('/children/{child}/report', [ParentChildController::class, 'report'])->name('children.report');
    Route::get('/billing', [ParentBillingController::class, 'index'])->name('billing');

    // Tickets
    Route::get('/tickets', [ParentTicketController::class, 'index'])->name('tickets');
    Route::post('/tickets', [ParentTicketController::class, 'store'])->name('tickets.store');
    Route::post('/tickets/{ticket}/reply', [ParentTicketController::class, 'reply'])->name('tickets.reply');
    Route::post('/tickets/{ticket}/review', [ParentTicketController::class, 'review'])->name('tickets.review');
});

// ── Teacher Routes ─────────────────────────────────────────────
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/', [TeacherDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
