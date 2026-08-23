<script setup>
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  success:    { type: Boolean, default: false },
  status:     { type: String, default: '' },
  amount:     { type: Number, default: 0 },
  currency:   { type: String, default: 'SAR' },
  reference:  { type: String, default: '' },
  plan_name:  { type: String, default: null },
  ends_at:    { type: String, default: null },
  home_route: { type: String, default: 'login' },
})

const homeUrl = () => {
  try { return route(props.home_route) } catch (e) { return '/' }
}
const formatDate = (d) => d ? new Date(d).toLocaleDateString('ar-EG') : '—'
</script>

<template>
  <Head :title="success ? 'تم الدفع بنجاح' : 'لم تكتمل العملية'" />

  <div class="result-page" dir="rtl">
    <!-- أشكال 3D تجريدية — نفس هوية الصفحة الرئيسية -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="result-shell">
      <!-- شخصية بارع -->
      <div class="res-char-wrap">
        <div class="res-char-glow"></div>
        <img :src="success ? '/images/characters/26.png' : '/images/characters/09.png'"
             alt="شخصية بارع" class="res-char" />
      </div>

      <div class="card" :class="success ? 'ok' : 'fail'">
        <Link href="/" class="card-logo"><img src="/images/logo.png" alt="بارع" /></Link>

        <div class="icon-wrap" :class="success ? 'ok' : 'fail'">
          <i :class="success ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-xmark'"></i>
        </div>

        <h1 class="title">{{ success ? 'تم الدفع بنجاح!' : 'لم تكتمل عملية الدفع' }}</h1>
        <p class="sub">
          {{ success
            ? 'تم تفعيل الاشتراك وإضافته إلى حسابك. استمتع بالتعلّم!'
            : 'لم يتم خصم أي مبلغ، أو حدثت مشكلة أثناء الدفع. يمكنك المحاولة مرة أخرى.' }}
        </p>

        <div class="details">
          <div v-if="plan_name" class="row"><span>الباقة</span><b>{{ plan_name }}</b></div>
          <div class="row"><span>المبلغ</span><b>{{ amount.toLocaleString('ar-EG') }} <i class="fa-solid fa-saudi-riyal-symbol"></i></b></div>
          <div v-if="success && ends_at" class="row"><span>صالح حتى</span><b>{{ formatDate(ends_at) }}</b></div>
          <div class="row"><span>رقم العملية</span><b class="mono">{{ reference }}</b></div>
        </div>

        <div class="actions">
          <a :href="homeUrl()" class="btn primary">
            <span>{{ success ? 'الذهاب للوحة التحكم' : 'العودة للرئيسية' }}</span>
            <i class="fa-solid fa-arrow-left"></i>
          </a>
          <Link v-if="!success" :href="route('payment.checkout')" class="btn ghost">
            <i class="fa-solid fa-rotate-right"></i>
            <span>حاول مرة أخرى</span>
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* ── هوية بارع — نفس متغيّرات الصفحة الرئيسية ── */
.result-page {
  --brand:#7c5cbf; --brand-dark:#5f4398; --brand-soft:#f3effb; --ink:#1e1b2e;
  --coral:#f0806a; --teal:#4bb5a8; --amber:#f2b866; --navy:#3d6ea5;
  min-height: 100vh;
  display: flex; align-items: center; justify-content: center;
  padding: 2rem 1.5rem; position: relative; overflow: hidden;
  font-family: 'Tajawal', 'Poppins', sans-serif;
  background: #FAFAFB; color: var(--ink);
}

/* أشكال 3D تجريدية (blobs) */
.blob { position:absolute; border-radius:50%; filter:blur(60px); opacity:.5; pointer-events:none; }
.blob-1 { width:420px; height:420px; background:radial-gradient(circle,#c9b4f0,#7c5cbf); top:-120px; right:-80px; }
.blob-2 { width:340px; height:340px; background:radial-gradient(circle,#ffd9b0,#f0806a); bottom:-100px; left:-60px; opacity:.35; }
.blob-3 { width:260px; height:260px; background:radial-gradient(circle,#b8ede6,#4bb5a8); top:45%; left:12%; opacity:.3; }

/* التخطيط: شخصية + بطاقة */
.result-shell {
  position: relative; z-index: 1; width: 100%; max-width: 900px;
  display: grid; grid-template-columns: 1fr; gap: 2.5rem; align-items: center;
}
@media (min-width: 1024px) { .result-shell { grid-template-columns: .8fr 1fr; } }

.res-char-wrap { position: relative; display: none; justify-content: center; }
@media (min-width: 1024px) { .res-char-wrap { display: flex; } }
.res-char {
  position: relative; z-index: 2; width: 100%; max-width: 300px; height: auto;
  filter: drop-shadow(0 24px 40px rgba(30,27,46,.18));
  animation: float-char 6s ease-in-out infinite;
}
.res-char-glow {
  position: absolute; z-index: 1; bottom: 6%; left: 50%; transform: translateX(-50%);
  width: 78%; aspect-ratio: 1/1; border-radius: 50%;
  background: radial-gradient(circle, rgba(124,92,191,.30), transparent 68%); filter: blur(26px);
}
@keyframes float-char { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-16px); } }
@media (prefers-reduced-motion: reduce) { .res-char { animation: none; } }

/* بطاقة زجاجية — نفس أسلوب الصفحة الرئيسية */
.card {
  position: relative; background: rgba(255,255,255,.7); backdrop-filter: blur(16px);
  width: 100%; max-width: 460px; margin: 0 auto;
  border: 1px solid #f0eef7; border-radius: 28px;
  padding: 2.4rem 2.2rem; text-align: center;
  box-shadow: 0 12px 40px rgba(30,27,46,.07);
  border-top: 5px solid var(--brand);
}
.card.ok { border-top-color: var(--teal); }
.card.fail { border-top-color: var(--coral); }

.card-logo { display: block; margin-bottom: 1.2rem; }
.card-logo img { width: 130px; max-width: 60%; }

.icon-wrap {
  width: 84px; height: 84px; border-radius: 50%; margin: 0 auto 1.3rem;
  display: flex; align-items: center; justify-content: center; font-size: 2.5rem;
}
.icon-wrap.ok { background: #eaf7f5; color: var(--teal); }
.icon-wrap.fail { background: #fdeeea; color: var(--coral); }

.title { font-size: 1.6rem; font-weight: 900; color: var(--ink); margin-bottom: .6rem; }
.sub { color: #6B7280; font-size: .95rem; line-height: 1.9; margin-bottom: 1.7rem; }

.details { background: #fafafb; border: 1px solid #f0eef7; border-radius: 18px; padding: 1.1rem 1.3rem; margin-bottom: 1.7rem; }
.row { display: flex; justify-content: space-between; align-items: center; padding: .55rem 0; font-size: .9rem; border-bottom: 1px solid #f3f2f7; }
.row:last-child { border-bottom: none; }
.row span { color: #94A3B8; font-weight: 700; }
.row b { color: var(--ink); font-weight: 800; }
.mono { font-family: ui-monospace, monospace; font-size: .8rem; }

.actions { display: flex; flex-direction: column; gap: .7rem; }
.btn {
  display: inline-flex; align-items: center; justify-content: center; gap: 10px;
  padding: 15px 30px; border-radius: 9999px; font-weight: 700; font-size: 1rem;
  text-decoration: none; cursor: pointer; border: none; font-family: inherit;
  transition: all .25s ease;
}
.btn.primary { background: var(--brand); color: #fff; box-shadow: 0 8px 24px rgba(124,92,191,.28); }
.btn.primary:hover { background: var(--brand-dark); transform: translateY(-2px); }
.btn.ghost { background: #fff; color: var(--ink); border: 2px solid #ece8f5; }
.btn.ghost:hover { border-color: var(--brand); color: var(--brand); }

@media (max-width: 480px) { .card { padding: 2rem 1.4rem; } }
</style>
