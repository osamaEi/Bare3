<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Path;
use App\Models\PaymentTransaction;
use App\Models\Plan;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\Review;
use App\Models\ScormPackage;
use App\Models\StudentBadge;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ────────────────────────────────────
        User::updateOrCreate(['email' => 'admin@bare3.sa'], [
            'name'     => 'مسؤول بارع',
            'password' => Hash::make('admin123456'),
            'role'     => 'admin',
            'is_active'=> true,
            'email_verified_at' => now(),
        ]);

        // ── Teachers ─────────────────────────────────
        $teachers = collect([
            ['name' => 'د. نورة الغامدي',  'email' => 'noura@bare3.sa'],
            ['name' => 'أ. سعد المالكي',   'email' => 'saad@bare3.sa'],
            ['name' => 'أ. ريم الشمري',    'email' => 'reem@bare3.sa'],
        ])->map(fn ($t) => User::updateOrCreate(['email' => $t['email']], [
            'name' => $t['name'], 'password' => Hash::make('password'),
            'role' => 'teacher', 'is_active' => true,
            'email_verified_at' => now(),
        ]));

        // ── Parents ──────────────────────────────────
        $parents = collect([
            ['name' => 'أحمد العمري',   'email' => 'ahmed@bare3.sa'],
            ['name' => 'خالد العتيبي',  'email' => 'khalid@bare3.sa'],
            ['name' => 'منيرة الحربي',  'email' => 'munira@bare3.sa'],
            ['name' => 'فهد الدوسري',   'email' => 'fahad@bare3.sa'],
            ['name' => 'عبدالله السالم','email' => 'abdulla@bare3.sa'],
        ])->map(fn ($p) => User::updateOrCreate(['email' => $p['email']], [
            'name' => $p['name'], 'password' => Hash::make('password'),
            'role' => 'parent', 'is_active' => true,
            'email_verified_at' => now(),
        ]));

        // ── Students ─────────────────────────────────
        $students = collect([
            ['name' => 'سارة الزهراني',  'email' => 'sara@bare3.sa',    'level' => 'primary'],
            ['name' => 'نورة القحطاني',  'email' => 'nora@bare3.sa',    'level' => 'middle'],
            ['name' => 'محمد الشمري',    'email' => 'moh@bare3.sa',     'level' => 'high'],
            ['name' => 'عمر الرشيدي',    'email' => 'omar@bare3.sa',    'level' => 'primary'],
            ['name' => 'لمى السبيعي',    'email' => 'lama@bare3.sa',    'level' => 'middle'],
            ['name' => 'يزيد البقمي',    'email' => 'yazeed@bare3.sa',  'level' => 'high'],
            ['name' => 'دانة الحارثي',   'email' => 'dana@bare3.sa',    'level' => 'primary'],
            ['name' => 'تركي المطيري',   'email' => 'turki@bare3.sa',   'level' => 'middle'],
        ])->map(fn ($s) => User::updateOrCreate(['email' => $s['email']], [
            'name' => $s['name'], 'password' => Hash::make('password'),
            'role' => 'student', 'is_active' => true,
            'email_verified_at' => now(),
        ]));

        // ── Parent → Children links ──────────────────
        // أحمد العمري يصبح ولي أمر لأول ثلاثة طلاب
        $ahmed = $parents->firstWhere('email', 'ahmed@bare3.sa');
        if ($ahmed) {
            $ahmed->children()->syncWithoutDetaching(
                $students->take(3)->mapWithKeys(fn ($s) => [$s->id => ['created_at' => now()]])->all()
            );
        }
        // خالد العتيبي يصبح ولي أمر للطفلين التاليين
        $khalid = $parents->firstWhere('email', 'khalid@bare3.sa');
        if ($khalid) {
            $khalid->children()->syncWithoutDetaching(
                $students->slice(3, 2)->mapWithKeys(fn ($s) => [$s->id => ['created_at' => now()]])->all()
            );
        }

        // ── Paths ────────────────────────────────────
        $pathsData = [
            ['slug' => 'creativity',    'title' => 'مهارة الإبداع والابتكار',         'icon' => 'fa-solid fa-lightbulb',       'color' => '#38BDF8', 'sort_order' => 1],
            ['slug' => 'communication', 'title' => 'مهارة التواصل والإلقاء',           'icon' => 'fa-solid fa-microphone-lines', 'color' => '#EC4899', 'sort_order' => 2],
            ['slug' => 'finance',       'title' => 'مهارة الوعي المالي',               'icon' => 'fa-solid fa-coins',           'color' => '#16A34A', 'sort_order' => 3],
            ['slug' => 'emotional',     'title' => 'الذكاء العاطفي والاجتماعي',        'icon' => 'fa-solid fa-heart-pulse',     'color' => '#F59E0B', 'sort_order' => 4],
            ['slug' => 'digital',       'title' => 'المهارة التقنية والمواطنة الرقمية','icon' => 'fa-solid fa-laptop-code',     'color' => '#8B5CF6', 'sort_order' => 5],
        ];

        $paths = collect($pathsData)->map(fn ($p) => Path::updateOrCreate(['slug' => $p['slug']], array_merge($p, ['is_active' => true])));

        // ── Lessons, Videos, SCORM, Quizzes per path ──
        foreach ($paths as $path) {
            for ($i = 1; $i <= 3; $i++) {
                $lesson = Lesson::updateOrCreate(
                    ['path_id' => $path->id, 'sort_order' => $i],
                    ['title' => "درس $i — {$path->title}", 'grade_level' => 'all', 'is_active' => true]
                );

                Video::updateOrCreate(['lesson_id' => $lesson->id], [
                    'title'            => "فيديو درس $i",
                    'file_path'        => "videos/{$path->slug}/lesson-$i.mp4",
                    'duration_seconds' => rand(300, 900),
                    'watch_threshold'  => 80,
                ]);

                ScormPackage::updateOrCreate(['lesson_id' => $lesson->id], [
                    'title'        => "نشاط تفاعلي $i",
                    'version'      => $i % 2 === 0 ? '2004' : '1.2',
                    'package_path' => "scorm/{$path->slug}/activity-$i.zip",
                    'entry_point'  => 'index.html',
                    'file_size_kb' => rand(2000, 8000),
                    'is_active'    => true,
                ]);

                $quiz = Quiz::updateOrCreate(['lesson_id' => $lesson->id], [
                    'title'       => "اختبار درس $i",
                    'pass_mark'   => 70,
                    'max_attempts'=> 3,
                ]);

                for ($q = 1; $q <= 4; $q++) {
                    $question = QuizQuestion::updateOrCreate(
                        ['quiz_id' => $quiz->id, 'sort_order' => $q],
                        ['text' => "سؤال $q في الاختبار"]
                    );

                    QuizOption::where('question_id', $question->id)->delete();
                    $correct = rand(0, 3);
                    for ($o = 0; $o < 4; $o++) {
                        QuizOption::create([
                            'question_id' => $question->id,
                            'text'        => "خيار " . ($o + 1),
                            'is_correct'  => $o === $correct,
                            'sort_order'  => $o,
                        ]);
                    }
                }
            }

            // Badge per path
            Badge::updateOrCreate(['path_id' => $path->id], [
                'name'  => 'وسام ' . $path->title,
                'image' => "badges/{$path->slug}.svg",
                'type'  => 'path',
            ]);
        }

        // ── Plans ────────────────────────────────────
        Plan::updateOrCreate(['slug' => 'trial'], [
            'name' => 'الباقة التجريبية', 'type' => 'individual',
            'price' => 0, 'billing_cycle' => 'monthly',
            'features' => ['مسار واحد', 'اختبارات', 'شهادة رقمية'],
            'is_active' => true,
        ]);
        $heroplan = Plan::updateOrCreate(['slug' => 'heroes'], [
            'name' => 'باقة الأبطال', 'type' => 'individual',
            'price' => 99, 'billing_cycle' => 'monthly',
            'features' => ['جميع المسارات', 'تقارير ولي الأمر', 'أوسمة', 'إشعارات'],
            'is_active' => true,
        ]);
        $schoolPlan = Plan::updateOrCreate(['slug' => 'schools'], [
            'name' => 'باقة المدارس', 'type' => 'school',
            'price' => 199, 'billing_cycle' => 'monthly',
            'features' => ['لوحة المعلم', 'إدارة فصول', 'تقارير الفصل'],
            'is_active' => true,
        ]);

        // ── Subscriptions & Transactions ─────────────
        $gateways = ['mada', 'tabby', 'tamara'];
        foreach ($students->take(6) as $idx => $student) {
            $plan = $idx < 4 ? $heroplan : $schoolPlan;
            $sub  = Subscription::updateOrCreate(['user_id' => $student->id], [
                'plan_id'    => $plan->id,
                'status'     => 'active',
                'starts_at'  => now()->subMonths(rand(1, 6)),
                'ends_at'    => now()->addMonths(rand(1, 6)),
                'auto_renew' => true,
            ]);

            $tx = PaymentTransaction::create([
                'user_id'        => $student->id,
                'subscription_id'=> $sub->id,
                'gateway'        => $gateways[array_rand($gateways)],
                'gateway_tx_id'  => 'TXN-' . strtoupper(Str::random(10)),
                'amount'         => $plan->price,
                'currency'       => 'SAR',
                'status'         => 'success',
                'created_at'     => now()->subDays(rand(1, 30)),
            ]);
        }

        // ── Enrollments ──────────────────────────────
        foreach ($students as $idx => $student) {
            $path = $paths->get($idx % $paths->count());
            Enrollment::updateOrCreate(
                ['student_id' => $student->id, 'path_id' => $path->id],
                ['grade_level' => 'primary', 'status' => $idx < 3 ? 'completed' : 'active', 'started_at' => now()->subMonths(rand(1, 3))]
            );
        }

        // ── Badges earned ────────────────────────────
        $badge = Badge::first();
        if ($badge) {
            foreach ($students->take(4) as $student) {
                StudentBadge::updateOrCreate(
                    ['student_id' => $student->id, 'badge_id' => $badge->id],
                    ['earned_at' => now()->subDays(rand(1, 14)), 'seen' => true]
                );
            }
        }

        // ── Certificates ─────────────────────────────
        foreach ($students->take(3) as $idx => $student) {
            $enrollment = Enrollment::where('student_id', $student->id)->first();
            if ($enrollment) {
                Certificate::updateOrCreate(['student_id' => $student->id, 'path_id' => $enrollment->path_id], [
                    'enrollment_id' => $enrollment->id,
                    'cert_number'   => 'BARE3-2026-' . str_pad($idx + 1, 5, '0', STR_PAD_LEFT),
                    'issued_at'     => now()->subDays(rand(1, 20)),
                ]);
            }
        }

        // ── Blog ─────────────────────────────────────
        $cats = ['تطوير الذات', 'التعليم', 'الوعي المالي', 'التقنية'];
        foreach ($cats as $cat) {
            BlogCategory::updateOrCreate(['slug' => Str::slug($cat)], ['name' => $cat]);
        }

        $cat = BlogCategory::first();
        $author = User::where('role', 'admin')->first();
        foreach (['لماذا الوعي المالي مهم لأبنائنا؟', 'كيف تنمّي الإبداع عند طفلك؟', '٥ تطبيقات للبرمجة للأطفال'] as $i => $title) {
            BlogPost::updateOrCreate(['slug' => Str::slug($title) . '-' . ($i + 1)], [
                'category_id'  => $cat->id,
                'author_id'    => $author->id,
                'title'        => $title,
                'excerpt'      => 'مقدمة قصيرة عن ' . $title,
                'content'      => '<p>محتوى المقالة هنا...</p>',
                'status'       => 'published',
                'views_count'  => rand(100, 2000),
                'published_at' => now()->subDays(rand(1, 60)),
            ]);
        }

        // ── Reviews ──────────────────────────────────
        $reviewsData = [
            ['name' => 'أحمد العمري',    'role' => 'ولي أمر',     'rating' => 5, 'body' => 'منصة ممتازة وتجربة رائعة لأبنائنا.'],
            ['name' => 'د. نورة الغامدي', 'role' => 'مديرة مدرسة', 'rating' => 5, 'body' => 'لوحة المعلم احترافية جداً وتساعدنا في المتابعة.'],
            ['name' => 'محمد الشمري',    'role' => 'ولي أمر',     'rating' => 5, 'body' => 'شهادة الإنجاز فرحت بنتي كثيراً بها.'],
        ];

        foreach ($reviewsData as $r) {
            Review::updateOrCreate(['name' => $r['name']], array_merge($r, ['is_approved' => true, 'is_featured' => true]));
        }

        $this->command->info('✅ Database seeded successfully!');
    }
}
