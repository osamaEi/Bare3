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

const insights = computed(() => props.latestPosts.slice(0, 5))

// المسارات الخمسة — بالعبارات التسويقية المخصّصة + بيانات الـ hover
const tracks = [
  {
    key: 'creativity', title: 'الإبداع والابتكار والتفكير التصميمي', icon: 'fa-lightbulb', color: '#f0806a',
    slogan: 'فكّر خارج الأطر.. حوّل خيالك إلى ابتكار يصنع الفارق',
    sub: 'هنا نعلّم العقول كيف تلاحظ وتتحدى المألوف',
    age: '٨–١٢ سنة', skill: 'حل المشكلات إبداعياً', price: '٧٩ ر.س/شهر',
  },
  {
    key: 'emotional', title: 'الذكاء العاطفي والاجتماعي', icon: 'fa-heart-pulse', color: '#f2b866',
    slogan: 'ثقة واتزان.. لنعبّر عن مشاعرنا بشجاعة ونقود حواراتنا بذكاء',
    sub: 'الحصن النفسي والمرونة التي تحمي شخصية أبنائكم في عالم المشتتات',
    age: '٦–١٢ سنة', skill: 'الوعي الذاتي والتعاطف', price: '٧٩ ر.س/شهر',
  },
  {
    key: 'finance', title: 'الوعي المالي الذكي', icon: 'fa-coins', color: '#4bb5a8',
    slogan: 'مستقبل مالي آمن.. من إدارة المصروف اليومي إلى صناعة أول مشروع',
    sub: 'نعلّم جيل اليوم كيف يدّخر، يستثمر، ويفكر كروّاد الأعمال',
    age: '٩–١٢ سنة', skill: 'الادخار والاستثمار', price: '٧٩ ر.س/شهر',
  },
  {
    key: 'digital', title: 'المهارة التقنية والمواطنة الرقمية', icon: 'fa-microchip', color: '#7c5cbf',
    slogan: 'كن أنت القائد.. طوّع أدوات الذكاء الاصطناعي والتكنولوجيا لخدمة إبداعك',
    sub: 'من مستهلك رقمي إلى مبتكر ذكي؛ نفهم لغة العصر ونبحر فيه بأمان',
    age: '٨–١٢ سنة', skill: 'الأمان والإبداع الرقمي', price: '٧٩ ر.س/شهر',
  },
  {
    key: 'decision', title: 'اتخاذ القرارات وحل المشكلات', icon: 'fa-chess-knight', color: '#3d6ea5',
    slogan: 'اصنع قرارك بثقة.. مواقف حية تصنع شخصية قيادية لا تعرف التردد',
    sub: 'تحديات يومية تُكسب المشتركين مهارة تفكيك المشكلات المعقدة وحلّها باقتدار',
    age: '١٠–١٢ سنة', skill: 'القيادة واتخاذ القرار', price: '٧٩ ر.س/شهر',
  },
]

// العبارات الفرعية للـ hero (rotating-style، نعرضها كشرائح)
const subSlogans = [
  'من مقاعد الدراسة إلى ريادة المستقبل — مهارات حقيقية لحياة ذكية',
  'أكثر من مجرد تعليم.. رحلة تفاعلية تبني الشخصية وتصنع القادة',
  'لأن الشهادة وحدها لا تكفي — نمنح أبناءكم أدوات التميز في عصر الذكاء الاصطناعي',
]

// فورم التواصل — مربوط بـ contact.store
const form = useForm({ name: '', email: '', phone: '', subject: '', message: '' })
function submitContact() {
  form.post(route('contact.store'), { preserveScroll: true, onSuccess: () => form.reset() })
}
</script>

<template>
  <Head title="بارع | مهارات جيل اليوم" />
  <Bare3Layout active="home">

    <!-- ============ HERO ============ -->
    <section id="hero" class="relative overflow-hidden bg-[#FAFAFB] pt-16 pb-24 sm:pt-24 sm:pb-32">
      <!-- أشكال 3D تجريدية مولّدة بالكود (هوية بارع الخاصة) -->
      <div class="blob blob-1"></div>
      <div class="blob blob-2"></div>
      <div class="blob blob-3"></div>

      <div class="max-w-5xl mx-auto px-6 text-center relative z-10">
        <span data-aos="fade-down" class="eyebrow">
          <span class="w-2 h-2 rounded-full bg-[var(--brand)] animate-pulse"></span>
          براعتك تبدأ من هنا
        </span>

        <h1 data-aos="fade-up" class="mt-7 text-4xl sm:text-5xl md:text-6xl font-black leading-[1.25] text-[var(--ink)]">
          لأن المستقبل لا ينتظر..<br>
          <span class="text-[var(--brand)]">نصقل مهارات جيل اليوم</span> ليقود المستقبل بثقة
        </h1>

        <p data-aos="fade-up" data-aos-delay="150" class="mt-6 text-lg sm:text-xl text-gray-500 leading-loose max-w-2xl mx-auto">
          رحلة تعليمية تفاعلية وقصصية ترافق أبناءكم من المدرسة للجامعة، وتصقل منظومة متكاملة من المهارات لصناعة روّاد وقادة الغد.
        </p>

        <div data-aos="fade-up" data-aos-delay="250" class="mt-9 flex flex-col sm:flex-row items-center justify-center gap-4">
          <a href="#tracks" class="cta-primary">
            <span>اكتشف مساراتنا</span>
            <i class="fa-solid fa-arrow-left"></i>
          </a>
          <Link :href="route('subscribe')" class="cta-ghost">
            <span>تصفّح الباقات</span>
          </Link>
        </div>

        <!-- العبارات الفرعية -->
        <div data-aos="fade-up" data-aos-delay="350" class="mt-12 grid sm:grid-cols-3 gap-4 max-w-4xl mx-auto">
          <div v-for="(s, i) in subSlogans" :key="i" class="glass-mini text-sm text-gray-600 leading-relaxed">
            {{ s }}
          </div>
        </div>
      </div>
    </section>

    <!-- ============ TRACKS (Glassmorphism + hover) ============ -->
    <section id="tracks" class="relative py-20 sm:py-28 bg-gradient-to-b from-white to-[var(--brand-soft)]">
      <div class="max-w-7xl mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-16" data-aos="fade-up">
          <span class="text-[var(--brand)] font-bold">مساراتنا التعليمية</span>
          <h2 class="text-3xl sm:text-4xl font-black text-[var(--ink)] mt-3">خمس مهارات.. تصنع شخصية قيادية متكاملة</h2>
          <p class="text-gray-500 mt-4 leading-loose">مرّر على كل مسار لتكتشف تفاصيله والمهارة التي يكتسبها طفلك.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-7">
          <div v-for="(t, i) in tracks" :key="t.key" class="track-card group" data-aos="fade-up" :data-aos-delay="i * 80"
               :style="{ '--tc': t.color }">
            <!-- الوجه الأمامي -->
            <div class="track-face">
              <div class="track-icon"><i class="fa-solid" :class="t.icon"></i></div>
              <h3 class="text-xl font-black text-[var(--ink)] leading-snug mb-3">{{ t.title }}</h3>
              <p class="text-[var(--tc)] font-bold leading-relaxed">{{ t.slogan }}</p>
              <p class="text-gray-500 text-sm mt-3 leading-relaxed">{{ t.sub }}</p>
            </div>

            <!-- طبقة الـ hover -->
            <div class="track-hover">
              <div class="flex items-center justify-between text-sm">
                <span class="hover-tag"><i class="fa-solid fa-child-reaching ml-1"></i> {{ t.age }}</span>
                <span class="hover-tag"><i class="fa-solid fa-star ml-1"></i> {{ t.skill }}</span>
              </div>
              <div class="text-center my-5">
                <div class="text-3xl font-black text-white">{{ t.price }}</div>
              </div>
              <Link :href="route('subscribe')" class="join-btn">
                انضم للمسار <i class="fa-solid fa-arrow-left"></i>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============ ABOUT (نبذة) ============ -->
    <section id="about" class="py-20 sm:py-28 bg-white">
      <div class="max-w-5xl mx-auto px-6 text-center">
        <span class="text-[var(--brand)] font-bold" data-aos="fade-up">عن بارع</span>
        <h2 class="text-3xl sm:text-4xl font-black text-[var(--ink)] mt-3" data-aos="fade-up">رعاية تُثمر استدامة</h2>
        <p class="text-gray-500 mt-6 leading-loose text-lg max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="100">
          منصة بارع التعليمية هي وجهتك الأولى لتمكين الأجيال الناشئة والشباب من مهارات القرن الحادي والعشرين — عبر رحلة تعليمية تفاعلية تربط المعرفة بمتطلبات المستقبل، وتشارك في صناعة روّاد وقادة الغد.
        </p>
        <Link :href="route('about')" class="cta-primary mt-8 inline-flex" data-aos="fade-up" data-aos-delay="150">
          <span>تعرّف علينا أكثر</span>
          <i class="fa-solid fa-arrow-left"></i>
        </Link>
      </div>
    </section>

    <!-- ============ INSIGHTS / المدونة ============ -->
    <section id="insights" class="py-20 sm:py-28 bg-[#FAFAFB]">
      <div class="max-w-7xl mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-16" data-aos="fade-up">
          <span class="text-[var(--brand)] font-bold">رؤانا</span>
          <h2 class="text-3xl sm:text-4xl font-black text-[var(--ink)] mt-3">أحدث المقالات</h2>
          <p class="text-gray-500 mt-4 leading-loose">رؤى تربوية تساعدك على إعداد جيل واعٍ ومبتكر.</p>
        </div>

        <div v-if="insights.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-7">
          <Link v-for="post in insights" :key="post.slug" :href="route('blog.show', post.slug)"
                class="insight-card group" data-aos="fade-up">
            <div class="insight-cover" :class="`c-${post.category ? (post.category.length % 4) : 0}`">
              <i class="fa-solid fa-feather-pointed"></i>
            </div>
            <div class="p-7">
              <span v-if="post.category" class="insight-cat">{{ post.category }}</span>
              <h3 class="font-black text-lg text-[var(--ink)] leading-snug mt-3 mb-2 group-hover:text-[var(--brand)] transition-colors">{{ post.title }}</h3>
              <p class="text-gray-500 text-sm line-clamp-3 leading-relaxed">{{ post.excerpt }}</p>
            </div>
          </Link>
        </div>

        <div class="text-center mt-14" data-aos="fade-up">
          <Link :href="route('blog')" class="cta-ghost inline-flex">
            <span>كل المقالات</span>
            <i class="fa-solid fa-arrow-left"></i>
          </Link>
        </div>
      </div>
    </section>

    <!-- ============ CONTACT ============ -->
    <section id="contact" class="py-20 sm:py-28 bg-white">
      <div class="max-w-3xl mx-auto px-6">
        <div class="text-center mb-12" data-aos="fade-up">
          <span class="text-[var(--brand)] font-bold">تواصل معنا</span>
          <h2 class="text-3xl sm:text-4xl font-black text-[var(--ink)] mt-3">عندك سؤال؟ راسلنا</h2>
          <p class="text-gray-500 mt-4">سيعود إليك فريقنا خلال يوم عمل واحد.</p>
        </div>

        <div class="glass-card p-8 sm:p-10" data-aos="fade-up" data-aos-delay="100">
          <div v-if="$page.props.flash?.success" class="mb-6 bg-green-50 text-green-700 border border-green-200 rounded-2xl px-5 py-3 text-sm font-medium">
            {{ $page.props.flash.success }}
          </div>
          <form class="space-y-5" @submit.prevent="submitContact">
            <div class="grid sm:grid-cols-2 gap-5">
              <div>
                <input type="text" v-model="form.name" placeholder="الاسم الكريم" class="field">
                <span v-if="form.errors.name" class="err">{{ form.errors.name }}</span>
              </div>
              <div>
                <input type="email" v-model="form.email" placeholder="البريد الإلكتروني" dir="ltr" class="field">
                <span v-if="form.errors.email" class="err">{{ form.errors.email }}</span>
              </div>
            </div>
            <input type="text" v-model="form.phone" placeholder="رقم الجوال (اختياري)" dir="ltr" class="field">
            <input type="text" v-model="form.subject" placeholder="الموضوع (اختياري)" class="field">
            <div>
              <textarea v-model="form.message" rows="4" placeholder="رسالتك" class="field resize-none"></textarea>
              <span v-if="form.errors.message" class="err">{{ form.errors.message }}</span>
            </div>
            <button type="submit" :disabled="form.processing" class="cta-primary w-full justify-center">
              <span>{{ form.processing ? 'جاري الإرسال...' : 'إرسال الرسالة' }}</span>
              <i class="fa-solid fa-paper-plane" style="transform:scaleX(-1)"></i>
            </button>
          </form>
        </div>
      </div>
    </section>

  </Bare3Layout>
</template>

<style scoped>
/* ── أشكال 3D تجريدية (blobs) — هوية بصرية خاصة بـ بارع ── */
.blob { position:absolute; border-radius:50%; filter:blur(60px); opacity:.5; pointer-events:none; }
.blob-1 { width:420px; height:420px; background:radial-gradient(circle,#c9b4f0,#7c5cbf); top:-120px; right:-80px; }
.blob-2 { width:340px; height:340px; background:radial-gradient(circle,#ffd9b0,#f0806a); bottom:-100px; left:-60px; opacity:.35; }
.blob-3 { width:260px; height:260px; background:radial-gradient(circle,#b8ede6,#4bb5a8); top:40%; left:15%; opacity:.3; }

.eyebrow {
  display:inline-flex; align-items:center; gap:8px; background:#fff; color:var(--brand);
  font-weight:700; font-size:.85rem; padding:8px 18px; border-radius:9999px;
  box-shadow:0 4px 20px rgba(124,92,191,.12); border:1px solid var(--brand-soft);
}

/* الأزرار — padding مضبوط + سهم لليسار */
.cta-primary {
  display:inline-flex; align-items:center; gap:10px; background:var(--brand); color:#fff;
  font-weight:700; font-size:1.05rem; padding:15px 34px; border-radius:9999px;
  box-shadow:0 8px 24px rgba(124,92,191,.28); transition:all .25s ease; text-decoration:none;
}
.cta-primary:hover { background:var(--brand-dark); transform:translateY(-2px); }
.cta-ghost {
  display:inline-flex; align-items:center; gap:10px; background:#fff; color:var(--ink);
  font-weight:700; font-size:1.05rem; padding:15px 34px; border-radius:9999px;
  border:2px solid #ece8f5; transition:all .25s ease; text-decoration:none;
}
.cta-ghost:hover { border-color:var(--brand); color:var(--brand); }

.glass-mini {
  background:rgba(255,255,255,.6); backdrop-filter:blur(10px);
  border:1px solid rgba(255,255,255,.9); border-radius:20px; padding:18px 20px;
  box-shadow:0 8px 30px rgba(30,27,46,.05);
}

/* ── بطاقات المسارات (Glassmorphism + hover reveal) ── */
.track-card {
  position:relative; overflow:hidden; border-radius:28px; min-height:280px;
  background:rgba(255,255,255,.65); backdrop-filter:blur(14px);
  border:1px solid rgba(255,255,255,.85); box-shadow:0 10px 40px rgba(30,27,46,.06);
  transition:transform .4s ease, box-shadow .4s ease;
}
.track-card:hover { transform:translateY(-8px); box-shadow:0 24px 50px rgba(30,27,46,.14); }
.track-face { padding:32px 28px; transition:opacity .35s ease; }
.track-icon {
  width:60px; height:60px; border-radius:18px; display:flex; align-items:center; justify-content:center;
  font-size:1.6rem; color:#fff; background:var(--tc); margin-bottom:18px;
  box-shadow:0 8px 20px color-mix(in srgb, var(--tc) 40%, transparent);
}
.track-hover {
  position:absolute; inset:0; padding:32px 28px; display:flex; flex-direction:column; justify-content:center;
  background:linear-gradient(160deg, color-mix(in srgb, var(--tc) 92%, black 5%), color-mix(in srgb, var(--tc) 70%, black 20%));
  opacity:0; transition:opacity .35s ease;
}
.track-card:hover .track-hover { opacity:1; }
.hover-tag {
  background:rgba(255,255,255,.2); color:#fff; font-weight:700; font-size:.72rem;
  padding:5px 12px; border-radius:9999px;
}
.join-btn {
  display:inline-flex; align-items:center; justify-content:center; gap:8px; background:#fff; color:var(--ink);
  font-weight:800; padding:13px; border-radius:9999px; transition:transform .2s ease; text-decoration:none;
}
.join-btn:hover { transform:scale(1.03); }

/* ── بطاقات المدونة ── */
.insight-card {
  background:#fff; border-radius:24px; overflow:hidden; border:1px solid #f0eef7;
  box-shadow:0 6px 24px rgba(30,27,46,.05); transition:transform .3s ease, box-shadow .3s ease; display:block; text-decoration:none;
}
.insight-card:hover { transform:translateY(-6px); box-shadow:0 18px 40px rgba(30,27,46,.1); }
.insight-cover { height:120px; display:flex; align-items:center; justify-content:center; font-size:2.4rem; color:#fff; }
.insight-cover.c-0 { background:linear-gradient(135deg,#7c5cbf,#5f4398); }
.insight-cover.c-1 { background:linear-gradient(135deg,#4bb5a8,#2f8a7e); }
.insight-cover.c-2 { background:linear-gradient(135deg,#f0806a,#d95f4a); }
.insight-cover.c-3 { background:linear-gradient(135deg,#f2b866,#d99236); }
.insight-cat {
  display:inline-block; background:var(--brand-soft); color:var(--brand);
  font-size:.72rem; font-weight:800; padding:4px 12px; border-radius:9999px;
}

/* ── فورم زجاجي ── */
.glass-card {
  background:rgba(255,255,255,.7); backdrop-filter:blur(16px);
  border:1px solid #f0eef7; border-radius:28px; box-shadow:0 12px 40px rgba(30,27,46,.07);
}
.field {
  width:100%; padding:14px 18px; border-radius:16px; border:2px solid #f0eef7; background:#fafafb;
  font-size:1rem; color:var(--ink); transition:border-color .2s ease;
}
.field:focus { outline:none; border-color:var(--brand); background:#fff; }
.err { color:#dc2626; font-size:.78rem; font-weight:700; margin-top:5px; display:block; }
</style>
