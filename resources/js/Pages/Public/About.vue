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
.hero-band { text-align: center; padding: 4rem 2rem 2rem; background: linear-gradient(135deg, #E0F4FF, #FCE7F3); }
.hero-band h1 { font-size: clamp(1.8rem, 4vw, 2.6rem); font-weight: 900; color: #1C1C2E; margin-bottom: .6rem; }
.hero-band p { color: #6B7280; font-size: 1.1rem; }

.about-intro { max-width: 1000px; margin: 0 auto; padding: 3rem 2rem; display: grid; grid-template-columns: 1.4fr 1fr; gap: 2.5rem; align-items: center; }
.intro-text h2 { font-size: 1.8rem; font-weight: 900; color: #1C1C2E; margin-bottom: 1rem; }
.intro-text p { color: #475569; line-height: 1.9; margin-bottom: 1rem; }
.intro-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.stat-box { background: #fff; border-radius: 18px; padding: 1.5rem; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,.05); }
.stat-val { font-size: 1.8rem; font-weight: 900; color: #38BDF8; }
.stat-label { color: #6B7280; font-weight: 700; font-size: .85rem; margin-top: .3rem; }

.values { background: #fff; padding: 3.5rem 2rem; }
.values-title { text-align: center; font-size: 1.8rem; font-weight: 900; color: #1C1C2E; margin-bottom: 2.5rem; }
.values-grid { max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; }
.value-card { text-align: center; padding: 1.8rem 1.2rem; border-radius: 20px; background: #F8FAFC; border: 1px solid #F1F5F9; transition: transform .25s; }
.value-card:hover { transform: translateY(-5px); }
.value-icon { width: 60px; height: 60px; border-radius: 16px; background: #E0F4FF; color: #0E7490; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 1rem; }
.value-card h3 { font-weight: 800; color: #1C1C2E; margin-bottom: .5rem; }
.value-card p { color: #6B7280; font-size: .9rem; line-height: 1.7; }

@media (max-width: 768px) { .about-intro { grid-template-columns: 1fr; } }
</style>
