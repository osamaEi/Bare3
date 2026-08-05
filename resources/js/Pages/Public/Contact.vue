<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import Bare3Layout from '@/Layouts/Bare3Layout.vue'

const props = defineProps({ brand: Object, footer: Object })

const page = usePage()
const flash = computed(() => page.props.flash?.success)

const form = useForm({ name: '', email: '', phone: '', subject: '', message: '' })
const submit = () => form.post(route('contact.store'), {
  preserveScroll: true,
  onSuccess: () => form.reset(),
})
</script>

<template>
  <Head title="تواصل معنا — بارع" />
  <Bare3Layout active="contact">
    <section class="hero-band">
      <h1>تواصل معنا</h1>
      <p>عندك سؤال أو اقتراح؟ يسعدنا أن نسمع منك</p>
    </section>

    <section class="contact-wrap">
      <!-- Info -->
      <div class="contact-info">
        <h2>معلومات التواصل</h2>
        <div class="info-row"><i class="fa-solid fa-location-dot"></i><span>{{ footer.contact_location }}</span></div>
        <div class="info-row"><i class="fa-solid fa-phone"></i><span>{{ footer.contact_phone }}</span></div>
        <div class="info-row"><i class="fa-solid fa-envelope"></i><span>{{ footer.contact_email }}</span></div>
        <div class="socials" v-if="footer.socials">
          <a v-for="(s, i) in footer.socials" :key="i" :href="s.href"><i :class="s.icon"></i></a>
        </div>
      </div>

      <!-- Form -->
      <div class="contact-form">
        <div v-if="flash" class="flash-ok"><i class="fa-solid fa-circle-check"></i> {{ flash }}</div>
        <div class="grid2">
          <div class="field">
            <label>الاسم</label>
            <input v-model="form.name" class="inp" />
            <span v-if="form.errors.name" class="err">{{ form.errors.name }}</span>
          </div>
          <div class="field">
            <label>البريد الإلكتروني</label>
            <input v-model="form.email" type="email" class="inp" />
            <span v-if="form.errors.email" class="err">{{ form.errors.email }}</span>
          </div>
          <div class="field">
            <label>الهاتف (اختياري)</label>
            <input v-model="form.phone" class="inp" />
          </div>
          <div class="field">
            <label>الموضوع (اختياري)</label>
            <input v-model="form.subject" class="inp" />
          </div>
        </div>
        <div class="field">
          <label>الرسالة</label>
          <textarea v-model="form.message" rows="5" class="inp"></textarea>
          <span v-if="form.errors.message" class="err">{{ form.errors.message }}</span>
        </div>
        <button class="btn-send" :disabled="form.processing" @click="submit">
          <i class="fa-solid fa-paper-plane"></i> {{ form.processing ? 'جاري الإرسال...' : 'إرسال الرسالة' }}
        </button>
      </div>
    </section>
  </Bare3Layout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@500;700;800&display=swap');

/* ===== HERO ===== */
.hero-band {
  position: relative;
  overflow: hidden;
  text-align: center;
  padding: 5.5rem 2rem 7rem;
  background:
    radial-gradient(circle at 15% 40%, rgba(242,184,102,.30), transparent 45%),
    radial-gradient(circle at 85% 60%, rgba(61,110,165,.30), transparent 45%),
    linear-gradient(135deg, #5f4398, #7c5cbf);
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
  color: #fff;
  margin-bottom: .7rem;
}

.hero-band p {
  position: relative;
  z-index: 1;
  color: rgba(255,255,255,.85);
  font-size: 1.15rem;
  font-weight: 600;
}

/* ===== CONTACT WRAP ===== */
.contact-wrap {
  max-width: 1040px;
  margin: 0 auto;
  padding: 4rem 2rem 5rem;
  display: grid;
  grid-template-columns: 1fr 1.6fr;
  gap: 2.4rem;
  align-items: start;
}

/* ===== INFO CARD — luggage-tag style ===== */
.contact-info {
  position: relative;
  background: linear-gradient(160deg, #FFF8E7, #FBEFD3);
  color: #1C1C2E;
  border-radius: 26px 10px 26px 10px;
  border: 2px dashed #DCC890;
  padding: 2.4rem 2rem;
}

.contact-info::before {
  content: '';
  position: absolute;
  top: 22px;
  left: 22px;
  width: 18px;
  height: 18px;
  background: #FFFBF0;
  border: 2px solid #DCC890;
  border-radius: 50%;
  box-shadow: inset 0 1px 3px rgba(0,0,0,.15);
}

.contact-info h2 {
  font-family: 'Baloo Bhaijaan 2', sans-serif;
  font-size: 1.3rem;
  font-weight: 800;
  margin: 0 0 1.7rem 2.2rem;
  position: relative;
}

.contact-info h2::before {
  content: 'بطاقة التعريف';
  display: block;
  font-family: 'Segoe UI', Tahoma, sans-serif;
  font-size: .68rem;
  font-weight: 700;
  letter-spacing: .12em;
  color: #A16207;
  margin-bottom: .35rem;
}

.info-row {
  display: flex;
  align-items: center;
  gap: .9rem;
  margin-bottom: 1.25rem;
  font-size: .92rem;
  font-weight: 600;
  color: #334155;
}

.info-row i {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: var(--row-soft, #E0F4FF);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--row-color, #0E7490);
  flex-shrink: 0;
  font-size: .95rem;
  border: 2px dashed var(--row-color, #0E7490);
  transform: rotate(var(--row-tilt, 0deg));
}

.info-row:nth-child(2) { --row-color: #0E7490; --row-soft: #E0F4FF; --row-tilt: -4deg; }
.info-row:nth-child(3) { --row-color: #BE185D; --row-soft: #FCE7F3; --row-tilt: 3deg; }
.info-row:nth-child(4) { --row-color: #A16207; --row-soft: #FEF3C7; --row-tilt: -3deg; }

.socials {
  display: flex;
  gap: .7rem;
  margin-top: 1.7rem;
  padding-top: 1.4rem;
  border-top: 2px dashed #DCC890;
}

.socials a {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 2px solid #DCC890;
  background: #FFFBF0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #92702F;
  transition: transform .2s ease, background .2s ease;
}

.socials a:hover {
  background: #fff;
  transform: translateY(-3px) rotate(-6deg);
}

/* ===== FORM CARD — airmail envelope frame ===== */
.contact-form {
  position: relative;
  background: #fff;
  border-radius: 26px;
  padding: 2.3rem 2.2rem;
  box-shadow: 0 12px 30px rgba(28,28,46,.08);
}

.contact-form::before {
  content: '';
  position: absolute;
  inset: -9px;
  border-radius: 30px;
  padding: 9px;
  background: repeating-linear-gradient(135deg,
    #EF4444 0 14px, #fff 14px 22px,
    #1C1C2E 22px 36px, #fff 36px 44px);
  -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
  z-index: -1;
}

.contact-form::after {
  content: '';
  position: absolute;
  top: -24px;
  right: 38px;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M2 12L22 2L13 22L11 13L2 12Z' fill='%2338BDF8'/%3E%3C/svg%3E") center/26px no-repeat;
  border: 3px dashed #94A3B8;
  box-shadow: 0 6px 16px rgba(28,28,46,.1);
}

.flash-ok {
  background: #F0FDF4;
  border: 1.5px solid #BBF7D0;
  color: #15803D;
  border-radius: 50px;
  padding: .8rem 1.2rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: .5rem;
}

.grid2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.1rem;
}

.field {
  display: flex;
  flex-direction: column;
  gap: .4rem;
  margin-bottom: 1.3rem;
}

.field label {
  font-size: .8rem;
  font-weight: 700;
  color: #94A3B8;
  letter-spacing: .02em;
}

.inp {
  padding: .55rem .1rem;
  border: none;
  border-bottom: 2px dashed #CBD5E1;
  border-radius: 0;
  font-family: inherit;
  font-size: .98rem;
  color: #1C1C2E;
  width: 100%;
  background: transparent;
  transition: border-color .2s ease, border-style .2s ease;
}

.inp:focus {
  outline: none;
  border-bottom: 2px solid #7c5cbf;
}

textarea.inp {
  background-image: repeating-linear-gradient(to bottom,
    transparent 0, transparent 33px, #E2E8F0 33px, #E2E8F0 35px);
  line-height: 35px;
  padding-top: 2px;
  resize: vertical;
}

textarea.inp:focus {
  background-image: repeating-linear-gradient(to bottom,
    transparent 0, transparent 33px, #BAE6FD 33px, #BAE6FD 35px);
}

.err {
  color: #DC2626;
  font-size: .78rem;
  font-weight: 700;
}

.btn-send {
  background: linear-gradient(135deg, #7c5cbf, #5f4398);
  color: #fff;
  border: none;
  font-family: inherit;
  font-weight: 800;
  font-size: .95rem;
  padding: .9rem 2.3rem;
  border-radius: 50px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: .6rem;
  box-shadow: 0 5px 0 #4a3479;
  transition: transform .12s ease, box-shadow .12s ease;
}

.btn-send:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 7px 0 #4a3479;
}

.btn-send:active:not(:disabled) {
  transform: translateY(3px);
  box-shadow: 0 2px 0 #4a3479;
}

.btn-send:disabled {
  opacity: .6;
  cursor: not-allowed;
  box-shadow: 0 5px 0 #4a3479;
}

@media (max-width: 768px) {
  .contact-wrap { grid-template-columns: 1fr; padding: 3rem 1.5rem 4rem; }
  .grid2 { grid-template-columns: 1fr; }
  .contact-form::after { top: -22px; right: 24px; width: 50px; height: 50px; }
}

@media (prefers-reduced-motion: reduce) {
  .btn-send, .socials a, .inp { transition: none; }
}
</style>