<script setup>
import { Head } from '@inertiajs/vue3'
import Bare3Layout from '@/Layouts/Bare3Layout.vue'

defineProps({ brand: Object, footer: Object })

const courses = [
  {
    num: '01', title: 'بارع مهني', color: '#2e5c87', icon: 'fa-briefcase',
    desc: 'برامج متخصصة لتعزيز الصحة النفسية المهنية، تطوير القيادة، ورفع كفاءة المؤسسات.',
    points: ['إدارة الضغوط المهنية', 'بناء الذكاء العاطفي', 'ثقافة الرفاهية في العمل'],
  },
  {
    num: '02', title: 'بارع أطفال', color: '#3fa79e', icon: 'fa-child',
    desc: 'دعم نفسي وتربوي متكامل للأطفال والأسر، مع التركيز على الصحة النفسية المبكرة.',
    points: ['فهم سلوك الطفل', 'مهارات التواصل الأسري', 'التعامل مع القلق والتوتر'],
  },
  {
    num: '03', title: 'بارع اجتماعي', color: '#e8636e', icon: 'fa-users',
    desc: 'تطوير قدرات العاملين في القطاع الاجتماعي والخيري مع الحفاظ على صحتهم النفسية.',
    points: ['الرعاية الذاتية للممارسين', 'إدارة الحالات المعقدة', 'بناء المرونة النفسية'],
  },
  {
    num: '04', title: 'بارع تعليمي', color: '#f0a15c', icon: 'fa-graduation-cap',
    desc: 'بناء بيئة تعليمية صحية تدعم الصحة النفسية للمعلمين والطلاب.',
    points: ['الصحة النفسية في المدرسة', 'مهارات المعلم الحديث', 'التعلم الاجتماعي العاطفي'],
  },
]
</script>

<template>
  <Head title="الكورسات والمسارات | Bare3" />
  <Bare3Layout active="courses">

    <!-- HERO -->
    <section class="courses-hero min-h-[70vh] flex items-center text-white">
      <div class="max-w-6xl mx-auto px-6 text-center">
        <h1 class="text-5xl md:text-6xl font-black mb-6" data-aos="fade-up">الكورسات والمسارات</h1>
        <p class="text-2xl text-purple-100 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
          حلول تخصصية علمية وعملية ترافق الإنسان وتمكّن الكيان
        </p>
      </div>
    </section>

    <!-- COURSES GRID -->
    <section class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-10">
          <div v-for="(c, i) in courses" :key="c.num" class="course-card bg-white rounded-3xl overflow-hidden shadow-xl"
               data-aos="fade-up" :data-aos-delay="i * 100">
            <div class="h-2" :style="{ background: c.color }"></div>
            <div class="p-10">
              <div class="flex justify-between items-start">
                <div>
                  <span class="text-5xl font-black" :style="{ color: c.color + '1a' }">{{ c.num }}</span>
                  <h3 class="text-3xl font-bold mt-2" :style="{ color: c.color }">{{ c.title }}</h3>
                </div>
                <i class="fa-solid text-5xl" :class="c.icon" :style="{ color: c.color + '33' }"></i>
              </div>
              <p class="mt-8 text-gray-600 leading-relaxed">{{ c.desc }}</p>
              <ul class="mt-8 space-y-3 text-sm">
                <li v-for="p in c.points" :key="p" class="flex items-center gap-3">
                  <i class="fa-solid fa-check text-green-500"></i> {{ p }}
                </li>
              </ul>
              <button class="mt-10 w-full text-white py-4 rounded-2xl font-medium transition hover:brightness-90"
                      :style="{ background: c.color }">
                اكتشف البرنامج
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </Bare3Layout>
</template>

<style scoped>
.courses-hero {
  background: linear-gradient(rgba(75,53,104,0.85), rgba(143,119,174,0.8)),
              url('https://www.amlood.net/images/workspace.jpg');
  background-size: cover;
  background-position: center;
}
.course-card { transition: all 0.4s ease; }
.course-card:hover { transform: translateY(-15px); box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.15); }
</style>
