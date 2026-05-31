<template>
  <AdminLayout page-title="لوحة التحكم">

    <!-- PAGE HEADER -->
    <div class="page-header">
      <div>
        <h1 class="page-title">لوحة التحكم</h1>
        <p class="page-sub">مرحباً بك في منصة بارع الإدارية — {{ today }}</p>
      </div>
      <div class="header-actions">
        <button class="btn-export"><i class="fa-solid fa-download"></i> تصدير التقرير</button>
      </div>
    </div>

    <!-- STATS CARDS -->
    <div class="stats-grid">
      <div v-for="stat in statCards" :key="stat.key" class="stat-card">
        <div class="stat-card-top">
          <div class="stat-icon" :style="{ background: stat.bg, color: stat.color }">
            <i :class="stat.icon"></i>
          </div>
          <div class="stat-change" :class="stat.up ? 'up' : 'down'">
            <i :class="stat.up ? 'fa-solid fa-arrow-trend-up' : 'fa-solid fa-arrow-trend-down'"></i>
            {{ stat.change }}
          </div>
        </div>
        <div class="stat-value">{{ stat.value }}</div>
        <div class="stat-label">{{ stat.label }}</div>
      </div>
    </div>

    <!-- CHARTS ROW -->
    <div class="charts-row">

      <!-- Revenue Chart -->
      <div class="card chart-card">
        <div class="card-header">
          <h3 class="card-title"><i class="fa-solid fa-chart-line"></i> إيرادات الأشهر الأخيرة</h3>
          <div class="card-badge sky">ر.س</div>
        </div>
        <div class="chart-bars">
          <div v-for="item in revenueChart" :key="item.month" class="bar-col">
            <div class="bar-tooltip">{{ item.amount.toLocaleString() }} ر.س</div>
            <div class="bar" :style="{ height: barHeight(item.amount) + '%' }"></div>
            <span class="bar-label">{{ item.month }}</span>
          </div>
        </div>
      </div>

      <!-- Paths Distribution -->
      <div class="card donut-card">
        <div class="card-header">
          <h3 class="card-title"><i class="fa-solid fa-chart-pie"></i> توزيع المسارات</h3>
        </div>
        <div class="donut-list">
          <div v-for="path in pathsDistribution" :key="path.name" class="donut-item">
            <div class="donut-dot" :style="{ background: path.color }"></div>
            <span class="donut-label">{{ path.name }}</span>
            <div class="donut-bar-wrap">
              <div class="donut-bar" :style="{ width: path.pct + '%', background: path.color }"></div>
            </div>
            <span class="donut-pct">{{ path.pct }}%</span>
          </div>
        </div>
      </div>

    </div>

    <!-- BOTTOM ROW -->
    <div class="bottom-row">

      <!-- Recent Activity -->
      <div class="card activity-card">
        <div class="card-header">
          <h3 class="card-title"><i class="fa-solid fa-bolt"></i> آخر النشاطات</h3>
          <button class="card-link">عرض الكل</button>
        </div>
        <div class="activity-list">
          <div v-for="item in recentActivity" :key="item.user" class="activity-item">
            <div class="activity-icon" :class="item.type">
              <i :class="activityIcon(item.type)"></i>
            </div>
            <div class="activity-body">
              <span class="activity-user">{{ item.user }}</span>
              <span class="activity-action">{{ item.action }}</span>
            </div>
            <span class="activity-time">{{ item.time }}</span>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card quick-card">
        <div class="card-header">
          <h3 class="card-title"><i class="fa-solid fa-zap"></i> إجراءات سريعة</h3>
        </div>
        <div class="quick-grid">
          <Link :href="route('admin.content')" class="quick-btn sky">
            <i class="fa-solid fa-circle-plus"></i>
            <span>إضافة درس</span>
          </Link>
          <Link :href="route('admin.users')" class="quick-btn pink">
            <i class="fa-solid fa-user-plus"></i>
            <span>إضافة مستخدم</span>
          </Link>
          <Link :href="route('admin.blog')" class="quick-btn lime">
            <i class="fa-solid fa-pen-nib"></i>
            <span>مقالة جديدة</span>
          </Link>
          <Link :href="route('admin.payments')" class="quick-btn amber">
            <i class="fa-solid fa-receipt"></i>
            <span>تقرير المدفوعات</span>
          </Link>
          <Link :href="route('admin.paths')" class="quick-btn purple">
            <i class="fa-solid fa-map-plus"></i>
            <span>مسار جديد</span>
          </Link>
          <button class="quick-btn dark" @click="exportReport">
            <i class="fa-solid fa-file-export"></i>
            <span>تصدير البيانات</span>
          </button>
        </div>

        <!-- Summary Pills -->
        <div class="summary-pills">
          <div class="summary-pill">
            <i class="fa-solid fa-certificate" style="color:#F59E0B"></i>
            <span>{{ stats.completions }} شهادة صدرت هذا الشهر</span>
          </div>
          <div class="summary-pill">
            <i class="fa-solid fa-coins" style="color:#84CC16"></i>
            <span>{{ stats.revenue_month.toLocaleString() }} ر.س إيرادات مايو</span>
          </div>
        </div>
      </div>

    </div>

  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  stats: Object,
  recent_activity: Array,
  revenue_chart: Array,
})

const today = new Date().toLocaleDateString('ar-SA', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })

const statCards = computed(() => [
  { key: 'students', label: 'إجمالي الطلاب',  value: props.stats.total_students.toLocaleString(), icon: 'fa-solid fa-graduation-cap', bg: '#EFF6FF', color: '#2563EB', change: '+٨٪ هذا الشهر', up: true },
  { key: 'parents',  label: 'أولياء الأمور',   value: props.stats.total_parents.toLocaleString(),  icon: 'fa-solid fa-people-roof',    bg: '#FDF4FF', color: '#9333EA', change: '+٥٪ هذا الشهر', up: true },
  { key: 'teachers', label: 'المعلمون',         value: props.stats.total_teachers.toLocaleString(), icon: 'fa-solid fa-chalkboard-user', bg: '#F0FDF4', color: '#16A34A', change: '+٢ جدد',       up: true },
  { key: 'revenue',  label: 'إيرادات الشهر',   value: props.stats.revenue_month.toLocaleString() + ' ر.س', icon: 'fa-solid fa-sack-dollar', bg: '#FFF7ED', color: '#EA580C', change: '+٣٢٪',  up: true },
  { key: 'paths',    label: 'المسارات النشطة', value: props.stats.active_paths,                    icon: 'fa-solid fa-map',            bg: '#F0F9FF', color: '#0E7490', change: 'لا تغيير',   up: true },
  { key: 'certs',    label: 'شهادات صدرت',     value: props.stats.completions.toLocaleString(),    icon: 'fa-solid fa-certificate',    bg: '#FFFBEB', color: '#D97706', change: '+١٢٪',        up: true },
])

const revenueChart = computed(() => props.revenue_chart ?? [])
const recentActivity = computed(() => props.recent_activity ?? [])

const maxRevenue = computed(() => Math.max(...revenueChart.value.map(r => r.amount)))
const barHeight = (amount) => Math.round((amount / maxRevenue.value) * 85) + 10

const pathsDistribution = [
  { name: 'الإبداع والابتكار',   pct: 28, color: '#38BDF8' },
  { name: 'التواصل والإلقاء',    pct: 22, color: '#EC4899' },
  { name: 'الوعي المالي',         pct: 20, color: '#84CC16' },
  { name: 'الذكاء العاطفي',       pct: 18, color: '#F59E0B' },
  { name: 'المواطنة الرقمية',     pct: 12, color: '#8B5CF6' },
]

const activityIcon = (type) => ({
  badge:    'fa-solid fa-medal',
  register: 'fa-solid fa-user-plus',
  payment:  'fa-solid fa-credit-card',
  complete: 'fa-solid fa-circle-check',
  cert:     'fa-solid fa-certificate',
}[type] ?? 'fa-solid fa-circle')

const exportReport = () => alert('جاري تصدير التقرير...')
</script>

<style scoped>
/* PAGE HEADER */
.page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
.page-title { font-size: 1.6rem; font-weight: 900; color: #1E293B; }
.page-sub { font-size: .88rem; color: #94A3B8; margin-top: .2rem; }
.btn-export { background: white; border: 1.5px solid #E2E8F0; color: #475569; font-family: inherit; font-weight: 700; font-size: .88rem; padding: .6rem 1.2rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: .5rem; transition: border-color .2s; }
.btn-export:hover { border-color: #38BDF8; color: #0E7490; }

/* STATS GRID */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}
.stat-card {
  background: white;
  border-radius: 16px;
  padding: 1.1rem 1.2rem;
  display: flex;
  flex-direction: column;
  gap: .6rem;
  box-shadow: 0 1px 8px rgba(0,0,0,.05);
  transition: transform .2s, box-shadow .2s;
  overflow: hidden;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,.08); }
.stat-card-top { display: flex; align-items: center; justify-content: space-between; }
.stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
.stat-change { font-size: .72rem; font-weight: 700; display: flex; align-items: center; gap: .2rem; }
.stat-change.up { color: #16A34A; }
.stat-change.down { color: #DC2626; }
.stat-value { font-size: 1.5rem; font-weight: 900; color: #1E293B; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.stat-label { font-size: .78rem; color: #94A3B8; font-weight: 600; }

/* CHARTS ROW */
.charts-row { display: grid; grid-template-columns: 1fr 360px; gap: 1rem; margin-bottom: 1.5rem; }
.card { background: white; border-radius: 16px; padding: 1.4rem; box-shadow: 0 1px 8px rgba(0,0,0,.05); }
.card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.2rem; }
.card-title { font-size: 1rem; font-weight: 800; color: #1E293B; display: flex; align-items: center; gap: .5rem; }
.card-title i { color: #38BDF8; }
.card-badge { font-size: .75rem; font-weight: 800; padding: .25rem .8rem; border-radius: 50px; }
.card-badge.sky { background: #E0F4FF; color: #0E7490; }
.card-link { background: none; border: none; cursor: pointer; font-family: inherit; font-weight: 700; font-size: .82rem; color: #38BDF8; }

/* CHART BARS */
.chart-card { }
.chart-bars { display: flex; align-items: flex-end; gap: .6rem; height: 160px; padding: 0 .5rem; }
.bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: .4rem; position: relative; height: 100%; justify-content: flex-end; }
.bar-col:hover .bar-tooltip { opacity: 1; }
.bar-tooltip { position: absolute; top: -30px; background: #1E293B; color: white; font-size: .7rem; font-weight: 700; padding: .2rem .6rem; border-radius: 6px; white-space: nowrap; opacity: 0; transition: opacity .2s; pointer-events: none; }
.bar { width: 100%; border-radius: 8px 8px 0 0; background: linear-gradient(180deg, #38BDF8 0%, #0E7490 100%); transition: height .6s ease; min-height: 8px; }
.bar-label { font-size: .72rem; color: #94A3B8; font-weight: 700; white-space: nowrap; }

/* DONUT / PATHS */
.donut-card { }
.donut-list { display: flex; flex-direction: column; gap: .8rem; }
.donut-item { display: flex; align-items: center; gap: .7rem; }
.donut-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.donut-label { font-size: .82rem; font-weight: 700; color: #475569; width: 110px; flex-shrink: 0; }
.donut-bar-wrap { flex: 1; background: #F1F5F9; border-radius: 50px; height: 8px; overflow: hidden; }
.donut-bar { height: 100%; border-radius: 50px; transition: width .6s ease; }
.donut-pct { font-size: .8rem; font-weight: 800; color: #1E293B; width: 36px; text-align: left; flex-shrink: 0; }

/* BOTTOM ROW */
.bottom-row { display: grid; grid-template-columns: 1fr 380px; gap: 1rem; }

/* ACTIVITY */
.activity-list { display: flex; flex-direction: column; }
.activity-item { display: flex; align-items: center; gap: .9rem; padding: .85rem 0; border-bottom: 1px solid #F1F5F9; }
.activity-item:last-child { border-bottom: none; }
.activity-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: .85rem; flex-shrink: 0; }
.activity-icon.badge    { background: #FFF7ED; color: #D97706; }
.activity-icon.register { background: #F0FDF4; color: #16A34A; }
.activity-icon.payment  { background: #EFF6FF; color: #2563EB; }
.activity-icon.complete { background: #F0FDF4; color: #16A34A; }
.activity-icon.cert     { background: #FFFBEB; color: #D97706; }
.activity-body { flex: 1; min-width: 0; }
.activity-user { font-weight: 800; color: #1E293B; font-size: .88rem; display: block; }
.activity-action { font-size: .8rem; color: #94A3B8; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.activity-time { font-size: .75rem; color: #CBD5E1; font-weight: 600; white-space: nowrap; flex-shrink: 0; }

/* QUICK ACTIONS */
.quick-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .6rem; margin-bottom: 1rem; }
.quick-btn { display: flex; flex-direction: column; align-items: center; gap: .4rem; padding: .9rem .5rem; border-radius: 12px; border: none; cursor: pointer; font-family: inherit; font-weight: 700; font-size: .78rem; transition: transform .15s, opacity .2s; text-decoration: none; }
.quick-btn:hover { transform: translateY(-2px); opacity: .9; }
.quick-btn i { font-size: 1.2rem; }
.quick-btn.sky    { background: #E0F4FF; color: #0E7490; }
.quick-btn.pink   { background: #FCE7F3; color: #9D174D; }
.quick-btn.lime   { background: #F0FDF4; color: #3F6212; }
.quick-btn.amber  { background: #FFF7ED; color: #92400E; }
.quick-btn.purple { background: #F5F3FF; color: #5B21B6; }
.quick-btn.dark   { background: #F1F5F9; color: #475569; }

.summary-pills { display: flex; flex-direction: column; gap: .5rem; }
.summary-pill { display: flex; align-items: center; gap: .6rem; background: #F8FAFC; border-radius: 10px; padding: .6rem .8rem; font-size: .82rem; font-weight: 700; color: #475569; }

/* RESPONSIVE */
@media (max-width: 1200px) {
  .stats-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 1100px) {
  .charts-row { grid-template-columns: 1fr; }
  .bottom-row { grid-template-columns: 1fr; }
}
@media (max-width: 900px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .quick-grid { grid-template-columns: repeat(3, 1fr); }
}
</style>
