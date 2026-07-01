<script setup>
import { ref } from 'vue'
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

const activeTab = ref(0)

const privacyItems = [
  {
    icon: 'fa-solid fa-database',
    title: 'جمع البيانات',
    desc: 'نقوم بجمع معلومات محدودة وضرورية فقط لتقديم الخدمة وتحسين تجربة المستخدم، مثل اسم المستخدم أو ولي الأمر، والبريد الإلكتروني، ورقم الجوال، والمرحلة الدراسية، وذلك لتقديم محتوى متوافق مع الفئة العمرية للمشترك.',
  },
  {
    icon: 'fa-solid fa-shield-halved',
    title: 'أمان وحماية الحسابات',
    desc: 'المنصة مصمَّمة لتكون بيئة رقمية آمنة ومحمية بالكامل، تضمن تفاعلاً إيجابياً ومثمراً يحترم خصوصية المشتركين في جميع مراحلهم العمرية.',
  },
  {
    icon: 'fa-solid fa-user-secret',
    title: 'سرية المعلومات',
    desc: 'نلتزم بعدم بيع أو تأجير أو مشاركة بيانات المستخدمين مع أي جهات تجارية أو إعلانية تابعة لأطراف ثالثة، تحت أي ظرف من الظروف.',
  },
  {
    icon: 'fa-solid fa-fingerprint',
    title: 'أمن البيانات',
    desc: 'نطبّق أعلى معايير التشفير والبروتوكولات السيبرانية لضمان حماية البيانات من أي اختراق أو وصول غير مصرّح به.',
  },
]

const termsItems = [
  {
    icon: 'fa-solid fa-copyright',
    title: 'الملكية الفكرية',
    desc: 'جميع محتويات المنصة من نصوص، وسيناريوهات، ورسوم متحركة، ومحتوى مرئي، وشخصيات كرتونية — وعلى رأسها بطل المنصة "بارع" — وألعاب تفاعلية، هي ملكية فكرية حصرية لمؤسسة رؤية بارع، ومحمية بموجب الأنظمة والقوانين المحلية والدولية لحقوق المؤلف والملكية الفكرية. لا يحق لأي مستخدم أو جهة نسخ أو توزيع أو إعادة استخدام هذا المحتوى تجارياً.',
  },
  {
    icon: 'fa-solid fa-house-user',
    title: 'الحساب الشخصي',
    desc: 'الاشتراك في المنصة مخصص للاستخدام العائلي الشخصي، أو للاستخدام التعليمي للمدارس والجامعات وفق رخص خاصة. يتحمّل المستخدم أو ولي الأمر مسؤولية الحفاظ على سرية بيانات الدخول لحسابه.',
  },
  {
    icon: 'fa-solid fa-book-open',
    title: 'جودة وتحديث المحتوى',
    desc: 'تلتزم المنصة بتقديم محتوى تعليمي وتطويري أصيل، حديث، وخالٍ من المعلومات المغلوطة، ومواكِب لجميع المستجدات التقنية والمالية والتربوية التي تتناسب مع مختلف الفئات العمرية والمراحل الدراسية والجامعية.',
  },
  {
    icon: 'fa-solid fa-arrows-rotate',
    title: 'تعديل الشروط',
    desc: 'تحتفظ المنصة بالحق في تحديث شروط الاستخدام وسياسات الأسعار والاشتراكات، بما يضمن تطوير جودة الخدمة، ويتم إشعار المستخدمين بأي تحديثات جوهرية.',
  },
]
</script>

<template>
  <Head title="عن المعهد — بارع" />
  <PublicShell :brand="brand" :footer="footer">
    <section class="hero-band">
      <h1>منصة بارع التعليمية </h1>
      <p>وجهتك الأولى لتمكين الأجيال الناشئة والشباب من مهارات القرن الحادي والعشرين</p>
    </section>

    <section class="about-intro">
      <div class="intro-text">
        <h2>رسالتنا</h2>
        <p>
         تمكين الأجيال من مهارات القرن الـ 21 بأسلوب تفاعلي وقصصي ممتع ومبتكر، يدمج بين المعرفة العلمية والأمان الرقمي، ويسد الفجوة بين التعليم الأكاديمي ومتطلبات المستقبل، بالشراكة الفاعلة مع المربين وأولياء الأمور والجهات التعليمية.
        </p>
        <h2>رؤيتنا</h2>
        <p>

أن نكون المنصة التعليمية الرقمية الأولى
 والأكثر موثوقية في المملكة العربية السعودية والعالم العربي في بناء وتطوير مهارات الحياة 
والمستقبل للأجيال والشباب، في جميع مراحلهم التعليمية لإنشاء جيل مبتكر،
 واعي تقنياً، ومتزن عاطفياً ومالياً.

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

    <div class="tab-switch-wrap">
      <div class="tab-track">
        <div class="tab-thumb" :class="activeTab === 1 ? 'pos-1' : 'pos-0'"></div>
        <button
          class="tab-btn"
          :class="{ active: activeTab === 0 }"
          @click="activeTab = 0"
        >
          <i class="fa-solid fa-lock"></i> سياسة الخصوصية
        </button>
        <button
          class="tab-btn"
          :class="{ active: activeTab === 1 }"
          @click="activeTab = 1"
        >
          <i class="fa-solid fa-file-signature"></i> شروط الاستخدام
        </button>
      </div>
    </div>

    <section class="policy-main">
      <div v-if="activeTab === 0" class="policy-panel">
        <div class="panel-head">
          <div class="panel-icon panel-icon--blue"><i class="fa-solid fa-user-shield"></i></div>
          <h2>سياسة الخصوصية</h2>
          <p>
            نضع خصوصية وأمان مستخدمينا في مقدّمة أولوياتنا، ونلتزم بحماية جميع البيانات
            الشخصية وفق نظام حماية البيانات الشخصية المعمول به في المملكة العربية السعودية.
          </p>
        </div>

        <div class="policy-list">
          <article
            v-for="(item, i) in privacyItems"
            :key="i"
            class="policy-item"
            :class="`policy-item--${i % 4}`"
          >
            <div class="policy-icon"><i :class="item.icon"></i></div>
            <div>
              <h3>{{ item.title }}</h3>
              <p>{{ item.desc }}</p>
            </div>
          </article>
        </div>
      </div>

      <div v-else class="policy-panel">
        <div class="panel-head">
          <div class="panel-icon panel-icon--pink"><i class="fa-solid fa-file-contract"></i></div>
          <h2>شروط وأحكام الاستخدام</h2>
          <p>
            نرحّب بكم في منصة بارع. يخضع استخدامكم للمنصة والاشتراك في مساراتها التفاعلية
            للشروط والأحكام التالية.
          </p>
        </div>

        <div class="policy-list">
          <article
            v-for="(item, i) in termsItems"
            :key="i"
            class="policy-item"
            :class="`policy-item--${i % 4}`"
          >
            <div class="policy-icon"><i :class="item.icon"></i></div>
            <div>
              <h3>{{ item.title }}</h3>
              <p>{{ item.desc }}</p>
            </div>
          </article>
        </div>
      </div>

      <div class="policy-footer">
        <div class="stars">٭ ٭ ٭</div>
        <p class="footer-line"><i class="fa-solid fa-heart"></i> بارع يهتم بخصوصية وسلامة كل طفل وأسرة</p>
        <p class="footer-sub">لأي استفسار حول هذه السياسات يسعدنا تواصلكم مع فريق الدعم</p>
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

/* ===== POLICY TAB SWITCH ===== */
.tab-switch-wrap {
  display: flex;
  justify-content: center;
  padding: 0 1.5rem;
  margin-top: 2.5rem;
  position: relative;
  z-index: 5;
}

.tab-track {
  position: relative;
  display: flex;
  width: 100%;
  max-width: 26rem;
  background: #fff;
  border: 2px solid #F1F5F9;
  border-radius: 999px;
  padding: .25rem;
  box-shadow: 0 10px 28px rgba(28,28,46,.08);
}

.tab-thumb {
  position: absolute;
  top: 4px;
  bottom: 4px;
  width: calc(50% - 4px);
  border-radius: 999px;
  background: linear-gradient(90deg, #38BDF8, #EC4899);
  transition: transform .35s cubic-bezier(.65,0,.35,1);
  z-index: 0;
}

.tab-thumb.pos-0 { transform: translateX(0); }
.tab-thumb.pos-1 { transform: translateX(calc(-100%)); }

.tab-btn {
  position: relative;
  z-index: 1;
  flex: 1;
  padding: .65rem 1rem;
  border-radius: 999px;
  font-family: 'Baloo Bhaijaan 2', sans-serif;
  font-weight: 700;
  font-size: .85rem;
  color: rgba(28,28,46,.55);
  transition: color .3s ease;
  background: transparent;
  border: none;
  cursor: pointer;
}

.tab-btn i { margin-inline-end: .35rem; }
.tab-btn.active { color: #fff; }

/* ===== POLICY MAIN ===== */
.policy-main {
  max-width: 860px;
  margin: 0 auto;
  padding: 3rem 1.5rem 4.5rem;
}

.panel-head {
  text-align: center;
  margin-bottom: 2.5rem;
}

.panel-icon {
  width: 3.5rem;
  height: 3.5rem;
  margin: 0 auto .9rem;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
}

.panel-icon--blue { background: #E0F4FF; color: #38BDF8; }
.panel-icon--pink { background: #FCE7F3; color: #EC4899; }

.panel-head h2 {
  font-family: 'Baloo Bhaijaan 2', sans-serif;
  font-size: 1.7rem;
  font-weight: 800;
  color: #1C1C2E;
  margin-bottom: .6rem;
}

.panel-head p {
  color: #6B7280;
  font-size: .92rem;
  line-height: 1.9;
  max-width: 32rem;
  margin: 0 auto;
}

/* ===== POLICY LIST ===== */
.policy-list {
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
}

.policy-item {
  --p-color: #38BDF8;
  --p-soft: #E0F4FF;
  display: flex;
  align-items: flex-start;
  gap: 1.1rem;
  background: #fff;
  border: 2px solid #F1F5F9;
  border-radius: 24px;
  padding: 1.4rem 1.5rem;
  transition: transform .25s ease, box-shadow .25s ease;
}

.policy-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 16px 32px rgba(28,28,46,.08);
}

.policy-item--0 { --p-color: #38BDF8; --p-soft: #E0F4FF; }
.policy-item--1 { --p-color: #EC4899; --p-soft: #FCE7F3; }
.policy-item--2 { --p-color: #A16207; --p-soft: #FEF3C7; }
.policy-item--3 { --p-color: #34D399; --p-soft: #D1FAE5; }

.policy-icon {
  flex-shrink: 0;
  width: 3.4rem;
  height: 3.4rem;
  border-radius: 38% 62% 55% 45% / 50% 45% 55% 50%;
  background: var(--p-soft);
  color: var(--p-color);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  transition: transform .3s ease, border-radius .3s ease;
}

.policy-item:hover .policy-icon {
  transform: scale(1.08) rotate(-6deg);
  border-radius: 50%;
}

.policy-item h3 {
  font-family: 'Baloo Bhaijaan 2', sans-serif;
  font-weight: 700;
  font-size: 1.05rem;
  color: #1C1C2E;
  margin-bottom: .4rem;
}

.policy-item p {
  color: #475569;
  font-size: .92rem;
  line-height: 1.85;
}

/* ===== POLICY FOOTER ===== */
.policy-footer {
  text-align: center;
  margin-top: 3rem;
}

.stars {
  color: #FACC15;
  font-size: 1rem;
  letter-spacing: .3rem;
  margin-bottom: .8rem;
}

.footer-line {
  font-family: 'Baloo Bhaijaan 2', sans-serif;
  font-weight: 700;
  color: rgba(28,28,46,.7);
  font-size: .95rem;
}

.footer-line i { color: #EC4899; margin-inline-end: .3rem; }

.footer-sub {
  color: #6B7280;
  font-size: .8rem;
  margin-top: .3rem;
}

@media (max-width: 768px) {
  .about-intro { grid-template-columns: 1fr; }
}

@media (max-width: 480px) {
  .intro-stats { grid-template-columns: 1fr 1fr; gap: .8rem; }
  .tab-btn { font-size: .75rem; padding: .6rem .5rem; }
  .tab-btn i { margin-inline-end: .2rem; }
  .policy-item { flex-direction: column; align-items: flex-start; }
}

@media (prefers-reduced-motion: reduce) {
  .stat-box, .value-card, .value-icon, .policy-item, .policy-icon, .tab-thumb { transition: none; }
}
</style>