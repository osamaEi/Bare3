<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminContentController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AdminSettingsController;
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
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

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

        Route::post('/scorm',                          [AdminContentController::class, 'storeScorm'])->name('scorm.store');
        Route::delete('/scorm/{id}',                   [AdminContentController::class, 'destroyScorm'])->name('scorm.destroy');

        Route::post('/quizzes',                        [AdminContentController::class, 'storeQuiz'])->name('quizzes.store');
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

    // Legacy aliases (keep sidebar links working)
    Route::get('/users',    [AdminUserController::class,    'index'])->name('users');
    Route::get('/paths',    [AdminContentController::class, 'index'])->name('paths');
    Route::get('/content',  [AdminContentController::class, 'index'])->name('content');
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments');
    Route::get('/blog',     [AdminBlogController::class,    'index'])->name('blog');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
