<template>
  <Head title="بارع - ولي الأمر" />
  <ParentLayout page-title="لوحة التحكم">

    <div class="page-head">
      <div>
        <h1 class="title">مرحباً بك في بوابة ولي الأمر 👋</h1>
        <p class="sub">تابع تقدّم أبنائك وأدِر اشتراكاتك من مكان واحد</p>
      </div>
      <button class="btn-add" @click="modal = true"><i class="fa-solid fa-plus"></i> إضافة طفل</button>
    </div>

    <!-- Stats -->
    <div class="stats">
      <div class="stat c-pink"><i class="fa-solid fa-children"></i><div><div class="num">{{ stats.children_count }}</div><div class="lbl">الأبناء</div></div></div>
      <div class="stat c-sky"><i class="fa-solid fa-graduation-cap"></i><div><div class="num">{{ stats.active_paths }}</div><div class="lbl">مسارات نشطة</div></div></div>
      <div class="stat c-amber"><i class="fa-solid fa-medal"></i><div><div class="num">{{ stats.total_badges }}</div><div class="lbl">أوسمة</div></div></div>
      <div class="stat c-lime"><i class="fa-solid fa-certificate"></i><div><div class="num">{{ stats.total_certificates }}</div><div class="lbl">شهادات</div></div></div>
    </div>

    <!-- Children -->
    <h2 class="section-title">أبنائي</h2>
    <div v-if="children.length === 0" class="empty">
      <i class="fa-solid fa-child-reaching"></i>
      <p>لم تُضِف أي طفل بعد. اضغط "إضافة طفل" لإنشاء حساب لابنك.</p>
    </div>
    <div v-else class="children-grid">
      <div v-for="c in children" :key="c.id" class="child-card">
        <div class="child-top">
          <div class="child-avatar">{{ c.avatar_letter }}</div>
          <div>
            <div class="child-name">{{ c.name }}</div>
            <div class="child-email">{{ c.email }}</div>
          </div>
        </div>
        <div class="child-prog">
          <div class="prog-row"><span>متوسط التقدّم</span><span>{{ c.avg_progress }}٪</span></div>
          <div class="bar"><div class="fill" :style="{ width: c.avg_progress + '%' }"></div></div>
        </div>
        <div class="child-meta">
          <span><i class="fa-solid fa-graduation-cap"></i> {{ c.paths_count }} مسار</span>
          <span><i class="fa-solid fa-medal"></i> {{ c.badges }}</span>
          <span><i class="fa-solid fa-certificate"></i> {{ c.certificates }}</span>
        </div>
        <Link :href="route('parent.children.report', c.id)" class="btn-report">
          <i class="fa-solid fa-chart-line"></i> تقرير الأداء
        </Link>
      </div>
    </div>

    <!-- Add child modal -->
    <div v-if="modal" class="modal-overlay" @click.self="modal = false">
      <div class="modal">
        <div class="modal-header"><h3>إضافة طفل</h3><button class="modal-close" @click="modal = false">×</button></div>
        <div class="modal-body">
          <div class="fg"><label>اسم الطفل</label><input v-model="form.name" class="inp" /></div>
          <div class="fg"><label>البريد الإلكتروني</label><input type="email" v-model="form.email" class="inp" /></div>
          <div class="fg"><label>كلمة المرور</label><input type="password" v-model="form.password" class="inp" /></div>
          <div class="fg"><label>الجنس</label>
            <select v-model="form.gender" class="inp"><option value="">—</option><option value="male">ذكر</option><option value="female">أنثى</option></select>
          </div>
          <div v-if="Object.keys(form.errors).length" class="errs">
            <p v-for="(m, k) in form.errors" :key="k">{{ m }}</p>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn-cancel" @click="modal = false">إلغاء</button>
          <button class="btn-save" :disabled="form.processing" @click="addChild">حفظ</button>
        </div>
      </div>
    </div>

  </ParentLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import ParentLayout from '@/Layouts/ParentLayout.vue'

defineProps({
  stats: { type: Object, default: () => ({ children_count: 0, active_paths: 0, total_badges: 0, total_certificates: 0 }) },
  children: { type: Array, default: () => [] },
})

const modal = ref(false)
const form = useForm({ name: '', email: '', password: '', gender: '' })
const addChild = () => form.post(route('parent.children.store'), {
  preserveScroll: true,
  onSuccess: () => { modal.value = false; form.reset() },
})
</script>

<style scoped>
.page-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
.title { font-size: 1.5rem; font-weight: 800; color: #1E293B; margin-bottom: .3rem; }
.sub { color: #64748B; }
.btn-add { background: #EC4899; color: #fff; border: none; font-family: inherit; font-weight: 700; font-size: .9rem; padding: .65rem 1.4rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: .5rem; }

.stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem; }
.stat { background: #fff; border-radius: 16px; padding: 1.3rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 12px rgba(0,0,0,.05); }
.stat i { font-size: 1.6rem; width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; }
.stat.c-pink i { background: #FCE7F3; color: #9D174D; }
.stat.c-sky i { background: #E0F4FF; color: #0E7490; }
.stat.c-amber i { background: #FEF3C7; color: #92400E; }
.stat.c-lime i { background: #F0FDF4; color: #3F6212; }
.stat .num { font-size: 1.7rem; font-weight: 800; color: #1E293B; line-height: 1; }
.stat .lbl { font-size: .8rem; color: #64748B; font-weight: 600; margin-top: .2rem; }

.section-title { font-size: 1.1rem; font-weight: 800; color: #1E293B; margin-bottom: 1rem; }
.empty { background: #fff; border-radius: 16px; padding: 3rem; text-align: center; color: #94A3B8; font-weight: 600; }
.empty i { font-size: 2.5rem; display: block; margin-bottom: .8rem; color: #CBD5E1; }

.children-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.2rem; }
.child-card { background: #fff; border-radius: 18px; padding: 1.4rem; box-shadow: 0 2px 12px rgba(0,0,0,.05); }
.child-top { display: flex; align-items: center; gap: .8rem; margin-bottom: 1rem; }
.child-avatar { width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #EC4899, #38BDF8); color: #fff; font-weight: 800; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; }
.child-name { font-weight: 800; color: #1E293B; }
.child-email { font-size: .8rem; color: #94A3B8; }
.child-prog { margin-bottom: 1rem; }
.prog-row { display: flex; justify-content: space-between; font-size: .8rem; font-weight: 700; color: #64748B; margin-bottom: .35rem; }
.bar { height: 8px; background: #F1F5F9; border-radius: 99px; overflow: hidden; }
.fill { height: 100%; background: linear-gradient(90deg, #EC4899, #38BDF8); border-radius: 99px; }
.child-meta { display: flex; gap: 1rem; font-size: .8rem; color: #64748B; font-weight: 700; margin-bottom: 1.2rem; }
.btn-report { display: flex; align-items: center; justify-content: center; gap: .5rem; background: #F1F5F9; color: #1E293B; font-weight: 700; font-size: .88rem; padding: .65rem; border-radius: 10px; text-decoration: none; transition: background .2s; }
.btn-report:hover { background: #E0F4FF; color: #0E7490; }

.modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); z-index: 200; display: flex; align-items: center; justify-content: center; padding: 1rem; }
.modal { background: #fff; border-radius: 20px; width: 460px; max-width: 95vw; box-shadow: 0 24px 70px rgba(0,0,0,.25); }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1.2rem 1.5rem; border-bottom: 1px solid #F1F5F9; }
.modal-header h3 { font-size: 1.05rem; font-weight: 800; color: #1E293B; }
.modal-close { background: #F1F5F9; border: none; cursor: pointer; font-size: 1.2rem; color: #64748B; width: 32px; height: 32px; border-radius: 50%; }
.modal-body { padding: 1.5rem; }
.modal-footer { padding: 1rem 1.5rem; border-top: 1px solid #F1F5F9; display: flex; justify-content: flex-end; gap: .6rem; }
.fg { display: flex; flex-direction: column; gap: .4rem; margin-bottom: 1rem; }
.fg label { font-size: .82rem; font-weight: 700; color: #475569; }
.inp { padding: .65rem .9rem; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; font-size: .9rem; color: #1E293B; }
.inp:focus { outline: none; border-color: #EC4899; }
.errs { background: #FEF2F2; border: 1.5px solid #FECACA; border-radius: 10px; padding: .7rem 1rem; }
.errs p { color: #DC2626; font-size: .82rem; font-weight: 700; margin: .1rem 0; }
.btn-cancel { background: #F1F5F9; border: none; color: #475569; font-family: inherit; font-weight: 700; padding: .65rem 1.4rem; border-radius: 10px; cursor: pointer; }
.btn-save { background: #EC4899; border: none; color: #fff; font-family: inherit; font-weight: 700; padding: .65rem 1.6rem; border-radius: 10px; cursor: pointer; }
.btn-save:disabled { opacity: .5; cursor: not-allowed; }

@media (max-width: 900px) { .stats { grid-template-columns: repeat(2, 1fr); } }
</style>
