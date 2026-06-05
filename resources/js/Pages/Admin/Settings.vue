<template>
  <AdminLayout page-title="الإعدادات">
    <div class="page-header">
      <div>
        <h1 class="page-title">إعدادات المنصة</h1>
        <p class="page-sub">تحكّم في الإعدادات العامة والتعليمية والإشعارات</p>
      </div>
    </div>

    <form @submit.prevent="save">
      <!-- General -->
      <div class="card">
        <div class="card-title"><i class="fa-solid fa-sliders"></i> إعدادات عامة</div>
        <div class="grid">
          <div class="field">
            <label>اسم المنصة</label>
            <input type="text" v-model="form.platform_name" class="inp" />
            <span v-if="form.errors.platform_name" class="err">{{ form.errors.platform_name }}</span>
          </div>
          <div class="field">
            <label>البريد الرسمي</label>
            <input type="email" v-model="form.platform_email" class="inp" />
            <span v-if="form.errors.platform_email" class="err">{{ form.errors.platform_email }}</span>
          </div>
        </div>
      </div>

      <!-- Learning -->
      <div class="card">
        <div class="card-title"><i class="fa-solid fa-graduation-cap"></i> الإعدادات التعليمية</div>
        <div class="grid grid-3">
          <div class="field">
            <label>درجة النجاح الافتراضية (٪)</label>
            <input type="number" min="50" max="100" v-model.number="form.pass_mark_default" class="inp" />
          </div>
          <div class="field">
            <label>نسبة مشاهدة الفيديو (٪)</label>
            <input type="number" min="50" max="100" v-model.number="form.video_threshold" class="inp" />
          </div>
          <div class="field">
            <label>أقصى عدد محاولات الاختبار</label>
            <input type="number" min="1" max="10" v-model.number="form.max_quiz_attempts" class="inp" />
          </div>
        </div>
      </div>

      <!-- Notifications -->
      <div class="card">
        <div class="card-title"><i class="fa-solid fa-bell"></i> الإشعارات</div>
        <label class="toggle-row">
          <input type="checkbox" v-model="form.notification_email" />
          <span>إرسال إشعارات عبر البريد الإلكتروني</span>
        </label>
        <label class="toggle-row">
          <input type="checkbox" v-model="form.notification_in_app" />
          <span>إشعارات داخل المنصة</span>
        </label>
      </div>

      <div class="save-bar">
        <span v-if="form.recentlySuccessful" class="saved">✓ تم حفظ الإعدادات</span>
        <button class="btn-save" :disabled="form.processing">
          <i class="fa-solid fa-floppy-disk"></i> حفظ الإعدادات
        </button>
      </div>
    </form>
  </AdminLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ settings: { type: Object, required: true } })

const form = useForm({ ...props.settings })

const save = () => form.post(route('admin.settings.update'), { preserveScroll: true })
</script>

<style scoped>
.page-header { margin-bottom: 1.5rem; }
.page-title { font-size: 1.6rem; font-weight: 900; color: #1E293B; }
.page-sub { font-size: .88rem; color: #94A3B8; margin-top: .2rem; }

.card { background: white; border-radius: 16px; box-shadow: 0 1px 8px rgba(0,0,0,.05); padding: 1.5rem; margin-bottom: 1.2rem; }
.card-title { font-size: 1.05rem; font-weight: 800; color: #1E293B; display: flex; align-items: center; gap: .5rem; margin-bottom: 1.2rem; }
.card-title i { color: #38BDF8; }

.grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.grid-3 { grid-template-columns: repeat(3, 1fr); }
.field { display: flex; flex-direction: column; gap: .4rem; }
.field label { font-size: .82rem; font-weight: 700; color: #475569; }
.inp { padding: .65rem .9rem; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; font-size: .9rem; color: #1E293B; }
.inp:focus { outline: none; border-color: #38BDF8; }
.err { color: #DC2626; font-size: .8rem; font-weight: 700; }

.toggle-row { display: flex; align-items: center; gap: .7rem; padding: .6rem 0; font-weight: 700; color: #475569; font-size: .9rem; cursor: pointer; }
.toggle-row input { width: 18px; height: 18px; accent-color: #38BDF8; cursor: pointer; }

.save-bar { display: flex; align-items: center; justify-content: flex-end; gap: 1rem; }
.saved { color: #16A34A; font-weight: 800; font-size: .88rem; }
.btn-save { background: #38BDF8; color: white; border: none; font-family: inherit; font-weight: 700; font-size: .92rem; padding: .75rem 1.8rem; border-radius: 12px; cursor: pointer; display: flex; align-items: center; gap: .5rem; }
.btn-save:disabled { opacity: .5; cursor: not-allowed; }

@media (max-width: 768px) { .grid, .grid-3 { grid-template-columns: 1fr; } }
</style>
