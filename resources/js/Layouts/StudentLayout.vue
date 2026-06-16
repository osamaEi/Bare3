<template>
  <div class="student-shell" dir="rtl">

    <!-- ═══ SIDEBAR ═══ -->
    <aside class="sidebar" :class="{ open: mobileOpen }">
      <div class="sb-logo">
        <img src="/images/logo.png" style="width:100px" alt="بارع" />
      </div>

      <div class="sb-profile">
        <div class="sb-profile-dots"></div>
        <img src="/images/b60204504019ec9db2deff871a1e00c6.png" alt="بارع" class="sb-image" />
        <div class="sb-name">{{ user?.name ?? 'طالب' }}</div>
      </div>

      <nav class="sb-nav">
        <div class="sb-nav-label">القائمة الرئيسية</div>

        <Link :href="route('student.dashboard')" class="nav-item" :class="{ active: isActive('student.dashboard') }">
          <div class="nav-icon"><span class="mi">home</span></div>
          الرئيسية
        </Link>
        <Link :href="route('student.paths')" class="nav-item" :class="{ active: isActive('student.paths') }">
          <div class="nav-icon" style="background:var(--pink-light);color:var(--pink-dark)"><span class="mi">menu_book</span></div>
          مساراتي
        </Link>
        <Link :href="route('student.badges')" class="nav-item" :class="{ active: isActive('student.badges') }">
          <div class="nav-icon" style="background:var(--amber-light);color:#92400E"><span class="mi">workspace_premium</span></div>
          شاراتي
        </Link>
        <Link :href="route('student.certificates')" class="nav-item" :class="{ active: isActive('student.certificates') }">
          <div class="nav-icon" style="background:var(--lime-light);color:var(--lime-dark)"><span class="mi">verified</span></div>
          شهاداتي
        </Link>
        <Link :href="route('student.notifications')" class="nav-item" :class="{ active: isActive('student.notifications') }">
          <div class="nav-icon" style="background:var(--sky-light);color:var(--sky-dark)"><span class="mi">notifications</span></div>
          الإشعارات
        </Link>

        <div class="sb-nav-label">أخرى</div>
        <Link :href="route('profile.edit')" class="nav-item">
          <div class="nav-icon"><span class="mi">settings</span></div>
          الإعدادات
        </Link>
        <Link :href="route('logout')" method="post" as="button" class="nav-item logout-btn">
          <div class="nav-icon" style="background:var(--pink-light);color:var(--pink-dark)"><span class="mi">logout</span></div>
          خروج
        </Link>
      </nav>
    </aside>

    <!-- ═══ MAIN ═══ -->
    <div class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="mobile-toggle" @click="mobileOpen = !mobileOpen"><span class="mi">menu</span></button>
          <div class="topbar-greeting">
            أهلًا، <strong>{{ firstName }}</strong> — جاهز للمغامرة؟
            <span class="mi" style="color:var(--amber);vertical-align:middle;font-size:1.1rem;margin-right:.3rem">waving_hand</span>
          </div>
        </div>
        <div class="topbar-right">
          <div class="streak-chip">
            <span class="mi">local_fire_department</span>
            {{ streak }} أيام متتالية
          </div>
          <div class="top-btn">
            <span class="mi">notifications</span>
            <span class="dot"></span>
          </div>
        </div>
      </div>

      <div class="content">
        <slot />
      </div>
    </div>

    <div v-if="mobileOpen" class="mobile-overlay" @click="mobileOpen = false"></div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
  level:  { type: [Number, String], default: 1 },
  xp:     { type: [Number, String], default: 0 },
  xpMax:  { type: [Number, String], default: 2000 },
  streak: { type: [Number, String], default: 0 },
})

const page = usePage()
const user = computed(() => page.props.auth?.user)
const firstName = computed(() => (user.value?.name ?? 'طالب').split(' ')[0])
const xpPct = computed(() => Math.min(100, Math.round((Number(props.xp) / Number(props.xpMax || 1)) * 100)))

const mobileOpen = ref(false)
const isActive = (name) => (page.props.ziggy?.current ?? '') === name
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@400;600;700;800&display=swap');
@import url('https://fonts.googleapis.com/icon?family=Material+Icons+Round');

.student-shell {
  --sky:#38BDF8; --sky-light:#E0F4FF; --sky-mid:#7DD3F8; --sky-dark:#0E7490;
  --pink:#EC4899; --pink-light:#FCE7F3; --pink-mid:#F9A8D4; --pink-dark:#9D174D;
  --lime:#84CC16; --lime-light:#F0FDF4; --lime-mid:#BEF264; --lime-dark:#3F6212;
  --amber:#F59E0B; --amber-light:#FEF3C7; --violet:#8B5CF6; --violet-light:#F3E8FF;
  --dark:#0F172A; --gray:#64748B; --light:#F1F5F9; --white:#fff; --border:#E2E8F0;
  --radius-xl:24px; --radius-md:14px;
  --shadow-sm:0 2px 12px rgba(15,23,42,.06); --shadow-md:0 6px 28px rgba(15,23,42,.10);
  font-family:'Baloo Bhaijaan 2', cursive; color:var(--dark); background:#EEF4FB; min-height:100vh;
}
.student-shell * { box-sizing:border-box; margin:0; padding:0; }

.mi { font-family:'Material Icons Round'; font-style:normal; font-weight:normal; line-height:1;
  display:inline-flex; align-items:center; justify-content:center; vertical-align:middle; user-select:none; }

/* SIDEBAR */
.sidebar { width:270px; min-height:100vh; background:var(--white); border-left:1px solid var(--border);
  display:flex; flex-direction:column; position:fixed; right:0; top:0; z-index:50; box-shadow:6px 0 32px rgba(14,116,144,.07); }
.sb-logo { padding:1.5rem 1.6rem 1.2rem; display:flex; align-items:center; gap:.8rem; border-bottom:1px solid var(--border); }

.sb-profile { margin:1.1rem 1.2rem; border-radius:20px; padding:1.3rem 1.1rem; text-align:center; position:relative; overflow:hidden;
  background:linear-gradient(145deg,#0E7490 0%,#8B5CF6 100%); color:#fff; box-shadow:0 8px 24px rgba(14,116,144,.3); }
.sb-profile-dots { position:absolute; inset:0; pointer-events:none;
  background:radial-gradient(circle at 70% 20%,rgba(255,255,255,.12) 0%,transparent 55%),radial-gradient(circle at 20% 80%,rgba(255,255,255,.08) 0%,transparent 45%); }
.sb-avatar-wrap { position:relative; display:inline-block; margin-bottom:.5rem; }
.sb-avatar { width:70px; height:70px; border-radius:50%; background:rgba(255,255,255,.18); backdrop-filter:blur(4px);
  border:3px solid rgba(255,255,255,.55); display:flex; align-items:center; justify-content:center; box-shadow:0 4px 16px rgba(0,0,0,.2); position:relative; z-index:1; }
.sb-avatar .mi { font-size:2.2rem; color:#fff; }
.sb-avatar-ring { position:absolute; inset:-5px; border-radius:50%; border:2px dashed rgba(255,255,255,.4); animation:spin 14s linear infinite; }
@keyframes spin { to { transform:rotate(360deg); } }
.sb-lvl-badge { position:absolute; bottom:-2px; left:-2px; width:24px; height:24px; border-radius:50%;
  background:var(--lime-mid); border:2px solid var(--white); display:flex; align-items:center; justify-content:center; z-index:2; }
.sb-lvl-badge .mi { font-size:.85rem; color:var(--lime-dark); }
.sb-image { width:100%; max-height:120px; object-fit:contain; position:relative; z-index:1; margin-bottom:.5rem; }
.sb-name { font-weight:800; font-size:1.05rem; position:relative; z-index:1; }
.sb-rank { display:inline-flex; align-items:center; gap:.3rem; background:rgba(255,255,255,.18); border-radius:50px;
  padding:.25rem .85rem; font-size:.76rem; font-weight:700; margin-top:.4rem; }
.sb-rank .mi { font-size:.9rem; color:var(--lime-mid); }
.sb-xp { margin-top:.8rem; }
.sb-xp-row { display:flex; justify-content:space-between; font-size:.72rem; font-weight:700; opacity:.82; margin-bottom:.35rem; }
.sb-xp-track { height:8px; background:rgba(255,255,255,.22); border-radius:99px; overflow:hidden; }
.sb-xp-fill { height:100%; background:linear-gradient(90deg,var(--lime-mid),#fff); border-radius:99px; box-shadow:0 0 8px rgba(190,242,100,.6); transition:width 1.2s cubic-bezier(.22,1,.36,1); }

.sb-nav { padding:.8rem .9rem; flex:1; overflow-y:auto; }
.sb-nav-label { font-size:.68rem; font-weight:800; color:var(--gray); letter-spacing:.08em; text-transform:uppercase; padding:.6rem .5rem .3rem; margin-top:.4rem; }
.nav-item { display:flex; align-items:center; gap:.75rem; padding:.7rem .9rem; border-radius:var(--radius-md);
  font-weight:700; font-size:.94rem; color:var(--gray); cursor:pointer; transition:all .22s; margin-bottom:.15rem; text-decoration:none; }
.nav-item:hover { background:var(--sky-light); color:var(--sky-dark); }
.nav-item:hover .nav-icon { background:var(--sky); color:#fff; }
.nav-item.active { background:linear-gradient(135deg,var(--sky-light) 0%,var(--pink-light) 100%); color:var(--sky-dark); font-weight:800; }
.nav-item.active .nav-icon { background:linear-gradient(135deg,var(--sky),var(--sky-dark)); color:#fff; box-shadow:0 4px 12px rgba(56,189,248,.4); }
.nav-icon { width:36px; height:36px; border-radius:11px; display:flex; align-items:center; justify-content:center;
  font-size:.9rem; flex-shrink:0; background:#F1F5F9; color:var(--gray); transition:all .22s; }
.nav-icon .mi { font-size:1.15rem; }
.logout-btn { width:100%; border:none; background:none; font-family:inherit; text-align:right; }

/* MAIN */
.main { margin-right:270px; min-height:100vh; display:flex; flex-direction:column; }
.topbar { background:rgba(255,255,255,.88); backdrop-filter:blur(16px); border-bottom:1px solid var(--border);
  padding:0 2rem; height:68px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:40; }
.topbar-left { display:flex; align-items:center; gap:.8rem; }
.topbar-greeting { font-size:1rem; font-weight:700; color:var(--gray); }
.topbar-greeting strong { color:var(--sky-dark); font-weight:800; }
.topbar-right { display:flex; align-items:center; gap:.7rem; }
.mobile-toggle { display:none; background:none; border:none; cursor:pointer; color:var(--gray); }
.mobile-toggle .mi { font-size:1.5rem; }
.streak-chip { display:flex; align-items:center; gap:.4rem; background:var(--amber-light); border:1.5px solid var(--amber);
  border-radius:50px; padding:.35rem 1rem; font-weight:800; font-size:.88rem; color:#92400E; box-shadow:0 2px 8px rgba(245,158,11,.15); }
.streak-chip .mi { font-size:1rem; color:var(--amber); }
.top-btn { width:40px; height:40px; border-radius:12px; background:var(--white); border:1.5px solid var(--border);
  display:flex; align-items:center; justify-content:center; color:var(--gray); cursor:pointer; transition:all .18s; position:relative; box-shadow:var(--shadow-sm); }
.top-btn .mi { font-size:1.2rem; }
.top-btn:hover { background:var(--sky-light); border-color:var(--sky); color:var(--sky-dark); transform:scale(1.06); }
.top-btn .dot { position:absolute; top:-3px; right:-3px; width:10px; height:10px; border-radius:50%; background:var(--pink); border:2px solid var(--white); }

.content { padding:1.8rem 2rem 3rem; flex:1; }

.mobile-overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:45; }

@media(max-width:768px) {
  .sidebar { transform:translateX(100%); transition:transform .3s; }
  .sidebar.open { transform:translateX(0); }
  .main { margin-right:0; }
  .mobile-toggle { display:flex; }
  .content { padding:1.2rem; }
}
</style>
