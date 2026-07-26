<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import Bare3Layout from '@/Layouts/Bare3Layout.vue'

const props = defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  content: { type: Object, default: () => ({}) },
  latestPosts: { type: Array, default: () => [] },
})

const IMG = '/images/new-bare3'

// أول 5 مقالات لعرضها في قسم "رؤانا" (مع صور غلاف احتياطية)
const insightCovers = [
  'https://i.pinimg.com/736x/0f/81/bf/0f81bfb0f356a37edebece4e9b575932.jpg',
  'https://i.pinimg.com/736x/31/c2/06/31c206905aa424484fcfce4613fbcb24.jpg',
  'https://i.pinimg.com/736x/0b/ca/de/0bcade7415533cf12d21b704425357b1.jpg',
  'https://i.pinimg.com/vwebp/736x/a6/50/a1/a650a1cd83d27b077cb56ee9132f6abf.webp',
  'https://i.pinimg.com/236x/fe/60/d6/fe60d661ac3ebfd6bf3ae0ab5195ce64.jpg',
]
const insights = computed(() => props.latestPosts.slice(0, 5))

// فورم التواصل — مربوط بـ contact.store
const tracks = [
  { value: 'بارع مهني',    color: 'var(--navy)' },
  { value: 'بارع أطفال',   color: 'var(--orange)' },
  { value: 'بارع اجتماعي', color: 'var(--coral)' },
  { value: 'بارع تعليمي',  color: 'var(--teal)' },
]
const form = useForm({ name: '', email: '', phone: '', subject: '', message: '' })
function submitContact() {
  form.post(route('contact.store'), { preserveScroll: true, onSuccess: () => form.reset() })
}
</script>

<template>
  <Head title="بارع | Bare3" />
  <Bare3Layout active="home">

    <!-- HERO -->
    <section id="hero" class="hero-bg relative min-h-[500px] sm:min-h-[600px] flex items-center justify-center sm:justify-end">
      <div class="relative z-10 px-6 sm:px-0 text-center sm:text-right max-w-2xl sm:ml-8 mx-auto sm:mx-0">
        <span data-aos="fade-down" class="baree-eyebrow">
          <i class="fa-solid fa-sun"></i> براعتك تبدأ من هنا
        </span>
        <h1 data-aos="zoom-in" data-aos-delay="150" class="baree-title mt-5">
          نمنح أجيالنا مهارات الغد.. <span class="baree-title__accent">اليوم</span>
        </h1>
        <p data-aos="fade-up" data-aos-delay="200" class="baree-sub mt-4">
          رحلة تعليمية تفاعلية وقصصية ترافق أبناءكم من المدرسة للجامعة، وتصقل منظومة متكاملة من المهارات لصناعة رواد وقادة الغد.
        </p>
        <ul data-aos="fade-up" data-aos-delay="300" class="baree-path mt-8">
          <li><span class="baree-path__dot" style="background:var(--orange)"></span>اجتماعية وعاطفية</li>
          <li><span class="baree-path__dot" style="background:var(--coral)"></span>شخصية</li>
          <li><span class="baree-path__dot" style="background:var(--teal)"></span>تقنية</li>
          <li><span class="baree-path__dot" style="background:var(--navy)"></span>مالية</li>
        </ul>
        <a href="#tracks" data-aos="fade-up" data-aos-delay="400" class="baree-cta mt-8 sm:mt-10">
          <span>اكتشف مساراتنا</span>
          <i class="fa-solid fa-arrow-left"></i>
        </a>
      </div>
      <img :src="`${IMG}/03.png`" class="decor-img hero-img1 hide-mobile" data-aos="fade-left" data-aos-delay="500">
      <img :src="`${IMG}/26.png`" class="decor-img hero-img2 hide-mobile" data-aos="fade-left" data-aos-delay="500">
    </section>

    <!-- ABOUT -->
    <section id="about" class="relative overflow-hidden bg-cover" :style="{ backgroundImage: `url('${IMG}/about-bg.png')` }">
      <div class="relative max-w-6xl mx-auto px-6 md:px-10 py-16 md:py-24">
        <div class="grid grid-cols-1 md:grid-cols-2">
          <div class="text-right" data-aos="fade-right">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900">عن بارع</h2>
            <div class="mt-2 inline-block">
              <span class="en text-slate-400 text-lg tracking-wide">About Bare3</span>
              <div class="h-[3px] w-full bg-violet-400 mt-1"></div>
            </div>
            <h3 class="mt-10 text-3xl sm:text-4xl md:text-5xl font-extrabold text-violet-400">رعاية تُثمر استدامة</h3>

            <div class="mt-10 flex items-start justify-end gap-5" data-aos="fade-left" data-aos-delay="100">
              <div class="relative shrink-0 bg-violet-200/70 rounded-full p-4">
                <img :src="`${IMG}/leaderboard.png`" class="w-10">
              </div>
              <p class="text-slate-800 leading-loose text-base sm:text-lg max-w-xl">
                منصة بارع التعليمية هي وجهتك الأولى لتمكين الأجيال الناشئة والشباب من مهارات القرن الحادي والعشرين.
              </p>
              <div class="h-full w-px bg-slate-200 self-stretch"></div>
            </div>

            <div class="mt-10 ms-12 flex items-start justify-end gap-5" data-aos="fade-left" data-aos-delay="250">
              <div class="relative shrink-0 bg-violet-200/70 rounded-full p-4">
                <img :src="`${IMG}/blog-icon.png`" class="w-10">
              </div>
              <p class="text-slate-800 leading-loose text-base sm:text-lg max-w-2xl">
                نرافق الأجيال في مختلف مراحلهم الدراسية والجامعية عبر رحلة تعليمية تفاعلية، تهدف إلى صقل منظومة متكاملة من المهارات (الاجتماعية والعاطفية والشخصية والتقنية والمالية)؛ لنربط المعرفة بمتطلبات المستقبل، ونشارك في صناعة رواد وقادة الغد.
              </p>
              <div class="h-full w-px bg-slate-200 self-stretch"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- VALUES -->
    <section class="values-bg py-12 sm:py-20" :style="{ backgroundImage: `linear-gradient(rgba(122,94,160,0.7),rgba(122,94,160,0.7)), url('${IMG}/values-bg.png')` }">
      <div class="max-w-7xl mx-auto px-4 sm:px-1">
        <div class="flex items-center gap-6 mb-16" data-aos="fade-down">
          <div class="flex-1 h-px bg-gradient-to-r from-transparent via-purple-300 to-transparent"></div>
          <div class="text-center">
            <h2 class="text-5xl font-black text-gray-100 tracking-[-2px]">قيمنا</h2>
            <p class="en text-purple-200 text-xl font-medium -mt-1">bare3 Values</p>
          </div>
          <div class="flex-1 h-px bg-gradient-to-r from-transparent via-purple-300 to-transparent"></div>
        </div>

        <div class="relative grid grid-cols-1 md:grid-cols-6 gap-6 md:gap-8 py-10">
          <div class="fade-card bg-white/10 border border-white/30 rounded-[2.5rem_1rem_2.5rem_1rem] p-8 sm:p-14 text-center text-white md:col-span-4 md:-rotate-2 shadow-2xl hover:rotate-0 hover:scale-[1.02] transition-transform duration-500" data-aos="flip-up">
            <i class="fa-solid fa-circle-check text-6xl mb-6"></i>
            <h3 class="text-3xl font-bold mb-3">المسؤولية والجودة</h3>
            <div class="w-14 h-[2px] bg-white/60 mx-auto mb-4"></div>
            <p class="text-lg text-purple-50 leading-loose">تلتزم بمسؤوليتها المهنية والوطنية وتضع أعلى معايير الجودة لضمان مخرجات تتسم بالدقة والموثوقية.</p>
          </div>
          <div class="fade-card bg-white/10 border border-white/30 rounded-full p-8 sm:p-10 text-center text-white md:col-span-2 md:rotate-3 shadow-xl aspect-square flex flex-col justify-center items-center hover:rotate-0 hover:scale-105 transition-transform duration-500" data-aos="flip-up" data-aos-delay="150">
            <i class="fa-solid fa-lightbulb text-4xl mb-4"></i>
            <h3 class="text-lg font-bold mb-2">الابتكار<br>والاستدامة</h3>
            <div class="w-8 h-[2px] bg-white/60 mx-auto mb-2"></div>
            <p class="text-xs text-purple-50 leading-snug px-2">نبتكر حلولاً إبداعية يمتد أثرها للفرد والمنظمة.</p>
          </div>
          <div class="fade-card bg-white/10 border border-white/30 rounded-2xl p-6 sm:p-8 text-center text-white md:col-span-2 md:rotate-[-4deg] shadow-lg flex flex-col justify-center hover:rotate-0 hover:scale-105 transition-transform duration-500" data-aos="flip-up" data-aos-delay="300">
            <i class="fa-solid fa-heart text-3xl mb-3"></i>
            <h3 class="text-base font-bold mb-2">العطاء والاحتواء</h3>
            <div class="w-8 h-[2px] bg-white/60 mx-auto mb-2"></div>
            <p class="text-xs text-purple-50 leading-snug">تنطلق من شغفها لخدمة الإنسان في بيئة آمنة وداعمة.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- TRACKS -->
    <section id="tracks" class="bg-gradient-to-r from-[#956BAD] to-[#5B3A73] text-white relative overflow-hidden py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-1 pt-16 pb-8 text-right">
        <div class="flex items-center gap-6 mb-16" data-aos="fade-down">
          <div class="flex-1 h-px bg-gradient-to-r from-transparent via-purple-300 to-transparent"></div>
          <div class="text-center">
            <h2 class="text-5xl font-black text-gray-100 tracking-[-2px]">مساراتنا</h2>
            <p class="en text-purple-200 text-xl font-medium -mt-1">bare3 Tracks</p>
          </div>
          <div class="flex-1 h-px bg-gradient-to-r from-transparent via-purple-300 to-transparent"></div>
        </div>
        <h3 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white mt-8" data-aos="fade-up" data-aos-delay="100">
          حلول تخصصية.. تُرافق الإنسان وتمكّن الكيان
        </h3>
        <p class="text-purple-100 leading-loose mt-4 mr-12 ml-auto text-lg sm:text-2xl max-w-5xl" data-aos="fade-up" data-aos-delay="200">
          صممنا مساراتنا في بارع لتغطي كافة جوانب جودة الحياة النفسية والاجتماعية، بمنهجية تجمع بين الفهم العميق للاحتياجات الفردية والمتطلبات الاستراتيجية للمنظومات.
        </p>
        <div class="mt-10" data-aos="fade-up" data-aos-delay="300">
          <Link :href="route('courses')" class="inline-flex items-center gap-2 bg-white text-purple-800 font-bold px-8 py-3.5 rounded-full hover:bg-purple-50 transition">
            تصفّح كل المسارات <i class="fa-solid fa-arrow-left"></i>
          </Link>
        </div>
      </div>
      <img :src="`${IMG}/06.png`" class="absolute hide-mobile -bottom-10 left-16 w-32 sm:w-48 opacity-70" data-aos="fade-right" data-aos-delay="400">
    </section>

    <!-- INSIGHTS / رؤانا -->
    <section id="insights" class="py-20 bg-white relative">
      <div class="max-w-7xl mx-auto px-6 relative">
        <div class="flex items-center gap-6 mb-16" data-aos="fade-down">
          <div class="flex-1 h-px bg-gradient-to-r from-transparent via-purple-300 to-transparent"></div>
          <div class="text-center">
            <h2 class="text-5xl font-black text-gray-900 tracking-[-2px]">رؤانا</h2>
            <p class="en text-purple-600 text-xl font-medium -mt-1">bare3 Insights</p>
          </div>
          <div class="flex-1 h-px bg-gradient-to-r from-transparent via-purple-300 to-transparent"></div>
        </div>

        <div class="max-w-2xl mx-auto text-center mb-16">
          <p class="text-xl text-gray-700 leading-relaxed mb-4">في هذا القسم نتشارك رؤى بارع حول الصحة النفسية والعوامل التي تؤثر على جودة الحياة.</p>
          <p class="text-lg text-gray-600">نستعرض المواضيع من زوايا متعددة لنربط المعرفة بالحياة اليومية.</p>
        </div>

        <div v-if="insights.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <Link v-for="(post, i) in insights" :key="post.slug" :href="route('blog.show', post.slug)"
                class="group bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-500 border border-purple-100 block">
            <div class="relative overflow-hidden">
              <img :src="insightCovers[i % insightCovers.length]" class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-700">
              <span class="absolute top-5 right-5 px-5 py-1.5 bg-white text-purple-700 text-sm font-bold rounded-3xl shadow">مقالة</span>
            </div>
            <div class="p-7">
              <h3 class="font-bold text-xl leading-tight mb-3 text-gray-900 group-hover:text-purple-700 transition-colors">{{ post.title }}</h3>
              <p class="text-gray-600 line-clamp-3">{{ post.excerpt }}</p>
            </div>
          </Link>
        </div>

        <div class="text-center mt-16">
          <Link :href="route('blog')" class="group inline-flex items-center gap-3 border-2 border-purple-600 text-purple-700 hover:bg-purple-600 hover:text-white px-9 py-4 rounded-3xl text-lg font-medium transition-all">
            المزيد من الرؤى
            <span class="text-2xl group-hover:rotate-90 transition-transform">↗</span>
          </Link>
        </div>
      </div>
    </section>

    <!-- CONTACT -->
    <section id="contact" class="relative overflow-hidden bg-[var(--purple-100)] pt-24 pb-24">
      <div class="max-w-7xl mx-auto px-6 relative">
        <div class="max-w-2xl mb-16 text-right mr-0 mx-auto md:mx-0">
          <span class="inline-flex items-center gap-2 bg-white text-[var(--purple-800)] text-sm font-bold px-4 py-1.5 rounded-full shadow-sm">
            <span class="w-1.5 h-1.5 rounded-full bg-[var(--coral)]"></span> تواصل معنا
          </span>
          <h2 class="font-extrabold text-4xl sm:text-5xl mt-5 text-[var(--purple-900)] leading-[1.15]">
            اختر مسارك، <br class="hidden sm:block"> وابدأ الحديث معنا
          </h2>
          <p class="mt-5 text-[var(--purple-800)]/70 leading-loose">
            كل مسار من مسارات بارع له طابعه الخاص. اختر ما يناسبك، واملأ بياناتك، وسيعود إليك فريقنا خلال يوم عمل واحد.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">

          <!-- بيانات التواصل -->
          <div class="text-right order-2 md:order-1">
            <div class="flex flex-col gap-4">
              <div class="relative bg-white rounded-2xl pr-7 pl-6 py-5 shadow-sm flex items-center gap-4 justify-end">
                <div class="text-right">
                  <p class="font-bold text-[var(--purple-900)]">المقر الرئيسي</p>
                  <p class="text-[var(--purple-800)]/60 text-sm mt-0.5">الرياض، السعودية</p>
                </div>
                <div class="w-11 h-11 shrink-0 rounded-xl bg-[var(--navy)]/10 flex items-center justify-center"><i class="fa-solid fa-location-dot text-[var(--navy)]"></i></div>
                <span class="absolute right-0 top-4 bottom-4 w-1 rounded-full bg-[var(--navy)]"></span>
              </div>
              <div class="relative bg-white rounded-2xl pr-7 pl-6 py-5 shadow-sm flex items-center gap-4 justify-end">
                <div class="text-right">
                  <p class="font-bold text-[var(--purple-900)]" dir="ltr">info@Bare3.Net</p>
                  <p class="text-[var(--purple-800)]/60 text-sm mt-0.5">للاستفسارات والخدمات</p>
                </div>
                <div class="w-11 h-11 shrink-0 rounded-xl bg-[var(--teal)]/10 flex items-center justify-center"><i class="fa-solid fa-envelope text-[var(--teal)]"></i></div>
                <span class="absolute right-0 top-4 bottom-4 w-1 rounded-full bg-[var(--teal)]"></span>
              </div>
              <div class="relative bg-white rounded-2xl pr-7 pl-6 py-5 shadow-sm flex items-center gap-4 justify-end">
                <div class="text-right">
                  <p class="font-bold text-[var(--purple-900)]" dir="ltr">Hr@Bare3.Net</p>
                  <p class="text-[var(--purple-800)]/60 text-sm mt-0.5">للراغبين بالانضمام إلينا</p>
                </div>
                <div class="w-11 h-11 shrink-0 rounded-xl bg-[var(--coral)]/10 flex items-center justify-center"><i class="fa-solid fa-briefcase text-[var(--coral)]"></i></div>
                <span class="absolute right-0 top-4 bottom-4 w-1 rounded-full bg-[var(--coral)]"></span>
              </div>
            </div>
          </div>

          <!-- النموذج -->
          <div class="relative bg-white rounded-[2rem] p-7 sm:p-10 shadow-xl order-1 md:order-2">
            <div class="absolute -top-4 -left-4 w-14 h-14 bg-[var(--orange)] rounded-full flex items-center justify-center shadow-lg">
              <i class="fa-regular fa-comment-dots text-white"></i>
            </div>
            <h3 class="text-2xl sm:text-3xl font-bold text-[var(--purple-900)] mb-1">شاركنا فكرتك</h3>
            <p class="text-[var(--purple-800)]/60 mb-7 text-sm">اختر المسار المناسب واملأ البيانات</p>

            <div v-if="$page.props.flash?.success" class="mb-6 bg-green-50 text-green-700 border border-green-200 rounded-2xl px-5 py-3 text-sm font-medium">
              {{ $page.props.flash.success }}
            </div>

            <form class="space-y-6" @submit.prevent="submitContact">
              <div>
                <span class="block text-sm font-medium text-[var(--purple-900)] mb-4">نوع المسار المهتم به</span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                  <label v-for="t in tracks" :key="t.value" class="cursor-pointer block">
                    <input type="radio" name="track" :value="t.value" v-model="form.subject" class="peer hidden">
                    <span class="flex items-center justify-center gap-1.5 border-2 text-xs sm:text-sm font-bold py-3 rounded-xl transition"
                          :style="{ borderColor: t.color, color: form.subject === t.value ? '#fff' : t.color, background: form.subject === t.value ? t.color : 'transparent' }">
                      {{ t.value }}
                    </span>
                  </label>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-[var(--purple-900)] mb-2">* الاسم الكريم</label>
                <input type="text" v-model="form.name" placeholder="أدخل اسمك الكريم"
                       class="w-full px-5 py-3.5 rounded-2xl border-2 border-[var(--purple-100)] bg-[var(--purple-100)]/40 focus:border-[var(--purple-700)] focus:outline-none focus:bg-white text-[var(--purple-900)] transition-colors">
                <span v-if="form.errors.name" class="text-red-500 text-xs mt-1 block">{{ form.errors.name }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-[var(--purple-900)] mb-2">* البريد الإلكتروني</label>
                <input type="email" v-model="form.email" placeholder="example@mail.com" dir="ltr"
                       class="w-full px-5 py-3.5 rounded-2xl border-2 border-[var(--purple-100)] bg-[var(--purple-100)]/40 focus:border-[var(--purple-700)] focus:outline-none focus:bg-white text-[var(--purple-900)] transition-colors">
                <span v-if="form.errors.email" class="text-red-500 text-xs mt-1 block">{{ form.errors.email }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-[var(--purple-900)] mb-2">رقم الجوال (اختياري)</label>
                <input type="text" v-model="form.phone" placeholder="05xxxxxxxx" dir="ltr"
                       class="w-full px-5 py-3.5 rounded-2xl border-2 border-[var(--purple-100)] bg-[var(--purple-100)]/40 focus:border-[var(--purple-700)] focus:outline-none focus:bg-white text-[var(--purple-900)] transition-colors">
              </div>

              <div>
                <label class="block text-sm font-medium text-[var(--purple-900)] mb-2">* رسالتك</label>
                <textarea v-model="form.message" rows="4" placeholder="اكتب رسالتك هنا..."
                          class="w-full px-5 py-3.5 rounded-2xl border-2 border-[var(--purple-100)] bg-[var(--purple-100)]/40 focus:border-[var(--purple-700)] focus:outline-none focus:bg-white text-[var(--purple-900)] resize-none transition-colors"></textarea>
                <span v-if="form.errors.message" class="text-red-500 text-xs mt-1 block">{{ form.errors.message }}</span>
              </div>

              <button type="submit" :disabled="form.processing"
                      class="w-full bg-[var(--blue-btn)] hover:bg-[var(--navy)] transition-colors text-white font-bold py-4 rounded-full text-lg mt-2 shadow-lg flex items-center justify-center gap-3 disabled:opacity-60">
                <span>{{ form.processing ? 'جاري الإرسال...' : 'إرسال الرسالة' }}</span>
                <i class="fa-solid fa-paper-plane text-sm" style="transform:scaleX(-1);"></i>
              </button>
            </form>
          </div>

        </div>
      </div>
      <img :src="`${IMG}/09.png`" class="absolute hide-mobile top-16 left-24 h-72 w-40 opacity-40 -z-0" data-aos="fade-right" data-aos-delay="400">
    </section>

  </Bare3Layout>
</template>

<style scoped>
.baree-eyebrow {
  display:inline-flex; align-items:center; gap:8px; color:var(--orange); font-weight:700;
  font-size:.85rem; padding:6px 16px; border:1px solid rgba(240,161,92,.4);
  background:rgba(240,161,92,.1); border-radius:9999px;
}
.baree-title { color:#f3f0f8; font-weight:800; line-height:1.35; font-size:1.6rem; }
@media (min-width:640px){ .baree-title{ font-size:2.15rem; } }
@media (min-width:768px){ .baree-title{ font-size:2.6rem; } }
.baree-title__accent { color:var(--orange); }
.baree-sub { color:var(--purple-100); opacity:.85; font-weight:500; font-size:1rem; line-height:1.9; }

.baree-path { position:relative; display:flex; flex-wrap:wrap; align-items:flex-start; justify-content:center; gap:28px; padding-top:16px; list-style:none; }
.baree-path::before { content:''; position:absolute; top:0; right:0; left:0; height:2px;
  background-image:linear-gradient(to left, var(--purple-300) 50%, transparent 50%); background-size:10px 2px; background-repeat:repeat-x; opacity:.55; }
@media (min-width:640px){ .baree-path{ justify-content:flex-end; } }
.baree-path li { display:flex; flex-direction:column; align-items:center; gap:6px; font-size:.78rem; font-weight:700; color:var(--purple-100); }
.baree-path__dot { width:12px; height:12px; border-radius:9999px; box-shadow:0 0 0 4px rgba(255,255,255,.08); }

.baree-cta { display:inline-flex; align-items:center; gap:10px; background:var(--blue-btn); padding:14px 30px; border-radius:9999px; color:#fff; font-weight:700; font-size:1.05rem; transition:background .25s ease, transform .25s ease; }
.baree-cta:hover { background:#274a70; transform:translateY(-2px); }

.hero-bg { background:linear-gradient(180deg, rgba(122,94,160,.55), rgba(75,53,104,.75)), url('/images/new-bare3/bg-hero.png'); background-size:cover; background-position:center; }
.values-bg { background-size:cover; background-position:center; }
.fade-card { backdrop-filter: blur(2px); }

.decor-img { position:absolute; opacity:.7; transition:all .4s ease; }
.hero-img1 { bottom:5%; right:10%; width:130px; height:280px; }
.hero-img2 { bottom:8%; right:20%; width:110px; height:260px; }
@media (min-width:1024px){ .hero-img1{ width:160px; height:320px; right:18%; } .hero-img2{ width:140px; height:300px; right:32%; } }
@media (min-width:1280px){ .hero-img1{ width:180px; right:12%; height:380px; } .hero-img2{ width:160px; right:20%; height:300px; } }

.hide-mobile { display:none; }
@media (min-width:768px){ .hide-mobile{ display:block; } }
</style>
