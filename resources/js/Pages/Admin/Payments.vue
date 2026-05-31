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
        <div class="kpi-val">{{ kpi.value }}</div>
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
              <div class="gw-amount">{{ gw.amount.toLocaleString() }} ر.س</div>
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
          <span class="card-badge sky">ر.س</span>
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
            <option value="مدى">مدى</option>
            <option value="تابي">تابي</option>
            <option value="تمارا">تمارا</option>
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
              <td class="tx-amount">{{ tx.amount.toLocaleString() }} ر.س</td>
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
          الإجمالي: <strong>{{ filteredTotal.toLocaleString() }} ر.س</strong>
        </div>
      </div>
    </div>

    <!-- TX DETAIL MODAL -->
    <div v-if="txModal" class="modal-overlay" @click.self="txModal = false">
      <div class="modal">
        <div class="modal-header">
          <h3>تفاصيل العملية #{{ selectedTx?.id }}</h3>
          <button class="modal-close" @click="txModal = false"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
          <div class="tx-detail-grid">
            <div class="tx-detail-row"><span>المستخدم</span><strong>{{ selectedTx?.user }}</strong></div>
            <div class="tx-detail-row"><span>الباقة</span><strong>{{ selectedTx?.plan }}</strong></div>
            <div class="tx-detail-row"><span>البوابة</span><strong>{{ selectedTx?.gateway }}</strong></div>
            <div class="tx-detail-row"><span>المبلغ</span><strong>{{ selectedTx?.amount?.toLocaleString() }} ر.س</strong></div>
            <div class="tx-detail-row"><span>التاريخ</span><strong>{{ selectedTx?.date }}</strong></div>
            <div class="tx-detail-row"><span>الحالة</span>
              <span class="status-badge" :class="selectedTx?.status">{{ statusLabel(selectedTx?.status) }}</span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn-cancel" @click="txModal = false">إغلاق</button>
          <button class="btn-print"><i class="fa-solid fa-print"></i> طباعة</button>
        </div>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const period = ref('month')
const txGateway = ref('')
const txStatus = ref('')
const txSearch = ref('')
const txModal = ref(false)
const selectedTx = ref(null)

const kpiCards = [
  { label: 'إجمالي الإيرادات',   value: '١٨,٤٥٠ ر.س', icon: 'fa-solid fa-sack-dollar',    bg: '#FFF7ED', color: '#EA580C', change: '+٣٢٪', up: true },
  { label: 'عمليات ناجحة',       value: '٢٣٤',          icon: 'fa-solid fa-circle-check',   bg: '#F0FDF4', color: '#16A34A', change: '+١٨٪', up: true },
  { label: 'متوسط قيمة الاشتراك',value: '٧٨.٨ ر.س',    icon: 'fa-solid fa-chart-line',     bg: '#EFF6FF', color: '#2563EB', change: '+٥٪',  up: true },
  { label: 'عمليات مُستردّة',    value: '١١',            icon: 'fa-solid fa-rotate-left',    bg: '#FEF2F2', color: '#DC2626', change: '-٣٪',  up: false },
]

const gateways = [
  { name: 'مدى',   icon: 'fa-solid fa-credit-card', bg: '#E0F4FF', color: '#0E7490', amount: 9200,  pct: 50, txCount: 118 },
  { name: 'تابي',  icon: 'fa-solid fa-clock-rotate-left', bg: '#F5F3FF', color: '#5B21B6', amount: 5510, pct: 30, txCount: 72 },
  { name: 'تمارا', icon: 'fa-solid fa-hand-holding-dollar', bg: '#F0FDF4', color: '#15803D', amount: 3740, pct: 20, txCount: 44 },
]

const planSplit = [
  { name: 'باقة الأبطال (٩٩ ر.س)', color: '#EC4899', pct: 60, amount: 11070 },
  { name: 'باقة المدارس (١٩٩ ر.س)', color: '#8B5CF6', pct: 25, amount: 4612 },
  { name: 'الباقة التجريبية',        color: '#94A3B8', pct: 15, amount: 2768 },
]

const monthlyRevenue = [
  { month: 'يناير',  amount: 9200,  current: false },
  { month: 'فبراير', amount: 11400, current: false },
  { month: 'مارس',   amount: 13800, current: false },
  { month: 'أبريل',  amount: 12600, current: false },
  { month: 'مايو',   amount: 18450, current: true },
]
const maxMonthly = computed(() => Math.max(...monthlyRevenue.map(m => m.amount)))

const transactions = ref([
  { id: 'TXN-٢٠٢٦-٠٠١', user: 'أحمد العمري',    plan: 'باقة الأبطال',   gateway: 'مدى',   amount: 99,  date: '٢٠٢٦/٠٥/٢٨', status: 'success'  },
  { id: 'TXN-٢٠٢٦-٠٠٢', user: 'سارة الزهراني',   plan: 'باقة الأبطال',   gateway: 'تابي',  amount: 99,  date: '٢٠٢٦/٠٥/٢٧', status: 'success'  },
  { id: 'TXN-٢٠٢٦-٠٠٣', user: 'محمد الشمري',     plan: 'باقة المدارس',  gateway: 'تمارا', amount: 199, date: '٢٠٢٦/٠٥/٢٦', status: 'success'  },
  { id: 'TXN-٢٠٢٦-٠٠٤', user: 'نورة القحطاني',   plan: 'الباقة التجريبية',gateway: 'مدى',  amount: 0,   date: '٢٠٢٦/٠٥/٢٦', status: 'success'  },
  { id: 'TXN-٢٠٢٦-٠٠٥', user: 'فهد الدوسري',     plan: 'باقة الأبطال',   gateway: 'مدى',   amount: 99,  date: '٢٠٢٦/٠٥/٢٥', status: 'pending'  },
  { id: 'TXN-٢٠٢٦-٠٠٦', user: 'عبدالله السالم',  plan: 'باقة المدارس',  gateway: 'تابي',  amount: 199, date: '٢٠٢٦/٠٥/٢٤', status: 'success'  },
  { id: 'TXN-٢٠٢٦-٠٠٧', user: 'منيرة الحربي',    plan: 'باقة الأبطال',   gateway: 'تمارا', amount: 99,  date: '٢٠٢٦/٠٥/٢٣', status: 'failed'   },
  { id: 'TXN-٢٠٢٦-٠٠٨', user: 'خالد العتيبي',    plan: 'باقة الأبطال',   gateway: 'مدى',   amount: 99,  date: '٢٠٢٦/٠٥/٢٢', status: 'refunded' },
  { id: 'TXN-٢٠٢٦-٠٠٩', user: 'د. نورة الغامدي', plan: 'باقة المدارس',  gateway: 'تابي',  amount: 199, date: '٢٠٢٦/٠٥/٢٠', status: 'success'  },
  { id: 'TXN-٢٠٢٦-٠١٠', user: 'أ. سعد المالكي',  plan: 'باقة المدارس',  gateway: 'تمارا', amount: 199, date: '٢٠٢٦/٠٥/١٨', status: 'success'  },
])

const filteredTx = computed(() =>
  transactions.value.filter(tx => {
    const gw = !txGateway.value || tx.gateway === txGateway.value
    const st = !txStatus.value  || tx.status === txStatus.value
    const sr = !txSearch.value  || tx.user.includes(txSearch.value) || tx.id.includes(txSearch.value)
    return gw && st && sr
  })
)

const filteredTotal = computed(() =>
  filteredTx.value.filter(tx => tx.status === 'success').reduce((s, tx) => s + tx.amount, 0)
)

const statusLabel = (s) => ({ success: 'ناجحة', pending: 'معلّقة', failed: 'فاشلة', refunded: 'مُستردّة' }[s] ?? s)
const gwIcon = (gw) => ({ مدى: 'fa-solid fa-credit-card', تابي: 'fa-solid fa-clock-rotate-left', تمارا: 'fa-solid fa-hand-holding-dollar' }[gw] ?? 'fa-solid fa-money-bill')
const avatarColor = (name) => {
  const colors = ['#38BDF8','#EC4899','#84CC16','#F59E0B','#8B5CF6']
  return colors[name.charCodeAt(0) % colors.length]
}

const viewTx = (tx) => { selectedTx.value = tx; txModal.value = true }
const refundTx = (tx) => {
  if (confirm(`استرداد ${tx.amount} ر.س للمستخدم "${tx.user}"؟`)) {
    tx.status = 'refunded'
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
