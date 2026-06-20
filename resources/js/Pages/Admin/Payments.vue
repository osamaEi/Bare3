<template>
  <AdminLayout page-title="المدفوعات والمالية">

    <!-- HEADER -->
    <div class="page-header">
      <div>
        <h1 class="page-title">المدفوعات والمالية</h1>
        <p class="page-sub">تتبع العمليات المالية عبر مدى · تابي · تمارا</p>
      </div>
      <div class="header-actions">
        <select v-model="period" class="period-sel">
          <option value="week">هذا الأسبوع</option>
          <option value="month" selected>هذا الشهر</option>
          <option value="quarter">آخر ٣ أشهر</option>
          <option value="year">هذا العام</option>
        </select>
        <button class="btn-export"><i class="fa-solid fa-download"></i> تصدير PDF</button>
      </div>
    </div>

    <!-- KPI CARDS -->
    <div class="kpi-grid">
      <div v-for="kpi in kpiCards" :key="kpi.label" class="kpi-card">
        <div class="kpi-top">
          <div class="kpi-icon" :style="{ background: kpi.bg, color: kpi.color }">
            <i :class="kpi.icon"></i>
          </div>
          <span class="kpi-change" :class="kpi.up ? 'up' : 'down'">
            <i :class="kpi.up ? 'fa-solid fa-arrow-trend-up' : 'fa-solid fa-arrow-trend-down'"></i>
            {{ kpi.change }}
          </span>
        </div>
        <div class="kpi-val" v-html="kpi.value"></div>
        <div class="kpi-label">{{ kpi.label }}</div>
      </div>
    </div>

    <!-- GATEWAY SPLIT + CHART ROW -->
    <div class="split-row">

      <!-- Gateway Cards -->
      <div class="gateway-col">
        <div class="section-heading">توزيع بوابات الدفع</div>
        <div class="gateway-cards">
          <div v-for="gw in gateways" :key="gw.name" class="gateway-card">
            <div class="gw-logo" :style="{ background: gw.bg, color: gw.color }">
              <i :class="gw.icon"></i>
            </div>
            <div class="gw-info">
              <div class="gw-name">{{ gw.name }}</div>
              <div class="gw-amount">{{ gw.amount.toLocaleString() }} <i class="fa-solid fa-saudi-riyal-symbol"></i></div>
            </div>
            <div class="gw-right">
              <div class="gw-pct-val">{{ gw.pct }}%</div>
              <div class="gw-bar-wrap">
                <div class="gw-bar" :style="{ width: gw.pct + '%', background: gw.color }"></div>
              </div>
              <div class="gw-count">{{ gw.txCount }} عملية</div>
            </div>
          </div>
        </div>

        <!-- Plan Split -->
        <div class="section-heading mt-sm">مبيعات حسب الباقة</div>
        <div class="plan-split">
          <div v-for="plan in planSplit" :key="plan.name" class="plan-row">
            <span class="plan-dot" :style="{ background: plan.color }"></span>
            <span class="plan-name">{{ plan.name }}</span>
            <div class="plan-bar-wrap">
              <div class="plan-bar" :style="{ width: plan.pct + '%', background: plan.color }"></div>
            </div>
            <span class="plan-amount">{{ plan.amount.toLocaleString() }}</span>
          </div>
        </div>
      </div>

      <!-- Monthly Chart -->
      <div class="chart-col card">
        <div class="card-header">
          <h3 class="card-title"><i class="fa-solid fa-chart-area"></i> الإيرادات الشهرية</h3>
          <span class="card-badge sky"><i class="fa-solid fa-saudi-riyal-symbol"></i></span>
        </div>
        <div class="monthly-chart">
          <div v-for="m in monthlyRevenue" :key="m.month" class="monthly-col">
            <div class="monthly-tooltip">{{ m.amount.toLocaleString() }}</div>
            <div class="monthly-bar"
              :style="{ height: (m.amount / maxMonthly * 160) + 'px', background: m.current ? 'linear-gradient(180deg,#38BDF8,#0E7490)' : '#E2E8F0' }">
            </div>
            <div class="monthly-label">{{ m.month }}</div>
            <div class="monthly-val" :class="{ current: m.current }">{{ (m.amount/1000).toFixed(1) }}k</div>
          </div>
        </div>
      </div>

    </div>

    <!-- TRANSACTIONS TABLE -->
    <div class="card mt-card">
      <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-list-ul"></i> العمليات المالية</h3>
        <div class="header-filters">
          <select v-model="txGateway" class="filter-sel">
            <option value="">كل البوابات</option>
            <option value="paytabs">PayTabs</option>
            <option value="mada">مدى</option>
            <option value="tabby">تابي</option>
            <option value="tamara">تمارا</option>
          </select>
          <select v-model="txStatus" class="filter-sel">
            <option value="">كل الحالات</option>
            <option value="success">ناجحة</option>
            <option value="pending">معلّقة</option>
            <option value="failed">فاشلة</option>
            <option value="refunded">مُستردّة</option>
          </select>
          <div class="search-wrap">
            <i class="fa-solid fa-search search-ico"></i>
            <input v-model="txSearch" type="text" placeholder="ابحث باسم المستخدم..." class="search-input" />
          </div>
        </div>
      </div>

      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>رقم العملية</th>
              <th>المستخدم</th>
              <th>الباقة</th>
              <th>البوابة</th>
              <th>المبلغ</th>
              <th>التاريخ</th>
              <th>الحالة</th>
              <th>إجراء</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="tx in filteredTx" :key="tx.id">
              <td class="tx-id">{{ tx.id }}</td>
              <td>
                <div class="user-cell">
                  <div class="user-avatar" :style="{ background: avatarColor(tx.user) }">{{ tx.user[0] }}</div>
                  <span>{{ tx.user }}</span>
                </div>
              </td>
              <td><span class="plan-badge">{{ tx.plan }}</span></td>
              <td>
                <span class="gw-badge" :class="tx.gateway.toLowerCase()">
                  <i :class="gwIcon(tx.gateway)"></i> {{ tx.gateway }}
                </span>
              </td>
              <td class="tx-amount">{{ tx.amount.toLocaleString() }} <i class="fa-solid fa-saudi-riyal-symbol"></i></td>
              <td class="text-gray">{{ tx.date }}</td>
              <td>
                <span class="status-badge" :class="tx.status">{{ statusLabel(tx.status) }}</span>
              </td>
              <td>
                <button class="act-sm view" title="تفاصيل" @click="viewTx(tx)"><i class="fa-solid fa-eye"></i></button>
                <button v-if="tx.status === 'success'" class="act-sm refund" title="استرداد" @click="refundTx(tx)"><i class="fa-solid fa-rotate-left"></i></button>
              </td>
            </tr>
            <tr v-if="filteredTx.length === 0">
              <td colspan="8" class="empty-row">لا توجد عمليات مطابقة</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="table-footer">
        <span class="page-info">{{ filteredTx.length }} عملية</span>
        <div class="footer-total">
          الإجمالي: <strong>{{ filteredTotal.toLocaleString() }} <i class="fa-solid fa-saudi-riyal-symbol"></i></strong>
        </div>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  transactions:  { type: Object, default: () => ({ data: [] }) },
  revenue_month: { type: Number, default: 0 },
  revenue_year:  { type: Number, default: 0 },
  revenue_total: { type: Number, default: 0 },
  gateway_split: { type: Array,  default: () => [] },
  monthly_chart: { type: Array,  default: () => [] },
  plan_split:    { type: Array,  default: () => [] },
  counts:        { type: Object, default: () => ({ success: 0, refunded: 0, failed: 0, pending: 0 }) },
  filters:       { type: Object, default: () => ({}) },
})

const period = ref('month')
const txGateway = ref(props.filters.gateway ?? '')
const txStatus = ref(props.filters.status ?? '')
const txSearch = ref(props.filters.search ?? '')

const GW = { paytabs: { label: 'PayTabs', icon: 'fa-solid fa-credit-card', bg: '#FEF3C7', color: '#B45309' },
             mada: { label: 'مدى', icon: 'fa-solid fa-credit-card', bg: '#E0F4FF', color: '#0E7490' },
             tabby: { label: 'تابي', icon: 'fa-solid fa-clock-rotate-left', bg: '#F5F3FF', color: '#5B21B6' },
             tamara: { label: 'تمارا', icon: 'fa-solid fa-hand-holding-dollar', bg: '#F0FDF4', color: '#15803D' } }
const PLAN_COLORS = ['#EC4899', '#8B5CF6', '#94A3B8', '#38BDF8', '#16A34A']

const fmtNum = (n) => Number(n ?? 0).toLocaleString('ar-EG')

const kpiCards = computed(() => {
  const successCount = props.counts.success || 1
  const avg = props.revenue_total / successCount
  return [
    { label: 'إجمالي الإيرادات', value: fmtNum(props.revenue_total) + ' <i class="fa-solid fa-saudi-riyal-symbol"></i>', icon: 'fa-solid fa-sack-dollar', bg: '#FFF7ED', color: '#EA580C', change: '', up: true },
    { label: 'عمليات ناجحة', value: fmtNum(props.counts.success), icon: 'fa-solid fa-circle-check', bg: '#F0FDF4', color: '#16A34A', change: '', up: true },
    { label: 'متوسط قيمة الاشتراك', value: avg.toFixed(1) + ' <i class="fa-solid fa-saudi-riyal-symbol"></i>', icon: 'fa-solid fa-chart-line', bg: '#EFF6FF', color: '#2563EB', change: '', up: true },
    { label: 'عمليات مُستردّة', value: fmtNum(props.counts.refunded), icon: 'fa-solid fa-rotate-left', bg: '#FEF2F2', color: '#DC2626', change: '', up: false },
  ]
})

const gatewaysTotal = computed(() => props.gateway_split.reduce((s, g) => s + Number(g.total ?? 0), 0) || 1)
const gateways = computed(() => props.gateway_split.map(g => {
  const meta = GW[g.gateway] ?? { label: g.gateway, icon: 'fa-solid fa-money-bill', bg: '#F1F5F9', color: '#475569' }
  return { name: meta.label, icon: meta.icon, bg: meta.bg, color: meta.color,
    amount: Number(g.total ?? 0), pct: Math.round(Number(g.total ?? 0) / gatewaysTotal.value * 100), txCount: g.count ?? 0 }
}))

const plansTotal = computed(() => props.plan_split.reduce((s, p) => s + Number(p.total ?? 0), 0) || 1)
const planSplit = computed(() => props.plan_split.map((p, i) => ({
  name: p.name, color: PLAN_COLORS[i % PLAN_COLORS.length],
  pct: Math.round(Number(p.total ?? 0) / plansTotal.value * 100), amount: Number(p.total ?? 0),
})))

const monthlyRevenue = computed(() => {
  const arr = props.monthly_chart.map(m => ({ month: m.month, amount: Number(m.amount ?? 0), current: false }))
  if (arr.length) arr[arr.length - 1].current = true
  return arr
})
const maxMonthly = computed(() => Math.max(1, ...monthlyRevenue.value.map(m => m.amount)))

// Server-filtered transactions, mapped to table shape
const filteredTx = computed(() => (props.transactions?.data ?? []).map(tx => ({
  id: tx.gateway_tx_id ?? ('#' + tx.id),
  realId: tx.id,
  user: tx.user?.name ?? '—',
  plan: tx.subscription?.plan?.name ?? '—',
  gateway: GW[tx.gateway]?.label ?? tx.gateway,
  amount: Number(tx.amount ?? 0),
  date: tx.created_at ? new Date(tx.created_at).toLocaleDateString('ar-EG') : '—',
  status: tx.status,
})))

const filteredTotal = computed(() =>
  filteredTx.value.filter(tx => tx.status === 'success').reduce((s, tx) => s + tx.amount, 0)
)

// Debounced server-side filtering
let timer = null
watch([txGateway, txStatus, txSearch], () => {
  clearTimeout(timer)
  timer = setTimeout(() => {
    router.get(route('admin.payments.index'), {
      gateway: txGateway.value || undefined,
      status: txStatus.value || undefined,
      search: txSearch.value || undefined,
    }, { preserveState: true, replace: true, preserveScroll: true })
  }, 350)
})

const statusLabel = (s) => ({ success: 'ناجحة', pending: 'معلّقة', failed: 'فاشلة', refunded: 'مُستردّة' }[s] ?? s)
const gwIcon = (gw) => ({ مدى: 'fa-solid fa-credit-card', تابي: 'fa-solid fa-clock-rotate-left', تمارا: 'fa-solid fa-hand-holding-dollar' }[gw] ?? 'fa-solid fa-money-bill')
const avatarColor = (name) => {
  const colors = ['#38BDF8','#EC4899','#84CC16','#F59E0B','#8B5CF6']
  return colors[(name?.charCodeAt(0) ?? 0) % colors.length]
}

const viewTx = (tx) => { router.get(route('admin.payments.show', tx.realId)) }
const refundTx = (tx) => {
  if (confirm(`استرداد ${tx.amount} ريال للمستخدم "${tx.user}"؟`)) {
    router.patch(route('admin.payments.refund', tx.realId), {}, { preserveScroll: true })
  }
}
</script>

<style scoped>
.page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
.page-title { font-size: 1.6rem; font-weight: 900; color: #1E293B; }
.page-sub { font-size: .88rem; color: #94A3B8; margin-top: .2rem; }
.header-actions { display: flex; gap: .6rem; align-items: center; }
.period-sel { padding: .6rem .9rem; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; font-size: .85rem; color: #475569; background: white; cursor: pointer; }
.btn-export { background: white; border: 1.5px solid #E2E8F0; color: #475569; font-family: inherit; font-weight: 700; font-size: .85rem; padding: .6rem 1.1rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: .4rem; }

/* KPIs */
.kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
.kpi-card { background: white; border-radius: 16px; padding: 1.3rem; box-shadow: 0 1px 8px rgba(0,0,0,.05); }
.kpi-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: .8rem; }
.kpi-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.kpi-change { font-size: .78rem; font-weight: 700; display: flex; align-items: center; gap: .25rem; }
.kpi-change.up { color: #16A34A; }
.kpi-change.down { color: #DC2626; }
.kpi-val { font-size: 1.6rem; font-weight: 900; color: #1E293B; }
.kpi-label { font-size: .8rem; color: #94A3B8; font-weight: 600; margin-top: .2rem; }

/* SPLIT ROW */
.split-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; }
.section-heading { font-size: .85rem; font-weight: 800; color: #64748B; margin-bottom: .8rem; text-transform: uppercase; letter-spacing: .05em; }
.mt-sm { margin-top: 1.2rem; }

/* GATEWAY CARDS */
.gateway-cards { display: flex; flex-direction: column; gap: .7rem; }
.gateway-card { background: white; border-radius: 14px; padding: 1rem 1.2rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 1px 8px rgba(0,0,0,.05); }
.gw-logo { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
.gw-info { flex: 1; }
.gw-name { font-weight: 800; color: #1E293B; font-size: .92rem; }
.gw-amount { font-size: .82rem; color: #64748B; }
.gw-right { width: 100px; }
.gw-pct-val { font-size: .82rem; font-weight: 800; color: #1E293B; text-align: left; margin-bottom: .25rem; }
.gw-bar-wrap { background: #F1F5F9; border-radius: 50px; height: 6px; overflow: hidden; margin-bottom: .2rem; }
.gw-bar { height: 100%; border-radius: 50px; }
.gw-count { font-size: .72rem; color: #94A3B8; text-align: left; }

/* PLAN SPLIT */
.plan-split { display: flex; flex-direction: column; gap: .7rem; }
.plan-row { display: flex; align-items: center; gap: .7rem; }
.plan-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.plan-name { font-size: .82rem; font-weight: 700; color: #475569; width: 140px; flex-shrink: 0; }
.plan-bar-wrap { flex: 1; background: #F1F5F9; border-radius: 50px; height: 8px; overflow: hidden; }
.plan-bar { height: 100%; border-radius: 50px; }
.plan-amount { font-size: .82rem; font-weight: 800; color: #1E293B; width: 60px; text-align: left; flex-shrink: 0; }

/* CHART */
.card { background: white; border-radius: 16px; box-shadow: 0 1px 8px rgba(0,0,0,.05); overflow: hidden; }
.mt-card { margin-top: 1.2rem; }
.chart-col { padding: 0; }
.card-header { display: flex; align-items: center; justify-content: space-between; padding: 1.2rem 1.4rem; border-bottom: 1px solid #F1F5F9; }
.card-title { font-size: 1rem; font-weight: 800; color: #1E293B; display: flex; align-items: center; gap: .5rem; }
.card-title i { color: #38BDF8; }
.card-badge { font-size: .75rem; font-weight: 800; padding: .25rem .8rem; border-radius: 50px; }
.card-badge.sky { background: #E0F4FF; color: #0E7490; }

.monthly-chart { display: flex; align-items: flex-end; gap: .6rem; height: 180px; padding: 1rem 1.4rem 1.2rem; }
.monthly-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: .3rem; position: relative; height: 100%; justify-content: flex-end; }
.monthly-col:hover .monthly-tooltip { opacity: 1; }
.monthly-tooltip { position: absolute; top: -28px; background: #1E293B; color: white; font-size: .68rem; font-weight: 700; padding: .15rem .5rem; border-radius: 5px; white-space: nowrap; opacity: 0; transition: opacity .2s; pointer-events: none; }
.monthly-bar { width: 100%; border-radius: 6px 6px 0 0; min-height: 8px; transition: height .5s ease; }
.monthly-label { font-size: .7rem; color: #94A3B8; font-weight: 700; }
.monthly-val { font-size: .72rem; font-weight: 800; color: #94A3B8; }
.monthly-val.current { color: #0E7490; }

/* TABLE */
.table-wrap { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { padding: .75rem 1rem; text-align: right; font-size: .78rem; font-weight: 700; color: #94A3B8; background: #FAFAFA; border-bottom: 1px solid #F1F5F9; white-space: nowrap; }
.data-table td { padding: .85rem 1rem; border-bottom: 1px solid #F8FAFC; font-size: .88rem; vertical-align: middle; }
.data-table tbody tr:hover { background: #FAFAFA; }

.header-filters { display: flex; gap: .6rem; align-items: center; flex-wrap: wrap; }
.filter-sel { padding: .5rem .8rem; border: 1.5px solid #E2E8F0; border-radius: 8px; font-family: inherit; font-size: .82rem; color: #475569; background: white; }
.search-wrap { position: relative; }
.search-ico { position: absolute; right: .8rem; top: 50%; transform: translateY(-50%); color: #CBD5E1; font-size: .82rem; }
.search-input { padding: .5rem 2.2rem .5rem .8rem; border: 1.5px solid #E2E8F0; border-radius: 8px; font-family: inherit; font-size: .82rem; color: #1E293B; width: 180px; }
.search-input:focus { outline: none; border-color: #38BDF8; }

.tx-id { font-size: .78rem; color: #94A3B8; font-weight: 700; }
.user-cell { display: flex; align-items: center; gap: .6rem; }
.user-avatar { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: .78rem; flex-shrink: 0; }
.text-gray { color: #64748B; }
.tx-amount { font-weight: 800; color: #1E293B; }

.plan-badge { background: #F1F5F9; color: #475569; font-size: .75rem; font-weight: 700; padding: .2rem .7rem; border-radius: 50px; }
.gw-badge { display: inline-flex; align-items: center; gap: .3rem; font-size: .78rem; font-weight: 700; padding: .2rem .7rem; border-radius: 50px; }
.gw-badge.مدى   { background: #E0F4FF; color: #0E7490; }
.gw-badge.تابي  { background: #F5F3FF; color: #5B21B6; }
.gw-badge.تمارا { background: #F0FDF4; color: #15803D; }

.status-badge { font-size: .75rem; font-weight: 700; padding: .2rem .7rem; border-radius: 50px; }
.status-badge.success  { background: #F0FDF4; color: #15803D; }
.status-badge.pending  { background: #FFFBEB; color: #B45309; }
.status-badge.failed   { background: #FEF2F2; color: #DC2626; }
.status-badge.refunded { background: #F1F5F9; color: #475569; }

.act-sm { width: 28px; height: 28px; border-radius: 7px; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: .75rem; margin-left: .25rem; }
.act-sm.view   { background: #EFF6FF; color: #2563EB; }
.act-sm.refund { background: #FFF7ED; color: #EA580C; }
.empty-row { text-align: center; color: #94A3B8; padding: 3rem !important; }

.table-footer { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.4rem; border-top: 1px solid #F1F5F9; }
.page-info { font-size: .82rem; color: #94A3B8; }
.footer-total { font-size: .9rem; color: #475569; }
.footer-total strong { color: #1E293B; font-size: 1rem; }

/* MODAL */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 200; display: flex; align-items: center; justify-content: center; }
.modal { background: white; border-radius: 20px; width: 460px; max-width: 95vw; box-shadow: 0 20px 60px rgba(0,0,0,.15); }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1.2rem 1.5rem; border-bottom: 1px solid #F1F5F9; }
.modal-header h3 { font-size: 1rem; font-weight: 800; color: #1E293B; }
.modal-close { background: none; border: none; cursor: pointer; font-size: 1.1rem; color: #94A3B8; }
.modal-body { padding: 1.5rem; }
.tx-detail-grid { display: flex; flex-direction: column; gap: .7rem; }
.tx-detail-row { display: flex; justify-content: space-between; align-items: center; padding: .5rem 0; border-bottom: 1px solid #F8FAFC; font-size: .9rem; }
.tx-detail-row span { color: #64748B; }
.tx-detail-row strong { color: #1E293B; }
.modal-footer { display: flex; gap: .7rem; justify-content: flex-end; padding: 1rem 1.5rem; border-top: 1px solid #F1F5F9; }
.btn-cancel { background: #F1F5F9; border: none; color: #475569; font-family: inherit; font-weight: 700; font-size: .9rem; padding: .65rem 1.4rem; border-radius: 10px; cursor: pointer; }
.btn-print { background: #38BDF8; border: none; color: white; font-family: inherit; font-weight: 700; font-size: .9rem; padding: .65rem 1.4rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: .5rem; }

@media (max-width: 1100px) {
  .split-row { grid-template-columns: 1fr; }
  .kpi-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
  .kpi-grid { grid-template-columns: 1fr 1fr; }
}
</style>
