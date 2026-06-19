<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  brand:  { type: Object, default: () => ({ logo: '/images/logo.png' }) },
  footer: { type: Object, default: () => ({}) },
})
</script>

<template>
  <div class="pub" dir="rtl">
    <!-- NAVBAR -->
    <nav class="pub-nav">
      <div class="pub-nav-inner">
        <Link href="/" class="pub-logo"><img :src="brand.logo" alt="بارع" /></Link>
        <ul class="pub-links">
          <li><Link href="/">الرئيسية</Link></li>
          <li><Link :href="route('subscribe')">الاشتراك</Link></li>
          <li><Link :href="route('about')">عن المعهد</Link></li>
          <li><Link :href="route('contact')">تواصل معنا</Link></li>
        </ul>
        <Link :href="route('login')" class="pub-btn">تسجيل الدخول</Link>
      </div>
    </nav>

    <!-- PAGE CONTENT -->
    <main class="pub-main">
      <slot />
    </main>

    <!-- FOOTER -->
    <footer class="pub-footer">
      <div class="pub-footer-inner">
        <div class="pf-brand">
          <img :src="footer.logo || brand.logo" alt="بارع" class="pf-logo" />
          <p v-if="footer.desc" v-html="footer.desc"></p>
          <div class="pf-socials" v-if="footer.socials">
            <a v-for="(s, i) in footer.socials" :key="i" :href="s.href"><i :class="s.icon"></i></a>
          </div>
        </div>
        <div class="pf-col">
          <h4>روابط</h4>
          <ul>
            <li><Link href="/">الرئيسية</Link></li>
            <li><Link :href="route('subscribe')">الاشتراك</Link></li>
            <li><Link :href="route('about')">عن المعهد</Link></li>
            <li><Link :href="route('contact')">تواصل معنا</Link></li>
          </ul>
        </div>
        <div class="pf-col" v-if="footer">
          <h4>{{ footer.col_contact_title || 'تواصل معنا' }}</h4>
          <address>
            {{ footer.contact_location }}<br>
            {{ footer.contact_phone }}<br>
            {{ footer.contact_email }}
          </address>
        </div>
      </div>
      <div class="pf-bottom">{{ footer.copyright || '© بارع — جميع الحقوق محفوظة' }}</div>
    </footer>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@400;600;700;800&display=swap');
.pub { font-family: 'ThmanyahSans', 'Segoe UI', Tahoma, sans-serif !important;
  --sky: #38BDF8; --sky-dark: #0E7490; --pink: #EC4899; --dark: #1C1C2E; --gray: #6B7280; --light: #F8FAFC;
  color: var(--dark); background: var(--light); min-height: 100vh; display: flex; flex-direction: column; }

.pub-nav { background: rgba(255,255,255,.95); backdrop-filter: blur(12px); border-bottom: 3px solid #E0F4FF; position: sticky; top: 0; z-index: 50; padding: 0 2rem; }
.pub-nav-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; height: 70px; gap: 1rem; }
.pub-logo img { width: 160px; }
.pub-links { display: flex; gap: 1.8rem; list-style: none; }
.pub-links a { font-weight: 700; color: var(--dark); text-decoration: none; transition: color .2s; }
.pub-links a:hover { color: var(--sky-dark); }
.pub-btn { background: var(--sky); color: #fff; font-weight: 800; padding: .6rem 1.4rem; border-radius: 50px; text-decoration: none; box-shadow: 0 4px 0 var(--sky-dark); }

.pub-main { flex: 1; }

.pub-footer { background: var(--dark); color: #fff; padding: 3rem 2rem 1.5rem; margin-top: 3rem; }
.pub-footer-inner { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 2rem; }
.pf-logo { width: 150px; margin-bottom: 1rem; }
.pf-brand p { font-size: .85rem; opacity: .7; line-height: 1.7; margin-bottom: 1rem; }
.pf-socials { display: flex; gap: .7rem; }
.pf-socials a { width: 36px; height: 36px; border-radius: 50%; border: 1px solid rgba(255,255,255,.15); display: flex; align-items: center; justify-content: center; color: #fff; }
.pf-socials a:hover { background: rgba(255,255,255,.1); }
.pf-col h4 { font-weight: 800; margin-bottom: 1rem; }
.pf-col ul { list-style: none; }
.pf-col li { margin-bottom: .6rem; }
.pf-col a { color: #fff; opacity: .7; text-decoration: none; font-size: .88rem; }
.pf-col a:hover { opacity: 1; }
.pf-col address { font-style: normal; opacity: .7; font-size: .88rem; line-height: 2; }
.pf-bottom { max-width: 1100px; margin: 2rem auto 0; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,.1); text-align: center; font-size: .82rem; opacity: .6; }

@media (max-width: 768px) {
  .pub-links { display: none; }
  .pub-footer-inner { grid-template-columns: 1fr; }
}
</style>
