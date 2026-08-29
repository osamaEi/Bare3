<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  brand:  { type: Object, default: () => ({ logo: '/images/logo-horizontal.png' }) },
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
          <li><Link :href="route('blog')">المدونة</Link></li>
          <li><Link :href="route('contact')">تواصل معنا</Link></li>
        </ul>
        <Link :href="route('login')" class="pub-btn">تسجيل الدخول</Link>
      </div>
    </nav>

    <!-- PAGE CONTENT -->
    <main class="pub-main">
      <slot />
    </main>

<footer class="pub-footer">
  <div class="pub-footer-inner">

    <div class="footer-grid">

      <div class="pf-brand">
        <div class="logo-text">
          <img :src="footer.logo || brand.logo" class="pf-logo" alt="logo" />
        </div>

        <p v-if="footer.desc" v-html="footer.desc"></p>

        <div class="pf-socials" v-if="footer.socials">
          <a v-for="(s, i) in footer.socials" :key="i" :href="s.href">
            <i :class="s.icon"></i>
          </a>
        </div>
      </div>

      <div class="pf-col">
        <h4>{{ footer.col_pages_title || 'روابط' }}</h4>
        <ul>
          <li><Link href="/">الرئيسية</Link></li>
          <li><Link :href="route('subscribe')">الاشتراك</Link></li>
          <li><Link :href="route('about')">عن المعهد</Link></li>
          <li><Link :href="route('blog')">المدونة</Link></li>
          <li><Link :href="route('contact')">تواصل معنا</Link></li>
        </ul>
      </div>

      <div class="pf-col" v-if="footer.col_help">
        <h4>{{ footer.col_help_title || 'المساعدة' }}</h4>
        <ul>
          <li v-for="(l, i) in footer.col_help" :key="i">
            <a :href="l.href">{{ l.label }}</a>
          </li>
        </ul>
      </div>

      <div class="pf-col">
        <h4>{{ footer.col_contact_title || 'تواصل معنا' }}</h4>
        <ul class="footer-links-list">
          <li><Link :href="route('privacy.policy')" class="footer-link-item">سياسة الخصوصية</Link></li>
          <li><Link :href="route('terms')" class="footer-link-item">الشروط والأحكام</Link></li>
        </ul>
        <address>
          {{ footer.contact_location }}<br>
          {{ footer.contact_phone }}<br>
          {{ footer.contact_email }}
        </address>
      </div>

    </div>

    <div class="pf-bottom">
      <span>{{ footer.copyright || '© بارع — جميع الحقوق محفوظة' }}</span>

      <div class="footer-langs">
        <span>🇪🇬 العربية</span>
        <span>🇬🇧 English</span>
      </div>
    </div>

  </div>
</footer>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Poppins:wght@400;500;700;800&display=swap');

.pub {
  font-family: 'Tajawal', 'Poppins', sans-serif;
  --brand: #7c5cbf;
  --brand-dark: #5f4398;
  --brand-soft: #f3effb;
  --ink: #1e1b2e;
  --coral: #f0806a;
  --teal: #4bb5a8;
  --amber: #f2b866;
  color: var(--ink);
  background: #fafafb;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.pub-nav {
  background: rgba(255,255,255,0.92);
  backdrop-filter: blur(16px);
  border-bottom: 1px solid #f0ebfa;
  position: sticky;
  top: 0;
  z-index: 50;
  padding: 0 1.5rem;
}

.pub-nav-inner {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 74px;
  gap: 1rem;
}

.pub-logo img {
  width: 155px;
  height: auto;
}

.pub-links {
  display: flex;
  gap: 1.6rem;
  list-style: none;
  margin: 0;
  padding: 0;
}

.pub-links a {
  font-weight: 700;
  color: var(--ink);
  text-decoration: none;
  transition: color 0.2s ease;
}

.pub-links a:hover {
  color: var(--brand);
}

.pub-btn {
  background: var(--brand);
  color: #fff;
  font-weight: 800;
  padding: 0.68rem 1.2rem;
  border-radius: 999px;
  text-decoration: none;
  box-shadow: 0 8px 20px rgba(124, 92, 191, 0.18);
  transition: transform 0.2s ease, background 0.2s ease;
}

.pub-btn:hover {
  background: var(--brand-dark);
  transform: translateY(-1px);
}

.pub-main {
  flex: 1;
  background: linear-gradient(180deg, #fcfbff 0%, #f7f5fc 100%);
}

.pub-footer {
  padding: 3.5rem 1.5rem 2rem;
  background: linear-gradient(180deg, rgba(30,27,46,0.97), rgba(45,34,68,0.98));
  color: #fff;
}

.pub-footer-inner {
  max-width: 1200px;
  margin: 0 auto;
}

.footer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
  margin-bottom: 2.5rem;
}

.pf-logo {
  width: 150px;
  margin-bottom: 1rem;
}

.pf-brand p {
  font-size: 0.9rem;
  opacity: 0.75;
  line-height: 1.8;
  margin-bottom: 1rem;
}

.pf-socials {
  display: flex;
  gap: 0.7rem;
}

.pf-socials a {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 1px solid rgba(255,255,255,0.16);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  transition: 0.2s ease;
}

.pf-socials a:hover {
  background: var(--brand);
  transform: translateY(-2px);
}

.pf-col h4 {
  font-weight: 800;
  margin-bottom: 1rem;
}

.pf-col ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.pf-col li {
  margin-bottom: 0.6rem;
}

.pf-col a {
  opacity: 0.78;
  font-size: 0.9rem;
  text-decoration: none;
  transition: 0.2s ease;
}

.pf-col a:hover {
  opacity: 1;
  color: #fff;
}

.footer-links-list {
  margin-bottom: 1rem;
}

.footer-link-item {
  display: inline-block;
  padding: 0.2rem 0;
  color: #fff;
  font-weight: 700;
}

.pf-col address {
  font-style: normal;
  opacity: 0.78;
  font-size: 0.9rem;
  line-height: 2;
}

.pf-bottom {
  border-top: 1px solid rgba(255,255,255,0.1);
  padding-top: 1.3rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
  font-size: 0.82rem;
  opacity: 0.7;
}

.footer-langs {
  display: flex;
  gap: 1rem;
}

@media (max-width: 768px) {
  .pub-nav {
    padding: 0 1rem;
  }

  .pub-links {
    display: none;
  }

  .pub-btn {
    padding: 0.55rem 1rem;
    font-size: 0.92rem;
  }

  .footer-grid {
    grid-template-columns: 1fr;
  }
}
</style>
