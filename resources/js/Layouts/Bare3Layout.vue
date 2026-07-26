<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'

defineProps({
  active: { type: String, default: 'home' }, // home | about | blog | courses
})

const IMG = '/images/new-bare3'
const mobileOpen = ref(false)

onMounted(() => {
  if (window.AOS) window.AOS.init({ duration: 800, easing: 'ease-out-cubic', once: true, offset: 80 })
})
</script>

<template>
  <div class="bare3 bg-white text-[#2b2b2b] overflow-x-hidden">

    <!-- ============ HEADER ============ -->
    <header class="w-full border-b border-gray-100 sticky top-0 bg-white/95 backdrop-blur z-50">
      <div class="max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6 py-4">
        <Link href="/" class="flex flex-col items-end leading-none">
          <img :src="`${IMG}/logo-horizontal.png`" alt="Bare3" class="w-24 sm:w-32">
        </Link>

        <nav class="hidden md:flex items-center gap-10 text-lg font-medium text-gray-800">
          <Link href="/" class="nav-link pb-2" :class="active === 'home' ? 'active' : 'hover:text-purple-700'">الرئيسية</Link>
          <Link :href="route('about')" class="nav-link pb-2" :class="active === 'about' ? 'active' : 'hover:text-purple-700'">عن بارع</Link>
          <Link :href="route('blog')" class="nav-link pb-2" :class="active === 'blog' ? 'active' : 'hover:text-purple-700'">المدونة</Link>
          <Link :href="route('courses')" class="nav-link pb-2" :class="active === 'courses' ? 'active' : 'hover:text-purple-700'">الكورسات</Link>
        </nav>

        <div class="flex items-center gap-4">
          <img :src="`${IMG}/profile.png`" alt="Profile" class="w-8 h-8 rounded-full border border-gray-300">
          <button class="hamburger md:hidden" :class="{ open: mobileOpen }" aria-label="فتح القائمة"
                  :aria-expanded="mobileOpen ? 'true' : 'false'" @click="mobileOpen = !mobileOpen">
            <span></span><span></span><span></span>
          </button>
        </div>
      </div>

      <!-- Mobile nav panel -->
      <nav class="mobile-nav md:hidden border-t border-gray-100 bg-white" :class="{ open: mobileOpen }">
        <div class="flex flex-col items-end gap-1 px-6 py-4 text-base font-medium text-gray-800">
          <Link href="/" class="w-full text-right py-2 hover:text-purple-700" @click="mobileOpen = false">الرئيسية</Link>
          <Link :href="route('about')" class="w-full text-right py-2 hover:text-purple-700" @click="mobileOpen = false">عن بارع</Link>
          <Link :href="route('blog')" class="w-full text-right py-2 hover:text-purple-700" @click="mobileOpen = false">المدونة</Link>
          <Link :href="route('courses')" class="w-full text-right py-2 hover:text-purple-700" @click="mobileOpen = false">الكورسات</Link>
        </div>
      </nav>
    </header>

    <!-- PAGE CONTENT -->
    <main>
      <slot />
    </main>

    <!-- ============ FOOTER ============ -->
    <footer class="text-purple-900 bg-purple-50">
      <div class="max-w-7xl mx-auto px-6 py-16 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-10 text-right">
        <div>
          <img :src="`${IMG}/logo-horizontal.png`" alt="Bare3" class="w-28 sm:w-32 ml-auto mb-5">
          <p class="text-sm leading-loose text-purple-900/90">
            منصة متخصصة في الصحة النفسية وجودة الحياة، تصمم حلولاً تخصصية ترافق الإنسان وتمكّن الكيان.
          </p>
          <div class="flex justify-center gap-3 mt-6">
            <a href="#" class="w-9 h-9 bg-purple-100 hover:bg-purple-200 transition-colors rounded-2xl flex items-center justify-center text-lg"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" class="w-9 h-9 bg-purple-100 hover:bg-purple-200 transition-colors rounded-2xl flex items-center justify-center text-lg"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#" class="w-9 h-9 bg-purple-100 hover:bg-purple-200 transition-colors rounded-2xl flex items-center justify-center text-lg"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" class="w-9 h-9 bg-purple-100 hover:bg-purple-200 transition-colors rounded-2xl flex items-center justify-center text-lg"><i class="fa-brands fa-linkedin-in"></i></a>
          </div>
        </div>

        <div>
          <h4 class="font-bold text-lg mb-5">روابط سريعة</h4>
          <ul class="space-y-3 text-sm">
            <li><Link href="/" class="footer-link text-purple-900/90">الرئيسية</Link></li>
            <li><Link :href="route('about')" class="footer-link text-purple-900/90">عن بارع</Link></li>
            <li><Link :href="route('blog')" class="footer-link text-purple-900/90">المدونة</Link></li>
            <li><Link :href="route('courses')" class="footer-link text-purple-900/90">الكورسات</Link></li>
          </ul>
        </div>

        <div>
          <h4 class="font-bold text-lg mb-5">تواصل معنا</h4>
          <ul class="space-y-3 text-sm text-purple-900/90">
            <li>الرياض، المملكة العربية السعودية</li>
            <li dir="ltr" class="text-right">info@Bare3.Net</li>
            <li dir="ltr" class="text-right">Hr@Bare3.Net</li>
          </ul>
        </div>
      </div>

      <div class="border-t border-purple-100">
        <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col md:flex-row items-center justify-between gap-3 text-xs text-purple-900/80">
          <p>© 2026 بارع. جميع الحقوق محفوظة.</p>
          <div class="flex gap-6">
            <Link :href="route('about')" class="footer-link">سياسة الخصوصية</Link>
            <Link :href="route('about')" class="footer-link">الشروط والأحكام</Link>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<style>
/* ألوان وخطوط التصميم الجديد — عامة عشان الصفحات كلها تستخدمها */
.bare3 {
  --purple-900:#4b3568; --purple-800:#5f4380; --purple-700:#7a5ea0; --purple-600:#8f77ae;
  --purple-500:#a996c2; --purple-300:#c9b9dc; --purple-100:#efe9f5;
  --blue-btn:#2f5c8a; --orange:#f0a15c; --coral:#e8636e; --teal:#3fa79e; --navy:#2e5c87;
}
.bare3, .bare3 * { font-family:'Tajawal','Poppins',sans-serif; }
.bare3 .en { font-family:'Poppins',sans-serif; }

.bare3 .nav-link { position:relative; }
.bare3 .nav-link.active { color:var(--purple-700); font-weight:700; }
.bare3 .nav-link.active::after {
  content:''; position:absolute; bottom:-8px; right:0; left:0; height:2px; background:var(--purple-700);
}
.bare3 .footer-link { transition:color .2s ease; }
.bare3 .footer-link:hover { font-weight:bold; }

/* Mobile nav */
.bare3 .mobile-nav { max-height:0; overflow:hidden; transition:max-height .35s ease; }
.bare3 .mobile-nav.open { max-height:400px; }
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
