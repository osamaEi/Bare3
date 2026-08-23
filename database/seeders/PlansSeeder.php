<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Database\Seeder;

/**
 * يزرع باقات بارِع المعتمدة (المفردة / المسار الكامل / السنوي الشامل / العائلية).
 *
 * مهم: لا نحذف الباقات القديمة حذفاً نهائياً لأن جدول subscriptions
 * مرتبط بها عبر cascadeOnDelete — أي حذف يمسح اشتراكات العملاء الحاليين.
 * لذلك نُعطّل (is_active = false) أي باقة قديمة لها اشتراكات،
 * ونحذف فقط الباقات القديمة غير المستخدمة.
 */
class PlansSeeder extends Seeder
{
    public function run(): void
    {
        $slugs = collect($this->plans())->pluck('slug')->all();

        // 1) أنشئ/حدّث الباقات المعتمدة
        foreach ($this->plans() as $data) {
            Plan::updateOrCreate(['slug' => $data['slug']], $data);
        }

        // 2) تعامل مع الباقات القديمة
        Plan::whereNotIn('slug', $slugs)->get()->each(function (Plan $old) {
            $inUse = Subscription::where('plan_id', $old->id)->exists();

            if ($inUse) {
                // لها اشتراكات — نُخفيها فقط للحفاظ على سجلات العملاء
                $old->update(['is_active' => false]);
                $this->command?->warn("تم تعطيل الباقة القديمة (لها اشتراكات): {$old->slug}");
            } else {
                $old->delete();
                $this->command?->info("تم حذف الباقة القديمة غير المستخدمة: {$old->slug}");
            }
        });
    }

    private function plans(): array
    {
        return [
            [
                'slug'          => 'single-topic',
                'name'          => 'الباقة المفردة',
                'type'          => 'individual',
                'price'         => 29,
                'currency'      => 'SAR',
                'billing_cycle' => 'monthly',
                'is_active'     => true,
                'features'      => [
                    'وصول لمحتوى الموضوع المختار.',
                    'اختبارات تفاعلية وتقييم فوري عند إكمال الدرس.',
                    'شهادة إنجاز رقمية عند إتمام الموضوع.',
                ],
            ],
            [
                'slug'          => 'full-path',
                'name'          => 'المسار الكامل',
                'type'          => 'individual',
                'price'         => 139,
                'currency'      => 'SAR',
                'billing_cycle' => 'monthly',
                'is_active'     => true,
                'features'      => [
                    'وصول لجميع دروس ومحتويات المسار المختار.',
                    'اختبارات تفاعلية لقياس الفهم بعد كل درس.',
                    'أوسمة تحفيزية ولوحة الصدارة في ملف الطالب.',
                    'متابعة التقدّم عبر لوحة ولي الأمر.',
                    'شهادة إتمام مسار رقمية عند الإنجاز.',
                ],
            ],
            [
                'slug'          => 'annual-full',
                'name'          => 'الاشتراك السنوي الشامل',
                'type'          => 'individual',
                'price'         => 499,
                'currency'      => 'SAR',
                'billing_cycle' => 'yearly',
                'is_active'     => true,
                'features'      => [
                    'وصول كامل لجميع المسارات والدروس بالمنصة.',
                    'يتضمن حسابين مستقلين (2 مستخدم).',
                    'اختبارات تفاعلية وأوسمة تحفيزية.',
                    'لوحة ولي الأمر لمتابعة تقارير أداء الأبناء.',
                    'شهادات إنجاز رقمية للموضوعات والمسارات.',
                    'استخدام النقاط المكتسبة في المتجر الرقمي.',
                ],
            ],
            [
                'slug'          => 'family',
                'name'          => 'الباقة العائلية',
                'type'          => 'individual',
                'price'         => 799,
                'currency'      => 'SAR',
                'billing_cycle' => 'yearly',
                'is_active'     => true,
                'features'      => [
                    'وصول لجميع المسارات والدروس بالمنصة.',
                    'تتضمن 4 حسابات مستقلة (4 مستخدمين).',
                    'لوحة ولي الأمر لمتابعة تقدم جميع الأبناء من مكان واحد.',
                    'اختبارات تفاعلية وأوسمة تحفيزية لكل مستخدم.',
                    'شهادات إنجاز رقمية عند إتمام المسارات.',
                    'استخدام النقاط المكتسبة في المتجر الرقمي.',
                ],
            ],
        ];
    }
}
