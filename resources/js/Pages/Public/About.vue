<script setup>
import { Head } from '@inertiajs/vue3'
import PublicShell from '@/Components/PublicShell.vue'

defineProps({ brand: Object, footer: Object })

const values = [
  { icon: 'fa-solid fa-shield-halved', title: 'بيئة آمنة', desc: 'محتوى مراجَع ومصنّف حسب العمر، بدون إعلانات أو روابط خارجية.' },
  { icon: 'fa-solid fa-lightbulb',     title: 'تعلّم بالإبداع', desc: 'نحوّل التعلّم إلى مغامرة ممتعة تشعل فضول الطفل وخياله.' },
  { icon: 'fa-solid fa-heart',         title: 'شغف ورعاية', desc: 'فريق تربوي متخصص يهتم بكل تفصيلة في رحلة طفلك.' },
  { icon: 'fa-solid fa-chart-line',    title: 'متابعة مستمرة', desc: 'تقارير دورية تمكّن ولي الأمر من متابعة تقدّم طفله أولاً بأول.' },
]

const stats = [
  { val: '+١٢,٠٠٠', label: 'طفل بارع' },
  { val: '+٢٠٠',    label: 'قصة ونشاط' },
  { val: '٥',       label: 'مسارات مهارية' },
  { val: '٤.٩',     label: 'تقييم الأهل' },
]
</script>

<template>
  <Head title="عن المعهد — بارع" />
  <PublicShell :brand="brand" :footer="footer">
    <section class="hero-band">
      <h1>عن معهد بارع</h1>
      <p>منصة تعليمية تفاعلية للأطفال من سن ٤ إلى ١٢ سنة</p>
    </section>

    <section class="about-intro">
      <div class="intro-text">
        <h2>رسالتنا</h2>
        <p>
          في "بارع" نؤمن أن كل طفل يحمل بداخله موهبة فريدة تستحق أن تُكتشف وتُنمّى.
          لذلك صمّمنا منصة تعليمية تجمع بين الخيال والعلم والابتكار، لتحوّل وقت الشاشة
          إلى رحلة تعلّم ممتعة وآمنة تبني مهارات الطفل وثقته بنفسه.
        </p>
        <p>
          نقدّم مسارات مهارية متنوّعة — من الإبداع والتواصل إلى الوعي المالي والذكاء العاطفي
          والمهارات الرقمية — كلها بأسلوب قصصي تفاعلي يحبّه الأطفال.
        </p>
      </div>
      <div class="intro-stats">
        <div v-for="(s, i) in stats" :key="i" class="stat-box">
          <div class="stat-val">{{ s.val }}</div>
          <div class="stat-label">{{ s.label }}</div>
        </div>
      </div>
    </section>

    <section class="values">
      <h2 class="values-title">قيمنا</h2>
      <div class="values-grid">
        <div v-for="(v, i) in values" :key="i" class="value-card">
          <div class="value-icon"><i :class="v.icon"></i></div>
          <h3>{{ v.title }}</h3>
          <p>{{ v.desc }}</p>
        </div>
      </div>
    </section>
  </PublicShell>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@500;700;800&display=swap');

/* ===== HERO ===== */
.hero-band {
  position: relative;
  overflow: hidden;
  text-align: center;
  padding: 5.5rem 2rem 7rem;
  background: linear-gradient(155deg, #FFE9C7 0%, #FFD7EC 48%, #DCF1FF 100%);
}

.hero-band::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 12% 25%, rgba(255,255,255,.55) 0, rgba(255,255,255,0) 8%),
    radial-gradient(circle at 88% 20%, rgba(255,255,255,.5) 0, rgba(255,255,255,0) 7%),
    radial-gradient(circle at 80% 70%, rgba(255,255,255,.45) 0, rgba(255,255,255,0) 9%),
    radial-gradient(circle at 8% 75%, rgba(255,255,255,.4) 0, rgba(255,255,255,0) 6%);
  pointer-events: none;
}

.hero-band::after {
  content: '';
  position: absolute;
  left: 0; right: 0; bottom: -1px;
  height: 64px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 64' preserveAspectRatio='none'%3E%3Cpath d='M0,32 C150,64 350,0 600,28 C850,56 1050,8 1200,32 L1200,64 L0,64 Z' fill='%23F8FAFC'/%3E%3C/svg%3E");
  background-size: 100% 100%;
}

.hero-band h1 {
  position: relative;
  z-index: 1;
  font-family: 'Baloo Bhaijaan 2', sans-serif;
  font-size: clamp(2rem, 4.2vw, 2.9rem);
  font-weight: 800;
  color: #1C1C2E;
  margin-bottom: .7rem;
}

.hero-band p {
  position: relative;
  z-index: 1;
  color: #475569;
  font-size: 1.15rem;
  font-weight: 600;
}

/* ===== ABOUT INTRO ===== */
.about-intro {
  max-width: 1040px;
  margin: 0 auto;
  padding: 3.5rem 2rem 3rem;
  display: grid;
  grid-template-columns: 1.4fr 1fr;
  gap: 3rem;
  align-items: center;
}

.intro-text h2 {
  font-family: 'Baloo Bhaijaan 2', sans-serif;
  font-size: 1.9rem;
  font-weight: 800;
  color: #1C1C2E;
  margin-bottom: 1.1rem;
  position: relative;
  display: inline-block;
}

.intro-text h2::after {
  content: '';
  display: block;
  width: 56px;
  height: 6px;
  border-radius: 50px;
  margin-top: .5rem;
  background: linear-gradient(90deg, #38BDF8, #EC4899);
}

.intro-text p {
  color: #475569;
  line-height: 1.95;
  margin-bottom: 1rem;
  font-size: 1.02rem;
}

.intro-stats {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.1rem;
}

.stat-box {
  position: relative;
  background: #fff;
  border-radius: 22px;
  padding: 1.6rem 1.2rem;
  text-align: center;
  box-shadow: 0 6px 20px rgba(28,28,46,.06);
  border: 2px solid #F1F5F9;
  overflow: hidden;
  transition: transform .25s ease, box-shadow .25s ease;
}

.stat-box::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 5px;
  background: var(--stat-color, #38BDF8);
}

.stat-box:nth-child(1) { --stat-color: #38BDF8; }
.stat-box:nth-child(2) { --stat-color: #EC4899; }
.stat-box:nth-child(3) { --stat-color: #FACC15; }
.stat-box:nth-child(4) { --stat-color: #34D399; }

.stat-box:hover {
  transform: translateY(-4px) rotate(-1deg);
  box-shadow: 0 12px 28px rgba(28,28,46,.1);
}

.stat-val {
  font-family: 'Baloo Bhaijaan 2', sans-serif;
  font-size: 1.85rem;
  font-weight: 800;
  color: var(--stat-color, #38BDF8);
}

.stat-label {
  color: #6B7280;
  font-weight: 700;
  font-size: .85rem;
  margin-top: .35rem;
}

/* ===== VALUES ===== */
.values {
  background: #fff;
  padding: 4rem 2rem 4.5rem;
  position: relative;
}

.values-title {
  text-align: center;
  font-family: 'Baloo Bhaijaan 2', sans-serif;
  font-size: 1.9rem;
  font-weight: 800;
  color: #1C1C2E;
  margin-bottom: .6rem;
}

.values-title::after {
  content: '٭ ٭ ٭';
  display: block;
  text-align: center;
  color: #FACC15;
  font-size: 1rem;
  letter-spacing: .3rem;
  margin-top: .4rem;
  margin-bottom: 1.6rem;
}

.values-grid {
  max-width: 1040px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1.6rem;
}

.value-card {
  --v-color: #38BDF8;
  --v-soft: #E0F4FF;
  text-align: center;
  padding: 2rem 1.3rem;
  border-radius: 24px;
  background: #FAFBFF;
  border: 2px solid #F1F5F9;
  transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
}

.value-card:nth-child(1) { --v-color: #0E7490; --v-soft: #DFF3FF; }
.value-card:nth-child(2) { --v-color: #BE185D; --v-soft: #FCE7F3; }
.value-card:nth-child(3) { --v-color: #A16207; --v-soft: #FEF3C7; }
.value-card:nth-child(4) { --v-color: #047857; --v-soft: #D1FAE5; }

.value-card:hover {
  transform: translateY(-6px);
  border-color: var(--v-soft);
  box-shadow: 0 16px 32px rgba(28,28,46,.08);
}

.value-icon {
  width: 64px;
  height: 64px;
  border-radius: 38% 62% 55% 45% / 50% 45% 55% 50%;
  background: var(--v-soft);
  color: var(--v-color);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  margin: 0 auto 1.1rem;
  transition: transform .3s ease, border-radius .3s ease;
}

.value-card:hover .value-icon {
  transform: scale(1.08) rotate(-6deg);
  border-radius: 50%;
}

.value-card h3 {
  font-family: 'Baloo Bhaijaan 2', sans-serif;
  font-weight: 700;
  font-size: 1.1rem;
  color: #1C1C2E;
  margin-bottom: .5rem;
}

.value-card p {
  color: #6B7280;
  font-size: .9rem;
  line-height: 1.75;
}

@media (max-width: 768px) {
  .about-intro { grid-template-columns: 1fr; }
}

@media (max-width: 480px) {
  .intro-stats { grid-template-columns: 1fr 1fr; gap: .8rem; }
}

@media (prefers-reduced-motion: reduce) {
  .stat-box, .value-card, .value-icon { transition: none; }
}
</style>