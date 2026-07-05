<template>
<div>

<!-- Support Chat Widget -->
<SupportChat />


<!-- NAVBAR -->

<nav id="top">
  <div class="nav-inner">
    <div class="nav-logo" style="display:flex; align-items:center; gap:.4rem;">
      <img :src="c.brand.logo" style="width: 200px;" alt="">
    </div>
    <ul>
      <li><a href="#top">الرئيسية</a></li>
      <li><a href="#pricing">الاشتراك</a></li>
      <li><a href="#about">عن المعهد</a></li>
      <li><a href="#blog">المدونة</a></li>
      <li><a href="#contact">تواصل معنا</a></li>
    </ul>
    <div class="login-dd" ref="loginDd">
      <button class="btn-nav" @click="loginOpen = !loginOpen">
         تسجيل الدخول <i class="fa-solid fa-chevron-down" :class="{ open: loginOpen }"></i>
      </button>
      <div class="login-menu" v-if="loginOpen">
        <a :href="route('login.student')" class="login-item student">
          <span class="li-ic"><i class="fa-solid fa-child-reaching"></i></span>
          <span><span class="li-title">طالب</span><span class="li-sub">ابدأ مغامرة التعلّم</span></span>
        </a>
        <a :href="route('login.parent')" class="login-item parent">
          <span class="li-ic"><i class="fa-solid fa-user-group"></i></span>
          <span><span class="li-title">ولي أمر</span><span class="li-sub">تابع تقدّم أبنائك</span></span>
        </a>
        <a :href="route('login.admin')" class="login-item admin">
          <span class="li-ic"><i class="fa-solid fa-shield-halved"></i></span>
          <span><span class="li-title">الإدارة</span><span class="li-sub">لوحة التحكم</span></span>
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero pb-8">
  <div class="hero-blob1"></div>
  <div class="hero-blob2"></div>
  <div class="hero-blob3"></div>
  <div class="hero-inner">
    <div class="hero-text">
      <div class="badge-top">
        <span class="badge-dot"></span>
        {{ c.hero.badge }}
      </div>
      <h1>
        <span class="text-5xl">
            {{ c.hero.title_line1 }}<br>
        </span>
        <span class="hl-sky text-5xl">{{ c.hero.title_hl1 }}</span>
        <span class="hl-pink text-5xl">{{ c.hero.title_hl2 }}</span><br>
        <span style="margin-top:.6rem; display:block;" class="text-5xl">{{ c.hero.title_line2 }} <i class="fa-solid fa-crown crown"></i></span>
      </h1>
      <p class="hero-desc">{{ c.hero.desc }}</p>
      <div class="hero-btns">
        <button class="btn-primary">{{ c.hero.btn_primary }}</button>
        <button class="btn-secondary">{{ c.hero.btn_secondary }}</button>
      </div>
      <div class="hero-stats">
        <span v-for="(s, i) in c.stats" :key="i" class="stat-pill" :class="s.color"><i :class="s.icon"></i> {{ s.text }}</span>
      </div>
    </div>
    <div class="hero-img">
      <img :src="c.hero.image" alt="شخصيات كرتونية" />
    </div>
  </div>
 
</section>


<!-- FEATURES -->
<section id="features" class="relative py-12 overflow-hidden bg-gradient-to-b from-violet-50 via-white to-sky-50">
  <div class="section-center">
    <div class="section-tag"><i class="fa-solid fa-stars" style="color:var(--sky)"></i> {{ c.features_header.tag }}</div>
    <h2 class="text-4xl font-bold text-center mb-4">{{ c.features_header.title }}</h2>
    <p class="section-sub text-center max-w-2xl mx-auto">{{ c.features_header.sub }}</p>
  </div>

  <div class="max-w-6xl mx-auto px-4 relative" id="features-wrapper">

    <!-- خلفية سحرية -->
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(167,139,250,0.08)_0%,transparent_50%)]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_60%,rgba(103,232,249,0.08)_0%,transparent_50%)]"></div>

    <!-- SVG الخيط السحري -->
    <svg id="magic-thread" class="absolute inset-0 w-full h-full pointer-events-none" style="z-index:0;overflow:visible;" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="threadGrad" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%"   stop-color="#8b5cf6"/>
          <stop offset="33%"  stop-color="#38bdf8"/>
          <stop offset="66%"  stop-color="#ec4899"/>
          <stop offset="100%" stop-color="#a855f7"/>
        </linearGradient>
        <filter id="threadGlow">
          <feGaussianBlur stdDeviation="3" result="blur"/>
          <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
      </defs>
      <path id="thread-glow-path" fill="none" stroke="url(#threadGrad)" stroke-width="6" stroke-linecap="round" stroke-dasharray="10 8" filter="url(#threadGlow)" opacity="0.35"/>
      <path id="thread-main-path" fill="none" stroke="url(#threadGrad)" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="10 7"/>
      <path id="thread-run-path" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" opacity="0.8"/>
    </svg>

    <div class="space-y-10 relative" style="z-index:1">

      <div v-for="(f, i) in c.features" :key="i"
           class="flex flex-col items-center gap-8 md:gap-16"
           :class="i % 2 === 0 ? 'md:flex-row' : 'md:flex-row-reverse'" data-aos="fade-up">
        <div class="md:w-5/12" :class="i % 2 === 0 ? 'order-2 md:order-1' : ''">
          <div class="feat-card bg-white/80 backdrop-blur-xl p-6 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300"
               :class="featBorder[i % 4]">
            <div class="feat-icon text-5xl mb-4" :class="featIconColor[i % 4]">
              <i :class="f.icon"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ f.title }}</h3>
            <p class="text-gray-600 leading-relaxed">{{ f.desc }}</p>
          </div>
        </div>
        <div class="md:w-5/12 flex justify-center" :class="i % 2 === 0 ? 'order-1 md:order-2' : ''">
          <div class="relative group" :id="'circle-' + (i + 1)">
            <div class="absolute -inset-6 rounded-full blur-3xl opacity-30 group-hover:opacity-50 transition" :class="featBlob[i % 4]"></div>
            <div class="w-64 h-64 rounded-full bg-white shadow-2xl flex items-center justify-center p-5 relative">
              <img :src="f.image" :alt="f.title" class="w-full h-full object-contain drop-shadow-2xl transition-transform duration-700 group-hover:scale-110">
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>



<!-- PRICING -->
<section id="pricing">
  <div class="section-center">
    <div class="section-tag"><i class="fa-solid fa-rocket" style="color:var(--pink)"></i> {{ c.pricing_header.tag }}</div>
    <h2>{{ c.pricing_header.title }}</h2>
    <p class="section-sub">{{ c.pricing_header.sub }}</p>
  </div>
  <div class="pricing-grid">
    <div v-for="(p, i) in c.pricing" :key="i" class="price-card" :class="{ featured: p.featured }">
      <div v-if="p.featured" style="position:absolute;top:-14px;left:50%;transform:translateX(-50%);background:var(--lime-mid);color:var(--lime-dark);font-size:.78rem;font-weight:800;padding:.3rem 1.2rem;border-radius:50px;white-space:nowrap;box-shadow:0 2px 8px rgba(0,0,0,.15)"><i class="fa-solid fa-fire"></i> الأكثر شعبية</div>
      <div class="price-badge">{{ p.badge }}</div>
      <div class="price-amount">{{ p.amount }} <i class="fa-solid fa-saudi-riyal-symbol riyal-ic"></i> <span class="price-unit">{{ p.unit }}</span></div>
      <div class="price-name">{{ p.name }}</div>
      <ul class="price-features">
        <li v-for="(feat, fi) in p.features" :key="fi"><i class="fa-solid fa-circle-check"></i> {{ feat }}</li>
      </ul>
      <button class="btn-price">{{ p.btn }}</button>
    </div>
  </div>
  <div class="price-note">
    <p>{{ c.pricing_note.line1 }}</p>
    <p style="font-size:.8rem;margin-top:.3rem">{{ c.pricing_note.line2 }}</p>
  </div>
</section>



<!-- PARENTS -->
<section id="parents">
  <div class="section-center">
    <div class="section-tag"><i class="fa-solid fa-people-group" style="color:var(--sky-dark)"></i> {{ c.parents_header.tag }}</div>
    <h2>{{ c.parents_header.title }}</h2>
    <p class="section-sub">{{ c.parents_header.sub }}</p>
  </div>
  <div class="parents-grid">
    <div v-for="(p, i) in c.parents" :key="i" class="parent-card" :class="p.color">
      <div class="parent-icon"><i :class="p.icon"></i></div>
      <h4>{{ p.title }}</h4>
      <p >{{ p.desc }}</p>
    </div>
  </div>
</section>



<!-- REVIEWS -->
<section id="reviews">
  <div class="section-center">
    <div class="section-tag"><i class="fa-solid fa-comment-dots" style="color:var(--pink)"></i> {{ c.reviews_header.tag }}</div>
    <h2>{{ c.reviews_header.title }}</h2>
    <p class="section-sub">{{ c.reviews_header.sub }}</p>
  </div>
  <div class="reviews-grid">
    <div v-for="(t, i) in c.testimonials" :key="i" class="review-card" :class="{ featured: t.featured }">
      <div class="review-chip">{{ t.chip }}</div>
      <div class="review-stars">★★★★★</div>
      <h4>{{ t.title }}</h4>
      <p>{{ t.comment }}</p>
      <div class="review-author">
        <img :src="t.avatar" class="review-avatar" alt="">
        <div><div class="review-name" >{{ t.name }}</div><div class="review-loc">{{ t.loc }}</div></div>
      </div>
    </div>
  </div>
</section>



<!-- FAQ -->
<section id="faq">
  <div class="section-center">
    <div class="section-tag">{{ c.faq_header.tag }}</div>
    <h2>{{ c.faq_header.title }}</h2>
  </div>
  <div class="faq-list">
    <div v-for="(item, i) in c.faq" :key="i" class="faq-item" :class="'s' + ((i % 5) + 1)">
      <button class="faq-btn" @click="toggleFaq($event.currentTarget)">
        <span>{{ item.q }}</span>
        <span class="faq-icon">+</span>
      </button>
      <div class="faq-answer">{{ item.a }}</div>
    </div>
  </div>
</section>


<!-- CTA -->
<section id="cta" class="py-2">
  <div class="relative z-[1] max-w-6xl mx-auto px-6">
    <div class="grid grid-cols-1 md:grid-cols-5 items-center gap-6">

      <!-- الصورة اليسار - مخفية على الموبايل -->
      <div class="justify-center hidden md:flex" data-aos="fade-right">
        <img :src="c.cta.image_left" alt="طفل يتعلم" class="w-36 lg:w-52">
      </div>

      <!-- النص -->
      <div class="text-center px-2 sm:px-6 md:col-span-3" data-aos="fade-up">
        <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
          {{ c.cta.title }}
        </h2>

        <!-- الصورتين جنب بعض على الموبايل فقط -->
        <div class="flex justify-center gap-4 sm:gap-6 mb-6 md:hidden">
          <img :src="c.cta.image_left" alt="طفل يتعلم" class="w-24 sm:w-28">
          <img :src="c.cta.image_right" alt="طفلة تتعلم" class="w-24 sm:w-28">
        </div>

        <p class="text-base sm:text-lg md:text-xl lg:text-2xl mb-8 leading-relaxed">
          {{ c.cta.desc }}
        </p>
        <button class="btn-cta">
          {{ c.cta.btn }}
        </button>
      </div>

      <!-- الصورة اليمين - مخفية على الموبايل -->
      <div class="justify-center hidden md:flex" data-aos="fade-left">
        <img :src="c.cta.image_right" alt="طفلة تتعلم" class="w-36 lg:w-52">
      </div>

    </div>
  </div>
</section>



<!-- ABOUT -->
<section id="about" class="home-about">
  <div class="section-center">
    <div class="section-tag"><i class="fa-solid fa-graduation-cap" style="color:var(--sky-dark)"></i> عن المعهد</div>
    <h2>منصة بارع التعليمية</h2>
    <p class="section-sub">وجهتك الأولى لتمكين الأجيال من مهارات القرن الحادي والعشرين</p>
  </div>
  <div class="about-cards">
    <div class="about-card">
      <div class="about-ic sky"><i class="fa-solid fa-bullseye"></i></div>
      <h3>رسالتنا</h3>
      <p>تمكين الأجيال من مهارات القرن الـ 21 بأسلوب تفاعلي وقصصي ممتع ومبتكر، يدمج بين المعرفة العلمية والأمان الرقمي، ويسدّ الفجوة بين التعليم الأكاديمي ومتطلبات المستقبل.</p>
    </div>
    <div class="about-card">
      <div class="about-ic pink"><i class="fa-solid fa-eye"></i></div>
      <h3>رؤيتنا</h3>
      <p>أن نكون المنصة التعليمية الرقمية الأولى والأكثر موثوقية في المملكة والعالم العربي في بناء وتطوير مهارات الحياة، لإنشاء جيل مبتكر واعٍ تقنياً ومتزن عاطفياً ومالياً.</p>
    </div>
  </div>
  <div class="about-more">
    <a :href="route('about')" class="about-link">تعرّف علينا أكثر <i class="fa-solid fa-arrow-left"></i></a>
  </div>
</section>

<!-- BLOG -->
<section id="blog" class="home-blog" v-if="latestPosts.length">
  <div class="section-center">
    <div class="section-tag"><i class="fa-solid fa-newspaper" style="color:var(--pink)"></i> المدونة</div>
    <h2>أحدث المقالات</h2>
    <p class="section-sub">رؤى تربوية تساعدك على إعداد جيل واعٍ ومبتكر</p>
  </div>
  <div class="home-blog-grid">
    <Link v-for="post in latestPosts" :key="post.slug" :href="route('blog.show', post.slug)" class="home-blog-card">
      <span v-if="post.category" class="hb-cat">{{ post.category }}</span>
      <h3>{{ post.title }}</h3>
      <p>{{ post.excerpt }}</p>
      <span class="hb-more">اقرأ المقال <i class="fa-solid fa-arrow-left"></i></span>
    </Link>
  </div>
  <div class="about-more">
    <a :href="route('blog')" class="about-link">كل المقالات <i class="fa-solid fa-arrow-left"></i></a>
  </div>
</section>

<!-- CONTACT -->
<section id="contact" class="home-contact">
  <div class="section-center">
    <div class="section-tag"><i class="fa-solid fa-envelope" style="color:var(--sky-dark)"></i> تواصل معنا</div>
    <h2>عندك سؤال؟ راسلنا</h2>
    <p class="section-sub">فريقنا جاهز للرد على استفساراتك</p>
  </div>
  <form class="contact-form" @submit.prevent="submitContact">
    <div v-if="$page.props.flash?.success" class="contact-ok">{{ $page.props.flash.success }}</div>
    <div class="cf-row">
      <div class="cf-field">
        <input v-model="contactForm.name" type="text" placeholder="الاسم" />
        <span v-if="contactForm.errors.name" class="cf-err">{{ contactForm.errors.name }}</span>
      </div>
      <div class="cf-field">
        <input v-model="contactForm.email" type="email" placeholder="البريد الإلكتروني" />
        <span v-if="contactForm.errors.email" class="cf-err">{{ contactForm.errors.email }}</span>
      </div>
    </div>
    <div class="cf-row">
      <div class="cf-field">
        <input v-model="contactForm.phone" type="text" placeholder="رقم الجوال (اختياري)" />
      </div>
      <div class="cf-field">
        <input v-model="contactForm.subject" type="text" placeholder="الموضوع (اختياري)" />
      </div>
    </div>
    <div class="cf-field">
      <textarea v-model="contactForm.message" rows="4" placeholder="رسالتك"></textarea>
      <span v-if="contactForm.errors.message" class="cf-err">{{ contactForm.errors.message }}</span>
    </div>
    <button type="submit" class="btn-primary" :disabled="contactForm.processing">
      {{ contactForm.processing ? 'جاري الإرسال...' : 'إرسال الرسالة' }}
    </button>
  </form>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-inner">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="logo-text">
          <div class="nav-logo" style="display:flex; align-items:center; gap:.4rem;">
            <img :src="c.footer.logo" style="width: 200px;" alt="">
          </div>
        </div>
        <p v-html="c.footer.desc"></p>
        <div class="footer-socials">
          <a v-for="(s, i) in c.footer.socials" :key="i" :href="s.href"><i :class="s.icon"></i></a>
        </div>
      </div>
      <div>
        <h4>{{ c.footer.col_pages_title }}</h4>
        <ul>
          <li v-for="(l, i) in c.footer.col_pages" :key="i"><a :href="l.href">{{ l.label }}</a></li>
        </ul>
      </div>
      <div>
        <h4>{{ c.footer.col_help_title }}</h4>
        <ul>
          <li v-for="(l, i) in c.footer.col_help" :key="i"><a :href="l.href">{{ l.label }}</a></li>
        </ul>
      </div>
      <div>
        <h4>{{ c.footer.col_contact_title }}</h4>
        <address>
          {{ c.footer.contact_location }}<br>
          {{ c.footer.contact_phone }}<br>
          {{ c.footer.contact_email }}
        </address>
      </div>
    </div>
    <div class="footer-bottom">
      <span>{{ c.footer.copyright }}</span>
      <div class="footer-langs">
        <span>🇪🇬 العربية</span>
        <span>🇬🇧 English</span>
      </div>
    </div>
  </div>
</footer>

</div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import SupportChat from '../Components/SupportChat.vue'

const props = defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  content: { type: Object, default: () => ({}) },
  latestPosts: { type: Array, default: () => [] },
})

// محتوى الصفحة (يصل من الباك-إند مدموجًا مع الافتراضيات)
const c = computed(() => props.content)

// ألوان بطاقات المميزات حسب الترتيب (محافظة على التصميم الأصلي)
// ألوان بطاقات المميزات — متناسقة مع باليتة الموقع (وردي/سماوي/بنفسجي) بدون أخضر
const featBorder = ['border border-violet-100 hover:border-violet-200', 'border border-sky-100 hover:border-sky-200', 'border border-pink-100 hover:border-pink-200', 'border border-fuchsia-100 hover:border-fuchsia-200']
const featIconColor = ['text-violet-600', 'text-sky-600', 'text-pink-600', 'text-fuchsia-600']
const featBlob = ['bg-gradient-to-br from-violet-200 to-purple-200', 'bg-gradient-to-br from-sky-200 to-cyan-200', 'bg-gradient-to-br from-pink-200 to-rose-200', 'bg-gradient-to-br from-fuchsia-200 to-purple-200']

// فورم التواصل (يرسل إلى نفس مسار صفحة "تواصل معنا")
const contactForm = useForm({ name: '', email: '', phone: '', subject: '', message: '' })
function submitContact() {
  contactForm.post(route('contact.store'), {
    preserveScroll: true,
    onSuccess: () => contactForm.reset(),
  })
}

const loginOpen = ref(false)
const loginDd = ref(null)
function closeLogin(e) {
  if (loginDd.value && !loginDd.value.contains(e.target)) loginOpen.value = false
}
onMounted(() => document.addEventListener('click', closeLogin))
onBeforeUnmount(() => document.removeEventListener('click', closeLogin))

function toggleFaq(btn) {
  const item = btn.closest('.faq-item')
  const isOpen = item.classList.contains('open')
  document.querySelectorAll('.faq-item.open').forEach(el => el.classList.remove('open'))
  if (!isOpen) item.classList.add('open')
}

onMounted(() => {
  // AOS
  if (window.AOS) window.AOS.init()

  // Magic Thread
  setTimeout(() => {
    function circleAnchor(circleEl, wrapperEl, side) {
      const wRect = wrapperEl.getBoundingClientRect()
      const inner = circleEl.querySelector('.rounded-full.bg-white')
      const r = inner ? inner.getBoundingClientRect() : circleEl.getBoundingClientRect()
      const cx = r.left - wRect.left + r.width / 2
      const cy = r.top  - wRect.top  + r.height / 2
      const radius = r.width / 2
      return { x: cx, y: side === 'bottom' ? cy + radius : cy - radius }
    }

    function buildThread() {
      const wrapper = document.getElementById('features-wrapper')
      if (!wrapper) return
      const c1 = document.getElementById('circle-1')
      const c2 = document.getElementById('circle-2')
      const c3 = document.getElementById('circle-3')
      const c4 = document.getElementById('circle-4')
      if (!c1 || !c2 || !c3 || !c4) return

      const p1 = circleAnchor(c1, wrapper, 'bottom')
      const p2 = circleAnchor(c2, wrapper, 'top')
      const p3 = circleAnchor(c2, wrapper, 'bottom')
      const p4 = circleAnchor(c3, wrapper, 'top')
      const p5 = circleAnchor(c3, wrapper, 'bottom')
      const p6 = circleAnchor(c4, wrapper, 'top')

      const dFull = [
        `M ${p1.x} ${p1.y}`,
        `C ${p1.x} ${p1.y + 55} ${p2.x} ${p2.y - 55} ${p2.x} ${p2.y}`,
        `C ${p2.x} ${p2.y + 35} ${p3.x} ${p3.y - 35} ${p3.x} ${p3.y}`,
        `C ${p3.x} ${p3.y + 55} ${p4.x} ${p4.y - 55} ${p4.x} ${p4.y}`,
        `C ${p4.x} ${p4.y + 35} ${p5.x} ${p5.y - 35} ${p5.x} ${p5.y}`,
        `C ${p5.x} ${p5.y + 55} ${p6.x} ${p6.y - 55} ${p6.x} ${p6.y}`
      ].join(' ')

      const svg      = document.getElementById('magic-thread')
      const mainPath = document.getElementById('thread-main-path')
      const glowPath = document.getElementById('thread-glow-path')
      const runPath  = document.getElementById('thread-run-path')
      if (!mainPath || !glowPath || !runPath) return

      svg.setAttribute('viewBox', `0 0 ${wrapper.offsetWidth} ${wrapper.offsetHeight}`)
      mainPath.setAttribute('d', dFull)
      glowPath.setAttribute('d', dFull)
      runPath.setAttribute('d', dFull)

      try {
        const len = mainPath.getTotalLength()
        runPath.style.strokeDasharray = `24 ${len}`
        let start = null
        function animate(ts) {
          if (!start) start = ts
          const progress = ((ts - start) / 3500) % 1
          runPath.style.strokeDashoffset = -(progress * (len + 24))
          requestAnimationFrame(animate)
        }
        requestAnimationFrame(animate)
      } catch(e) {}
    }

    buildThread()
    window.addEventListener('resize', () => setTimeout(buildThread, 150))
  }, 200)
})
</script>

<style>
/* ── CSS مأخوذ 1:1 من الملف الأصلي ── */
@import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@400;600;700;800&display=swap');

:root {
  --sky: #EC4899; --sky-light: #FCE7F3; --sky-mid: #F9A8D4; --sky-dark: #9D174D;
  --pink: #38BDF8; --pink-light: #E0F4FF; --pink-mid: #7DD3F8; --pink-dark: #0E7490;
  /* اللون الثالث: بنفسجي بدل الأخضر — يكمّل الوردي والأزرق */
  --lime: #A855F7; --lime-light: #F5F3FF; --lime-mid: #D8B4FE; --lime-dark: #7E22CE;
  --dark: #1C1C2E; --dark-mid: #2E2E42; --gray: #6B7280; --light: #F8FAFC; --white: #FFFFFF;
  --grad-hero: linear-gradient(135deg, #FCE7F3 0%, #E0F4FF 50%, #F5F3FF 100%);
  --grad-cta: linear-gradient(135deg, #EC4899 0%, #38BDF8 100%);
  --grad-pricing: linear-gradient(135deg, #FCE7F3 0%, #E0F4FF 100%);
  --grad-card-pro: linear-gradient(135deg,#E0F4FF 0%,#7DD3F8 35%,#38BDF8 70%,#0E7490 100%);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'ThmanyahSans', 'Segoe UI', Tahoma, sans-serif !important; color: var(--dark); overflow-x: hidden; background: var(--light); }
a { text-decoration: none; color: inherit; }
ul { list-style: none; }

nav { background: rgba(255,255,255,0.92); backdrop-filter: blur(12px); border-bottom: 3px solid var(--sky-light); position: sticky; top: 0; z-index: 50; padding: 0 2rem; }
.nav-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; height: 72px; gap: 1rem; }
.nav-logo { font-size: 1.8rem; font-weight: 800; }
.nav-logo span.s { color: var(--sky); }
.nav-logo span.p { color: var(--pink); }
.nav-logo span.l { color: var(--lime-dark); }
nav ul { display: flex; gap: 2rem; }
nav ul a { font-weight: 700; font-size: 1rem; color: var(--dark); transition: color .2s; }
nav ul a:hover { color: var(--sky-dark); }
.btn-nav { background: var(--pink); color: #fff; 
font-family: inherit; font-weight: 800; font-size: 1rem;
 padding: .6rem 1.6rem; border-radius: 50px;
  border: none; cursor: pointer; transition: transform .15s, box-shadow .15s; box-shadow: 0 4px 0 rgba(0,0,0,.3); }
.btn-nav:hover { transform: translateY(-2px); }
.btn-nav:active { transform: translateY(1px); box-shadow: 0 2px 0 rgba(0,0,0,.3); }
.btn-nav i { font-size: .8rem; transition: transform .2s; }
.btn-nav i.open { transform: rotate(180deg); }

/* Login dropdown */
.login-dd { position: relative; }
.login-menu { position: absolute; top: calc(100% + .6rem); left: 0; background: #fff; border-radius: 18px;
  box-shadow: 0 16px 48px rgba(15,23,42,.18); border: 1px solid #E2E8F0; padding: .5rem; min-width: 250px; z-index: 100;
  animation: ddpop .16s ease; }
@keyframes ddpop { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
.login-item { display: flex; align-items: center; gap: .8rem; padding: .7rem .8rem; border-radius: 12px; text-decoration: none;
  transition: background .18s; }
.login-item:hover { background: #F8FAFC; }
.li-ic { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
.login-item.student .li-ic { background: #E0F4FF; color: #0E7490; }
.login-item.parent .li-ic { background: #F5F3FF; color: #6D28D9; }
.login-item.admin .li-ic { background: #1E293B; color: #7DD3F8; }
.li-title { display: block; font-weight: 800; font-size: .95rem; color: #1C1C2E; }
.li-sub { display: block; font-size: .76rem; color: #94A3B8; font-weight: 600; }

.hero { background: var(--grad-hero); padding: 5rem 2rem 2rem; position: relative; overflow: hidden; }
.hero-blob1 { position: absolute; width: 380px; height: 380px; border-radius: 50%; background: var(--sky); opacity: .12; top: -80px; right: -80px; pointer-events: none; }
.hero-blob2 { position: absolute; width: 280px; height: 280px; border-radius: 50%; background: var(--pink); opacity: .12; bottom: -60px; left: -60px; pointer-events: none; }
.hero-blob3 { position: absolute; width: 200px; height: 200px; border-radius: 50%; background: var(--lime); opacity: .1; top: 40%; left: 40%; pointer-events: none; }
.hero-inner { max-width: 1100px; margin: 0 auto; display: flex; flex-wrap: wrap; align-items: center; gap: 3rem; position: relative; z-index: 1; }
.hero-text { flex: 1; min-width: 280px; }
.badge-top { display: inline-flex; align-items: center; gap: .5rem; background: var(--white); border: 3px solid var(--sky); border-radius: 50px; padding: .4rem 1.2rem; font-weight: 700; font-size: .9rem; margin-bottom: 1.5rem; box-shadow: 0 3px 0 var(--sky-light); }
.badge-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--pink); animation: blink 1.4s ease-in-out infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.2} }
h1 { font-size: clamp(2.6rem, 5vw, 3.6rem); font-weight: 800; line-height: 1.3; margin-bottom: 1.2rem; }
.hl-sky { display: inline-block; background: var(--sky); color: var(--white); padding: 4px .35em 9px; border-radius: 12px; transform: rotate(-1.5deg); margin-left: .2rem; }
.hl-pink { display: inline-block; background: var(--pink); color: var(--white); padding: 6px .35em 11px; border-radius: 12px; transform: rotate(1.5deg); margin-right: .2rem; }
.crown { color: var(--sky); font-size: 2rem; vertical-align: middle; }
.hero-desc { font-size: 1.15rem; color: var(--gray); line-height: 1.8; max-width: 440px; margin-bottom: 2rem; }
.hero-btns { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
.btn-primary { background: var(--pink); color: #fff; font-family: inherit; font-weight: 800; font-size: 1.1rem; padding: .9rem 2.2rem; border-radius: 50px; border: none; cursor: pointer; box-shadow: 0 5px 0 var(--pink-dark); transition: transform .15s; }
.btn-primary:hover { transform: translateY(-2px); }
.btn-primary:active { transform: translateY(2px); box-shadow: 0 2px 0 var(--pink-dark); }
.btn-secondary { background: var(--white); color: var(--dark); font-family: inherit; font-weight: 700; font-size: 1rem; padding: .9rem 2rem; border-radius: 50px; border: 2px solid var(--dark); cursor: pointer; transition: background .2s; }
.btn-secondary:hover { background: var(--sky-light); }
.hero-stats { display: flex; flex-wrap: wrap; gap: .7rem; }
.stat-pill { background: var(--white); border-radius: 50px; padding: .4rem 1rem; font-weight: 700; font-size: .85rem; display: flex; align-items: center; gap: .4rem; }
.stat-pill.sky { border: 3px solid var(--sky); }
.stat-pill.pink { border: 3px solid var(--pink); }
.stat-pill.lime { border: 3px solid var(--lime); }
.stat-pill.sky i { color: var(--sky); }
.stat-pill.pink i { color: var(--pink); }
.stat-pill.lime i { color: var(--lime-dark); }
.hero-img { flex: 1; min-width: 260px; display: flex; justify-content: center; }
.hero-img img { width: 460px; max-width: 100%; animation: float 4s ease-in-out infinite; }
@keyframes float { 0%,100%{transform:translateY(0) rotate(-1deg)} 50%{transform:translateY(-20px) rotate(1deg)} }

#features { padding: 5rem 2rem; background: var(--light); position: relative; }
.section-tag { display: inline-flex; align-items: center; gap: .6rem; background: var(--white); padding: .5rem 1.6rem; border-radius: 2rem; font-weight: 800; font-size: 1.1rem; box-shadow: 0 2px 12px rgba(236,72,153,.15); margin-bottom: 1rem; color: var(--sky-dark); }
.section-center { text-align: center; margin-bottom: 3rem; }
h2 { font-size: clamp(1.8rem, 4vw, 2.8rem); font-weight: 800; margin-bottom: .5rem; }
.section-sub { font-size: 1.15rem; color: var(--gray); max-width: 480px; margin: 0 auto; }
.cards-grid { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; }
.feat-card { background: var(--white); border-radius: 24px; padding: 2rem 1.5rem; text-align: center; box-shadow: 0 4px 24px rgba(0,0,0,.06); transition: transform .3s, box-shadow .3s; position: relative; overflow: hidden; }
.feat-card:hover { transform: translateY(-6px) rotate(-.5deg); box-shadow: 0 12px 32px rgba(0,0,0,.1); }
.feat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 5px; }
.feat-card.c1::before { background: linear-gradient(90deg, var(--sky), var(--sky-mid)); }
.feat-card.c2::before { background: linear-gradient(90deg, var(--lime), var(--lime-mid)); }
.feat-card.c3::before { background: linear-gradient(90deg, var(--pink), var(--pink-mid)); }
.feat-card.c4::before { background: linear-gradient(90deg, var(--sky-dark), var(--sky)); }
.feat-icon { width: 64px; height: 64px; border-radius: 16px; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; }
.feat-card.c1 .feat-icon { background: var(--sky-light); color: var(--sky-dark); }
.feat-card.c2 .feat-icon { background: var(--lime-light); color: var(--lime-dark); }
.feat-card.c3 .feat-icon { background: var(--pink-light); color: var(--pink-dark); }
.feat-card.c4 .feat-icon { background: var(--sky-light); color: var(--sky-dark); }
.feat-card h3 { font-size: 1.2rem; font-weight: 800; margin-bottom: .5rem; }
.feat-card p { color: var(--gray); line-height: 1.7; font-size: 1.2rem; }

#pricing { padding: 5rem 2rem; background: var(--grad-pricing); }
.pricing-grid { max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; align-items: center; }
.price-card { background: var(--white); border-radius: 28px; padding: 2.2rem 2rem; box-shadow: 0 4px 24px rgba(0,0,0,.08); transition: transform .3s; border: 2px solid transparent; }
.price-card:hover { transform: translateY(-5px); }
.price-card.featured { background: var(--pink); color: #fff; transform: scale(1.04); border-color: var(--white); box-shadow: 0 12px 40px rgba(14,116,144,.25); }
.price-card.featured:hover { transform: scale(1.06) translateY(-4px); }
.price-badge { font-size: .8rem; font-weight: 800; padding: .3rem 1rem; border-radius: 50px; display: inline-block; margin-bottom: .8rem; }
.price-card:not(.featured) .price-badge { background: var(--sky-light); color: var(--sky-dark); }
.price-card.featured .price-badge { background: rgba(255,255,255,.2); color: #fff; }
.price-amount { font-size: 3.5rem; font-weight: 800; line-height: 1; }
.price-unit { font-size: 1.2rem; opacity: .7; }
.price-name { font-size: 1.4rem; font-weight: 800; margin: .8rem 0 1.4rem; }
.price-features { list-style: none; padding: 0; margin-bottom: 1.8rem; }
.price-features li { display: flex; align-items: center; gap: .6rem; font-size: .95rem; padding: .4rem 0; border-bottom: 1px solid rgba(0,0,0,.05); }
.price-card.featured .price-features li { border-bottom-color: rgba(255,255,255,.1); }
.price-features li i { font-size: .7rem; flex-shrink: 0; }
.price-card:not(.featured) .price-features li i { color: var(--lime-dark); }
.price-card.featured .price-features li i { color: var(--lime-mid); }
.btn-price { width: 100%; padding: 1rem; border-radius: 16px; border: none; cursor: pointer; font-family: inherit; font-weight: 800; font-size: 1rem; transition: transform .15s; }
.btn-price:hover { transform: scale(1.02); }
.price-card:not(.featured) .btn-price { background: var(--pink); color: #fff; }
.price-card.featured .btn-price { background: #fff; color: var(--pink-dark); }
.price-note { text-align: center; margin-top: 2rem; color: var(--gray); font-size: .9rem; }

#parents { padding: 5rem 2rem; background: var(--light); }
.parents-grid { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.2rem; }
.parent-card { background: var(--white); border-radius: 20px; padding: 1.8rem; border: 2px solid transparent; box-shadow: 0 2px 16px rgba(0,0,0,.05); transition: transform .25s, border-color .25s; }
.parent-card:hover { transform: translateY(-4px); }
.parent-card.sky:hover { border-color: var(--sky); }
.parent-card.pink:hover { border-color: var(--pink); }
.parent-card.lime:hover { border-color: var(--lime); }
.parent-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 1rem; }
.parent-card.sky .parent-icon { background: var(--sky-light); color: var(--sky-dark); }
.parent-card.pink .parent-icon { background: var(--pink-light); color: var(--pink-dark); }
.parent-card.lime .parent-icon { background: var(--lime-light); color: var(--lime-dark); }
.parent-card h4 { font-size: 1.1rem; font-weight: 800; margin-bottom: .5rem; }
.parent-card p { color: var(--gray); font-size: 1.2rem; line-height: 1.7; }

#reviews { padding: 5rem 2rem; background: linear-gradient(135deg, var(--sky-light) 0%, var(--pink-light) 100%); }
.reviews-grid { max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; align-items: start; }
.review-card { background: var(--white); border-radius: 24px; padding: 2rem; position: relative; box-shadow: 0 4px 24px rgba(0,0,0,.07); transition: transform .3s; }
.review-card:hover { transform: translateY(-5px); }
.review-chip { position: absolute; top: -14px; right: 1.5rem; font-size: .75rem; font-weight: 800; padding: .3rem .9rem; border-radius: 50px; }
.review-card:not(.featured) .review-chip { background: var(--sky); color: #fff; }
.review-card.featured .review-chip { background: var(--pink); color: #fff; }
.review-stars { color: #FFB800; font-size: 1.1rem; margin: .5rem 0 1rem; }
.review-card h4 { font-weight: 800; margin-bottom: .6rem; }
.review-card p { font-size: .92rem; line-height: 1.7; margin-bottom: 1.2rem; }
.review-author { display: flex; align-items: center; gap: .8rem; }
.review-avatar { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid var(--sky); }
.review-name { font-weight: 800; font-size: .95rem; }
.review-loc { font-size: .8rem; opacity: .6; }

#faq { padding: 5rem 2rem; background: var(--gray-light); }
.faq-list { max-width: 700px; margin: 0 auto; display: flex; flex-direction: column; gap: .8rem; }
.faq-item { background: var(--white); border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.05); }
.faq-item.s1 { border: 2px solid var(--sky); }
.faq-item.s2 { border: 2px solid var(--pink); }
.faq-item.s3 { border: 2px solid var(--lime); }
.faq-item.s4 { border: 2px solid var(--sky-dark); }
.faq-item.s5 { border: 2px solid var(--pink-dark); }
.faq-btn { width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1.1rem 1.5rem; background: none; border: none; cursor: pointer; font-family: inherit; font-weight: 800; font-size: 1rem; text-align: right; color: var(--dark); }
.faq-btn:hover { background: var(--sky-light); }
.faq-icon { font-size: 1.5rem; transition: transform .3s; line-height: 1; color: var(--sky-dark); }
.faq-item.open .faq-icon { transform: rotate(45deg); }
.faq-answer { max-height: 0; overflow: hidden;
 transition: max-height .35s ease, padding .3s ease; padding: 0 1.5rem;
  color: var(--gray); font-size: 1.1rem; line-height: 1.8; }
.faq-item.open .faq-answer { max-height: 200px; padding-bottom: 1.2rem; }

#cta { padding: 2rem; text-align: center; background: var(--pink); position: relative; overflow: hidden; }
#cta::before { content: ''; position: absolute; inset: 0; background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.06'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); pointer-events: none; }
#cta h2 { color: #fff; margin-bottom: .8rem; }
#cta p { color: rgba(255,255,255,.85); font-size: 1.1rem; margin-bottom: 2rem; }
.btn-cta { background: var(--white); color: var(--pink-dark); font-family: inherit; font-weight: 800; font-size: 1.2rem; padding: 1rem 3rem; border-radius: 50px; border: none; cursor: pointer; box-shadow: 0 5px 0 rgba(0,0,0,.15); transition: transform .15s; }
.btn-cta:hover { transform: translateY(-2px); }
.btn-cta:active { transform: translateY(2px); }

footer { padding: 3.5rem 2rem 2rem; }
.footer-inner { max-width: 1100px; margin: 0 auto; }
.footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 2rem; margin-bottom: 2.5rem; }
.footer-brand .logo-text { font-size: 1.6rem; font-weight: 800; margin-bottom: .5rem; }
.footer-brand p { font-size: .85rem; opacity: .7; line-height: 1.7; margin-bottom: 1rem; }
.footer-socials { display: flex; gap: .7rem; }
.footer-socials a { width: 38px; height: 38px; border-radius: 50%; border: 1px solid rgba(255,255,255,.15); display: flex; align-items: center; justify-content: center; font-size: .9rem; transition: background .2s, transform .2s; }
.footer-socials a:hover { background: rgba(255,255,255,.1); transform: translateY(-2px); }
footer h4 { font-weight: 800; font-size: 1rem; margin-bottom: 1rem; }
footer ul li { margin-bottom: .6rem; }
footer ul a { opacity: .7; font-size: .88rem; transition: opacity .2s; }
footer ul a:hover { opacity: 1; }
footer address { font-style: normal; opacity: .7; font-size: .88rem; line-height: 2; }
.footer-bottom { border-top: 1px solid rgba(255,255,255,.1); padding-top: 1.5rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; font-size: .83rem; opacity: .6; }
.footer-langs { display: flex; gap: 1.2rem; }
.footer-langs span { cursor: pointer; transition: opacity .2s; }
.footer-langs span:hover { opacity: 1; }

/* ── ABOUT section ── */
.home-about { padding: 5rem 2rem; background: var(--light); }
.about-cards { max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.about-card { background: var(--white); border-radius: 24px; padding: 2.2rem 2rem; box-shadow: 0 4px 24px rgba(0,0,0,.06); border: 2px solid #F1F5F9; transition: transform .3s; }
.about-card:hover { transform: translateY(-5px); }
.about-ic { width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1.1rem; }
.about-ic.sky { background: var(--pink-light); color: var(--pink-dark); }
.about-ic.pink { background: var(--sky-light); color: var(--sky-dark); }
.about-card h3 { font-size: 1.3rem; font-weight: 800; margin-bottom: .7rem; }
.about-card p { color: var(--gray); line-height: 1.9; font-size: 1.02rem; }
.about-more { text-align: center; margin-top: 2rem; }
.about-link { display: inline-flex; align-items: center; gap: .5rem; color: var(--sky-dark); font-weight: 800; font-size: 1rem; }
.about-link:hover { color: var(--sky); }

/* ── BLOG section ── */
.home-blog { padding: 5rem 2rem; background: linear-gradient(135deg, var(--sky-light) 0%, var(--pink-light) 100%); }
.home-blog-grid { max-width: 1050px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; }
.home-blog-card { background: var(--white); border-radius: 22px; padding: 1.8rem 1.6rem; box-shadow: 0 4px 20px rgba(0,0,0,.06); text-decoration: none; color: inherit; display: flex; flex-direction: column; transition: transform .3s, box-shadow .3s; }
.home-blog-card:hover { transform: translateY(-6px); box-shadow: 0 14px 32px rgba(0,0,0,.1); }
.hb-cat { align-self: flex-start; background: var(--sky-light); color: var(--sky-dark); font-size: .72rem; font-weight: 800; padding: .25rem .8rem; border-radius: 50px; margin-bottom: .8rem; }
.home-blog-card h3 { font-weight: 800; font-size: 1.1rem; line-height: 1.5; margin-bottom: .6rem; color: var(--dark); }
.home-blog-card p { color: var(--gray); font-size: .9rem; line-height: 1.8; flex: 1; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.hb-more { color: var(--pink); font-weight: 800; font-size: .85rem; display: inline-flex; align-items: center; gap: .35rem; }

/* ── CONTACT section ── */
.home-contact { padding: 5rem 2rem; background: var(--light); }
.contact-form { max-width: 640px; margin: 0 auto; }
.contact-ok { background: var(--lime-light); color: var(--lime-dark); border: 2px solid var(--lime-mid); border-radius: 14px; padding: .9rem 1.2rem; font-weight: 700; margin-bottom: 1.2rem; text-align: center; }
.cf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
.cf-field { display: flex; flex-direction: column; margin-bottom: 1rem; }
.cf-row .cf-field { margin-bottom: 0; }
.contact-form input, .contact-form textarea { font-family: inherit; font-size: 1rem; padding: .9rem 1.1rem; border-radius: 14px; border: 2px solid #E2E8F0; background: var(--white); transition: border-color .2s; resize: vertical; }
.contact-form input:focus, .contact-form textarea:focus { outline: none; border-color: var(--sky); }
.cf-err { color: #DC2626; font-size: .8rem; font-weight: 700; margin-top: .35rem; }
.contact-form .btn-primary { width: 100%; margin-top: .5rem; }
.contact-form .btn-primary:disabled { opacity: .6; cursor: not-allowed; }

@media (max-width: 768px) {
  nav ul { display: none; }
  .hero-img img { width: 300px; }
  .hero-inner { text-align: center; }
  .hero-btns { justify-content: center; }
  .hero-stats { justify-content: center; }
  .about-cards { grid-template-columns: 1fr; }
  .cf-row { grid-template-columns: 1fr; }
}
</style>
