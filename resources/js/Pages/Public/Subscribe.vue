<script setup>
import { Head } from '@inertiajs/vue3'
import Bare3Layout from '@/Layouts/Bare3Layout.vue'

defineProps({
  brand:   Object,
  pricing: { type: Array, default: () => [] },
  footer:  Object,
})
</script>

<template>
  <Head title="الاشتراك — بارع" />
  <Bare3Layout active="subscribe">
    <section class="hero-band">
      <div class="hero-content">
        <span class="eyebrow">ابدأ رحلة تعليمية ممتعة</span>
        <h1>استثمر في مستقبل ابنك.. اختر الباقة المناسبة وابدأ الآن</h1>
        <p>باقات مرنة، محتوى تفاعلي، ومتابعة آمنة تساعد طفلك على النمو بثقة ونموّ متوازن.</p>
        <div class="hero-actions">
          <a href="#plans" class="primary-btn">عرض الباقات</a>
          <a href="#policies" class="secondary-btn">سياسة الخصوصية والشروط</a>
        </div>
      </div>
      <img src="/images/characters/20.png" alt="شخصية بارع" class="hero-char" />
    </section>

    <section id="plans" class="pricing-wrap">
      <div class="section-head">
        <span class="section-tag">الخطط المتاحة</span>
        <h2>اختيار سهل.. ونتيجة واضحة</h2>
        <p>كل باقة تشمل محتوى تعليمي مخصص، أنشطة تفاعلية، ومتابعة سهلة لأولياء الأمور.</p>
      </div>

      <div class="pricing-grid">
        <div v-for="(p, i) in pricing"
             :key="i"
             class="price-card"
             :class="{ featured: p.featured }">

          <div v-if="p.featured" class="pop-badge">
            <i class="fa-solid fa-fire"></i> الأكثر شعبية
          </div>

          <div class="price-badge">{{ p.badge }}</div>

          <div class="price-amount">
            {{ p.amount }} <i class="fa-solid fa-saudi-riyal-symbol"></i> <span class="price-unit">{{ p.unit }}</span>
          </div>

          <div class="price-name">{{ p.name }}</div>

          <ul class="price-features">
            <li v-for="(feat, fi) in p.features" :key="fi">
              <i class="fa-solid fa-circle-check"></i>
              {{ feat }}
            </li>
          </ul>

          <button class="btn-price">{{ p.btn }}</button>
        </div>
      </div>

      <p class="note">يمكنك الإلغاء في أي وقت • آمن للأطفال ١٠٠٪ • دعم سريع ومباشر</p>
    </section>

    <section id="policies" class="policies-wrap">
      <div class="policies-card">
        <div class="section-head compact">
          <span class="section-tag">الحقوق والسياسات</span>
          <h2>سياسة الخصوصية والشروط والأحكام</h2>
          <p>نؤمن بوضوح المعلومات وشفافيتها لضمان تجربة آمنة ومريحة لجميع المستخدمين.</p>
        </div>

        <div class="policy-grid">
          <article>
            <h3>سياسة الخصوصية</h3>
            <p>نقوم بجمع البيانات الضرورية لتقديم الخدمات التعليمية والاحتفاظ بملف مستخدم آمن، مع الالتزام بحماية المعلومات الشخصية وعدم مشاركتها دون موافقة واضحة.</p>
          </article>

          <article>
            <h3>الشروط والأحكام</h3>
            <p>باستخدامك للمنصة فإنك توافق على الالتزام بالقوانين واللوائح المعتمدة، واستخدام المحتوى التعليمي بطريقة مسؤولة، مع احترام حقوق المنصة والمستخدمين الآخرين.</p>
          </article>
        </div>
      </div>
    </section>
  </Bare3Layout>
</template>

<style scoped>
.hero-band {
  position: relative;
  overflow: hidden;
  padding: 5rem 1.5rem 4rem;
  background:
    radial-gradient(circle at 15% 40%, rgba(242,184,102,.28), transparent 45%),
    radial-gradient(circle at 85% 60%, rgba(61,110,165,.28), transparent 45%),
    linear-gradient(135deg, #5f4398, #7c5cbf);
  --brand: #7c5cbf;
  --brand-dark: #5f4398;
  --brand-soft: #f3effb;
  --ink: #1e1b2e;
  --coral: #f0806a;
  --teal: #4bb5a8;
  --amber: #f2b866;
}

/* شخصية بارع بجانب بطاقة الباقات */
.hero-band .hero-char {
  position: absolute;
  bottom: 0;
  inset-inline-start: 2%;
  width: 210px;
  height: auto;
  z-index: 0;
  opacity: .95;
  filter: drop-shadow(0 18px 32px rgba(30, 27, 46, .18));
  pointer-events: none;
}
@media (max-width: 1279px) { .hero-band .hero-char { display: none; } }

.hero-content {
  position: relative;
  z-index: 1;
  max-width: 980px;
  margin: 0 auto;
  text-align: center;
  background: rgba(255,255,255,0.84);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(124, 92, 191, 0.12);
  border-radius: 32px;
  padding: 3rem 1.5rem;
  box-shadow: 0 20px 60px rgba(30, 27, 46, 0.08);
}

.eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 999px;
  background: var(--brand-soft);
  color: var(--brand-dark);
  font-weight: 800;
  font-size: 0.95rem;
  margin-bottom: 1rem;
}

.hero-band h1 {
  font-size: clamp(1.8rem, 4vw, 3rem);
  font-weight: 900;
  color: var(--ink);
  margin: 0 0 1rem;
  line-height: 1.35;
}

.hero-band p {
  color: #5b556f;
  font-size: 1.08rem;
  line-height: 1.9;
  max-width: 720px;
  margin: 0 auto;
}

.hero-actions {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 0.9rem;
  margin-top: 1.8rem;
}

.primary-btn,
.secondary-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.95rem 1.3rem;
  border-radius: 999px;
  font-weight: 800;
  text-decoration: none;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.primary-btn {
  background: linear-gradient(135deg, var(--brand) 0%, var(--coral) 100%);
  color: #fff;
  box-shadow: 0 10px 24px rgba(124, 92, 191, 0.18);
}

.secondary-btn {
  background: #fff;
  color: var(--ink);
  border: 1px solid rgba(124, 92, 191, 0.16);
}

.primary-btn:hover,
.secondary-btn:hover {
  transform: translateY(-2px);
}

.pricing-wrap {
  padding: 4.5rem 1.5rem 3rem;
  background: linear-gradient(135deg, #ffffff 0%, #f8f7ff 100%);
}

.section-head {
  max-width: 780px;
  margin: 0 auto 2rem;
  text-align: center;
}

.section-head.compact { margin-bottom: 1.5rem; }

.section-tag {
  display: inline-block;
  margin-bottom: 0.7rem;
  color: var(--brand-dark);
  font-weight: 800;
  font-size: 0.95rem;
}

.section-head h2 {
  font-size: clamp(1.5rem, 3vw, 2.2rem);
  font-weight: 900;
  color: var(--ink);
  margin: 0 0 0.75rem;
}

.section-head p {
  color: #6b7280;
  line-height: 1.9;
  margin: 0;
}

.pricing-grid {
  max-width: 1120px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
  gap: 1.25rem;
  align-items: stretch;
}

.price-card {
  position: relative;
  background: #fff;
  border-radius: 28px;
  padding: 2rem 1.25rem;
  box-shadow: 0 12px 30px rgba(30, 27, 46, 0.08);
  border: 1px solid #f1ebff;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.price-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 18px 40px rgba(30, 27, 46, 0.12);
}

.price-card.featured {
  background: linear-gradient(135deg, var(--brand) 0%, var(--teal) 100%);
  color: #fff;
  transform: scale(1.02);
}

.price-card.featured:hover {
  transform: scale(1.04) translateY(-4px);
}

.pop-badge {
  position: absolute;
  top: -12px;
  left: 50%;
  transform: translateX(-50%);
  background: #fff4b6;
  color: #7a5a00;
  font-size: 0.78rem;
  font-weight: 800;
  padding: 0.35rem 1rem;
  border-radius: 999px;
  white-space: nowrap;
}

.price-badge {
  display: inline-block;
  font-size: 0.8rem;
  font-weight: 800;
  padding: 0.35rem 0.9rem;
  border-radius: 999px;
  margin-bottom: 0.9rem;
  background: var(--brand-soft);
  color: var(--brand-dark);
}

.price-card.featured .price-badge {
  background: rgba(255,255,255,0.2);
  color: #fff;
}

.price-amount {
  font-size: 3rem;
  font-weight: 900;
  line-height: 1;
}

.price-unit {
  font-size: 1.2rem;
  opacity: 0.8;
}

.price-name {
  font-size: 1.25rem;
  font-weight: 800;
  margin: 0.8rem 0 1.2rem;
}

.price-features {
  list-style: none;
  padding: 0;
  margin: 0 0 1.5rem;
}

.price-features li {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  font-size: 0.96rem;
  padding: 0.4rem 0;
  border-bottom: 1px solid rgba(30, 27, 46, 0.08);
}

.price-card.featured .price-features li {
  border-bottom-color: rgba(255,255,255,0.16);
}

.price-features li i {
  color: var(--teal);
  font-size: 0.78rem;
  flex-shrink: 0;
}

.price-card.featured .price-features li i {
  color: #dcfce7;
}

.btn-price {
  display: block;
  width: 100%;
  padding: 0.95rem 1rem;
  border-radius: 16px;
  border: none;
  cursor: pointer;
  font-weight: 800;
  font-size: 1rem;
  background: var(--brand);
  color: #fff;
}

.price-card.featured .btn-price {
  background: #fff;
  color: var(--ink);
}

.note {
  text-align: center;
  margin-top: 1.8rem;
  color: #6b7280;
  font-size: 0.95rem;
}

.policies-wrap {
  padding: 0 1.5rem 4rem;
}

.policies-card {
  max-width: 1120px;
  margin: 0 auto;
  background: linear-gradient(135deg, #f8f7ff 0%, #f3f8ff 100%);
  border-radius: 32px;
  padding: 2rem 1.25rem;
  box-shadow: 0 12px 35px rgba(30, 27, 46, 0.06);
}

.policy-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1rem;
}

.policy-grid article {
  background: rgba(255,255,255,0.92);
  border-radius: 22px;
  padding: 1.2rem;
  border: 1px solid #e8e1f8;
}

.policy-grid h3 {
  font-size: 1.05rem;
  font-weight: 800;
  margin: 0 0 0.6rem;
  color: var(--ink);
}

.policy-grid p {
  color: #5b556f;
  line-height: 1.8;
  margin: 0;
}

@media (max-width: 768px) {
  .hero-band {
    padding: 4rem 1rem 3rem;
  }

  .hero-content {
    padding: 2rem 1rem;
  }

  .pricing-wrap,
  .policies-wrap {
    padding-inline: 1rem;
  }
}
</style>
