<template>
  <div class="student-shell" dir="rtl">

    <!-- ═══ SIDEBAR ═══ -->
    <aside class="sidebar" :class="{ open: mobileOpen }">
      <div class="sb-logo">
        <img src="/images/logo-horizontal.png" alt="بارع" />
      </div>

      <div class="sb-profile">
        <div class="sb-profile-dots"></div>
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
        <Link :href="route('payment.checkout')" class="nav-item" :class="{ active: isActive('payment.checkout') }">
          <div class="nav-icon" style="background:var(--lime-light);color:var(--lime-dark)"><span class="mi">credit_card</span></div>
          الاشتراك والدفع
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
          <div ref="notifWrap" class="notif-wrap">
            <button class="top-btn" type="button" aria-haspopup="true"
                    :aria-expanded="notifOpen ? 'true' : 'false'"
                    @click.stop="notifOpen = !notifOpen">
              <span class="mi">notifications</span>
              <span v-if="unreadCount > 0" class="dot"></span>
            </button>

            <transition name="drop">
              <div v-if="notifOpen" class="notif-menu">
                <div class="notif-head">
                  <span>الإشعارات</span>
                  <button v-if="unreadCount > 0" class="notif-mark" @click="markAllRead">
                    تعليم الكل كمقروء
                  </button>
                </div>

                <div v-if="notifItems.length === 0" class="notif-empty">
                  <span class="mi">notifications_off</span>
                  لا توجد إشعارات
                </div>

                <div v-else class="notif-list">
                  <button v-for="n in notifItems" :key="n.id" type="button"
                          class="notif-item" :class="{ unread: !n.read }"
                          @click="openNotification(n)">
                    <span class="notif-ico" :class="`t-${n.type || 'info'}`">
                      <span class="mi">{{ typeIcon(n.type) }}</span>
                    </span>
                    <span class="notif-body">
                      <span class="notif-title">{{ n.title }}</span>
                      <span class="notif-text">{{ n.body }}</span>
                      <span class="notif-time">{{ n.created_at }}</span>
                    </span>
                  </button>
                </div>

                <Link :href="route('student.notifications')" class="notif-all" @click="notifOpen = false">
                  عرض كل الإشعارات
                </Link>
              </div>
            </transition>
          </div>
        </div>
      </div>

      <div class="content">
        <!-- أشكال 3D تجريدية — نفس هوية الصفحة الرئيسية -->
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="content-inner">
          <slot />
        </div>
      </div>
    </div>

    <div v-if="mobileOpen" class="mobile-overlay" @click="mobileOpen = false"></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

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

// ── قائمة الإشعارات المنسدلة ──
const notifOpen = ref(false)
const notifWrap = ref(null)
const notifItems = computed(() => page.props.notifications?.items ?? [])
const unreadCount = computed(() => page.props.notifications?.unread ?? 0)

const typeIcon = (type) => ({
  success: 'check_circle',
  warning: 'warning',
  error:   'error',
  info:    'info',
}[type] ?? 'info')

// فتح الإشعار: نعلّمه كمقروء ثم ننتقل لصفحة الإشعارات
const openNotification = (n) => {
  notifOpen.value = false
  if (!n.read) {
    router.patch(route('student.notifications.read', n.id), {}, {
      preserveScroll: true,
      preserveState: true,
      onFinish: () => router.visit(route('student.notifications')),
    })
    return
  }
  router.visit(route('student.notifications'))
}

const markAllRead = () => router.patch(route('student.notifications.readAll'), {}, {
  preserveScroll: true,
  preserveState: true,
})

const closeNotif = (e) => {
  if (notifWrap.value && !notifWrap.value.contains(e.target)) notifOpen.value = false
}
onMounted(() => document.addEventListener('click', closeNotif))
onBeforeUnmount(() => document.removeEventListener('click', closeNotif))
</script>

<style scoped>
/* Tajawal مُحمّل عالمياً في app.blade.php — نستورد أيقونات Material فقط */
@import url('https://fonts.googleapis.com/icon?family=Material+Icons+Round');

.student-shell {
  /* ── هوية بارع — نفس متغيّرات الصفحة الرئيسية ── */
  --brand:#7c5cbf; --brand-dark:#5f4398; --brand-soft:#f3effb; --ink:#1e1b2e;
  --coral:#f0806a; --teal:#4bb5a8; --amber:#f2b866; --navy:#3d6ea5;

  /* أسماء متوافقة مع الأنماط القديمة داخل الصفحات */
  --sky:var(--brand); --sky-light:var(--brand-soft); --sky-mid:#c9b4f0; --sky-dark:var(--brand-dark);
  --pink:var(--coral); --pink-light:#fdeeea; --pink-mid:#f6b5a6; --pink-dark:#c2513c;
  --lime:var(--teal); --lime-light:#eaf7f5; --lime-mid:#9fdcd3; --lime-dark:#2f8a7e;
  --amber-light:#fdf3e3; --violet:var(--brand); --violet-light:var(--brand-soft);
  --dark:var(--ink); --gray:#6B7280; --light:#f5f4f9; --white:#fff; --border:#f0eef7;
  --radius-xl:28px; --radius-md:16px;
  --shadow-sm:0 6px 24px rgba(30,27,46,.05); --shadow-md:0 12px 40px rgba(30,27,46,.07);
  font-family:'Tajawal','Poppins',sans-serif; color:var(--ink); background:#FAFAFB; min-height:100vh;
}
.student-shell * { box-sizing:border-box; margin:0; padding:0; }

.mi { font-family:'Material Icons Round'; font-style:normal; font-weight:normal; line-height:1;
  display:inline-flex; align-items:center; justify-content:center; vertical-align:middle; user-select:none; }

/* SIDEBAR */
.sidebar { width:270px; height:100vh; background:var(--white); border-left:1px solid var(--border);
  display:flex; flex-direction:column; position:fixed; right:0; top:0; z-index:50; box-shadow:6px 0 32px rgba(30,27,46,.05);
  overflow:hidden; }
.sb-logo, .sb-profile { flex-shrink:0; }
.sb-logo { padding:.9rem 1.6rem; display:flex; align-items:center; justify-content:center; border-bottom:1px solid var(--border); }
.sb-logo img { width:125px; max-width:100%; }

.sb-profile { margin:1.1rem 1.2rem; border-radius:22px; padding:1rem 1.1rem; text-align:center; position:relative; overflow:hidden;
  background:linear-gradient(145deg,var(--brand-dark) 0%,var(--brand) 100%); color:#fff; box-shadow:0 10px 28px rgba(124,92,191,.28); }
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
.sb-name { font-weight:800; font-size:1.05rem; position:relative; z-index:1; }
.sb-rank { display:inline-flex; align-items:center; gap:.3rem; background:rgba(255,255,255,.18); border-radius:50px;
  padding:.25rem .85rem; font-size:.76rem; font-weight:700; margin-top:.4rem; }
.sb-rank .mi { font-size:.9rem; color:var(--lime-mid); }
.sb-xp { margin-top:.8rem; }
.sb-xp-row { display:flex; justify-content:space-between; font-size:.72rem; font-weight:700; opacity:.82; margin-bottom:.35rem; }
.sb-xp-track { height:8px; background:rgba(255,255,255,.22); border-radius:99px; overflow:hidden; }
.sb-xp-fill { height:100%; background:linear-gradient(90deg,var(--lime-mid),#fff); border-radius:99px; box-shadow:0 0 8px rgba(190,242,100,.6); transition:width 1.2s cubic-bezier(.22,1,.36,1); }

.sb-nav { padding:.8rem .9rem 1.2rem; flex:1; overflow-y:auto; min-height:0;
  scrollbar-width:thin; scrollbar-color:#d9d4ea transparent; }
.sb-nav::-webkit-scrollbar { width:6px; }
.sb-nav::-webkit-scrollbar-thumb { background:#d9d4ea; border-radius:99px; }
.sb-nav::-webkit-scrollbar-track { background:transparent; }
.sb-nav-label { font-size:.68rem; font-weight:800; color:var(--gray); letter-spacing:.08em; text-transform:uppercase; padding:.6rem .5rem .3rem; margin-top:.4rem; }
.nav-item { display:flex; align-items:center; gap:.75rem; padding:.7rem .9rem; border-radius:var(--radius-md);
  font-weight:700; font-size:.94rem; color:var(--gray); cursor:pointer; transition:all .22s; margin-bottom:.15rem; text-decoration:none; }
.nav-item:hover { background:var(--brand-soft); color:var(--brand-dark); }
.nav-item:hover .nav-icon { background:var(--brand); color:#fff; }
.nav-item.active { background:var(--brand-soft); color:var(--brand-dark); font-weight:800; }
.nav-item.active .nav-icon { background:linear-gradient(135deg,var(--brand),var(--brand-dark)); color:#fff; box-shadow:0 6px 16px rgba(124,92,191,.35); }
.nav-icon { width:36px; height:36px; border-radius:11px; display:flex; align-items:center; justify-content:center;
  font-size:.9rem; flex-shrink:0; background:#F1F5F9; color:var(--gray); transition:all .22s; }
.nav-icon .mi { font-size:1.15rem; }
.logout-btn { width:100%; border:none; background:none; font-family:inherit; text-align:right; }

/* MAIN */
.main { margin-right:270px; min-height:100vh; display:flex; flex-direction:column; }
.topbar { background:rgba(255,255,255,.92); border-bottom:1px solid #f0f0f5;
  padding:0 2rem; height:72px; display:flex; align-items:center; justify-content:space-between;
  position:sticky; top:0; z-index:60; overflow:visible; }
.topbar-left { display:flex; align-items:center; gap:.8rem; }
.topbar-greeting { font-size:1rem; font-weight:700; color:var(--gray); }
.topbar-greeting strong { color:var(--brand); font-weight:800; }
.topbar-right { display:flex; align-items:center; gap:.7rem; }
.mobile-toggle { display:none; background:none; border:none; cursor:pointer; color:var(--gray); }
.mobile-toggle .mi { font-size:1.5rem; }
.streak-chip { display:flex; align-items:center; gap:.4rem; background:#fff; border:1px solid var(--brand-soft);
  border-radius:9999px; padding:.45rem 1.1rem; font-weight:700; font-size:.85rem; color:var(--brand);
  box-shadow:0 4px 20px rgba(124,92,191,.12); }
.streak-chip .mi { font-size:1rem; color:var(--amber); }
.top-btn { width:40px; height:40px; border-radius:12px; background:var(--white); border:1px solid var(--border);
  display:flex; align-items:center; justify-content:center; color:var(--gray); cursor:pointer; transition:all .2s ease;
  position:relative; box-shadow:var(--shadow-sm); font-family:inherit; padding:0; }
.top-btn .mi { font-size:1.2rem; }
.top-btn:hover { background:var(--brand-soft); border-color:var(--brand); color:var(--brand-dark); transform:translateY(-2px); }
.top-btn .dot { position:absolute; top:-3px; right:-3px; width:10px; height:10px; border-radius:50%; background:var(--coral); border:2px solid var(--white); }

/* ── قائمة الإشعارات المنسدلة ── */
.notif-wrap { position:relative; }
.notif-menu {
  position:absolute; top:calc(100% + .6rem); inset-inline-end:0;
  width:340px; max-width:calc(100vw - 2rem); background:#fff;
  border:1px solid var(--border); border-radius:18px;
  box-shadow:0 18px 45px rgba(30,27,46,.18); z-index:60; overflow:hidden;
}
.notif-head {
  display:flex; align-items:center; justify-content:space-between; gap:.5rem;
  padding:.85rem 1rem; border-bottom:1px solid var(--border);
  font-weight:800; font-size:.92rem; color:var(--ink);
}
.notif-mark { background:none; border:none; font-family:inherit; cursor:pointer;
  color:var(--brand); font-weight:700; font-size:.75rem; }
.notif-mark:hover { text-decoration:underline; }

.notif-list { max-height:340px; overflow-y:auto; scrollbar-width:thin; scrollbar-color:#d9d4ea transparent; }
.notif-list::-webkit-scrollbar { width:6px; }
.notif-list::-webkit-scrollbar-thumb { background:#d9d4ea; border-radius:99px; }

.notif-item {
  display:flex; align-items:flex-start; gap:.7rem; width:100%; text-align:right;
  padding:.8rem 1rem; background:none; border:none; border-bottom:1px solid #f7f6fa;
  font-family:inherit; cursor:pointer; transition:background .18s ease;
}
.notif-item:hover { background:var(--brand-soft); }
.notif-item.unread { background:#faf8fe; }
.notif-item.unread .notif-title { color:var(--brand-dark); }

.notif-ico { width:34px; height:34px; border-radius:10px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center; background:var(--brand-soft); color:var(--brand); }
.notif-ico .mi { font-size:1.05rem; }
.notif-ico.t-success { background:#eaf7f5; color:var(--teal); }
.notif-ico.t-warning { background:#fdf3e3; color:#a06a1f; }
.notif-ico.t-error   { background:#fdeeea; color:var(--coral); }

.notif-body { display:flex; flex-direction:column; gap:.15rem; min-width:0; }
.notif-title { font-weight:800; font-size:.86rem; color:var(--ink); }
.notif-text {
  font-size:.78rem; color:var(--gray); line-height:1.5;
  display:-webkit-box; -webkit-line-clamp:2; line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
}
.notif-time { font-size:.7rem; color:#94A3B8; font-weight:700; }

.notif-empty { padding:2rem 1rem; text-align:center; color:var(--gray); font-weight:700; font-size:.85rem; }
.notif-empty .mi { display:block; font-size:2rem; color:#CBD5E1; margin-bottom:.5rem; }

.notif-all { display:block; padding:.8rem; text-align:center; font-weight:800; font-size:.82rem;
  color:var(--brand); text-decoration:none; border-top:1px solid var(--border); }
.notif-all:hover { background:var(--brand-soft); }

.drop-enter-active, .drop-leave-active { transition:opacity .16s ease, transform .16s ease; }
.drop-enter-from, .drop-leave-to { opacity:0; transform:translateY(-.4rem); }

.content { padding:1.8rem 2rem 3rem; flex:1; position:relative; overflow:hidden; }
.content-inner { position:relative; z-index:1; }

/* أشكال 3D تجريدية (blobs) — خلفية ناعمة بهوية بارع */
.content .blob { position:absolute; border-radius:50%; filter:blur(60px); pointer-events:none; z-index:0; }
.content .blob-1 { width:420px; height:420px; background:radial-gradient(circle,#c9b4f0,#7c5cbf); top:-160px; left:-120px; opacity:.22; }
.content .blob-2 { width:320px; height:320px; background:radial-gradient(circle,#ffd9b0,#f0806a); bottom:-140px; right:-100px; opacity:.16; }

.mobile-overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:45; }

@media(max-width:768px) {
  .sidebar { transform:translateX(100%); transition:transform .3s; }
  .sidebar.open { transform:translateX(0); }
  .main { margin-right:0; }
  .mobile-toggle { display:flex; }
  .content { padding:1.2rem; }
}
</style>
