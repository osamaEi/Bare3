<script setup>
import { Head, Link } from '@inertiajs/vue3'
import PublicShell from '@/Components/PublicShell.vue'

defineProps({
  brand:   Object,
  pricing: { type: Array, default: () => [] },
  footer:  Object,
})
</script>

<template>
  <Head title="الاشتراك — بارع" />
  <PublicShell :brand="brand" :footer="footer">
    <section class="hero-band">
      <h1>اختر باقتك وابدأ رحلة التعلّم</h1>
      <p>باقات مرنة تناسب كل طفل — تعلّم ممتع وتقدّم سريع</p>
    </section>

    <section class="pricing-wrap">
      <div class="pricing-grid">
        <div v-for="(p, i) in pricing" :key="i" class="price-card" :class="{ featured: p.featured }">
          <div v-if="p.featured" class="pop-badge"><i class="fa-solid fa-fire"></i> الأكثر شعبية</div>
          <div class="price-badge">{{ p.badge }}</div>
          <div class="price-amount">{{ p.amount }} <span class="price-unit">{{ p.unit }}</span></div>
          <div class="price-name">{{ p.name }}</div>
          <ul class="price-features">
            <li v-for="(feat, fi) in p.features" :key="fi"><i class="fa-solid fa-circle-check"></i> {{ feat }}</li>
          </ul>
          <Link :href="route('register')" class="btn-price">{{ p.btn }}</Link>
        </div>
      </div>
      <p class="note">يمكنك الإلغاء في أي وقت • آمن للأطفال ١٠٠٪</p>
    </section>
  </PublicShell>
</template>

<style scoped>
.hero-band { text-align: center; padding: 4rem 2rem 2rem; background: linear-gradient(135deg, #E0F4FF, #FCE7F3); }
.hero-band h1 { font-size: clamp(1.8rem, 4vw, 2.6rem); font-weight: 900; color: #1C1C2E; margin-bottom: .6rem; }
.hero-band p { color: #6B7280; font-size: 1.1rem; }

.pricing-wrap { max-width: 1000px; margin: 0 auto; padding: 3rem 2rem; }
.pricing-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; align-items: center; }
.price-card { position: relative; background: #fff; border-radius: 28px; padding: 2.2rem 2rem; box-shadow: 0 4px 24px rgba(0,0,0,.08); border: 2px solid transparent; transition: transform .3s; }
.price-card:hover { transform: translateY(-5px); }
.price-card.featured { background: #38BDF8; color: #fff; transform: scale(1.04); }
.pop-badge { position: absolute; top: -14px; left: 50%; transform: translateX(-50%); background: #BEF264; color: #3F6212; font-size: .78rem; font-weight: 800; padding: .3rem 1.2rem; border-radius: 50px; white-space: nowrap; box-shadow: 0 2px 8px rgba(0,0,0,.15); }
.price-badge { font-size: .8rem; font-weight: 800; padding: .3rem 1rem; border-radius: 50px; display: inline-block; margin-bottom: .8rem; background: #E0F4FF; color: #0E7490; }
.price-card.featured .price-badge { background: rgba(255,255,255,.2); color: #fff; }
.price-amount { font-size: 3.2rem; font-weight: 900; line-height: 1; }
.price-unit { font-size: 1.1rem; opacity: .7; }
.price-name { font-size: 1.3rem; font-weight: 800; margin: .8rem 0 1.4rem; }
.price-features { list-style: none; padding: 0; margin-bottom: 1.8rem; }
.price-features li { display: flex; align-items: center; gap: .6rem; font-size: .92rem; padding: .4rem 0; border-bottom: 1px solid rgba(0,0,0,.05); }
.price-card.featured .price-features li { border-bottom-color: rgba(255,255,255,.15); }
.price-features li i { font-size: .7rem; color: #84CC16; }
.price-card.featured .price-features li i { color: #BEF264; }
.btn-price { display: block; text-align: center; width: 100%; padding: .9rem; border-radius: 16px; font-weight: 800; text-decoration: none; background: #38BDF8; color: #fff; }
.price-card.featured .btn-price { background: #fff; color: #0E7490; }
.note { text-align: center; margin-top: 2rem; color: #6B7280; font-size: .9rem; }
</style>
