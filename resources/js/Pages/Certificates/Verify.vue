<template>
  <Head title="التحقق من الشهادة — بارع" />
  <div class="verify-page" dir="rtl">
    <div class="card" :class="valid ? 'ok' : 'bad'">
      <div class="icon"><span class="mi">{{ valid ? 'verified' : 'gpp_bad' }}</span></div>

      <template v-if="valid">
        <h1>شهادة موثّقة ✓</h1>
        <p class="sub">هذه الشهادة صادرة رسميًا من منصة بارع</p>
        <div class="details">
          <div class="row"><span>الطالب</span><strong>{{ certificate.student }}</strong></div>
          <div class="row"><span>المسار</span><strong>{{ certificate.path }}</strong></div>
          <div class="row"><span>رقم الشهادة</span><strong>{{ certificate.cert_number }}</strong></div>
          <div class="row"><span>تاريخ الإصدار</span><strong>{{ certificate.issued_at }}</strong></div>
        </div>
      </template>

      <template v-else>
        <h1>شهادة غير صالحة</h1>
        <p class="sub">لم نتمكن من العثور على شهادة بهذا الرقم</p>
      </template>

      <a href="/" class="home">العودة للرئيسية</a>
    </div>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'

defineProps({
  valid:       { type: Boolean, default: false },
  certificate: { type: Object,  default: null },
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@400;600;700;800&display=swap');
@import url('https://fonts.googleapis.com/icon?family=Material+Icons+Round');
* { box-sizing:border-box; margin:0; }
.mi { font-family:'Material Icons Round'; font-style:normal; line-height:1; display:inline-flex; align-items:center; justify-content:center; }

.verify-page { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1.5rem;
  font-family:'Baloo Bhaijaan 2', cursive; background:linear-gradient(135deg,#E0F4FF 0%,#FCE7F3 50%,#F0FDF4 100%); }
.card { background:#fff; border-radius:28px; padding:2.5rem; max-width:460px; width:100%; text-align:center; box-shadow:0 20px 60px rgba(14,116,144,.18); }
.icon { width:80px; height:80px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.2rem; }
.icon .mi { font-size:2.6rem; color:#fff; }
.card.ok .icon { background:linear-gradient(135deg,#84CC16,#3F6212); }
.card.bad .icon { background:linear-gradient(135deg,#EC4899,#9D174D); }
h1 { font-size:1.6rem; font-weight:800; color:#0F172A; margin-bottom:.4rem; }
.sub { color:#64748B; font-weight:600; margin-bottom:1.6rem; }
.details { text-align:right; background:#F8FAFC; border-radius:16px; padding:1.2rem; margin-bottom:1.6rem; }
.row { display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid #E2E8F0; font-size:.92rem; }
.row:last-child { border-bottom:none; }
.row span { color:#64748B; font-weight:600; }
.row strong { color:#0F172A; font-weight:800; }
.home { display:inline-block; padding:.8rem 2rem; border-radius:50px; background:#38BDF8; color:#fff; font-weight:800; text-decoration:none; }
</style>
