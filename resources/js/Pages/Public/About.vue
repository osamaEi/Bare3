<script setup>
import { Head } from '@inertiajs/vue3'
import Bare3Layout from '@/Layouts/Bare3Layout.vue'

// props قادمة من الـ controller (نحافظ على التوافق حتى لو غير مستخدمة الآن)
defineProps({ brand: Object, footer: Object })
</script>

<template>
  <Head title="عن بارع | Bare3" />
  <Bare3Layout active="about">

    <!-- HERO -->
    <section class="hero-about min-h-[70vh] flex items-center text-white relative overflow-hidden">
      <div class="max-w-6xl mx-auto px-6 grid lg:grid-cols-[1fr_auto] items-center gap-8 w-full">
        <div class="text-center lg:text-right">
          <h1 class="text-5xl md:text-6xl font-black leading-tight mb-6" data-aos="fade-up">عن بارع</h1>
          <p class="text-2xl text-purple-100 mb-8" data-aos="fade-up" data-aos-delay="200">مهارات حقيقية.. لبناء شخصية قيادية واثقة</p>
          <div class="w-24 h-1 bg-white mx-auto lg:mx-0 rounded-full" data-aos="fade-up" data-aos-delay="400"></div>
        </div>
        <img src="/images/characters/09.png" alt="شخصية بارع"
             class="about-hero-char hidden lg:block" data-aos="fade-left" data-aos-delay="300" />
      </div>
    </section>

    <!-- MAIN CONTENT -->
    <section class="py-20">
      <div class="max-w-6xl mx-auto px-6">

        <div class="grid md:grid-cols-2 gap-16 items-center">
          <div data-aos="fade-right">
            <h2 class="section-title text-4xl font-bold text-gray-900 mb-8">رؤيتنا</h2>
            <p class="text-xl leading-relaxed text-gray-700">
              أن نكون المنصة الرائدة في المملكة والعالم العربي لتمكين الأطفال والناشئة من مهارات المستقبل،
              وتوظيف الذكاء الاصطناعي لتقديم تجربة تثقيفية وتفاعلية آمنة ومستدامة.
            </p>
          </div>
          <div data-aos="fade-left" class="flex justify-center">
            <div class="bg-gradient-to-br from-purple-100 to-purple-200 p-10 rounded-3xl w-full max-w-md">
              <div class="text-center">
                <img src="/images/characters/26.png" alt="" class="vision-char" loading="lazy" aria-hidden="true" />
                <h3 class="text-2xl font-bold text-purple-900">نزرع الوعي.. نحصد الاستدامة</h3>
              </div>
            </div>
          </div>
        </div>

        <!-- Our Story -->
        <div class="mt-28">
          <h2 class="section-title text-4xl font-bold text-center text-gray-900 mb-12">قصتنا</h2>
          <div class="max-w-3xl mx-auto text-lg leading-relaxed text-gray-700 space-y-8 text-right">
            <p>نشأت منصة "بارِع" من إيمان عميق بأن تعليم الغد لا يتوقف عند المناهج الأكاديمية التقليدية.</p>
            <p>نحن نرافق الأجيال الناشئة والشباب عبر رحلة تعليمية تفاعلية تدمج بين المهارات الصلبة (كالبرمجة والذكاء الاصطناعي) والمهارات الناعمة (كالتفكير الناقد والوعي المالي والقيادة) لإعداد جيل ملهم وقادر على صناعة المستقبل.</p>
          </div>
        </div>

        <!-- Values -->
        <div class="mt-28 bg-purple-50 rounded-3xl py-16 px-8">
          <h2 class="section-title text-4xl font-bold text-center text-gray-900 mb-12">قيمنا الأساسية</h2>
          <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center" data-aos="zoom-in" data-aos-delay="100">
              <div class="w-20 h-20 mx-auto bg-white rounded-2xl flex items-center justify-center shadow-md mb-6">
                <i class="fa-solid fa-shield-halved text-4xl text-purple-700"></i>
              </div>
              <h3 class="font-bold text-xl mb-3">الابتكار التقني</h3>
              <p class="text-gray-600">نبتكر أدوات تفاعلية تثير شغف الطفل وتجعل التعلم رحلة ممتعة.</p>
            </div>
            <div class="text-center" data-aos="zoom-in" data-aos-delay="200">
              <div class="w-20 h-20 mx-auto bg-white rounded-2xl flex items-center justify-center shadow-md mb-6">
                <i class="fa-solid fa-lightbulb text-4xl text-purple-700"></i>
              </div>
              <h3 class="font-bold text-xl mb-3">الأمان والجودة</h3>
              <p class="text-gray-600">نلتزم بتقديم محتوى آمن وبيئة رقمية موثوقة تناسب قيم مجتمعنا.</p>
            </div>
            <div class="text-center" data-aos="zoom-in" data-aos-delay="300">
              <div class="w-20 h-20 mx-auto bg-white rounded-2xl flex items-center justify-center shadow-md mb-6">
                <i class="fa-solid fa-hands-holding-child text-4xl text-purple-700"></i>
              </div>
              <h3 class="font-bold text-xl mb-3">الأثر والتمكين</h3>
              <p class="text-gray-600">نمنح أبناءكم المهارات التي تبني شخصياتهم وتصنع حضورهم بثقة.</p>
            </div>
          </div>
        </div>

      </div>
    </section>
  </Bare3Layout>
</template>

<style scoped>
.about-hero-char {
  width:300px; height:auto;
  filter:drop-shadow(0 24px 40px rgba(0,0,0,.28));
}
.vision-char {
  width:170px; height:auto; margin:0 auto 1.25rem;
  filter:drop-shadow(0 16px 28px rgba(95,67,152,.28));
}
.hero-about {
  background:
    radial-gradient(circle at 20% 30%, rgba(240,128,106,.35), transparent 45%),
    radial-gradient(circle at 80% 70%, rgba(75,181,168,.3), transparent 45%),
    linear-gradient(135deg, #5f4398, #7c5cbf);
}
.section-title { position: relative; display: inline-block; }
.section-title:after {
  content: ''; position: absolute; bottom: -8px; right: 0; width: 60%; height: 4px;
  background: linear-gradient(to left, #7a5ea0, #a996c2); border-radius: 9999px;
}
</style>
