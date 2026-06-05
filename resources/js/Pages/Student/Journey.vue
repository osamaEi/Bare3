<template>
  <Head :title="journey.enrollment.title + ' — بارع'" />
  <StudentLayout>
    <div class="journey-head" :style="{ '--accent': journey.enrollment.color || '#38BDF8' }">
      <Link :href="route('student.paths')" class="back"><span class="mi">arrow_forward_ios</span> المسارات</Link>
      <h1>{{ journey.enrollment.title }}</h1>
      <div class="prog-row">
        <div class="prog-bar"><div class="prog-fill" :style="{ width: journey.enrollment.progress + '%' }"></div></div>
        <span class="prog-pct">{{ journey.enrollment.progress }}٪</span>
      </div>
    </div>

    <div class="lessons">
      <component
        :is="l.status === 'locked' ? 'div' : 'a'"
        v-for="(l, i) in journey.lessons"
        :key="l.id"
        :href="l.status === 'locked' ? null : route('student.lesson', l.id)"
        class="lesson-row"
        :class="l.status"
      >
        <div class="lesson-num">{{ i + 1 }}</div>
        <div class="lesson-info">
          <div class="lesson-title">{{ l.title }}</div>
          <div class="lesson-state">
            <template v-if="l.status === 'completed'"><span class="mi">check_circle</span> مكتمل</template>
            <template v-else-if="l.status === 'locked'"><span class="mi">lock</span> مقفل</template>
            <template v-else><span class="mi">play_circle</span> متاح الآن</template>
          </div>
        </div>
        <span class="mi chevron">{{ l.status === 'locked' ? 'lock' : 'chevron_left' }}</span>
      </component>
    </div>
  </StudentLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

defineProps({ journey: { type: Object, required: true } })
</script>

<style scoped>
@import url('https://fonts.googleapis.com/icon?family=Material+Icons+Round');
* { box-sizing:border-box; }
.mi { font-family:'Material Icons Round'; font-style:normal; line-height:1; display:inline-flex; align-items:center; justify-content:center; vertical-align:middle; }

.journey-head { background:#fff; border-radius:24px; padding:1.6rem 1.8rem; margin-bottom:1.4rem; border:1.5px solid #E2E8F0; box-shadow:0 2px 12px rgba(15,23,42,.06); }
.back { display:inline-flex; align-items:center; gap:.3rem; color:#64748B; font-weight:700; font-size:.85rem; text-decoration:none; margin-bottom:.7rem; }
.back .mi { font-size:.9rem; }
.journey-head h1 { font-size:1.5rem; font-weight:800; color:#0F172A; margin-bottom:1rem; }
.prog-row { display:flex; align-items:center; gap:.8rem; }
.prog-bar { flex:1; height:10px; background:#E2E8F0; border-radius:99px; overflow:hidden; }
.prog-fill { height:100%; border-radius:99px; background:var(--accent); transition:width .8s; }
.prog-pct { font-weight:800; color:var(--accent); font-size:.9rem; }

.lessons { display:flex; flex-direction:column; gap:.7rem; }
.lesson-row { display:flex; align-items:center; gap:1rem; background:#fff; border:1.5px solid #E2E8F0; border-radius:18px;
  padding:1.1rem 1.3rem; text-decoration:none; transition:all .2s; }
.lesson-row.completed, .lesson-row.in_progress { cursor:pointer; }
.lesson-row.in_progress:hover, .lesson-row.completed:hover { border-color:#38BDF8; transform:translateX(-4px); box-shadow:0 6px 20px rgba(15,23,42,.08); }
.lesson-row.locked { opacity:.55; cursor:not-allowed; }
.lesson-num { width:40px; height:40px; border-radius:12px; background:#F1F5F9; color:#64748B; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.lesson-row.completed .lesson-num { background:#F0FDF4; color:#3F6212; }
.lesson-row.in_progress .lesson-num { background:#E0F4FF; color:#0E7490; }
.lesson-info { flex:1; }
.lesson-title { font-weight:800; color:#0F172A; margin-bottom:.2rem; }
.lesson-state { font-size:.8rem; font-weight:700; color:#64748B; display:flex; align-items:center; gap:.3rem; }
.lesson-state .mi { font-size:1rem; }
.lesson-row.completed .lesson-state { color:#3F6212; }
.lesson-row.in_progress .lesson-state { color:#0E7490; }
.chevron { color:#94A3B8; font-size:1.3rem; }
</style>
