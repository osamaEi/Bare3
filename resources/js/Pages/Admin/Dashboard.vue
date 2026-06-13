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

    <!-- ══════════════════════════════════════════
         STUDENT PROGRESS SECTION
    ══════════════════════════════════════════ -->
    <div class="section-divider">
      <div class="section-divider-line"></div>
      <span class="section-divider-title"><i class="fa-solid fa-chart-line"></i> تقدم الطلبة</span>
      <div class="section-divider-line"></div>
    </div>

    <!-- Progress Overview Cards -->
    <div class="progress-overview">
      <div class="pov-card" style="--accent:#38BDF8;--bg:#E0F4FF">
        <div class="pov-icon"><i class="fa-solid fa-book-open"></i></div>
        <div class="pov-body">
          <div class="pov-val">{{ progress_stats.total_enrollments.toLocaleString() }}</div>
          <div class="pov-lbl">إجمالي التسجيلات</div>
          <div class="pov-sub">
            <span class="pov-chip active">{{ progress_stats.active_enrollments }} نشط</span>
            <span class="pov-chip done">{{ progress_stats.completed_enrollments }} مكتمل</span>
          </div>
        </div>
      </div>

      <div class="pov-card" style="--accent:#22C55E;--bg:#F0FDF4">
        <div class="pov-icon"><i class="fa-solid fa-list-check"></i></div>
        <div class="pov-body">
          <div class="pov-val">{{ progress_stats.completed_lessons.toLocaleString() }}</div>
          <div class="pov-lbl">دروس مكتملة</div>
          <div class="pov-progress-wrap">
            <div class="pov-bar">
              <div class="pov-fill" :style="{ width: progress_stats.avg_progress + '%', background: '#22C55E' }"></div>
            </div>
            <span class="pov-pct">{{ progress_stats.avg_progress }}%</span>
          </div>
          <div class="pov-sub-lbl">متوسط الإتمام من {{ progress_stats.total_lessons.toLocaleString() }} درس</div>
        </div>
      </div>

      <div class="pov-card" style="--accent:#F59E0B;--bg:#FFFBEB">
        <div class="pov-icon"><i class="fa-solid fa-pen-to-square"></i></div>
        <div class="pov-body">
          <div class="pov-val">{{ progress_stats.passed_quizzes.toLocaleString() }}</div>
          <div class="pov-lbl">اختبارات ناجحة</div>
          <div class="pov-progress-wrap">
            <div class="pov-bar">
              <div class="pov-fill" :style="{ width: progress_stats.quiz_pass_rate + '%', background: '#F59E0B' }"></div>
            </div>
            <span class="pov-pct">{{ progress_stats.quiz_pass_rate }}%</span>
          </div>
          <div class="pov-sub-lbl">معدل النجاح من {{ progress_stats.total_quizzes.toLocaleString() }} محاولة</div>
        </div>
      </div>

      <Link :href="route('admin.students.progress')" class="pov-card pov-link" style="--accent:#EC4899;--bg:#FDF2F8">
        <div class="pov-icon"><i class="fa-solid fa-users"></i></div>
        <div class="pov-body">
          <div class="pov-val">{{ stats.total_students.toLocaleString() }}</div>
          <div class="pov-lbl">إجمالي الطلبة</div>
          <div class="pov-action">
            <span>عرض كل الطلبة</span>
            <i class="fa-solid fa-arrow-left"></i>
          </div>
        </div>
      </Link>
    </div>

    <!-- Top Students Table -->
    <div class="card students-card">
      <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-trophy"></i> الطلبة الأكثر تقدماً</h3>
        <Link :href="route('admin.students.progress')" class="see-all-btn">
          عرض الكل <i class="fa-solid fa-arrow-left"></i>
        </Link>
      </div>

      <div v-if="top_students.length === 0" class="empty-students">
        <i class="fa-solid fa-user-graduate"></i>
        <p>لا يوجد طلبة مسجّلون بعد</p>
      </div>

      <table v-else class="students-table">
        <thead>
          <tr>
            <th>#</th>
            <th>الطالب</th>
            <th>المسارات</th>
            <th>مكتملة</th>
            <th>التقدم</th>
            <th>الشارات</th>
            <th>الشهادات</th>
            <th>الحالة</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(s, i) in top_students" :key="s.id">
            <td class="td-rank">
              <span v-if="i < 3" class="rank-medal" :class="['gold','silver','bronze'][i]">
                {{ ['🥇','🥈','🥉'][i] }}
              </span>
              <span v-else class="rank-num">{{ i + 1 }}</span>
            </td>
            <td>
              <div class="student-cell">
                <div class="student-av" :style="{ background: avatarGrad(i) }">{{ s.name?.[0] }}</div>
                <div>
                  <div class="student-name">{{ s.name }}</div>
                  <div class="student-email">{{ s.email }}</div>
                </div>
              </div>
            </td>
            <td><span class="num-chip sky">{{ s.enrollments }}</span></td>
            <td><span class="num-chip green">{{ s.completed }}</span></td>
            <td>
              <div class="inline-progress">
                <div class="inline-bar">
                  <div class="inline-fill" :style="{ width: s.enrollments > 0 ? Math.round(s.completed/s.enrollments*100)+'%' : '0%' }"></div>
                </div>
                <span class="inline-pct">{{ s.enrollments > 0 ? Math.round(s.completed/s.enrollments*100) : 0 }}%</span>
              </div>
            </td>
            <td><span class="num-chip amber">{{ s.badges }}</span></td>
            <td><span class="num-chip purple">{{ s.certificates }}</span></td>
            <td>
              <span class="status-dot" :class="s.is_active ? 'active' : 'inactive'">
                {{ s.is_active ? 'نشط' : 'موقوف' }}
              </span>
            </td>
            <td>
              <Link :href="route('admin.students.progress.show', s.id)" class="detail-btn">
                <i class="fa-solid fa-eye"></i> تفاصيل
              </Link>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="table-footer">
        <Link :href="route('admin.students.progress')" class="full-report-btn">
          <i class="fa-solid fa-chart-line"></i> عرض تقرير التقدم الكامل
        </Link>
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
          <Link :href="route('admin.students.progress')" class="quick-btn indigo">
            <i class="fa-solid fa-chart-line"></i>
            <span>تقدم الطلبة</span>
          </Link>
          <Link :href="route('admin.tickets')" class="quick-btn rose">
            <i class="fa-solid fa-ticket"></i>
            <span>التذاكر</span>
          </Link>
          <Link :href="route('admin.payments')" class="quick-btn amber">
            <i class="fa-solid fa-receipt"></i>
            <span>المدفوعات</span>
          </Link>
          <Link :href="route('admin.blog')" class="quick-btn lime">
            <i class="fa-solid fa-pen-nib"></i>
            <span>مقالة جديدة</span>
          </Link>
        </div>

        <!-- Summary Pills -->
        <div class="summary-pills">
          <div class="summary-pill">
            <i class="fa-solid fa-certificate" style="color:#F59E0B"></i>
            <span>{{ stats.completions }} شهادة صدرت هذا الشهر</span>
          </div>
          <div class="summary-pill">
            <i class="fa-solid fa-circle-check" style="color:#22C55E"></i>
            <span>{{ progress_stats.completed_enrollments }} مسار مكتمل</span>
          </div>
          <div class="summary-pill">
            <i class="fa-solid fa-coins" style="color:#84CC16"></i>
            <span>{{ stats.revenue_month.toLocaleString() }} ر.س إيرادات الشهر</span>
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
  stats:           Object,
  recent_activity: Array,
  revenue_chart:   Array,
  paths_dist:      Array,
  progress_stats:  Object,
  top_students:    Array,
})

const today = new Date().toLocaleDateString('ar-SA', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })

const statCards = computed(() => [
  { key: 'students', label: 'إجمالي الطلاب',   value: props.stats.total_students.toLocaleString(),                    icon: 'fa-solid fa-graduation-cap',  bg: '#EFF6FF', color: '#2563EB', change: '+٨٪ هذا الشهر', up: true },
  { key: 'parents',  label: 'أولياء الأمور',    value: props.stats.total_parents.toLocaleString(),                     icon: 'fa-solid fa-people-roof',     bg: '#FDF4FF', color: '#9333EA', change: '+٥٪ هذا الشهر', up: true },
  { key: 'teachers', label: 'المعلمون',          value: props.stats.total_teachers.toLocaleString(),                    icon: 'fa-solid fa-chalkboard-user', bg: '#F0FDF4', color: '#16A34A', change: '+٢ جدد',       up: true },
  { key: 'revenue',  label: 'إيرادات الشهر',    value: props.stats.revenue_month.toLocaleString() + ' ر.س',           icon: 'fa-solid fa-sack-dollar',     bg: '#FFF7ED', color: '#EA580C', change: '+٣٢٪',          up: true },
  { key: 'paths',    label: 'المسارات النشطة',  value: props.stats.active_paths,                                       icon: 'fa-solid fa-map',             bg: '#F0F9FF', color: '#0E7490', change: 'لا تغيير',     up: true },
  { key: 'certs',    label: 'شهادات صدرت',      value: props.stats.completions.toLocaleString(),                       icon: 'fa-solid fa-certificate',     bg: '#FFFBEB', color: '#D97706', change: '+١٢٪',          up: true },
])

const revenueChart      = computed(() => props.revenue_chart ?? [])
const recentActivity    = computed(() => props.recent_activity ?? [])
const pathsDistribution = computed(() => props.paths_dist ?? [])

const maxRevenue = computed(() => Math.max(...revenueChart.value.map(r => r.amount), 1))
const barHeight  = (amount) => Math.round((amount / maxRevenue.value) * 85) + 10

const avatarGrads = [
  'linear-gradient(135deg,#38BDF8,#0284C7)',
  'linear-gradient(135deg,#EC4899,#DB2777)',
  'linear-gradient(135deg,#22C55E,#16A34A)',
  'linear-gradient(135deg,#F59E0B,#D97706)',
  'linear-gradient(135deg,#8B5CF6,#7C3AED)',
  'linear-gradient(135deg,#EF4444,#DC2626)',
  'linear-gradient(135deg,#14B8A6,#0D9488)',
  'linear-gradient(135deg,#F97316,#EA580C)',
]
const avatarGrad = (i) => avatarGrads[i % avatarGrads.length]

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
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap');
* { box-sizing: border-box; margin: 0; padding: 0; }

/* PAGE HEADER */
.page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
.page-title { font-size: 1.6rem; font-weight: 900; color: #1E293B; }
.page-sub { font-size: .88rem; color: #94A3B8; margin-top: .2rem; }
.btn-export { background: white; border: 1.5px solid #E2E8F0; color: #475569; font-family: inherit; font-weight: 700; font-size: .88rem; padding: .6rem 1.2rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: .5rem; transition: border-color .2s; }
.btn-export:hover { border-color: #38BDF8; color: #0E7490; }

/* STATS GRID */
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
.stat-card { background: white; border-radius: 16px; padding: 1.1rem 1.2rem; display: flex; flex-direction: column; gap: .6rem; box-shadow: 0 1px 8px rgba(0,0,0,.05); transition: transform .2s, box-shadow .2s; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,.08); }
.stat-card-top { display: flex; align-items: center; justify-content: space-between; }
.stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
.stat-change { font-size: .72rem; font-weight: 700; display: flex; align-items: center; gap: .2rem; }
.stat-change.up { color: #16A34A; }
.stat-change.down { color: #DC2626; }
.stat-value { font-size: 1.5rem; font-weight: 900; color: #1E293B; line-height: 1.2; }
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
.chart-bars { display: flex; align-items: flex-end; gap: .6rem; height: 160px; padding: 0 .5rem; }
.bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: .4rem; position: relative; height: 100%; justify-content: flex-end; }
.bar-col:hover .bar-tooltip { opacity: 1; }
.bar-tooltip { position: absolute; top: -30px; background: #1E293B; color: white; font-size: .7rem; font-weight: 700; padding: .2rem .6rem; border-radius: 6px; white-space: nowrap; opacity: 0; transition: opacity .2s; pointer-events: none; }
.bar { width: 100%; border-radius: 8px 8px 0 0; background: linear-gradient(180deg, #38BDF8 0%, #0E7490 100%); transition: height .6s ease; min-height: 8px; }
.bar-label { font-size: .72rem; color: #94A3B8; font-weight: 700; white-space: nowrap; }

/* DONUT / PATHS */
.donut-list { display: flex; flex-direction: column; gap: .8rem; }
.donut-item { display: flex; align-items: center; gap: .7rem; }
.donut-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.donut-label { font-size: .82rem; font-weight: 700; color: #475569; width: 110px; flex-shrink: 0; }
.donut-bar-wrap { flex: 1; background: #F1F5F9; border-radius: 50px; height: 8px; overflow: hidden; }
.donut-bar { height: 100%; border-radius: 50px; transition: width .6s ease; }
.donut-pct { font-size: .8rem; font-weight: 800; color: #1E293B; width: 36px; text-align: left; flex-shrink: 0; }

/* ── SECTION DIVIDER ── */
.section-divider { display: flex; align-items: center; gap: 1rem; margin: .5rem 0 1.4rem; }
.section-divider-line { flex: 1; height: 1px; background: #E2E8F0; }
.section-divider-title { font-size: .88rem; font-weight: 800; color: #64748B; white-space: nowrap; display: flex; align-items: center; gap: .45rem; }
.section-divider-title i { color: #38BDF8; }

/* ── PROGRESS OVERVIEW ── */
.progress-overview { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.4rem; }
.pov-card { background: white; border-radius: 16px; padding: 1.2rem 1.3rem; box-shadow: 0 1px 8px rgba(0,0,0,.05); display: flex; align-items: flex-start; gap: 1rem; border-top: 3px solid var(--accent); transition: transform .2s, box-shadow .2s; text-decoration: none; }
.pov-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,.08); }
.pov-link { cursor: pointer; }
.pov-icon { width: 44px; height: 44px; border-radius: 12px; background: var(--bg); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
.pov-body { flex: 1; min-width: 0; }
.pov-val { font-size: 1.6rem; font-weight: 900; color: #1E293B; line-height: 1.1; }
.pov-lbl { font-size: .78rem; color: #94A3B8; font-weight: 600; margin-top: .2rem; margin-bottom: .55rem; }
.pov-sub { display: flex; gap: .4rem; flex-wrap: wrap; }
.pov-chip { font-size: .72rem; font-weight: 700; border-radius: 20px; padding: .15rem .6rem; }
.pov-chip.active { background: #E0F4FF; color: #0284C7; }
.pov-chip.done   { background: #F0FDF4; color: #16A34A; }
.pov-progress-wrap { display: flex; align-items: center; gap: .6rem; margin-bottom: .3rem; }
.pov-bar { flex: 1; height: 7px; background: #F1F5F9; border-radius: 99px; overflow: hidden; }
.pov-fill { height: 100%; border-radius: 99px; transition: width .6s ease; }
.pov-pct { font-size: .82rem; font-weight: 800; color: #1E293B; width: 36px; flex-shrink: 0; }
.pov-sub-lbl { font-size: .72rem; color: #94A3B8; font-weight: 600; }
.pov-action { display: flex; align-items: center; gap: .5rem; font-size: .82rem; font-weight: 700; color: var(--accent); margin-top: .3rem; }

/* ── STUDENTS TABLE ── */
.students-card { margin-bottom: 1.5rem; padding: 0; overflow: hidden; }
.students-card .card-header { padding: 1.2rem 1.4rem; margin-bottom: 0; border-bottom: 1px solid #F1F5F9; }
.see-all-btn { display: inline-flex; align-items: center; gap: .4rem; font-size: .83rem; font-weight: 700; color: #38BDF8; text-decoration: none; transition: color .2s; }
.see-all-btn:hover { color: #0284C7; }

.empty-students { text-align: center; padding: 3rem 2rem; color: #94A3B8; }
.empty-students i { font-size: 2rem; margin-bottom: .6rem; display: block; }

.students-table { width: 100%; border-collapse: collapse; font-size: .86rem; }
.students-table thead { background: #F8FAFC; }
.students-table th { padding: .75rem 1rem; text-align: right; font-weight: 800; color: #64748B; font-size: .77rem; border-bottom: 1px solid #F1F5F9; white-space: nowrap; }
.students-table td { padding: .8rem 1rem; border-bottom: 1px solid #F8FAFC; vertical-align: middle; color: #1E293B; }
.students-table tr:last-child td { border-bottom: none; }
.students-table tr:hover td { background: #FAFBFF; }

.td-rank { width: 40px; text-align: center; }
.rank-medal { font-size: 1.2rem; }
.rank-num { font-size: .8rem; font-weight: 800; color: #94A3B8; }

.student-cell { display: flex; align-items: center; gap: .7rem; }
.student-av { width: 34px; height: 34px; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: .88rem; flex-shrink: 0; }
.student-name  { font-weight: 700; color: #1E293B; font-size: .88rem; }
.student-email { font-size: .75rem; color: #94A3B8; }

.num-chip { font-size: .78rem; font-weight: 800; border-radius: 20px; padding: .2rem .65rem; display: inline-block; }
.num-chip.sky    { background: #E0F4FF; color: #0284C7; }
.num-chip.green  { background: #F0FDF4; color: #16A34A; }
.num-chip.amber  { background: #FFFBEB; color: #D97706; }
.num-chip.purple { background: #F5F3FF; color: #7C3AED; }

.inline-progress { display: flex; align-items: center; gap: .5rem; }
.inline-bar { width: 70px; height: 6px; background: #F1F5F9; border-radius: 99px; overflow: hidden; }
.inline-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #38BDF8, #0284C7); transition: width .4s; }
.inline-pct { font-size: .76rem; font-weight: 800; color: #64748B; min-width: 30px; }

.status-dot { font-size: .72rem; font-weight: 700; border-radius: 20px; padding: .2rem .65rem; }
.status-dot.active   { background: #F0FDF4; color: #16A34A; }
.status-dot.inactive { background: #FEF2F2; color: #DC2626; }

.detail-btn { background: #38BDF8; color: white; border-radius: 8px; padding: .35rem .85rem; font-size: .78rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: .35rem; transition: background .2s; white-space: nowrap; }
.detail-btn:hover { background: #0284C7; }

.table-footer { padding: 1rem 1.4rem; border-top: 1px solid #F1F5F9; display: flex; justify-content: center; }
.full-report-btn { display: inline-flex; align-items: center; gap: .6rem; background: linear-gradient(135deg, #38BDF8, #0284C7); color: white; border-radius: 10px; padding: .65rem 1.8rem; font-size: .88rem; font-weight: 700; text-decoration: none; transition: opacity .2s; }
.full-report-btn:hover { opacity: .9; }

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
.activity-user   { font-weight: 800; color: #1E293B; font-size: .88rem; display: block; }
.activity-action { font-size: .8rem; color: #94A3B8; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.activity-time   { font-size: .75rem; color: #CBD5E1; font-weight: 600; white-space: nowrap; flex-shrink: 0; }

/* QUICK ACTIONS */
.quick-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .6rem; margin-bottom: 1rem; }
.quick-btn { display: flex; flex-direction: column; align-items: center; gap: .4rem; padding: .9rem .5rem; border-radius: 12px; border: none; cursor: pointer; font-family: inherit; font-weight: 700; font-size: .78rem; transition: transform .15s, opacity .2s; text-decoration: none; }
.quick-btn:hover { transform: translateY(-2px); opacity: .9; }
.quick-btn i { font-size: 1.2rem; }
.quick-btn.sky    { background: #E0F4FF; color: #0E7490; }
.quick-btn.pink   { background: #FCE7F3; color: #9D174D; }
.quick-btn.indigo { background: #EEF2FF; color: #4338CA; }
.quick-btn.rose   { background: #FFF1F2; color: #BE123C; }
.quick-btn.amber  { background: #FFF7ED; color: #92400E; }
.quick-btn.lime   { background: #F0FDF4; color: #3F6212; }

.summary-pills { display: flex; flex-direction: column; gap: .5rem; }
.summary-pill { display: flex; align-items: center; gap: .6rem; background: #F8FAFC; border-radius: 10px; padding: .6rem .8rem; font-size: .82rem; font-weight: 700; color: #475569; }

/* RESPONSIVE */
@media (max-width: 1300px) { .progress-overview { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 1100px) { .charts-row { grid-template-columns: 1fr; } .bottom-row { grid-template-columns: 1fr; } }
@media (max-width: 900px)  { .stats-grid { grid-template-columns: repeat(2, 1fr); } .progress-overview { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px)  { .stats-grid { grid-template-columns: repeat(2, 1fr); } .students-table { font-size: .78rem; } }
</style>
