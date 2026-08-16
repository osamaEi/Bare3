<template>
  <Head title="المسارات — بارع" />
  <StudentLayout>
    <div class="page-head">
      <h1>اختر مهارتك وابدأ المغامرة</h1>
      <p>كل مسار رحلة من الفيديو والتفاعل والاختبار — أكملها واكسب وسامك!</p>
    </div>

    <div class="paths-grid">
      <div v-for="p in paths" :key="p.id" class="path-card">
        <div class="path-icon" :style="{ background: (p.color || '#7c5cbf') + '22', color: p.color || '#7c5cbf' }">
          <span class="mi">{{ p.icon }}</span>
        </div>
        <h3>{{ p.title }}</h3>
        <p class="path-desc">{{ p.description }}</p>
        <div class="path-meta"><span class="mi">menu_book</span> {{ p.lessons_count }} درس</div>

        <Link v-if="p.enrolled" :href="route('student.journey', p.enrollment_id)" class="btn continue">
          <span class="mi">play_arrow</span> أكمل المسار
        </Link>
        <button v-else class="btn start" @click="openPicker(p)">
          <span class="mi">rocket_launch</span> ابدأ الآن
        </button>
      </div>
    </div>

    <!-- Grade picker modal -->
    <div v-if="picking" class="modal-overlay" @click.self="picking = null">
      <div class="modal">
        <button class="modal-close" @click="picking = null"><span class="mi">close</span></button>
        <h2>اختر مرحلتك الدراسية</h2>
        <p>سنعرض لك الدروس المناسبة لمستواك</p>
        <div class="grade-options">
          <button v-for="g in grades" :key="g.value" class="grade-opt" @click="enroll(g.value)" :disabled="form.processing">
            <span class="mi">{{ g.icon }}</span>
            {{ g.label }}
          </button>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

defineProps({ paths: { type: Array, default: () => [] } })

const grades = [
  { value: 'primary', label: 'ابتدائي', icon: 'child_care' },
  { value: 'middle',  label: 'متوسط',  icon: 'school' },
  { value: 'high',    label: 'ثانوي',  icon: 'menu_book' },
]

const picking = ref(null)
const form = useForm({ grade_level: '' })

const openPicker = (p) => { picking.value = p }

const enroll = (grade) => {
  form.grade_level = grade
  form.post(route('student.paths.enroll', picking.value.id), {
    onSuccess: () => { picking.value = null },
  })
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/icon?family=Material+Icons+Round');

.page-head, .paths-grid, .modal-overlay {
  --sky:#7c5cbf; --sky-dark:#5f4398; --pink:#f0806a; --pink-dark:#c2513c;
  --lime:#4bb5a8; --dark:#1e1b2e; --gray:#6B7280; --border:#f0eef7;
}
* { box-sizing:border-box; }
.mi { font-family:'Material Icons Round'; font-style:normal; line-height:1; display:inline-flex; align-items:center; justify-content:center; vertical-align:middle; }

.page-head { margin-bottom:1.6rem; }
.page-head h1 { font-size:1.6rem; font-weight:800; color:var(--dark); margin-bottom:.3rem; }
.page-head p { color:var(--gray); font-weight:600; }

.paths-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:1.2rem; }
.path-card { background:#fff; border-radius:24px; padding:1.6rem; border:1.5px solid var(--border);
  box-shadow:0 2px 12px rgba(15,23,42,.06); transition:transform .2s, box-shadow .2s; display:flex; flex-direction:column; }
.path-card:hover { transform:translateY(-5px); box-shadow:0 12px 32px rgba(15,23,42,.12); }
.path-icon { width:64px; height:64px; border-radius:18px; display:flex; align-items:center; justify-content:center; margin-bottom:1rem; }
.path-icon .mi { font-size:2rem; }
.path-card h3 { font-size:1.15rem; font-weight:800; color:var(--dark); margin-bottom:.4rem; }
.path-desc { color:var(--gray); font-size:.88rem; line-height:1.6; flex:1; margin-bottom:1rem; }
.path-meta { font-size:.82rem; color:var(--gray); font-weight:700; display:flex; align-items:center; gap:.3rem; margin-bottom:1rem; }
.path-meta .mi { font-size:1rem; }

.btn { display:flex; align-items:center; justify-content:center; gap:.4rem; padding:.8rem; border-radius:50px;
  font-family:inherit; font-weight:800; font-size:.95rem; border:none; cursor:pointer; text-decoration:none; transition:transform .15s; }
.btn:hover { transform:translateY(-2px); }
.btn.start { background:var(--sky); color:#fff; box-shadow:0 4px 0 var(--sky-dark); }
.btn.continue { background:var(--lime); color:#fff; box-shadow:0 4px 0 #2f8a7e; }
.btn .mi { font-size:1.2rem; }

.modal-overlay { position:fixed; inset:0; background:rgba(15,23,42,.55); display:flex; align-items:center; justify-content:center; z-index:100; padding:1rem; }
.modal { background:#fff; border-radius:28px; padding:2.2rem; max-width:440px; width:100%; text-align:center; position:relative; }
.modal-close { position:absolute; top:1rem; left:1rem; background:#f5f4f9; border:none; width:36px; height:36px; border-radius:50%; cursor:pointer; color:var(--gray); }
.modal h2 { font-size:1.4rem; font-weight:800; color:var(--dark); margin-bottom:.4rem; }
.modal p { color:var(--gray); font-weight:600; margin-bottom:1.6rem; }
.grade-options { display:flex; gap:.8rem; }
.grade-opt { flex:1; display:flex; flex-direction:column; align-items:center; gap:.5rem; padding:1.2rem .6rem;
  border:2px solid var(--border); border-radius:18px; background:#F8FAFC; font-family:inherit; font-weight:800; font-size:.95rem;
  color:var(--dark); cursor:pointer; transition:all .18s; }
.grade-opt:hover { border-color:var(--sky); background:var(--sky-light, #f3effb); transform:translateY(-3px); }
.grade-opt .mi { font-size:2rem; color:var(--sky-dark); }

@media(max-width:480px) { .grade-options { flex-direction:column; } }
</style>
