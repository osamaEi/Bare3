<template>
  <AdminLayout page-title="الاشتراكات">
    <div class="page-header">
      <div>
        <h1 class="page-title">إدارة الاشتراكات</h1>
        <p class="page-sub">تابع اشتراكات المستخدمين وأدِر الباقات والحالات</p>
      </div>
      <button class="btn-add" @click="openCreate"><i class="fa-solid fa-plus"></i> اشتراك جديد</button>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat"><div class="stat-val">{{ stats.total }}</div><div class="stat-lbl">إجمالي</div></div>
      <div class="stat ok"><div class="stat-val">{{ stats.active }}</div><div class="stat-lbl">نشط</div></div>
      <div class="stat warn"><div class="stat-val">{{ stats.expired }}</div><div class="stat-lbl">منتهٍ</div></div>
      <div class="stat gray"><div class="stat-val">{{ stats.cancelled }}</div><div class="stat-lbl">ملغى</div></div>
      <div class="stat sky"><div class="stat-val">{{ stats.mrr }} <small>ريال</small></div><div class="stat-lbl">دخل شهري متكرر</div></div>
    </div>

    <!-- Filters -->
    <div class="card filters">
      <input v-model="f.search" class="inp" placeholder="بحث بالاسم أو البريد..." @input="applyFilters" />
      <select v-model="f.status" class="inp" @change="applyFilters">
        <option value="">كل الحالات</option>
        <option value="active">نشط</option>
        <option value="trial">تجريبي</option>
        <option value="expired">منتهٍ</option>
        <option value="cancelled">ملغى</option>
      </select>
      <select v-model="f.plan_id" class="inp" @change="applyFilters">
        <option value="">كل الباقات</option>
        <option v-for="p in plans" :key="p.id" :value="p.id">{{ p.name }}</option>
      </select>
    </div>

    <!-- Table -->
    <div class="card">
      <div v-if="subscriptions.data.length === 0" class="empty">لا توجد اشتراكات</div>
      <table v-else class="tbl">
        <thead>
          <tr><th>المستخدم</th><th>الباقة</th><th>الحالة</th><th>يبدأ</th><th>ينتهي</th><th>متبقٍ</th><th>تجديد</th><th></th></tr>
        </thead>
        <tbody>
          <tr v-for="s in subscriptions.data" :key="s.id">
            <td><div class="u-name">{{ s.user }}</div><div class="u-mail">{{ s.email }}</div></td>
            <td><div class="p-name">{{ s.plan }}</div><div class="p-price">{{ s.price }} ريال / {{ cycleLabel(s.cycle) }}</div></td>
            <td><span class="badge" :class="s.status">{{ statusLabel(s.status) }}</span></td>
            <td class="dt">{{ s.starts_at }}</td>
            <td class="dt">{{ s.ends_at }}</td>
            <td>
              <span v-if="s.status === 'active' && s.days_left !== null" :class="s.days_left < 7 ? 'days-warn' : 'days-ok'">
                {{ s.days_left > 0 ? s.days_left + ' يوم' : 'انتهى' }}
              </span>
              <span v-else class="days-na">—</span>
            </td>
            <td><i class="fa-solid" :class="s.auto_renew ? 'fa-circle-check c-ok' : 'fa-circle-xmark c-na'"></i></td>
            <td class="actions">
              <button class="btn-ic" @click="openEdit(s)"><i class="fa-solid fa-pen"></i></button>
              <button class="btn-ic del" @click="del(s)"><i class="fa-solid fa-trash"></i></button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="subscriptions.links && subscriptions.last_page > 1" class="pagination">
        <Link v-for="(l, i) in subscriptions.links" :key="i" :href="l.url || ''"
              class="pg" :class="{ active: l.active, disabled: !l.url }" v-html="l.label" />
      </div>
    </div>

    <!-- Create / Edit modal -->
    <div v-if="modal" class="modal-bg" @click.self="modal = false">
      <div class="modal">
        <h3 class="modal-title">{{ form.id ? 'تعديل الاشتراك' : 'اشتراك جديد' }}</h3>

        <div class="field" v-if="!form.id">
          <label>المستخدم</label>
          <select v-model="form.user_id" class="inp">
            <option :value="null" disabled>— اختر —</option>
            <option v-for="u in students" :key="u.id" :value="u.id">{{ u.name }} ({{ roleLabel(u.role) }})</option>
          </select>
          <span v-if="form.errors.user_id" class="err">{{ form.errors.user_id }}</span>
        </div>

        <div class="field">
          <label>الباقة</label>
          <select v-model="form.plan_id" class="inp">
            <option :value="null" disabled>— اختر —</option>
            <option v-for="p in plans" :key="p.id" :value="p.id">{{ p.name }} ({{ p.price }} ريال)</option>
          </select>
          <span v-if="form.errors.plan_id" class="err">{{ form.errors.plan_id }}</span>
        </div>

        <div class="grid2">
          <div class="field">
            <label>الحالة</label>
            <select v-model="form.status" class="inp">
              <option value="active">نشط</option>
              <option value="trial">تجريبي</option>
              <option value="expired">منتهٍ</option>
              <option value="cancelled">ملغى</option>
            </select>
          </div>
          <label class="toggle"><input type="checkbox" v-model="form.auto_renew" /> تجديد تلقائي</label>
        </div>

        <div class="grid2">
          <div class="field">
            <label>تاريخ البدء</label>
            <input type="date" v-model="form.starts_at" class="inp" />
          </div>
          <div class="field">
            <label>تاريخ الانتهاء</label>
            <input type="date" v-model="form.ends_at" class="inp" />
            <span v-if="form.errors.ends_at" class="err">{{ form.errors.ends_at }}</span>
          </div>
        </div>

        <div class="modal-actions">
          <button class="btn-cancel" @click="modal = false">إلغاء</button>
          <button class="btn-confirm" :disabled="form.processing" @click="save">حفظ</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  subscriptions: { type: Object, default: () => ({ data: [], links: [] }) },
  plans:         { type: Array, default: () => [] },
  students:      { type: Array, default: () => [] },
  filters:       { type: Object, default: () => ({}) },
  stats:         { type: Object, default: () => ({}) },
})

const f = reactive({ search: props.filters.search ?? '', status: props.filters.status ?? '', plan_id: props.filters.plan_id ?? '' })
let t = null
const applyFilters = () => {
  clearTimeout(t)
  t = setTimeout(() => router.get(route('admin.subscriptions'), f, { preserveState: true, replace: true }), 350)
}

const modal = ref(false)
const form = useForm({ id: null, user_id: null, plan_id: null, status: 'active', starts_at: '', ends_at: '', auto_renew: true })

const openCreate = () => {
  form.reset(); form.clearErrors()
  form.starts_at = new Date().toISOString().slice(0, 10)
  modal.value = true
}
const openEdit = (s) => {
  form.clearErrors()
  form.id = s.id; form.plan_id = planIdByName(s.plan); form.status = s.status
  form.starts_at = s.starts_at; form.ends_at = s.ends_at; form.auto_renew = s.auto_renew
  modal.value = true
}
const planIdByName = (name) => props.plans.find(p => p.name === name)?.id ?? null

const save = () => {
  const opts = { preserveScroll: true, onSuccess: () => { modal.value = false; form.reset() } }
  if (form.id) form.patch(route('admin.subscriptions.update', form.id), opts)
  else form.post(route('admin.subscriptions.store'), opts)
}
const del = (s) => {
  if (confirm(`حذف اشتراك ${s.user}؟`)) router.delete(route('admin.subscriptions.destroy', s.id), { preserveScroll: true })
}

const statusLabel = (s) => ({ active: 'نشط', trial: 'تجريبي', expired: 'منتهٍ', cancelled: 'ملغى' }[s] ?? s)
const cycleLabel = (c) => ({ monthly: 'شهري', yearly: 'سنوي' }[c] ?? c)
const roleLabel = (r) => ({ student: 'طالب', parent: 'ولي أمر' }[r] ?? r)
</script>

<style scoped>
.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
.page-title { font-size: 1.6rem; font-weight: 900; color: #1E293B; }
.page-sub { font-size: .88rem; color: #94A3B8; margin-top: .2rem; }
.btn-add { background: #38BDF8; color: #fff; border: none; font-family: inherit; font-weight: 700; font-size: .88rem; padding: .6rem 1.3rem; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: .4rem; }

.stats-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: .9rem; margin-bottom: 1.2rem; }
.stat { background: #fff; border-radius: 14px; padding: 1rem 1.2rem; box-shadow: 0 1px 8px rgba(0,0,0,.05); border-right: 4px solid #CBD5E1; }
.stat.ok { border-color: #16A34A; } .stat.warn { border-color: #F59E0B; } .stat.gray { border-color: #94A3B8; } .stat.sky { border-color: #38BDF8; }
.stat-val { font-size: 1.5rem; font-weight: 900; color: #1E293B; }
.stat-val small { font-size: .8rem; font-weight: 700; color: #94A3B8; }
.stat-lbl { font-size: .78rem; color: #94A3B8; font-weight: 700; margin-top: .2rem; }

.card { background: white; border-radius: 16px; box-shadow: 0 1px 8px rgba(0,0,0,.05); padding: 1.2rem; margin-bottom: 1.2rem; }
.filters { display: flex; gap: .8rem; flex-wrap: wrap; }
.inp { padding: .6rem .9rem; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; font-size: .88rem; color: #1E293B; }
.filters .inp:first-child { flex: 1; min-width: 200px; }
.inp:focus { outline: none; border-color: #38BDF8; }

.empty { text-align: center; color: #94A3B8; padding: 2.5rem; font-weight: 700; }
.tbl { width: 100%; border-collapse: collapse; }
.tbl th { text-align: right; font-size: .76rem; color: #94A3B8; font-weight: 700; padding: .6rem; border-bottom: 1.5px solid #F1F5F9; white-space: nowrap; }
.tbl td { padding: .7rem .6rem; border-bottom: 1px solid #F1F5F9; font-size: .85rem; }
.u-name, .p-name { font-weight: 800; color: #1E293B; }
.u-mail, .p-price { font-size: .76rem; color: #94A3B8; margin-top: .1rem; }
.dt { color: #475569; white-space: nowrap; }
.badge { font-size: .74rem; font-weight: 800; padding: .2rem .7rem; border-radius: 50px; }
.badge.active { background: #DCFCE7; color: #15803D; }
.badge.trial { background: #E0F2FE; color: #0369A1; }
.badge.expired { background: #FEF3C7; color: #B45309; }
.badge.cancelled { background: #F1F5F9; color: #64748B; }
.days-ok { color: #16A34A; font-weight: 700; } .days-warn { color: #DC2626; font-weight: 800; } .days-na { color: #CBD5E1; }
.c-ok { color: #16A34A; } .c-na { color: #CBD5E1; }
.actions { display: flex; gap: .4rem; }
.btn-ic { background: #F1F5F9; color: #475569; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; }
.btn-ic:hover { background: #E2E8F0; } .btn-ic.del { background: #FEF2F2; color: #DC2626; }

.pagination { display: flex; gap: .3rem; justify-content: center; margin-top: 1.2rem; flex-wrap: wrap; }
.pg { padding: .4rem .8rem; border-radius: 8px; font-size: .82rem; font-weight: 700; color: #475569; background: #F1F5F9; text-decoration: none; }
.pg.active { background: #38BDF8; color: #fff; } .pg.disabled { opacity: .4; pointer-events: none; }

.modal-bg { position: fixed; inset: 0; background: rgba(15,23,42,.5); display: flex; align-items: center; justify-content: center; z-index: 200; padding: 1rem; }
.modal { background: #fff; border-radius: 16px; padding: 1.6rem; width: 100%; max-width: 460px; box-shadow: 0 20px 60px rgba(0,0,0,.2); }
.modal-title { font-size: 1.15rem; font-weight: 900; color: #1E293B; margin-bottom: 1.2rem; }
.field { display: flex; flex-direction: column; gap: .35rem; margin-bottom: .9rem; }
.field label { font-size: .82rem; font-weight: 700; color: #475569; }
.modal .inp { width: 100%; }
.grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: end; }
.toggle { display: flex; align-items: center; gap: .5rem; font-weight: 700; font-size: .85rem; color: #475569; cursor: pointer; padding-bottom: .7rem; }
.toggle input { width: 17px; height: 17px; accent-color: #38BDF8; }
.err { color: #DC2626; font-size: .78rem; font-weight: 700; }
.modal-actions { display: flex; justify-content: flex-end; gap: .6rem; margin-top: .5rem; }
.btn-cancel { background: #F1F5F9; color: #475569; border: none; font-family: inherit; font-weight: 700; font-size: .85rem; padding: .6rem 1.3rem; border-radius: 10px; cursor: pointer; }
.btn-confirm { background: #16A34A; color: #fff; border: none; font-family: inherit; font-weight: 700; font-size: .85rem; padding: .6rem 1.3rem; border-radius: 10px; cursor: pointer; }
.btn-confirm:disabled { opacity: .5; cursor: not-allowed; }

@media (max-width: 900px) { .stats-row { grid-template-columns: repeat(2, 1fr); } .tbl { display: block; overflow-x: auto; } }
@media (max-width: 600px) { .grid2 { grid-template-columns: 1fr; } }
</style>
