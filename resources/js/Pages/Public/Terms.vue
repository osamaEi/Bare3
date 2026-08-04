<script setup>
import { Head } from '@inertiajs/vue3'
import PublicShell from '@/Components/PublicShell.vue'

defineProps({
  brand: Object,
  footer: Object,
})
</script>

<template>
  <Head title="الشروط والأحكام — بارع" />
  <PublicShell :brand="brand" :footer="footer">
    <section class="page-wrap">
      <div class="page-card">
        <span class="eyebrow">الشروط والأحكام</span>
        <h1>شروط الاستخدام الخاصة بالمنصة</h1>
        <p class="lead">باستخدامك لمنصة بارع فإنك توافق على الالتزام بالضوابط المنصوص عليها أدناه.</p>

        <div class="content-block">
          <h2>1. قبول الشروط</h2>
          <p>يعتبر استخدامك للمنصة قبولاً ضمنياً للشروط والأحكام الواردة في هذه الصفحة، وقد يتم تحديثها من وقت لآخر.</p>
        </div>

        <div class="content-block">
          <h2>2. استخدام المنصة</h2>
          <p>يجب استخدام المنصة بشكل مسؤول، وعدم نشر أو تحميل محتوى يخل بالقوانين أو يسبب أذى للآخرين أو يضر بالمنصة.</p>
        </div>

        <div class="content-block">
          <h2>3. الاشتراكات</h2>
          <p>تخضع الاشتراكات لشروط الدفع والتجديد المعلنة داخل المنصة، ويحق للمنصة تعديل الخطة أو الأسعار وفقاً للسياسات المعتمدة.</p>
        </div>

        <div class="content-block">
          <h2>4. الملكية الفكرية</h2>
          <p>جميع المحتويات التعليمية والمواد والهوية البصرية للمنصة مملوكة لمنصة بارع وتخضع لحماية حقوق الملكية الفكرية.</p>
        </div>

        <div class="content-block">
          <h2>5. التواصل مع الدعم</h2>
          <p>في حال وجود أي استفسار أو مشكلة، يمكنك التواصل معنا عبر البريد أو واتساب الدعم المذكور في صفحة التواصل.</p>
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
