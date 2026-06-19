<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import PublicShell from '@/Components/PublicShell.vue'

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
  <PublicShell :brand="brand" :footer="footer">
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
  </PublicShell>
</template>

<style scoped>
.hero-band { text-align: center; padding: 4rem 2rem 2rem; background: linear-gradient(135deg, #E0F4FF, #FCE7F3); }
.hero-band h1 { font-size: clamp(1.8rem, 4vw, 2.6rem); font-weight: 900; color: #1C1C2E; margin-bottom: .6rem; }
.hero-band p { color: #6B7280; font-size: 1.1rem; }

.contact-wrap { max-width: 1000px; margin: 0 auto; padding: 3rem 2rem; display: grid; grid-template-columns: 1fr 1.6fr; gap: 2rem; align-items: start; }
.contact-info { background: #1C1C2E; color: #fff; border-radius: 20px; padding: 2rem; }
.contact-info h2 { font-size: 1.3rem; font-weight: 900; margin-bottom: 1.5rem; }
.info-row { display: flex; align-items: center; gap: .8rem; margin-bottom: 1.1rem; font-size: .92rem; }
.info-row i { width: 38px; height: 38px; border-radius: 10px; background: rgba(255,255,255,.1); display: flex; align-items: center; justify-content: center; color: #7DD3F8; flex-shrink: 0; }
.socials { display: flex; gap: .7rem; margin-top: 1.5rem; }
.socials a { width: 38px; height: 38px; border-radius: 50%; border: 1px solid rgba(255,255,255,.15); display: flex; align-items: center; justify-content: center; color: #fff; }
.socials a:hover { background: rgba(255,255,255,.1); }

.contact-form { background: #fff; border-radius: 20px; padding: 2rem; box-shadow: 0 4px 24px rgba(0,0,0,.06); }
.flash-ok { background: #F0FDF4; border: 1.5px solid #BBF7D0; color: #15803D; border-radius: 12px; padding: .8rem 1rem; font-weight: 700; margin-bottom: 1.2rem; display: flex; align-items: center; gap: .5rem; }
.grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.field { display: flex; flex-direction: column; gap: .35rem; margin-bottom: 1rem; }
.field label { font-size: .82rem; font-weight: 700; color: #475569; }
.inp { padding: .7rem .9rem; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; font-size: .9rem; color: #1C1C2E; width: 100%; }
.inp:focus { outline: none; border-color: #38BDF8; }
.err { color: #DC2626; font-size: .78rem; font-weight: 700; }
.btn-send { background: #38BDF8; color: #fff; border: none; font-family: inherit; font-weight: 800; font-size: .95rem; padding: .85rem 2rem; border-radius: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: .5rem; box-shadow: 0 4px 0 #0E7490; }
.btn-send:disabled { opacity: .6; cursor: not-allowed; }

@media (max-width: 768px) { .contact-wrap { grid-template-columns: 1fr; } .grid2 { grid-template-columns: 1fr; } }
</style>
