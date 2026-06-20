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
    <div class="blob blob1"></div>
    <div class="blob blob2"></div>

    <div class="card" :class="success ? 'ok' : 'fail'">
      <div class="icon-wrap" :class="success ? 'ok' : 'fail'">
        <i :class="success ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-xmark'"></i>
      </div>

      <h1 class="title">{{ success ? 'تم الدفع بنجاح! 🎉' : 'لم تكتمل عملية الدفع' }}</h1>
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
          <i class="fa-solid fa-house"></i> {{ success ? 'الذهاب للوحة التحكم' : 'العودة للرئيسية' }}
        </a>
        <Link v-if="!success" :href="route('payment.checkout')" class="btn ghost">
          <i class="fa-solid fa-rotate-right"></i> حاول مرة أخرى
        </Link>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@500;700;800&display=swap');

.result-page {
  min-height: 100vh;
  display: flex; align-items: center; justify-content: center;
  padding: 1.5rem; position: relative; overflow: hidden;
  font-family: 'ThmanyahSans', 'Baloo Bhaijaan 2', 'Segoe UI', Tahoma, sans-serif;
  background: linear-gradient(135deg, #F5F3FF 0%, #E0F4FF 50%, #FCE7F3 100%);
}
.blob { position: absolute; border-radius: 50%; pointer-events: none; }
.blob1 { width: 360px; height: 360px; background: #38BDF8; opacity: .12; top: -90px; right: -90px; }
.blob2 { width: 300px; height: 300px; background: #EC4899; opacity: .12; bottom: -80px; left: -80px; }

.card {
  position: relative; z-index: 1; background: #fff; width: 100%; max-width: 460px;
  border-radius: 28px; padding: 2.6rem 2.2rem; text-align: center;
  box-shadow: 0 20px 60px rgba(15,23,42,.15); border-top: 6px solid #38BDF8;
}
.card.ok { border-top-color: #16A34A; }
.card.fail { border-top-color: #DC2626; }

.icon-wrap {
  width: 88px; height: 88px; border-radius: 50%; margin: 0 auto 1.3rem;
  display: flex; align-items: center; justify-content: center; font-size: 2.6rem;
}
.icon-wrap.ok { background: #F0FDF4; color: #16A34A; }
.icon-wrap.fail { background: #FEF2F2; color: #DC2626; }

.title { font-size: 1.5rem; font-weight: 800; color: #1C1C2E; margin-bottom: .6rem; }
.sub { color: #64748B; font-size: .95rem; line-height: 1.8; margin-bottom: 1.6rem; }

.details { background: #F8FAFC; border-radius: 16px; padding: 1.1rem 1.3rem; margin-bottom: 1.6rem; }
.row { display: flex; justify-content: space-between; align-items: center; padding: .5rem 0; font-size: .9rem; border-bottom: 1px dashed #E2E8F0; }
.row:last-child { border-bottom: none; }
.row span { color: #94A3B8; font-weight: 600; }
.row b { color: #1E293B; font-weight: 800; }
.mono { font-family: ui-monospace, monospace; font-size: .8rem; }

.actions { display: flex; flex-direction: column; gap: .7rem; }
.btn {
  display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
  padding: .85rem 1.5rem; border-radius: 50px; font-weight: 800; font-size: .95rem;
  text-decoration: none; cursor: pointer; border: none; font-family: inherit;
}
.btn.primary { background: #38BDF8; color: #fff; box-shadow: 0 5px 0 #0E7490; }
.btn.primary:hover { transform: translateY(-2px); }
.btn.ghost { background: #F1F5F9; color: #475569; }
.btn.ghost:hover { background: #E2E8F0; }
</style>
