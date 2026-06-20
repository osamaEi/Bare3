<template>
  <AdminLayout page-title="الباقات">
    <div class="page-header">
      <div>
        <h1 class="page-title">إدارة الباقات</h1>
        <p class="page-sub">أضف وعدّل باقات الاشتراك وأسعارها ومميزاتها</p>
      </div>
      <button class="btn-add" @click="openCreate"><i class="fa-solid fa-plus"></i> باقة جديدة</button>
    </div>

    <div v-if="plans.length === 0" class="empty">لا توجد باقات بعد</div>

    <div v-else class="plans-grid">
      <div v-for="p in plans" :key="p.id" class="plan-card" :class="{ inactive: !p.is_active }">
        <div class="pc-head">
          <div>
            <div class="pc-name">{{ p.name }}</div>
            <span class="pc-type">{{ typeLabel(p.type) }}</span>
          </div>
          <span class="pc-status" :class="p.is_active ? 'on' : 'off'">{{ p.is_active ? 'مفعّلة' : 'معطّلة' }}</span>
        </div>

        <div class="pc-price">{{ p.price }} <span>{{ p.currency }} / {{ cycleLabel(p.billing_cycle) }}</span></div>

        <ul class="pc-features">
          <li v-for="(f, i) in p.features" :key="i"><i class="fa-solid fa-check"></i> {{ f }}</li>
          <li v-if="p.features.length === 0" class="muted">لا توجد مميزات</li>
        </ul>

        <div class="pc-sub-count"><i class="fa-solid fa-users"></i> {{ p.subscribers }} مشترك</div>

        <div class="pc-actions">
          <button class="btn-edit" @click="openEdit(p)"><i class="fa-solid fa-pen"></i> تعديل</button>
          <button class="btn-del" @click="del(p)" :title="p.subscribers > 0 ? 'مرتبطة باشتراكات' : 'حذف'"><i class="fa-solid fa-trash"></i></button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="modal" class="modal-bg" @click.self="modal = false">
      <div class="modal">
        <h3 class="modal-title">{{ form.id ? 'تعديل الباقة' : 'باقة جديدة' }}</h3>

        <div class="field">
          <label>اسم الباقة</label>
          <input v-model="form.name" class="inp" placeholder="مثال: باقة الأبطال" />
          <span v-if="form.errors.name" class="err">{{ form.errors.name }}</span>
        </div>

        <div class="grid2">
          <div class="field">
            <label>النوع</label>
            <select v-model="form.type" class="inp">
              <option value="individual">فردي</option>
              <option value="school">مدارس</option>
            </select>
          </div>
          <div class="field">
            <label>دورة الفوترة</label>
            <select v-model="form.billing_cycle" class="inp">
              <option value="monthly">شهري</option>
              <option value="yearly">سنوي</option>
            </select>
          </div>
          <div class="field">
            <label>السعر</label>
            <input type="number" min="0" step="0.01" v-model.number="form.price" class="inp" />
            <span v-if="form.errors.price" class="err">{{ form.errors.price }}</span>
          </div>
          <div class="field">
            <label>العملة</label>
            <input v-model="form.currency" class="inp" placeholder="SAR" />
          </div>
        </div>

        <div class="field">
          <label>المميزات</label>
          <div v-for="(feat, i) in form.features" :key="i" class="feat-row">
            <input v-model="form.features[i]" class="inp" placeholder="ميزة..." />
            <button class="btn-rm" @click="form.features.splice(i, 1)"><i class="fa-solid fa-xmark"></i></button>
          </div>
          <button class="btn-add-feat" @click="form.features.push('')"><i class="fa-solid fa-plus"></i> إضافة ميزة</button>
        </div>

        <label class="toggle"><input type="checkbox" v-model="form.is_active" /> الباقة مفعّلة</label>

        <div class="modal-actions">
          <button class="btn-cancel" @click="modal = false">إلغاء</button>
          <button class="btn-confirm" :disabled="form.processing" @click="save">حفظ</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ plans: { type: Array, default: () => [] } })

const modal = ref(false)
const form = useForm({ id: null, name: '', type: 'individual', price: 0, currency: 'SAR', billing_cycle: 'monthly', features: [], is_active: true })

const openCreate = () => {
  form.reset(); form.clearErrors()
  form.features = ['']
  modal.value = true
}
const openEdit = (p) => {
  form.clearErrors()
  form.id = p.id; form.name = p.name; form.type = p.type; form.price = p.price
  form.currency = p.currency; form.billing_cycle = p.billing_cycle
  form.features = p.features.length ? [...p.features] : ['']
  form.is_active = p.is_active
  modal.value = true
}
const save = () => {
  const opts = { preserveScroll: true, onSuccess: () => { modal.value = false; form.reset() } }
  if (form.id) form.patch(route('admin.plans.update', form.id), opts)
  else form.post(route('admin.plans.store'), opts)
}
const del = (p) => {
  if (p.subscribers > 0) { alert('لا يمكن حذف باقة مرتبطة باشتراكات. عطّلها بدلاً من ذلك.'); return }
  if (confirm(`حذف باقة "${p.name}"؟`)) router.delete(route('admin.plans.destroy', p.id), { preserveScroll: true })
}

const typeLabel = (t) => ({ individual: 'فردي', school: 'مدارس' }[t] ?? t)
const cycleLabel = (c) => ({ monthly: 'شهري', yearly: 'سنوي' }[c] ?? c)
</script>

<style scoped>
.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
.page-title { font-size: 1.6rem; font-weight: 900; color: #1E293B; }
.page-sub { font-size: .88rem; color: #94A3B8; margin-top: .2rem; }
.btn-add { background: #38BDF8; color: #fff; border: none; font-family: inherit; font-weight: 700; font-size: .88rem; padding: .6rem 1.3rem; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: .4rem; }

.empty { text-align: center; color: #94A3B8; padding: 3rem; font-weight: 700; background: #fff; border-radius: 16px; }
.plans-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.2rem; }
.plan-card { background: #fff; border-radius: 18px; padding: 1.5rem; box-shadow: 0 1px 10px rgba(0,0,0,.06); border: 2px solid #F1F5F9; display: flex; flex-direction: column; }
.plan-card.inactive { opacity: .65; }
.pc-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; }
.pc-name { font-size: 1.2rem; font-weight: 900; color: #1E293B; }
.pc-type { font-size: .74rem; color: #64748B; background: #F1F5F9; padding: .1rem .6rem; border-radius: 6px; font-weight: 700; }
.pc-status { font-size: .72rem; font-weight: 800; padding: .2rem .7rem; border-radius: 50px; }
.pc-status.on { background: #DCFCE7; color: #15803D; }
.pc-status.off { background: #FEF3C7; color: #B45309; }
.pc-price { font-size: 2rem; font-weight: 900; color: #38BDF8; margin-bottom: 1rem; }
.pc-price span { font-size: .85rem; font-weight: 700; color: #94A3B8; }
.pc-features { list-style: none; padding: 0; margin: 0 0 1rem; flex: 1; }
.pc-features li { display: flex; align-items: center; gap: .5rem; font-size: .85rem; color: #475569; padding: .3rem 0; }
.pc-features li i { color: #16A34A; font-size: .75rem; }
.pc-features li.muted { color: #CBD5E1; }
.pc-sub-count { font-size: .8rem; color: #64748B; font-weight: 700; padding: .6rem 0; border-top: 1px dashed #E2E8F0; margin-bottom: .8rem; }
.pc-sub-count i { color: #94A3B8; }
.pc-actions { display: flex; gap: .5rem; }
.btn-edit { flex: 1; background: #EFF6FF; color: #0E7490; border: none; font-family: inherit; font-weight: 700; font-size: .82rem; padding: .55rem; border-radius: 9px; cursor: pointer; }
.btn-del { background: #FEF2F2; color: #DC2626; border: none; width: 38px; border-radius: 9px; cursor: pointer; }

.modal-bg { position: fixed; inset: 0; background: rgba(15,23,42,.5); display: flex; align-items: center; justify-content: center; z-index: 200; padding: 1rem; overflow-y: auto; }
.modal { background: #fff; border-radius: 16px; padding: 1.6rem; width: 100%; max-width: 480px; box-shadow: 0 20px 60px rgba(0,0,0,.2); margin: auto; }
.modal-title { font-size: 1.15rem; font-weight: 900; color: #1E293B; margin-bottom: 1.2rem; }
.field { display: flex; flex-direction: column; gap: .35rem; margin-bottom: .9rem; }
.field label { font-size: .82rem; font-weight: 700; color: #475569; }
.inp { padding: .6rem .9rem; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; font-size: .88rem; color: #1E293B; width: 100%; }
.inp:focus { outline: none; border-color: #38BDF8; }
.grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.feat-row { display: flex; gap: .5rem; margin-bottom: .5rem; }
.btn-rm { background: #FEF2F2; color: #DC2626; border: none; width: 38px; border-radius: 9px; cursor: pointer; flex-shrink: 0; }
.btn-add-feat { background: #EFF6FF; color: #0E7490; border: 1.5px dashed #7DD3F8; font-family: inherit; font-weight: 700; font-size: .8rem; padding: .45rem 1rem; border-radius: 9px; cursor: pointer; }
.toggle { display: flex; align-items: center; gap: .5rem; font-weight: 700; font-size: .85rem; color: #475569; cursor: pointer; margin: .5rem 0 1rem; }
.toggle input { width: 17px; height: 17px; accent-color: #38BDF8; }
.err { color: #DC2626; font-size: .78rem; font-weight: 700; }
.modal-actions { display: flex; justify-content: flex-end; gap: .6rem; }
.btn-cancel { background: #F1F5F9; color: #475569; border: none; font-family: inherit; font-weight: 700; font-size: .85rem; padding: .6rem 1.3rem; border-radius: 10px; cursor: pointer; }
.btn-confirm { background: #16A34A; color: #fff; border: none; font-family: inherit; font-weight: 700; font-size: .85rem; padding: .6rem 1.3rem; border-radius: 10px; cursor: pointer; }
.btn-confirm:disabled { opacity: .5; cursor: not-allowed; }

@media (max-width: 600px) { .grid2 { grid-template-columns: 1fr; } }
</style>
