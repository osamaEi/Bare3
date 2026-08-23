<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Link } from '@inertiajs/vue3'

defineProps({
  active: { type: String, default: 'home' }, // home | about | courses | subscribe | blog
})

const mobileOpen = ref(false)
const loginOpen = ref(false)
const loginWrap = ref(null)

const loginOptions = [
  { key: 'student', label: 'طالب',   icon: 'fa-user-graduate',    route: 'login.student' },
  { key: 'parent',  label: 'ولي أمر', icon: 'fa-user-shield',      route: 'login.parent' },
  { key: 'admin',   label: 'مشرف',   icon: 'fa-user-gear',        route: 'login.admin' },
]

const closeLogin = (e) => {
  if (loginWrap.value && !loginWrap.value.contains(e.target)) loginOpen.value = false
}

onMounted(() => {
  if (window.AOS) window.AOS.init({ duration: 800, easing: 'ease-out-cubic', once: true, offset: 80 })
  document.addEventListener('click', closeLogin)
})

onBeforeUnmount(() => document.removeEventListener('click', closeLogin))
</script>

<template>
  <div class="bare3 bg-[#FAFAFB] text-[#2b2b2b] overflow-x-hidden">

    <!-- ============ HEADER ============ -->
    <header class="w-full border-b border-gray-100 sticky top-0 bg-white/80 backdrop-blur-xl z-50">
      <div class="max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6 py-2 sm:py-2.5">
        <Link href="/" class="flex items-center shrink-0 -my-2 sm:-my-4">
          <img src="/images/logo.png" alt="بارع" class="h-16 sm:h-28 w-auto">
        </Link>

        <nav class="hidden md:flex items-center gap-8 lg:gap-10 text-base lg:text-lg font-medium text-gray-800">
          <Link href="/" class="nav-link pb-2" :class="active === 'home' ? 'active' : 'hover:text-[var(--brand)]'">الرئيسية</Link>
          <Link :href="route('about')" class="nav-link pb-2" :class="active === 'about' ? 'active' : 'hover:text-[var(--brand)]'">عن بارع</Link>
          <Link :href="route('courses')" class="nav-link pb-2" :class="active === 'courses' ? 'active' : 'hover:text-[var(--brand)]'">المسارات</Link>
          <Link :href="route('subscribe')" class="nav-link pb-2" :class="active === 'subscribe' ? 'active' : 'hover:text-[var(--brand)]'">الباقات</Link>
          <Link :href="route('blog')" class="nav-link pb-2" :class="active === 'blog' ? 'active' : 'hover:text-[var(--brand)]'">المدونة</Link>
          <Link :href="route('trainers')" class="nav-link pb-2" :class="active === 'trainers' ? 'active' : 'hover:text-[var(--brand)]'">انضم كمدرب</Link>
        </nav>

        <div class="flex items-center gap-4">
          <div ref="loginWrap" class="relative hidden sm:block">
            <button type="button"
                    class="inline-flex items-center gap-2 bg-[var(--brand)] hover:bg-[var(--brand-dark)] text-white font-bold text-sm px-6 py-2.5 rounded-full transition-colors"
                    :aria-expanded="loginOpen ? 'true' : 'false'" aria-haspopup="true"
                    @click.stop="loginOpen = !loginOpen">
              تسجيل الدخول
              <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': loginOpen }"></i>
            </button>

            <transition name="drop">
              <div v-if="loginOpen" class="login-menu">
                <Link v-for="o in loginOptions" :key="o.key" :href="route(o.route)"
                      class="login-item" @click="loginOpen = false">
                  <i class="fa-solid" :class="o.icon"></i>
                  <span>{{ o.label }}</span>
                </Link>
              </div>
            </transition>
          </div>
          <button class="hamburger md:hidden" :class="{ open: mobileOpen }" aria-label="فتح القائمة"
                  :aria-expanded="mobileOpen ? 'true' : 'false'" @click="mobileOpen = !mobileOpen">
            <span></span><span></span><span></span>
          </button>
        </div>
      </div>

      <!-- Mobile nav panel -->
      <nav class="mobile-nav md:hidden border-t border-gray-100 bg-white" :class="{ open: mobileOpen }">
        <div class="flex flex-col items-end gap-1 px-6 py-4 text-base font-medium text-gray-800">
          <Link href="/" class="w-full text-right py-2 hover:text-[var(--brand)]" @click="mobileOpen = false">الرئيسية</Link>
          <Link :href="route('about')" class="w-full text-right py-2 hover:text-[var(--brand)]" @click="mobileOpen = false">عن بارع</Link>
          <Link :href="route('courses')" class="w-full text-right py-2 hover:text-[var(--brand)]" @click="mobileOpen = false">المسارات</Link>
          <Link :href="route('subscribe')" class="w-full text-right py-2 hover:text-[var(--brand)]" @click="mobileOpen = false">الباقات</Link>
          <Link :href="route('blog')" class="w-full text-right py-2 hover:text-[var(--brand)]" @click="mobileOpen = false">المدونة</Link>
          <Link :href="route('trainers')" class="w-full text-right py-2 hover:text-[var(--brand)]" @click="mobileOpen = false">انضم كمدرب</Link>
          <div class="w-full mt-2 pt-3 border-t border-gray-100">
            <span class="block text-right text-xs text-gray-400 mb-1">تسجيل الدخول</span>
            <Link v-for="o in loginOptions" :key="o.key" :href="route(o.route)"
                  class="w-full flex items-center justify-end gap-2 py-2 text-[var(--brand)] font-bold"
                  @click="mobileOpen = false">
              {{ o.label }}
              <i class="fa-solid" :class="o.icon"></i>
            </Link>
          </div>
        </div>
      </nav>
    </header>

    <!-- PAGE CONTENT -->
    <main>
      <slot />
    </main>

    <!-- ============ FOOTER ============ -->
    <footer class="footer-glass text-white relative">
      <div class="max-w-7xl mx-auto px-6 py-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 text-right">

        <!-- العمود 1: التعريف والهوية -->
        <div>
          <img src="/images/logo.png" alt="بارع" class="h-12 w-auto ml-auto mb-5 brightness-0 invert">
          <p class="text-sm leading-loose text-white/70">
            منصة تعليمية رقمية لتمكين الأجيال والشباب من مهارات القرن الـ21 عبر رحلات تفاعلية ممتعة.
          </p>
          <p class="text-xs text-white/50 mt-6 leading-relaxed">
            جميع الحقوق محفوظة لـ مؤسسة رؤية بارع © 2026
          </p>
        </div>

        <!-- العمود 2: رواد المنصة -->
        <div>
          <h4 class="font-bold text-lg mb-5">روابط المنصة</h4>
          <ul class="space-y-3 text-sm text-white/70">
            <li><Link href="/" class="footer-link">الرئيسية</Link></li>
            <li><Link :href="route('about')" class="footer-link">من نحن</Link></li>
            <li><Link :href="route('courses')" class="footer-link">المسارات التعليمية</Link></li>
            <li><a href="/#faq" class="footer-link">الأسئلة الشائعة</a></li>
            <li><Link :href="route('blog')" class="footer-link">المدونة</Link></li>
          </ul>
        </div>

        <!-- العمود 3: المسارات -->
        <div>
          <h4 class="font-bold text-lg mb-5">المسارات</h4>
          <ul class="space-y-3 text-sm text-white/70">
            <li><Link :href="route('courses')" class="footer-link">مسار التفكير الناقد والابتكار</Link></li>
            <li><Link :href="route('courses')" class="footer-link">مسار الذكاء العاطفي والاجتماعي</Link></li>
            <li><Link :href="route('courses')" class="footer-link">مسار الوعي المالي الذكي</Link></li>
            <li><Link :href="route('courses')" class="footer-link">مسار التمكين التقني</Link></li>
            <li><Link :href="route('courses')" class="footer-link">مسار القيادة واتخاذ القرار</Link></li>
          </ul>
        </div>

        <!-- العمود 4: الدعم والسياسات -->
        <div>
          <h4 class="font-bold text-lg mb-5">الدعم والتواصل</h4>
          <ul class="space-y-3 text-sm text-white/70">
            <li><Link :href="route('privacy.policy')" class="footer-link">سياسة الخصوصية</Link></li>
            <li><Link :href="route('terms')" class="footer-link">شروط وأحكام الاستخدام</Link></li>
            <li dir="ltr" class="text-right"><a href="mailto:info@bareaedu.sa" class="footer-link">info@bareaedu.sa</a></li>
            <li dir="ltr" class="text-right"><a href="https://wa.me/966500000000" class="footer-link"><i class="fa-brands fa-whatsapp ml-1"></i> واتساب الدعم</a></li>
          </ul>
          <div class="flex justify-end gap-3 mt-6">
            <a href="https://instagram.com/bareaedu.sa" class="social-ic"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://twitter.com/Bareaedusa" class="social-ic"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#" class="social-ic"><i class="fa-brands fa-linkedin-in"></i></a>
            <a href="#" class="social-ic"><i class="fa-brands fa-youtube"></i></a>
          </div>
        </div>
      </div>

      <!-- الشريط السفلي: وسائل الدفع + الترخيص -->
      <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col md:flex-row-reverse items-center justify-between gap-4 text-xs text-white/50">
          <!-- وسائل الدفع -->
          <div class="flex items-center gap-3">
            <span class="text-white/40">وسائل الدفع:</span>
            <span class="pay-chip">mada</span>
            <span class="pay-chip"><i class="fa-brands fa-cc-visa"></i></span>
            <span class="pay-chip"><i class="fa-brands fa-cc-mastercard"></i></span>
            <span class="pay-chip"><i class="fa-brands fa-apple-pay"></i></span>
          </div>
          <p>السجل التجاري / ترخيص المنصات التعليمية بالمملكة</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<style>
/* ── هوية بارع البصرية — ألوان ناعمة، بنفسجي مخفّف ── */
.bare3 {
  --brand:#7c5cbf;        /* بنفسجي هادئ */
  --brand-dark:#5f4398;
  --brand-soft:#f3effb;
  --ink:#1e1b2e;
  --coral:#f0806a; --teal:#4bb5a8; --amber:#f2b866; --navy:#3d6ea5;
}
/* ── قائمة تسجيل الدخول المنسدلة ── */
.bare3 .login-menu {
  position:absolute; top:calc(100% + .6rem); inset-inline-end:0;
  min-width:12rem; background:#fff; border:1px solid #EEF0F4;
  border-radius:1rem; box-shadow:0 18px 40px -12px rgb(30 27 46 / .22);
  padding:.4rem; z-index:60; overflow:hidden;
}
.bare3 .login-item {
  display:flex; align-items:center; gap:.7rem;
  padding:.65rem .85rem; border-radius:.7rem;
  font-size:.9rem; font-weight:700; color:#2b2b2b;
  transition:background .18s ease, color .18s ease;
}
.bare3 .login-item:hover { background:var(--brand-soft); color:var(--brand-dark); }
.bare3 .login-item i { width:1.1rem; text-align:center; color:var(--brand); }
.drop-enter-active, .drop-leave-active { transition:opacity .16s ease, transform .16s ease; }
.drop-enter-from, .drop-leave-to { opacity:0; transform:translateY(-.4rem); }

.bare3, .bare3 *:not([class*="fa-"]) { font-family:'Tajawal','Poppins',sans-serif; }
.bare3 .en { font-family:'Poppins',sans-serif; }

/* لا تُبدّل خط أيقونات Font Awesome */
.bare3 i[class*="fa-"],
.bare3 .fa, .bare3 .fas, .bare3 .far, .bare3 .fab,
.bare3 [class*="fa-"]::before {
  font-family:"Font Awesome 6 Free","Font Awesome 6 Brands" !important;
}
.bare3 i[class*="fa-solid"], .bare3 .fas { font-weight:900 !important; }
.bare3 i[class*="fa-regular"], .bare3 .far { font-weight:400 !important; }
.bare3 i[class*="fa-brands"], .bare3 .fab { font-family:"Font Awesome 6 Brands" !important; font-weight:400 !important; }

.bare3 .nav-link { position:relative; }
.bare3 .nav-link.active { color:var(--brand); font-weight:700; }
.bare3 .nav-link.active::after {
  content:''; position:absolute; bottom:-8px; right:0; left:0; height:2px; background:var(--brand); border-radius:9999px;
}
.bare3 .footer-link { transition:color .2s ease, padding .2s ease; }
.bare3 .footer-link:hover { color:#fff; padding-inline-end:4px; }

/* footer زجاجي داكن */
.footer-glass {
  background:linear-gradient(180deg, rgba(30,27,46,.96), rgba(45,34,68,.98));
  backdrop-filter: blur(18px);
}
.social-ic {
  width:38px; height:38px; border-radius:12px; display:flex; align-items:center; justify-content:center;
  background:rgba(255,255,255,.08); transition:all .2s ease;
}
.social-ic:hover { background:var(--brand); transform:translateY(-2px); }
.pay-chip {
  display:inline-flex; align-items:center; justify-content:center; min-width:42px; height:26px; padding:0 8px;
  border:1px solid rgba(255,255,255,.2); border-radius:6px; font-weight:700; font-size:.7rem;
  color:rgba(255,255,255,.6); background:rgba(255,255,255,.04);
}
.pay-chip i { font-size:1.1rem; }

/* Mobile nav */
.bare3 .mobile-nav { max-height:0; overflow:hidden; transition:max-height .35s ease; }
.bare3 .mobile-nav.open { max-height:460px; }
.bare3 .hamburger span {
  display:block; width:24px; height:2px; background:#2b2b2b; margin:5px 0;
  transition:transform .3s ease, opacity .3s ease;
}
.bare3 .hamburger.open span:nth-child(1){ transform:translateY(7px) rotate(45deg); }
.bare3 .hamburger.open span:nth-child(2){ opacity:0; }
.bare3 .hamburger.open span:nth-child(3){ transform:translateY(-7px) rotate(-45deg); }

@media (prefers-reduced-motion: reduce){
  .bare3 [data-aos]{ transition:none !important; transform:none !important; opacity:1 !important; }
}
</style>
