<template>
  <Head title="شهاداتي — بارع" />
  <StudentLayout>
    <div class="page-head">
      <h1>شهاداتي 🎓</h1>
      <p>كل شهادة دليل على مهارة أتقنتها — حمّلها وافتخر بها!</p>
    </div>

    <div v-if="certificates.length === 0" class="empty">
      <span class="mi">workspace_premium</span>
      <p>لم تحصل على شهادات بعد. أكمل مسارًا كاملًا لتكسب أول شهادة!</p>
    </div>

    <div v-else class="cert-grid">
      <div v-for="c in certificates" :key="c.id" class="cert-card">
        <div class="cert-ribbon"><span class="mi">verified</span></div>
        <h3>{{ c.path }}</h3>
        <div class="cert-num">{{ c.cert_number }}</div>
        <div class="cert-date"><span class="mi">event</span> {{ c.issued_at }}</div>
        <a v-if="c.has_pdf" :href="route('student.certificates.download', c.id)" class="btn">
          <span class="mi">download</span> تحميل PDF
        </a>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

defineProps({ certificates: { type: Array, default: () => [] } })
</script>

<style scoped>
@import url('https://fonts.googleapis.com/icon?family=Material+Icons+Round');
* { box-sizing:border-box; }
.mi { font-family:'Material Icons Round'; font-style:normal; line-height:1; display:inline-flex; align-items:center; justify-content:center; vertical-align:middle; }

.page-head { margin-bottom:1.6rem; }
.page-head h1 { font-size:1.6rem; font-weight:800; color:#0F172A; margin-bottom:.3rem; }
.page-head p { color:#64748B; font-weight:600; }

.empty { text-align:center; color:#64748B; font-weight:700; padding:3rem 1rem; }
.empty .mi { font-size:3rem; color:#CBD5E1; display:block; margin-bottom:.8rem; }

.cert-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr)); gap:1.2rem; }
.cert-card { background:#fff; border-radius:24px; padding:1.8rem 1.6rem; border:1.5px solid #E2E8F0; text-align:center;
  position:relative; box-shadow:0 2px 12px rgba(15,23,42,.06); transition:transform .2s; }
.cert-card:hover { transform:translateY(-5px); box-shadow:0 12px 32px rgba(15,23,42,.12); }
.cert-ribbon { width:56px; height:56px; border-radius:50%; background:linear-gradient(135deg,#38BDF8,#EC4899);
  display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; }
.cert-ribbon .mi { font-size:1.8rem; color:#fff; }
.cert-card h3 { font-size:1.15rem; font-weight:800; color:#0F172A; margin-bottom:.5rem; }
.cert-num { font-size:.8rem; color:#94A3B8; font-weight:700; margin-bottom:.4rem; font-family:monospace; }
.cert-date { font-size:.82rem; color:#64748B; font-weight:700; display:flex; align-items:center; justify-content:center; gap:.3rem; margin-bottom:1.2rem; }
.cert-date .mi { font-size:1rem; }
.btn { display:inline-flex; align-items:center; gap:.4rem; padding:.7rem 1.6rem; border-radius:50px; background:#0E7490; color:#fff;
  font-weight:800; font-size:.9rem; text-decoration:none; transition:transform .15s; }
.btn:hover { transform:translateY(-2px); }
.btn .mi { font-size:1.1rem; }
</style>
