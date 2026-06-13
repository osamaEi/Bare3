<template>
  <Head :title="'تقرير ' + report.child.name" />
  <ParentLayout page-title="تقرير الأداء">
    <Link :href="route('parent.dashboard')" class="back"><i class="fa-solid fa-chevron-right"></i> الأبناء</Link>

    <div class="child-head">
      <div class="avatar">{{ report.child.name[0] }}</div>
      <div>
        <h1>{{ report.child.name }}</h1>
        <p>{{ report.child.email }}</p>
      </div>
      <div class="head-stats">
        <div class="hs"><span class="n">{{ report.child.badges }}</span><span class="l">أوسمة</span></div>
        <div class="hs"><span class="n">{{ report.child.certificates }}</span><span class="l">شهادات</span></div>
      </div>
    </div>

    <!-- Paths -->
    <div class="card">
      <h3 class="card-title"><i class="fa-solid fa-graduation-cap"></i> المسارات</h3>
      <div v-if="report.paths.length === 0" class="empty">لم يبدأ أي مسار بعد</div>
      <div v-for="p in report.paths" :key="p.id" class="path-row">
        <span class="path-dot" :style="{ background: p.color || '#38BDF8' }"></span>
        <div class="path-info">
          <div class="path-title">{{ p.title }} <span class="grade">{{ gradeLabel(p.grade_level) }}</span></div>
          <div class="bar"><div class="fill" :style="{ width: p.progress + '%', background: p.color || '#38BDF8' }"></div></div>
        </div>
        <span class="path-pct">{{ p.progress }}٪</span>
        <span class="status" :class="p.status">{{ p.status === 'completed' ? 'مكتمل' : 'نشط' }}</span>
      </div>
    </div>

    <!-- Quiz attempts -->
    <div class="card">
      <h3 class="card-title"><i class="fa-solid fa-clipboard-check"></i> نتائج الاختبارات</h3>
      <div v-if="report.quiz_attempts.length === 0" class="empty">لا توجد محاولات اختبار بعد</div>
      <table v-else class="tbl">
        <thead><tr><th>الدرس</th><th>الدرجة</th><th>النتيجة</th><th>التاريخ</th></tr></thead>
        <tbody>
          <tr v-for="(a, i) in report.quiz_attempts" :key="i">
            <td>{{ a.lesson }}</td>
            <td class="score">{{ a.score }}٪</td>
            <td><span class="res" :class="a.passed ? 'pass' : 'fail'">{{ a.passed ? 'ناجح' : 'راسب' }}</span></td>
            <td class="date">{{ a.date }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </ParentLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import ParentLayout from '@/Layouts/ParentLayout.vue'

defineProps({ report: { type: Object, required: true } })
const gradeLabel = (g) => ({ primary: 'ابتدائي', middle: 'متوسط', high: 'ثانوي', all: 'الكل' }[g] ?? g)
</script>

<style scoped>
.back { font-size: .85rem; color: #94A3B8; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: .3rem; margin-bottom: 1rem; }
.child-head { background: #fff; border-radius: 18px; padding: 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 12px rgba(0,0,0,.05); margin-bottom: 1.4rem; flex-wrap: wrap; }
.avatar { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #EC4899, #38BDF8); color: #fff; font-weight: 800; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; }
.child-head h1 { font-size: 1.3rem; font-weight: 800; color: #1E293B; }
.child-head p { font-size: .85rem; color: #94A3B8; }
.head-stats { margin-right: auto; display: flex; gap: 1.5rem; }
.hs { text-align: center; }
.hs .n { display: block; font-size: 1.5rem; font-weight: 800; color: #EC4899; }
.hs .l { font-size: .76rem; color: #64748B; font-weight: 600; }

.card { background: #fff; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 12px rgba(0,0,0,.05); margin-bottom: 1.4rem; }
.card-title { font-size: 1rem; font-weight: 800; color: #1E293B; display: flex; align-items: center; gap: .5rem; margin-bottom: 1.2rem; }
.card-title i { color: #EC4899; }
.empty { color: #94A3B8; text-align: center; padding: 1.5rem; font-weight: 600; }

.path-row { display: flex; align-items: center; gap: .8rem; padding: .8rem 0; border-bottom: 1px solid #F8FAFC; }
.path-row:last-child { border-bottom: none; }
.path-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
.path-info { flex: 1; }
.path-title { font-weight: 700; color: #1E293B; font-size: .9rem; margin-bottom: .4rem; }
.grade { font-size: .72rem; color: #0E7490; background: #E0F4FF; padding: .1rem .5rem; border-radius: 50px; margin-right: .3rem; }
.bar { height: 7px; background: #F1F5F9; border-radius: 99px; overflow: hidden; }
.fill { height: 100%; border-radius: 99px; }
.path-pct { font-weight: 800; color: #1E293B; font-size: .85rem; width: 42px; text-align: left; }
.status { font-size: .72rem; font-weight: 800; padding: .2rem .7rem; border-radius: 50px; }
.status.completed { background: #F0FDF4; color: #15803D; }
.status.active { background: #E0F4FF; color: #0E7490; }

.tbl { width: 100%; border-collapse: collapse; }
.tbl th { text-align: right; padding: .7rem; font-size: .78rem; font-weight: 700; color: #94A3B8; background: #FAFAFA; border-bottom: 1px solid #F1F5F9; }
.tbl td { padding: .8rem .7rem; border-bottom: 1px solid #F8FAFC; font-size: .88rem; color: #1E293B; }
.score { font-weight: 800; }
.date { color: #94A3B8; }
.res { font-size: .74rem; font-weight: 800; padding: .15rem .6rem; border-radius: 50px; }
.res.pass { background: #F0FDF4; color: #15803D; }
.res.fail { background: #FEF2F2; color: #DC2626; }
</style>
