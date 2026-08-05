<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminContentController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminHomepageController;
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
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminContactController;
use App\Http\Controllers\Admin\AdminSubscriptionController;
use App\Http\Controllers\Admin\AdminPlanController;
use App\Http\Controllers\Student\NotificationController as StudentNotificationController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $defaults = config('homepage');
    $saved = \App\Models\Setting::get('homepage_content');
    $saved = is_string($saved) ? json_decode($saved, true) : $saved;
    $content = is_array($saved) ? array_replace_recursive($defaults, $saved) : $defaults;

    // Convert stored image paths to public URLs.
    $url = fn ($p) => \App\Http\Controllers\Admin\AdminHomepageController::imageUrl($p);
    $content['brand']['logo']   = $url($content['brand']['logo'] ?? null);
    $content['hero']['image']   = $url($content['hero']['image'] ?? null);
    $content['cta']['image_left']  = $url($content['cta']['image_left'] ?? null);
    $content['cta']['image_right'] = $url($content['cta']['image_right'] ?? null);
    $content['footer']['logo']  = $url($content['footer']['logo'] ?? null);
    foreach ($content['features'] as $i => $f) {
        $content['features'][$i]['image'] = $url($f['image'] ?? null);
    }
    foreach ($content['testimonials'] as $i => $t) {
        $content['testimonials'][$i]['avatar'] = $url($t['avatar'] ?? null);
    }

    $latestPosts = \App\Models\BlogPost::published()
        ->with('category:id,name')
        ->orderByDesc('published_at')
        ->limit(3)
        ->get()
        ->map(fn ($p) => [
            'title'    => $p->title,
            'slug'     => $p->slug,
            'excerpt'  => $p->excerpt,
            'category' => $p->category?->name,
        ]);

    return Inertia::render('Welcome', [
        'canLogin'    => Route::has('login'),
        'canRegister' => Route::has('register'),
        'content'     => $content,
        'latestPosts' => $latestPosts,
    ]);
});

// ── Public pages ───────────────────────────────────────────────
Route::get('/subscribe', [PublicPageController::class, 'subscribe'])->name('subscribe');
Route::get('/about',     [PublicPageController::class, 'about'])->name('about');
Route::get('/privacy-policy', [PublicPageController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms', [PublicPageController::class, 'terms'])->name('terms');
Route::get('/contact',   [PublicPageController::class, 'contact'])->name('contact');
Route::post('/contact',  [PublicPageController::class, 'contactStore'])->name('contact.store');
Route::get('/courses',       [PublicPageController::class, 'courses'])->name('courses');
Route::get('/blog',          [PublicPageController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}',   [PublicPageController::class, 'blogShow'])->name('blog.show');

// ── Payments (PayTabs) ─────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/checkout',  [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/checkout/pay', [PaymentController::class, 'pay'])->name('payment.pay');
});
// PayTabs redirects the browser back via POST from its own domain (cross-site,
// no session/CSRF token), so accept both verbs outside the auth group.
Route::match(['get', 'post'], '/payment/return', [PaymentController::class, 'paymentReturn'])->name('payment.return');
// Server-to-server IPN (no auth — PayTabs calls this directly)
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

Route::get('/dashboard', function () {
    return redirect()->route(auth()->user()->homeRoute());
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

    // Homepage content editor
    Route::get('/homepage',  [AdminHomepageController::class, 'index'])->name('homepage');
    Route::post('/homepage', [AdminHomepageController::class, 'update'])->name('homepage.update');

    // Student Progress
    Route::get('/students/progress', [AdminStudentProgressController::class, 'index'])->name('students.progress');
    Route::get('/students/{student}/progress', [AdminStudentProgressController::class, 'show'])->name('students.progress.show');
    Route::post('/students/{student}/grant-badge', [AdminStudentProgressController::class, 'grantBadge'])->name('students.grant-badge');
    Route::post('/students/{student}/grant-certificate', [AdminStudentProgressController::class, 'grantCertificate'])->name('students.grant-certificate');

    // Tickets
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets');
    Route::post('/tickets/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('tickets.reply');
    Route::patch('/tickets/{ticket}/close', [AdminTicketController::class, 'close'])->name('tickets.close');

    // Notifications
    Route::get('/notifications',  [AdminNotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');
    Route::delete('/notifications/{id}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');

    // Contact messages inbox
    Route::get('/contact-messages', [AdminContactController::class, 'index'])->name('contact-messages');
    Route::patch('/contact-messages/{id}/read', [AdminContactController::class, 'markRead'])->name('contact-messages.read');
    Route::delete('/contact-messages/{id}', [AdminContactController::class, 'destroy'])->name('contact-messages.destroy');

    // Plans
    Route::get('/plans',  [AdminPlanController::class, 'index'])->name('plans');
    Route::post('/plans', [AdminPlanController::class, 'store'])->name('plans.store');
    Route::patch('/plans/{plan}', [AdminPlanController::class, 'update'])->name('plans.update');
    Route::delete('/plans/{plan}', [AdminPlanController::class, 'destroy'])->name('plans.destroy');

    // Subscriptions
    Route::get('/subscriptions',  [AdminSubscriptionController::class, 'index'])->name('subscriptions');
    Route::post('/subscriptions', [AdminSubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::patch('/subscriptions/{subscription}', [AdminSubscriptionController::class, 'update'])->name('subscriptions.update');
    Route::delete('/subscriptions/{subscription}', [AdminSubscriptionController::class, 'destroy'])->name('subscriptions.destroy');

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

    // Notifications
    Route::get('/notifications',                [StudentNotificationController::class, 'index'])->name('notifications');
    Route::patch('/notifications/{id}/read',    [StudentNotificationController::class, 'markRead'])->name('notifications.read');
    Route::patch('/notifications/read-all',     [StudentNotificationController::class, 'markAllRead'])->name('notifications.readAll');
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Trigger: seed paths & lessons ──────────────────────────────
// تحذير: يحذف المسارات والدروس القديمة وكل ما يرتبط بها قبل الزرع.
Route::get('/seed-paths', function () {
    Artisan::call('db:seed', [
        '--class' => \Database\Seeders\PathsSeeder::class,
        '--force' => true,
    ]);

    return response(Artisan::output(), 200)->header('Content-Type', 'text/plain');
})->name('seed.paths');

// ── Trigger: fix permissions on uploaded storage files (403 fix) ──
Route::get('/fix-storage-perms', function () {
    $root = storage_path('app/public');
    $out = [];

    if (! is_dir($root)) {
        return response("Missing: $root", 200)->header('Content-Type', 'text/plain');
    }

    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($items as $item) {
        $target = $item->isDir() ? 0755 : 0644;
        $before = substr(sprintf('%o', $item->getPerms()), -4);
        @chmod($item->getPathname(), $target);
        clearstatcache(true, $item->getPathname());
        $after = substr(sprintf('%o', fileperms($item->getPathname())), -4);
        $out[] = sprintf('%s  %s -> %s', str_replace($root, '', $item->getPathname()), $before, $after);
    }

    @chmod($root, 0755);

    return response(implode(PHP_EOL, $out) ?: 'No files found.', 200)
        ->header('Content-Type', 'text/plain');
})->name('storage.fixperms');

// ── Trigger: create the public storage symlink ─────────────────
Route::get('/storage-link', function () {
    if (file_exists(public_path('storage'))) {
        return response('Storage link already exists.', 200)->header('Content-Type', 'text/plain');
    }

    Artisan::call('storage:link');

    return response(Artisan::output(), 200)->header('Content-Type', 'text/plain');
})->name('storage.link');

require __DIR__.'/auth.php';
