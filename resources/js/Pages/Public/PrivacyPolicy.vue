<script setup>
import { Head } from '@inertiajs/vue3'
import PublicShell from '@/Components/PublicShell.vue'

defineProps({
  brand: Object,
  footer: Object,
})
</script>

<template>
  <Head title="سياسة الخصوصية — بارع" />
  <PublicShell :brand="brand" :footer="footer">
    <section class="page-wrap">
      <div class="page-card">
        <span class="eyebrow">سياسة الخصوصية</span>
        <h1>كيف نستخدم بياناتك ونحميها</h1>
        <p class="lead">نحرص على حماية معلوماتك الشخصية وتقديم تجربة تعليمية آمنة وشفافة.</p>

        <div class="content-block">
          <h2>1. المعلومات التي نجمعها</h2>
          <p>نجمع المعلومات الأساسية اللازمة لتفعيل الحساب، مثل الاسم، البريد الإلكتروني، رقم الجوال، ومعلومات الحساب التعليمي عند الحاجة.</p>
        </div>

        <div class="content-block">
          <h2>2. كيفية استخدام المعلومات</h2>
          <p>نستخدم بياناتك لتوفير الخدمات التعليمية، تحسين تجربة المستخدم، التواصل معك بشأن الاشتراك أو الدعم، وضمان تشغيل المنصة بشكل آمن.</p>
        </div>

        <div class="content-block">
          <h2>3. حماية البيانات</h2>
          <p>نتخذ الإجراءات التقنية والتنظيمية المناسبة لحماية المعلومات من الوصول غير المصرح به أو التعديل أو الإتلاف.</p>
        </div>

        <div class="content-block">
          <h2>4. مشاركة البيانات</h2>
          <p>لن نشارك معلوماتك الشخصية مع أطراف ثالثة إلا بموافقة صريحة منك أو عندما تكون مشاركة إلزامية بموجب القانون.</p>
        </div>

        <div class="content-block">
          <h2>5. حقوقك</h2>
          <p>يمكنك التواصل معنا لطلب الاطلاع على بياناتك أو تعديلها أو حذفها وفق السياسات المعتمدة من المنصة.</p>
        </div>
      </div>
    </section>
  </PublicShell>
</template>

<style scoped>
.page-wrap {
  padding: 4rem 1.25rem 5rem;
  background: linear-gradient(135deg, #f7f0ff 0%, #fdf8f2 100%);
}

.page-card {
  max-width: 900px;
  margin: 0 auto;
  background: rgba(255,255,255,0.95);
  border: 1px solid #ece4ff;
  border-radius: 32px;
  padding: 2.2rem;
  box-shadow: 0 20px 45px rgba(30,27,46,0.08);
}

.eyebrow {
  display: inline-block;
  padding: 0.45rem 0.95rem;
  border-radius: 999px;
  background: #f3effb;
  color: #5f4398;
  font-weight: 800;
  margin-bottom: 1rem;
}

h1 {
  font-size: clamp(1.7rem, 3vw, 2.3rem);
  font-weight: 900;
  color: #1e1b2e;
  margin: 0 0 0.7rem;
}

.lead {
  color: #5f5b6f;
  line-height: 1.9;
  margin-bottom: 1.5rem;
}

.content-block {
  border-top: 1px solid #efeaf8;
  padding: 1rem 0;
}

.content-block h2 {
  font-size: 1.05rem;
  font-weight: 800;
  color: #1e1b2e;
  margin: 0 0 0.45rem;
}

.content-block p {
  color: #5f5b6f;
  line-height: 1.9;
  margin: 0;
}
</style>
