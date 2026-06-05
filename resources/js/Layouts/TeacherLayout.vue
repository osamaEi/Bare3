<template>
  <div dir="rtl" class="portal-shell">
    <aside class="sidebar" :class="{ collapsed }">
      <div class="sidebar-header">
        <img src="/images/logo.png" alt="بارع" class="sidebar-logo" v-if="!collapsed" />
        <span class="sidebar-logo-sm" v-else>ب</span>
        <button class="collapse-btn" @click="collapsed = !collapsed">
          <i :class="collapsed ? 'fa-solid fa-angles-left' : 'fa-solid fa-angles-right'"></i>
        </button>
      </div>
      <nav class="sidebar-nav">
        <Link :href="route('teacher.dashboard')" class="nav-item" :class="{ active: current === 'teacher.dashboard' }">
          <i class="fa-solid fa-gauge-high"></i><span v-if="!collapsed">لوحة التحكم</span>
        </Link>
        <a href="#" class="nav-item"><i class="fa-solid fa-chalkboard-user"></i><span v-if="!collapsed">فصولي</span></a>
        <a href="#" class="nav-item"><i class="fa-solid fa-user-plus"></i><span v-if="!collapsed">إضافة طلاب</span></a>
        <a href="#" class="nav-item"><i class="fa-solid fa-chart-column"></i><span v-if="!collapsed">تقارير الفصل</span></a>
      </nav>
      <div class="sidebar-footer" v-if="!collapsed">
        <div class="pu-user">
          <div class="pu-avatar">{{ initial }}</div>
          <div><div class="pu-name">{{ user?.name }}</div><div class="pu-role">معلم</div></div>
        </div>
        <Link :href="route('logout')" method="post" as="button" class="logout-btn"><i class="fa-solid fa-arrow-right-from-bracket"></i></Link>
      </div>
    </aside>
    <div class="main-wrap">
      <header class="topbar">
        <div class="breadcrumb"><span class="bread-home"><i class="fa-solid fa-house"></i></span><i class="fa-solid fa-chevron-left bread-sep"></i><span class="bread-current">{{ pageTitle }}</span></div>
        <div class="topbar-admin"><div class="pu-avatar-sm">{{ initial }}</div><span>{{ user?.name }}</span></div>
      </header>
      <main class="page-content"><slot /></main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
defineProps({ pageTitle: { type: String, default: 'لوحة التحكم' } })
const collapsed = ref(false)
const page = usePage()
const current = computed(() => page.props.ziggy?.current ?? '')
const user = computed(() => page.props.auth?.user)
const initial = computed(() => (user.value?.name?.[0]) ?? 'م')
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap');
* { box-sizing: border-box; margin: 0; padding: 0; }
.portal-shell { display: flex; min-height: 100vh; background: #F1F5F9; font-family: 'Cairo', sans-serif; direction: rtl; }
.sidebar { width: 256px; min-height: 100vh; background: #1C1C2E; display: flex; flex-direction: column; flex-shrink: 0; transition: width .3s ease; position: sticky; top: 0; height: 100vh; overflow: hidden; }
.sidebar.collapsed { width: 68px; }
.sidebar-header { display: flex; align-items: center; justify-content: space-between; padding: 1.2rem 1rem; border-bottom: 1px solid rgba(255,255,255,.08); }
.sidebar-logo { width: 110px; filter: brightness(0) invert(1); }
.sidebar-logo-sm { width: 36px; height: 36px; border-radius: 10px; background: #16A34A; color: white; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.1rem; }
.collapse-btn { background: none; border: none; cursor: pointer; color: rgba(255,255,255,.4); font-size: .85rem; padding: .3rem; border-radius: 6px; }
.collapse-btn:hover { color: white; background: rgba(255,255,255,.08); }
.sidebar-nav { flex: 1; padding: 1rem .6rem; display: flex; flex-direction: column; gap: .25rem; }
.nav-item { display: flex; align-items: center; gap: .9rem; padding: .75rem 1rem; border-radius: 12px; color: rgba(255,255,255,.55); font-weight: 700; font-size: .92rem; transition: background .2s, color .2s; text-decoration: none; white-space: nowrap; }
.nav-item i { font-size: 1rem; width: 18px; flex-shrink: 0; }
.nav-item:hover { background: rgba(255,255,255,.07); color: white; }
.nav-item.active { background: #16A34A; color: white; }
.sidebar-footer { padding: 1rem; border-top: 1px solid rgba(255,255,255,.08); display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
.pu-user { display: flex; align-items: center; gap: .7rem; }
.pu-avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #16A34A, #38BDF8); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: .9rem; }
.pu-name { color: white; font-weight: 700; font-size: .85rem; }
.pu-role { color: rgba(255,255,255,.4); font-size: .75rem; }
.logout-btn { background: none; border: none; cursor: pointer; color: rgba(255,255,255,.4); font-size: 1rem; padding: .4rem; border-radius: 8px; }
.logout-btn:hover { color: #EC4899; background: rgba(236,72,153,.1); }
.main-wrap { flex: 1; display: flex; flex-direction: column; min-width: 0; }
.topbar { height: 64px; background: white; border-bottom: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; position: sticky; top: 0; z-index: 10; }
.breadcrumb { display: flex; align-items: center; gap: .5rem; font-size: .88rem; color: #64748B; }
.bread-home { color: #94A3B8; }
.bread-sep { font-size: .65rem; color: #CBD5E1; }
.bread-current { font-weight: 700; color: #1E293B; }
.topbar-admin { display: flex; align-items: center; gap: .6rem; font-weight: 700; font-size: .88rem; color: #1E293B; }
.pu-avatar-sm { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #16A34A, #38BDF8); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: .8rem; }
.page-content { flex: 1; padding: 1.5rem; overflow-y: auto; }
</style>
