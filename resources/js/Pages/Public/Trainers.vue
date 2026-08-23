<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import Bare3Layout from '@/Layouts/Bare3Layout.vue'

defineProps({
  brand:  Object,
  footer: Object,
  tracks: { type: Array, default: () => [] },
})

const page = usePage()
const flash = computed(() => page.props.flash?.success)

const form = useForm({
  first_name: '', last_name: '', email: '', phone: '',
  country: '', tracks: [], linkedin: '', bio: '', cv: null,
})

const cvName = (e) => { form.cv = e.target.files?.[0] ?? null }

const submit = () => form.post(route('trainers.store'), {
  preserveScroll: true,
  forceFormData: true,
  onSuccess: () => form.reset(),
})
</script>

<template>
  <Head title="انضم لشبكة مدربي وخبراء بارِع" />
  <Bare3Layout active="trainers">

    <!-- HERO -->
    <section class="trainers-hero">
      <div class="hero-inner">
        <div class="hero-text">
          <h1 data-aos="fade-up">انضم لشبكة مدربي وخبراء بارِع</h1>
          <p class="hero-sub" data-aos="fade-up" data-aos-delay="150">
            لأن الأثر المستدام يصنعه المبدعون.. شاركنا شغفك في صقل مهارات قادة الغد
          </p>
        </div>
        <img src="/images/characters/01.png" alt="شخصية بارع" class="hero-char" data-aos="fade-left" data-aos-delay="200" />
      </div>
    </section>

    <!-- DESCRIPTION -->
    <section class="intro">
      <div class="intro-card" data-aos="fade-up">
        <p>
          في منصة "بارِع" نؤمن بأن الخبرة والمهارة هما الوقود الحقيقي لإلهام الناشئة والأطفال.
          نفتح باب الشراكة والاستقطاب لكل مدرب، خبير، وميسّر يمتلك الشغف في مجالات مهارات القرن الـ21
          (الذكاء الاصطناعي، الوعي المالي، التفكير التصميمي، وحل المشكلات) ليكون جزءاً من رحلتنا التفاعلية.
        </p>
      </div>
    </section>

    <!-- FORM -->
    <section class="form-section">
      <div class="form-card" data-aos="fade-up">
        <h2 class="form-title">نموذج التسجيل</h2>

        <div v-if="flash" class="flash-ok">
          <i class="fa-solid fa-circle-check"></i> {{ flash }}
        </div>

        <form @submit.prevent="submit">
          <div class="grid2">
            <div class="field">
              <label>الاسم الأول <span class="req">*</span></label>
              <input v-model="form.first_name" class="inp" />
              <span v-if="form.errors.first_name" class="err">{{ form.errors.first_name }}</span>
            </div>
            <div class="field">
              <label>الاسم الأخير <span class="req">*</span></label>
              <input v-model="form.last_name" class="inp" />
              <span v-if="form.errors.last_name" class="err">{{ form.errors.last_name }}</span>
            </div>
            <div class="field">
              <label>البريد الإلكتروني <span class="req">*</span></label>
              <input v-model="form.email" type="email" class="inp" dir="ltr" />
              <span v-if="form.errors.email" class="err">{{ form.errors.email }}</span>
            </div>
            <div class="field">
              <label>رقم الجوال / الواتساب <span class="req">*</span></label>
              <input v-model="form.phone" class="inp" dir="ltr" />
              <span v-if="form.errors.phone" class="err">{{ form.errors.phone }}</span>
            </div>
          </div>

          <div class="field">
            <label>الدولة والإقامة</label>
            <select v-model="form.country" class="inp">
              <option value="">— اختر الدولة —</option>
              <option>المملكة العربية السعودية</option>
              <option>الإمارات العربية المتحدة</option>
              <option>الكويت</option>
              <option>قطر</option>
              <option>البحرين</option>
              <option>عُمان</option>
              <option>مصر</option>
              <option>الأردن</option>
              <option>دولة أخرى</option>
            </select>
          </div>

          <div class="field">
            <label>مجال الخبرة / المسار المفضل <span class="hint-inline">(اختيار متعدد)</span></label>
            <div class="tracks-grid">
              <label v-for="t in tracks" :key="t" class="track-opt" :class="{ on: form.tracks.includes(t) }">
                <input type="checkbox" :value="t" v-model="form.tracks" />
                <span>{{ t }}</span>
              </label>
            </div>
          </div>

          <div class="field">
            <label>رابط حساب LinkedIn</label>
            <input v-model="form.linkedin" class="inp" dir="ltr" placeholder="https://linkedin.com/in/..." />
            <span v-if="form.errors.linkedin" class="err">{{ form.errors.linkedin }}</span>
          </div>

          <div class="field">
            <label>رفع السيرة الذاتية (CV)</label>
            <input type="file" accept=".pdf,.doc,.docx" class="inp" @change="cvName" />
            <span class="hint">مستند PDF أو Word — بحد أقصى 5 ميجابايت</span>
            <span v-if="form.errors.cv" class="err">{{ form.errors.cv }}</span>
          </div>

          <div class="field">
            <label>نبذة مختصرة عن الخبرات السابقة والمهارات</label>
            <textarea v-model="form.bio" rows="5" class="inp"></textarea>
            <span v-if="form.errors.bio" class="err">{{ form.errors.bio }}</span>
          </div>

          <button type="submit" class="btn-submit" :disabled="form.processing">
            <span>{{ form.processing ? 'جاري الإرسال...' : 'تقديم طلب الانضمام' }}</span>
            <i v-if="!form.processing" class="fa-solid fa-arrow-left"></i>
          </button>
        </form>
      </div>
    </section>

    <!-- CLOSING -->
    <section class="closing">
      <p data-aos="fade-up">
        في بارِع، لا نمنح المعرفة فقط، بل نسهم مع الخبراء في إعداد جيل ملهم وقادر على صناعة المستقبل.
      </p>
    </section>
  </Bare3Layout>
</template>

<style scoped>
/* ── HERO ── */
.trainers-hero {
  padding: 4rem 1.5rem 3.5rem;
  color: #fff;
  overflow: hidden;
  background:
    radial-gradient(circle at 15% 40%, rgba(242,184,102,.30), transparent 45%),
    radial-gradient(circle at 85% 60%, rgba(61,110,165,.30), transparent 45%),
    linear-gradient(135deg, #5f4398, #7c5cbf);
}
.hero-inner { max-width: 1100px; margin: 0 auto; display: grid; gap: 2rem; align-items: center; }
@media (min-width: 1024px) { .hero-inner { grid-template-columns: 1fr auto; } }
.hero-text { text-align: center; }
@media (min-width: 1024px) { .hero-text { text-align: right; } }
.trainers-hero h1 { font-size: clamp(1.8rem, 4vw, 2.9rem); font-weight: 900; line-height: 1.35; margin-bottom: 1rem; }
.hero-sub { font-size: 1.15rem; color: rgba(255,255,255,.88); line-height: 1.9; max-width: 640px; margin: 0 auto; }
@media (min-width: 1024px) { .hero-sub { margin: 0; } }
.hero-char { width: 260px; height: auto; filter: drop-shadow(0 24px 40px rgb(0 0 0 / .28)); display: none; }
@media (min-width: 1024px) { .hero-char { display: block; } }

/* ── INTRO ── */
.intro { padding: 3rem 1.5rem 0; background: #FAFAFB; }
.intro-card {
  max-width: 880px; margin: 0 auto; background: #fff;
  border: 1px solid #f0eef7; border-radius: 28px; padding: 2rem 2.2rem;
  box-shadow: 0 12px 40px rgba(30,27,46,.07);
}
.intro-card p { color: #4b5563; font-size: 1.05rem; line-height: 2.1; margin: 0; }

/* ── FORM ── */
.form-section { padding: 3rem 1.5rem; background: #FAFAFB; }
.form-card {
  max-width: 880px; margin: 0 auto; background: #fff;
  border: 1px solid #f0eef7; border-radius: 28px; padding: 2.4rem 2.2rem;
  box-shadow: 0 12px 40px rgba(30,27,46,.07);
}
.form-title { font-size: 1.5rem; font-weight: 900; color: #1e1b2e; margin-bottom: 1.6rem; }

.flash-ok {
  background: #eaf7f5; border: 1px solid #9fdcd3; color: #2f8a7e;
  border-radius: 14px; padding: .9rem 1.1rem; font-weight: 700; margin-bottom: 1.4rem;
  display: flex; align-items: center; gap: .5rem;
}

.grid2 { display: grid; grid-template-columns: 1fr; gap: 1.1rem; }
@media (min-width: 640px) { .grid2 { grid-template-columns: 1fr 1fr; } }

.field { display: flex; flex-direction: column; gap: .4rem; margin-bottom: 1.1rem; }
.field label { font-weight: 700; font-size: .9rem; color: #1e1b2e; }
.req { color: #f0806a; }
.hint-inline { font-weight: 600; font-size: .78rem; color: #94A3B8; }
.inp {
  width: 100%; padding: 14px 18px; border-radius: 16px; border: 2px solid #f0eef7;
  background: #fafafb; font-family: inherit; font-size: 1rem; color: #1e1b2e;
  outline: none; transition: border-color .2s ease, background .2s ease;
}
.inp:focus { border-color: #7c5cbf; background: #fff; }
textarea.inp { resize: vertical; line-height: 1.8; }
.hint { font-size: .76rem; color: #94A3B8; }
.err { color: #dc2626; font-size: .78rem; font-weight: 700; }

/* اختيار متعدد للمسارات */
.tracks-grid { display: grid; grid-template-columns: 1fr; gap: .6rem; margin-top: .2rem; }
@media (min-width: 640px) { .tracks-grid { grid-template-columns: 1fr 1fr; } }
.track-opt {
  display: flex; align-items: center; gap: .6rem; cursor: pointer;
  padding: .8rem 1rem; border: 2px solid #f0eef7; border-radius: 14px;
  background: #fafafb; font-size: .88rem; font-weight: 700; color: #4b5563;
  transition: all .2s ease;
}
.track-opt:hover { border-color: #c9b4f0; }
.track-opt.on { border-color: #7c5cbf; background: #f3effb; color: #5f4398; }
.track-opt input { width: 18px; height: 18px; accent-color: #7c5cbf; flex-shrink: 0; }

.btn-submit {
  display: inline-flex; align-items: center; justify-content: center; gap: 10px; width: 100%;
  background: #7c5cbf; color: #fff; font-family: inherit; font-weight: 700; font-size: 1.05rem;
  padding: 15px 34px; border: none; border-radius: 9999px; cursor: pointer;
  box-shadow: 0 8px 24px rgba(124,92,191,.28); transition: all .25s ease; margin-top: .6rem;
}
.btn-submit:hover:not(:disabled) { background: #5f4398; transform: translateY(-2px); }
.btn-submit:disabled { opacity: .6; cursor: not-allowed; }

/* ── CLOSING ── */
.closing { padding: 0 1.5rem 4rem; background: #FAFAFB; }
.closing p {
  max-width: 760px; margin: 0 auto; text-align: center;
  font-size: 1.15rem; font-weight: 800; line-height: 2; color: #5f4398;
  background: #f3effb; border-radius: 24px; padding: 2rem 2.2rem;
}
</style>
