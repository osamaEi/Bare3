<template>
  <AdminLayout page-title="الإشعارات">
    <div class="page-header">
      <div>
        <h1 class="page-title">الإشعارات</h1>
        <p class="page-sub">أرسل إشعارات للطلاب وأولياء الأمور وتابع الأرشيف</p>
      </div>
      <div class="head-stats">
        <div class="hs"><span class="hs-val">{{ stats.total }}</span><span class="hs-lbl">إجمالي</span></div>
        <div class="hs"><span class="hs-val">{{ stats.unread }}</span><span class="hs-lbl">غير مقروء</span></div>
      </div>
    </div>

    <!-- Send form -->
    <div class="card">
      <div class="card-title"><i class="fa-solid fa-paper-plane"></i> إرسال إشعار جديد</div>
      <div class="grid2">
        <div class="field">
          <label>المستقبِل</label>
          <select v-model="form.audience" class="inp">
            <option value="all">الكل (الطلاب + أولياء الأمور)</option>
            <option value="students">كل الطلاب</option>
            <option value="parents">كل أولياء الأمور</option>
            <option value="user">شخص محدد</option>
          </select>
        </div>
        <div class="field" v-if="form.audience === 'user'">
          <label>اختر الشخص</label>
          <select v-model="form.user_id" class="inp">
            <option :value="null" disabled>— اختر —</option>
            <option v-for="r in recipients" :key="r.id" :value="r.id">{{ r.name }} ({{ roleLabel(r.role) }})</option>
          </select>
          <span v-if="form.errors.user_id" class="err">{{ form.errors.user_id }}</span>
        </div>
        <div class="field">
          <label>النوع</label>
          <select v-model="form.type" class="inp">
            <option value="info">معلومة</option>
            <option value="success">نجاح</option>
            <option value="warning">تنبيه</option>
          </select>
        </div>
        <div class="field">
          <label>العنوان</label>
          <input v-model="form.title" class="inp" placeholder="عنوان الإشعار" />
          <span v-if="form.errors.title" class="err">{{ form.errors.title }}</span>
        </div>
      </div>
      <div class="field">
        <label>نص الإشعار</label>
        <textarea v-model="form.body" class="inp" rows="3" placeholder="اكتب الرسالة هنا..."></textarea>
        <span v-if="form.errors.body" class="err">{{ form.errors.body }}</span>
      </div>
      <div class="send-bar">
        <span v-if="form.recentlySuccessful" class="saved">✓ تم الإرسال</span>
        <button class="btn-send" :disabled="form.processing" @click="send">
          <i class="fa-solid fa-paper-plane"></i> {{ form.processing ? 'جاري الإرسال...' : 'إرسال' }}
        </button>
      </div>
    </div>

    <!-- Archive -->
    <div class="card">
      <div class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> الأرشيف</div>
      <div v-if="sent.length === 0" class="empty">لا توجد إشعارات بعد</div>
      <table v-else class="tbl">
        <thead>
          <tr><th>العنوان</th><th>المستقبِل</th><th>النوع</th><th>الحالة</th><th>التاريخ</th><th></th></tr>
        </thead>
        <tbody>
          <tr v-for="n in sent" :key="n.id">
            <td><div class="n-title">{{ n.title }}</div><div class="n-body">{{ n.body }}</div></td>
            <td>{{ n.recipient }} <span class="role-tag">{{ roleLabel(n.role) }}</span></td>
            <td><span class="type-badge" :class="n.type">{{ typeLabel(n.type) }}</span></td>
            <td><span :class="n.read ? 'st-read' : 'st-unread'">{{ n.read ? 'مقروء' : 'غير مقروء' }}</span></td>
            <td class="n-date">{{ n.created_at }}</td>
            <td><button class="btn-del" @click="del(n.id)"><i class="fa-solid fa-trash"></i></button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
  sent:       { type: Array, default: () => [] },
  recipients: { type: Array, default: () => [] },
  stats:      { type: Object, default: () => ({ total: 0, unread: 0 }) },
})

const form = useForm({ audience: 'all', user_id: null, type: 'info', title: '', body: '' })

const send = () => form.post(route('admin.notifications.store'), {
  preserveScroll: true,
  onSuccess: () => form.reset('title', 'body'),
})

const del = (id) => {
  if (confirm('حذف هذا الإشعار؟')) router.delete(route('admin.notifications.destroy', id), { preserveScroll: true })
}

const roleLabel = (r) => ({ student: 'طالب', parent: 'ولي أمر' }[r] ?? r)
const typeLabel = (t) => ({ info: 'معلومة', success: 'نجاح', warning: 'تنبيه' }[t] ?? t)
</script>

<style scoped>
.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
.page-title { font-size: 1.6rem; font-weight: 900; color: #1E293B; }
.page-sub { font-size: .88rem; color: #94A3B8; margin-top: .2rem; }
.head-stats { display: flex; gap: .8rem; }
.hs { background: #fff; border: 1.5px solid #E2E8F0; border-radius: 12px; padding: .6rem 1.2rem; text-align: center; }
.hs-val { display: block; font-size: 1.4rem; font-weight: 900; color: #38BDF8; }
.hs-lbl { font-size: .75rem; color: #94A3B8; font-weight: 700; }

.card { background: white; border-radius: 16px; box-shadow: 0 1px 8px rgba(0,0,0,.05); padding: 1.5rem; margin-bottom: 1.2rem; }
.card-title { font-size: 1.05rem; font-weight: 800; color: #1E293B; display: flex; align-items: center; gap: .5rem; margin-bottom: 1.2rem; }
.card-title i { color: #38BDF8; }

.grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.field { display: flex; flex-direction: column; gap: .35rem; margin-bottom: .8rem; }
.field label { font-size: .8rem; font-weight: 700; color: #475569; }
.inp { padding: .65rem .9rem; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; font-size: .9rem; color: #1E293B; width: 100%; }
.inp:focus { outline: none; border-color: #38BDF8; }
.err { color: #DC2626; font-size: .78rem; font-weight: 700; }

.send-bar { display: flex; align-items: center; justify-content: flex-end; gap: 1rem; margin-top: .5rem; }
.saved { color: #16A34A; font-weight: 800; font-size: .88rem; }
.btn-send { background: #38BDF8; color: white; border: none; font-family: inherit; font-weight: 700; font-size: .92rem; padding: .7rem 1.8rem; border-radius: 12px; cursor: pointer; display: flex; align-items: center; gap: .5rem; }
.btn-send:disabled { opacity: .5; cursor: not-allowed; }

.empty { text-align: center; color: #94A3B8; padding: 2rem; font-weight: 700; }
.tbl { width: 100%; border-collapse: collapse; }
.tbl th { text-align: right; font-size: .78rem; color: #94A3B8; font-weight: 700; padding: .6rem; border-bottom: 1.5px solid #F1F5F9; }
.tbl td { padding: .7rem .6rem; border-bottom: 1px solid #F1F5F9; font-size: .85rem; vertical-align: top; }
.n-title { font-weight: 800; color: #1E293B; }
.n-body { font-size: .8rem; color: #94A3B8; margin-top: .15rem; max-width: 320px; }
.n-date { color: #94A3B8; font-size: .8rem; white-space: nowrap; }
.role-tag { font-size: .72rem; color: #64748B; background: #F1F5F9; padding: .1rem .5rem; border-radius: 6px; }
.type-badge { font-size: .75rem; font-weight: 700; padding: .2rem .7rem; border-radius: 50px; }
.type-badge.info { background: #E0F2FE; color: #0369A1; }
.type-badge.success { background: #DCFCE7; color: #15803D; }
.type-badge.warning { background: #FEF3C7; color: #B45309; }
.st-read { color: #94A3B8; font-weight: 700; font-size: .8rem; }
.st-unread { color: #38BDF8; font-weight: 800; font-size: .8rem; }
.btn-del { background: #FEF2F2; color: #DC2626; border: none; width: 34px; height: 34px; border-radius: 8px; cursor: pointer; }
.btn-del:hover { background: #FEE2E2; }

@media (max-width: 768px) { .grid2 { grid-template-columns: 1fr; } .n-body { max-width: none; } }
</style>
