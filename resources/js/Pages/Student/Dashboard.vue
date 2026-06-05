<template>
  <Head title="بارع — الطالب" />
  <StudentLayout :level="7" :xp="1360" :xp-max="2000" :streak="stats.streak">

    <!-- Hero Banner -->
    <div class="hero-banner">
      <div class="hero-banner-bg"></div>
      <div class="hero-grid-overlay"></div>
      <div class="hero-content">
        <div class="hero-text">
          <div class="hero-tag">
            <span class="mi">auto_awesome</span>
            مرحبًا — جاهز لمغامرة تعلّم جديدة؟
          </div>
          <h2>استمر يا بطل! كل درس يقرّبك من الوسام</h2>
          <p>اختر مهارة من مساراتك وابدأ رحلتك التفاعلية</p>
        </div>
        <Link :href="route('student.paths')" class="hero-cta-btn">
          <span class="mi">play_circle</span>
          تصفّح المسارات
        </Link>
      </div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-card c-sky">
        <div class="stat-icon-wrap"><span class="mi">menu_book</span></div>
        <div><div class="stat-val">{{ stats.paths_completed }}</div><div class="stat-lbl">مهارة مكتملة</div></div>
      </div>
      <div class="stat-card c-pink">
        <div class="stat-icon-wrap"><span class="mi">local_fire_department</span></div>
        <div><div class="stat-val">{{ stats.streak }}</div><div class="stat-lbl">أيام متتالية</div></div>
      </div>
      <div class="stat-card c-lime">
        <div class="stat-icon-wrap"><span class="mi">workspace_premium</span></div>
        <div><div class="stat-val">{{ stats.badges }}</div><div class="stat-lbl">شارة مكسوبة</div></div>
      </div>
      <div class="stat-card c-amber">
        <div class="stat-icon-wrap"><span class="mi">verified</span></div>
        <div><div class="stat-val">{{ stats.certificates }}</div><div class="stat-lbl">شهادة</div></div>
      </div>
    </div>

    <!-- Main Grid -->
    <div class="main-grid">

      <!-- Current Paths -->
      <div class="card">
        <div class="card-hd">
          <div class="card-title"><span class="mi t-sky">auto_stories</span> مساراتي الحالية</div>
          <Link :href="route('student.paths')" class="card-more">عرض الكل <span class="mi">arrow_back_ios</span></Link>
        </div>

        <div v-if="paths.length === 0" class="empty">لم تبدأ أي مسار بعد — اختر مهارة وابدأ مغامرتك!</div>

        <Link v-for="p in paths" :key="p.id" :href="route('student.journey', p.id)" class="story-item">
          <div class="story-cover" :style="{ background: (p.color || '#38BDF8') + '22', color: p.color || '#38BDF8' }">
            <span class="mi">{{ p.icon || 'menu_book' }}</span>
          </div>
          <div class="story-info">
            <div class="story-name">{{ p.title }}</div>
            <div class="story-prog-row">
              <div class="story-bar"><div class="story-fill" :style="{ width: p.progress + '%' }"></div></div>
              <div class="story-pct">{{ p.progress }}٪</div>
            </div>
          </div>
          <span class="story-tag" :class="p.progress >= 100 ? 'tag-done' : (p.progress > 0 ? 'tag-cont' : 'tag-new')">
            {{ p.progress >= 100 ? 'مكتمل' : (p.progress > 0 ? 'متواصل' : 'جديد') }}
          </span>
        </Link>
      </div>

      <!-- Badges -->
      <div class="card">
        <div class="card-hd">
          <div class="card-title"><span class="mi t-amber">military_tech</span> آخر الشارات</div>
          <Link :href="route('student.badges')" class="card-more">كلها <span class="mi">arrow_back_ios</span></Link>
        </div>
        <div v-if="badges.length === 0" class="empty">لا توجد شارات بعد</div>
        <div v-else class="badges-grid">
          <div v-for="b in badges" :key="b.id" class="badge-tile" :class="{ earned: b.earned, locked: !b.earned }">
            <div class="badge-icon-wrap"><span class="mi">{{ b.icon || 'emoji_events' }}</span></div>
            <div class="badge-tile-name">{{ b.name }}</div>
          </div>
        </div>
      </div>

    </div>

  </StudentLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

defineProps({
  stats:  { type: Object, default: () => ({ paths_completed: 0, streak: 0, badges: 0, certificates: 0 }) },
  paths:  { type: Array,  default: () => [] },
  badges: { type: Array,  default: () => [] },
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/icon?family=Material+Icons+Round');

.hero-banner, .stats-row, .main-grid {
  --sky:#38BDF8; --sky-light:#E0F4FF; --sky-mid:#7DD3F8; --sky-dark:#0E7490;
  --pink:#EC4899; --pink-light:#FCE7F3; --pink-mid:#F9A8D4; --pink-dark:#9D174D;
  --lime:#84CC16; --lime-light:#F0FDF4; --lime-mid:#BEF264; --lime-dark:#3F6212;
  --amber:#F59E0B; --amber-light:#FEF3C7;
  --dark:#0F172A; --gray:#64748B; --light:#F1F5F9; --white:#fff; --border:#E2E8F0;
  --radius-xl:24px; --radius-md:14px;
  --shadow-sm:0 2px 12px rgba(15,23,42,.06); --shadow-md:0 6px 28px rgba(15,23,42,.10);
}
* { box-sizing:border-box; }
.mi { font-family:'Material Icons Round'; font-style:normal; line-height:1; display:inline-flex; align-items:center; justify-content:center; vertical-align:middle; }

.hero-banner { border-radius:28px; background:linear-gradient(130deg,#0E7490 0%,#0369A1 40%,#7C3AED 100%);
  position:relative; overflow:hidden; margin-bottom:1.6rem; box-shadow:0 12px 40px rgba(14,116,144,.28); display:flex; }
.hero-banner-bg { position:absolute; inset:0; pointer-events:none;
  background:radial-gradient(circle at 15% 50%,rgba(56,189,248,.25) 0%,transparent 50%),radial-gradient(circle at 85% 20%,rgba(139,92,246,.3) 0%,transparent 45%); }
.hero-grid-overlay { position:absolute; inset:0; pointer-events:none;
  background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px); background-size:32px 32px; }
.hero-content { position:relative; z-index:1; padding:2rem 2.5rem; flex:1; display:flex; align-items:center; justify-content:space-between; gap:1.5rem; flex-wrap:wrap; }
.hero-text { color:#fff; }
.hero-tag { display:inline-flex; align-items:center; gap:.4rem; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25);
  border-radius:50px; padding:.28rem .9rem; font-size:.8rem; font-weight:700; margin-bottom:.7rem; backdrop-filter:blur(6px); }
.hero-tag .mi { font-size:.95rem; color:var(--lime-mid); }
.hero-text h2 { font-size:clamp(1.4rem,2.5vw,2rem); font-weight:800; margin-bottom:.4rem; }
.hero-text p { opacity:.85; font-size:.97rem; line-height:1.6; }
.hero-cta-btn { background:var(--white); color:var(--sky-dark); font-family:inherit; font-weight:800; font-size:.97rem;
  padding:.85rem 2rem; border-radius:50px; border:none; cursor:pointer; white-space:nowrap; box-shadow:0 6px 20px rgba(0,0,0,.18);
  transition:transform .15s; display:flex; align-items:center; gap:.5rem; text-decoration:none; }
.hero-cta-btn:hover { transform:translateY(-3px); }
.hero-cta-btn .mi { font-size:1.15rem; }

.stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.6rem; }
.stat-card { background:var(--white); border-radius:var(--radius-xl); padding:1.4rem 1.3rem; display:flex; align-items:center; gap:1rem;
  box-shadow:var(--shadow-sm); border:1.5px solid var(--border); transition:all .25s; position:relative; overflow:hidden; }
.stat-card::after { content:''; position:absolute; top:0; right:0; width:4px; height:100%; }
.stat-card.c-sky::after { background:var(--sky); }
.stat-card.c-pink::after { background:var(--pink); }
.stat-card.c-lime::after { background:var(--lime); }
.stat-card.c-amber::after { background:var(--amber); }
.stat-card:hover { transform:translateY(-4px); box-shadow:var(--shadow-md); }
.stat-icon-wrap { width:54px; height:54px; border-radius:16px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.stat-icon-wrap .mi { font-size:1.6rem; }
.stat-card.c-sky .stat-icon-wrap { background:var(--sky-light); color:var(--sky-dark); }
.stat-card.c-pink .stat-icon-wrap { background:var(--pink-light); color:var(--pink-dark); }
.stat-card.c-lime .stat-icon-wrap { background:var(--lime-light); color:var(--lime-dark); }
.stat-card.c-amber .stat-icon-wrap { background:var(--amber-light); color:#92400E; }
.stat-val { font-size:1.8rem; font-weight:800; line-height:1.1; }
.stat-lbl { font-size:.8rem; color:var(--gray); font-weight:600; margin-top:.15rem; }

.main-grid { display:grid; grid-template-columns:1fr 340px; gap:1.5rem; }
.card { background:var(--white); border-radius:var(--radius-xl); padding:1.6rem; box-shadow:var(--shadow-sm); border:1.5px solid var(--border); }
.card-hd { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.2rem; }
.card-title { font-size:1.02rem; font-weight:800; display:flex; align-items:center; gap:.5rem; color:var(--dark); }
.card-title .mi { font-size:1.15rem; }
.t-sky { color:var(--sky-dark); } .t-amber { color:#92400E; }
.card-more { font-size:.82rem; font-weight:700; color:var(--sky-dark); cursor:pointer; display:flex; align-items:center; gap:.2rem; text-decoration:none; }
.card-more .mi { font-size:.9rem; }
.card-more:hover { text-decoration:underline; }
.empty { color:var(--gray); font-weight:600; text-align:center; padding:1.5rem; }

.story-item { display:flex; align-items:center; gap:.9rem; padding:.85rem 1rem; border-radius:var(--radius-md);
  border:1.5px solid transparent; margin-bottom:.55rem; cursor:pointer; background:var(--light); transition:all .2s; text-decoration:none; color:inherit; }
.story-item:hover { border-color:var(--sky); background:var(--sky-light); transform:translateX(-3px); }
.story-cover { width:50px; height:50px; border-radius:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.story-cover .mi { font-size:1.7rem; }
.story-info { flex:1; min-width:0; }
.story-name { font-weight:800; font-size:.93rem; margin-bottom:.3rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.story-prog-row { display:flex; align-items:center; gap:.5rem; }
.story-bar { flex:1; height:6px; background:#E2E8F0; border-radius:99px; overflow:hidden; }
.story-fill { height:100%; border-radius:99px; background:linear-gradient(90deg,var(--sky),var(--pink)); }
.story-pct { font-size:.76rem; font-weight:800; color:var(--sky-dark); }
.story-tag { font-size:.7rem; font-weight:800; padding:.22rem .7rem; border-radius:50px; flex-shrink:0; }
.tag-new { background:var(--pink-light); color:var(--pink-dark); }
.tag-cont { background:var(--sky-light); color:var(--sky-dark); }
.tag-done { background:var(--lime-light); color:var(--lime-dark); }

.badges-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:.65rem; }
.badge-tile { text-align:center; padding:.85rem .4rem .7rem; border-radius:var(--radius-md); border:1.5px solid var(--border);
  background:var(--light); cursor:pointer; transition:all .25s; }
.badge-tile:hover { transform:translateY(-4px) rotate(2deg); box-shadow:var(--shadow-md); }
.badge-tile.earned { background:var(--amber-light); border-color:var(--amber); }
.badge-tile.locked { filter:grayscale(.85); opacity:.45; }
.badge-icon-wrap { width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; margin:0 auto .4rem; }
.badge-icon-wrap .mi { font-size:1.35rem; }
.badge-tile.earned .badge-icon-wrap { background:rgba(245,158,11,.15); color:var(--amber); }
.badge-tile:not(.earned) .badge-icon-wrap { background:#F1F5F9; color:var(--gray); }
.badge-tile-name { font-size:.68rem; font-weight:800; color:var(--gray); }
.badge-tile.earned .badge-tile-name { color:#92400E; }

@media(max-width:1100px) { .stats-row { grid-template-columns:repeat(2,1fr); } .main-grid { grid-template-columns:1fr; } }
</style>
