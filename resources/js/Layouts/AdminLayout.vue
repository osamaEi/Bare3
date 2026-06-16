<template>
  <div dir="rtl" class="admin-shell">

    <!-- SIDEBAR -->
    <aside class="sidebar" :class="{ collapsed: sidebarCollapsed }">
      <div class="sidebar-header">
        <img src="/images/logo.png" alt="بارع" class="sidebar-logo" v-if="!sidebarCollapsed" />
        <span class="sidebar-logo-sm" v-else>ب</span>
        <button class="collapse-btn" @click="sidebarCollapsed = !sidebarCollapsed">
          <i :class="sidebarCollapsed ? 'fa-solid fa-angles-left' : 'fa-solid fa-angles-right'"></i>
        </button>
      </div>

      <nav class="sidebar-nav">
        <Link :href="route('admin.dashboard')" class="nav-item" :class="{ active: currentRoute === 'admin.dashboard' }">
          <i class="fa-solid fa-gauge-high"></i>
          <span v-if="!sidebarCollapsed">لوحة التحكم</span>
        </Link>
        <Link :href="route('admin.users')" class="nav-item" :class="{ active: currentRoute === 'admin.users' }">
          <i class="fa-solid fa-users"></i>
          <span v-if="!sidebarCollapsed">المستخدمون</span>
        </Link>
        <Link :href="route('admin.students.progress')" class="nav-item" :class="{ active: currentRoute === 'admin.students.progress' || currentRoute === 'admin.students.progress.show' }">
          <i class="fa-solid fa-chart-line"></i>
          <span v-if="!sidebarCollapsed">تقدم الطلبة</span>
        </Link>
        <Link :href="route('admin.paths')" class="nav-item" :class="{ active: currentRoute === 'admin.paths' }">
          <i class="fa-solid fa-map"></i>
          <span v-if="!sidebarCollapsed">المسارات</span>
        </Link>
        <Link :href="route('admin.content')" class="nav-item" :class="{ active: currentRoute === 'admin.content' }">
          <i class="fa-solid fa-photo-film"></i>
          <span v-if="!sidebarCollapsed">المحتوى</span>
        </Link>
        <Link :href="route('admin.payments')" class="nav-item" :class="{ active: currentRoute === 'admin.payments' }">
          <i class="fa-solid fa-credit-card"></i>
          <span v-if="!sidebarCollapsed">المدفوعات</span>
        </Link>
        <Link :href="route('admin.blog')" class="nav-item" :class="{ active: currentRoute === 'admin.blog' }">
          <i class="fa-solid fa-newspaper"></i>
          <span v-if="!sidebarCollapsed">المدونة</span>
        </Link>
        <Link :href="route('admin.tickets')" class="nav-item" :class="{ active: currentRoute === 'admin.tickets' }">
          <i class="fa-solid fa-ticket"></i>
          <span v-if="!sidebarCollapsed">تذاكر الدعم</span>
        </Link>
        <Link :href="route('admin.homepage')" class="nav-item" :class="{ active: currentRoute === 'admin.homepage' }">
          <i class="fa-solid fa-house"></i>
          <span v-if="!sidebarCollapsed">الصفحة الرئيسية</span>
        </Link>
        <Link :href="route('admin.settings')" class="nav-item" :class="{ active: currentRoute === 'admin.settings' }">
          <i class="fa-solid fa-gear"></i>
          <span v-if="!sidebarCollapsed">الإعدادات</span>
        </Link>
      </nav>

      <div class="sidebar-footer" v-if="!sidebarCollapsed">
        <div class="admin-user">
          <div class="admin-avatar">م</div>
          <div>
            <div class="admin-name">المسؤول</div>
            <div class="admin-role">Super Admin</div>
          </div>
        </div>
        <Link :href="route('logout')" method="post" as="button" class="logout-btn">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </Link>
      </div>
    </aside>

    <!-- MAIN -->
    <div class="main-wrap">
      <!-- TOP BAR -->
      <header class="topbar">
        <div class="topbar-right">
          <button class="mobile-toggle" @click="mobileSidebar = !mobileSidebar">
            <i class="fa-solid fa-bars"></i>
          </button>
          <div class="breadcrumb">
            <span class="bread-home"><i class="fa-solid fa-house"></i></span>
            <i class="fa-solid fa-chevron-left bread-sep"></i>
            <span class="bread-current">{{ pageTitle }}</span>
          </div>
        </div>
        <div class="topbar-left">

          <!-- Notifications -->
          <div class="dropdown-wrap" ref="notifRef">
            <button class="topbar-icon-btn" title="الإشعارات" @click="notifOpen = !notifOpen">
              <i class="fa-solid fa-bell"></i>
              <span class="notif-badge">٣</span>
            </button>
            <div class="dropdown-panel notif-panel" v-if="notifOpen">
              <div class="dropdown-header">الإشعارات</div>
              <div class="notif-item">
                <i class="fa-solid fa-user-plus notif-icon sky"></i>
                <div><div class="notif-title">مستخدم جديد</div><div class="notif-time">منذ ٥ دقائق</div></div>
              </div>
              <div class="notif-item">
                <i class="fa-solid fa-credit-card notif-icon pink"></i>
                <div><div class="notif-title">دفعة جديدة</div><div class="notif-time">منذ ٢٠ دقيقة</div></div>
              </div>
              <div class="notif-item">
                <i class="fa-solid fa-star notif-icon lime"></i>
                <div><div class="notif-title">تقييم جديد</div><div class="notif-time">منذ ساعة</div></div>
              </div>
              <div class="dropdown-footer">عرض كل الإشعارات</div>
            </div>
          </div>

          <a href="/" class="topbar-icon-btn" title="عرض الموقع" target="_blank">
            <i class="fa-solid fa-eye"></i>
          </a>

          <!-- Profile -->
          <div class="dropdown-wrap" ref="profileRef">
            <div class="topbar-admin" @click="profileOpen = !profileOpen" style="cursor:pointer">
              <div class="admin-avatar-sm">م</div>
              <span>المسؤول</span>
              <i class="fa-solid fa-chevron-down" style="font-size:.7rem;color:#94A3B8"></i>
            </div>
            <div class="dropdown-panel profile-panel" v-if="profileOpen">
              <div class="dropdown-header">المسؤول</div>
              <a href="/profile" class="profile-item"><i class="fa-solid fa-user"></i> الملف الشخصي</a>
              <a href="/admin" class="profile-item"><i class="fa-solid fa-gauge-high"></i> لوحة التحكم</a>
              <div class="dropdown-divider"></div>
              <Link :href="route('logout')" method="post" as="button" class="profile-item logout-item">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> تسجيل الخروج
              </Link>
            </div>
          </div>

        </div>
      </header>

      <!-- PAGE CONTENT -->
      <main class="page-content">
        <slot />
      </main>
    </div>

    <!-- MOBILE OVERLAY -->
    <div v-if="mobileSidebar" class="mobile-overlay" @click="mobileSidebar = false"></div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const props = defineProps({ pageTitle: { type: String, default: 'لوحة التحكم' } })

const sidebarCollapsed = ref(false)
const mobileSidebar = ref(false)
const page = usePage()
const currentRoute = computed(() => page.props.ziggy?.current ?? '')

const notifOpen = ref(false)
const profileOpen = ref(false)
const notifRef = ref(null)
const profileRef = ref(null)

function handleClickOutside(e) {
  if (notifRef.value && !notifRef.value.contains(e.target)) notifOpen.value = false
  if (profileRef.value && !profileRef.value.contains(e.target)) profileOpen.value = false
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }

.admin-shell {
  display: flex;
  min-height: 100vh;
  background: #F1F5F9;
  font-family: 'Cairo', sans-serif;
  direction: rtl;
}

/* ── SIDEBAR ── */
.sidebar {
  width: 256px;
  min-height: 100vh;
  background: #1C1C2E;
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  transition: width .3s ease;
  position: sticky;
  top: 0;
  height: 100vh;
  overflow: hidden;
}
.sidebar.collapsed { width: 68px; }

.sidebar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.2rem 1rem;
  border-bottom: 1px solid rgba(255,255,255,.08);
}
.sidebar-logo { width: 110px; filter: brightness(0) invert(1); }
.sidebar-logo-sm {
  width: 36px; height: 36px; border-radius: 10px;
  background: #38BDF8; color: white;
  display: flex; align-items: center; justify-content: center;
  font-weight: 900; font-size: 1.1rem;
}
.collapse-btn {
  background: none; border: none; cursor: pointer;
  color: rgba(255,255,255,.4); font-size: .85rem;
  padding: .3rem; border-radius: 6px;
  transition: color .2s, background .2s;
}
.collapse-btn:hover { color: white; background: rgba(255,255,255,.08); }

.sidebar-nav {
  flex: 1;
  padding: 1rem .6rem;
  display: flex;
  flex-direction: column;
  gap: .25rem;
  overflow-y: auto;
}
.nav-item {
  display: flex;
  align-items: center;
  gap: .9rem;
  padding: .75rem 1rem;
  border-radius: 12px;
  color: rgba(255,255,255,.55);
  font-weight: 700;
  font-size: .92rem;
  transition: background .2s, color .2s;
  text-decoration: none;
  white-space: nowrap;
}
.nav-item i { font-size: 1rem; width: 18px; flex-shrink: 0; }
.nav-item:hover { background: rgba(255,255,255,.07); color: white; }
.nav-item.active { background: #38BDF8; color: white; }

.sidebar-footer {
  padding: 1rem;
  border-top: 1px solid rgba(255,255,255,.08);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: .5rem;
}
.admin-user { display: flex; align-items: center; gap: .7rem; }
.admin-avatar {
  width: 36px; height: 36px; border-radius: 50%;
  background: linear-gradient(135deg, #38BDF8, #EC4899);
  display: flex; align-items: center; justify-content: center;
  color: white; font-weight: 800; font-size: .9rem;
}
.admin-name { color: white; font-weight: 700; font-size: .85rem; }
.admin-role { color: rgba(255,255,255,.4); font-size: .75rem; }
.logout-btn {
  background: none; border: none; cursor: pointer;
  color: rgba(255,255,255,.4); font-size: 1rem;
  padding: .4rem; border-radius: 8px; transition: color .2s, background .2s;
}
.logout-btn:hover { color: #EC4899; background: rgba(236,72,153,.1); }

/* ── MAIN ── */
.main-wrap {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}

/* ── TOPBAR ── */
.topbar {
  height: 64px;
  background: white;
  border-bottom: 1px solid #E2E8F0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.5rem;
  position: sticky;
  top: 0;
  z-index: 10;
}
.topbar-right, .topbar-left { display: flex; align-items: center; gap: 1rem; }
.mobile-toggle { display: none; background: none; border: none; cursor: pointer; font-size: 1.2rem; color: #64748B; }
.breadcrumb { display: flex; align-items: center; gap: .5rem; font-size: .88rem; color: #64748B; }
.bread-home { color: #94A3B8; }
.bread-sep { font-size: .65rem; color: #CBD5E1; }
.bread-current { font-weight: 700; color: #1E293B; }

.topbar-icon-btn {
  position: relative; background: none; border: none; cursor: pointer;
  width: 38px; height: 38px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  color: #64748B; font-size: 1rem; transition: background .2s, color .2s;
  text-decoration: none;
}
.topbar-icon-btn:hover { background: #F1F5F9; color: #1E293B; }
.notif-badge {
  position: absolute; top: 4px; left: 4px;
  background: #EC4899; color: white; border-radius: 50%;
  width: 16px; height: 16px; font-size: .6rem;
  display: flex; align-items: center; justify-content: center;
  font-weight: 800;
}
.topbar-admin { display: flex; align-items: center; gap: .6rem; font-weight: 700; font-size: .88rem; color: #1E293B; }
.admin-avatar-sm {
  width: 32px; height: 32px; border-radius: 50%;
  background: linear-gradient(135deg, #38BDF8, #EC4899);
  display: flex; align-items: center; justify-content: center;
  color: white; font-weight: 800; font-size: .8rem;
}

/* ── DROPDOWNS ── */
.dropdown-wrap { position: relative; }
.dropdown-panel {
  position: absolute; top: calc(100% + 10px); left: 0;
  background: white; border-radius: 14px;
  box-shadow: 0 8px 32px rgba(0,0,0,.12);
  border: 1px solid #E2E8F0;
  z-index: 100; min-width: 220px;
  overflow: hidden;
}
.notif-panel { min-width: 280px; }
.profile-panel { min-width: 200px; }
.dropdown-header {
  padding: .8rem 1rem; font-weight: 800; font-size: .85rem;
  color: #64748B; border-bottom: 1px solid #F1F5F9;
  background: #F8FAFC;
}
.dropdown-footer {
  padding: .7rem 1rem; font-size: .82rem; font-weight: 700;
  color: #38BDF8; text-align: center; cursor: pointer;
  border-top: 1px solid #F1F5F9;
}
.dropdown-footer:hover { background: #F1F5F9; }
.dropdown-divider { border-top: 1px solid #F1F5F9; margin: .3rem 0; }

.notif-item {
  display: flex; align-items: center; gap: .8rem;
  padding: .75rem 1rem; transition: background .2s; cursor: pointer;
}
.notif-item:hover { background: #F8FAFC; }
.notif-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: .85rem; flex-shrink: 0; }
.notif-icon.sky { background: #E0F4FF; color: #38BDF8; }
.notif-icon.pink { background: #FCE7F3; color: #EC4899; }
.notif-icon.lime { background: #F0FDF4; color: #84CC16; }
.notif-title { font-weight: 700; font-size: .85rem; color: #1E293B; }
.notif-time { font-size: .75rem; color: #94A3B8; margin-top: .1rem; }

.profile-item {
  display: flex; align-items: center; gap: .7rem;
  padding: .7rem 1rem; font-size: .88rem; font-weight: 600;
  color: #1E293B; text-decoration: none; transition: background .2s;
  cursor: pointer; background: none; border: none; width: 100%; text-align: right;
  font-family: inherit;
}
.profile-item:hover { background: #F8FAFC; }
.profile-item i { color: #64748B; width: 16px; }
.logout-item { color: #EC4899; }
.logout-item i { color: #EC4899; }

/* ── PAGE CONTENT ── */
.page-content { flex: 1; padding: 1.5rem; overflow-y: auto; }

/* ── MOBILE OVERLAY ── */
.mobile-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 40;
}

@media (max-width: 768px) {
  .sidebar { position: fixed; right: 0; top: 0; z-index: 50; transform: translateX(100%); transition: transform .3s; }
  .mobile-toggle { display: flex; }
}
</style>
