<template>
  <AdminLayout page-title="تفاصيل العملية">
    <div class="page-header">
      <div>
        <Link :href="route('admin.payments.index')" class="back"><i class="fa-solid fa-chevron-right"></i> المدفوعات</Link>
        <h1 class="page-title">تفاصيل العملية #{{ transaction.id }}</h1>
      </div>
      <button v-if="transaction.status === 'success'" class="btn-refund" :disabled="refunding" @click="refund">
        <i class="fa-solid fa-rotate-left"></i> استرداد المبلغ
      </button>
    </div>

    <div class="detail-grid">
      <!-- Transaction -->
      <div class="card">
        <div class="card-title"><i class="fa-solid fa-receipt"></i> بيانات العملية</div>
        <div class="row"><span>المبلغ</span><strong>{{ money(transaction.amount) }}</strong></div>
        <div class="row"><span>البوابة</span><span class="gw" :class="transaction.gateway">{{ gwLabel(transaction.gateway) }}</span></div>
        <div class="row"><span>الحالة</span><span class="status" :class="transaction.status">{{ statusLabel(transaction.status) }}</span></div>
        <div class="row"><span>معرّف البوابة</span><strong class="mono">{{ transaction.gateway_tx_id }}</strong></div>
        <div class="row"><span>التاريخ</span><strong>{{ fmt(transaction.created_at) }}</strong></div>
        <div class="row" v-if="transaction.refunded_at"><span>تاريخ الاسترداد</span><strong>{{ fmt(transaction.refunded_at) }}</strong></div>
      </div>

      <!-- Customer -->
      <div class="card">
        <div class="card-title"><i class="fa-solid fa-user"></i> العميل</div>
        <div class="row"><span>الاسم</span><strong>{{ transaction.user?.name ?? '—' }}</strong></div>
        <div class="row"><span>البريد</span><strong>{{ transaction.user?.email ?? '—' }}</strong></div>
        <div class="row"><span>الباقة</span><strong>{{ transaction.subscription?.plan?.name ?? '—' }}</strong></div>
      </div>

      <!-- Invoice -->
      <div class="card" v-if="transaction.invoice">
        <div class="card-title"><i class="fa-solid fa-file-invoice"></i> الفاتورة</div>
        <div class="row"><span>رقم الفاتورة</span><strong class="mono">{{ transaction.invoice.invoice_number }}</strong></div>
        <div class="row"><span>المبلغ</span><strong>{{ money(transaction.invoice.amount) }}</strong></div>
        <div class="row"><span>الضريبة</span><strong>{{ money(transaction.invoice.tax_amount) }}</strong></div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ transaction: { type: Object, required: true } })

const refunding = ref(false)

const money = (v) => Number(v ?? 0).toLocaleString('ar-EG', { minimumFractionDigits: 2 }) + ' ر.س'
const fmt = (d) => d ? new Date(d).toLocaleString('ar-EG') : '—'
const gwLabel = (g) => ({ mada: 'مدى', tabby: 'تابي', tamara: 'تمارا' }[g] ?? g)
const statusLabel = (s) => ({ success: 'ناجحة', pending: 'معلّقة', failed: 'فاشلة', refunded: 'مستردّة' }[s] ?? s)

const refund = () => {
  if (!confirm('هل تريد استرداد هذا المبلغ؟')) return
  refunding.value = true
  router.patch(route('admin.payments.refund', props.transaction.id), {}, {
    preserveScroll: true,
    onFinish: () => { refunding.value = false },
  })
}
</script>

<style scoped>
.page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
.back { font-size: .82rem; color: #94A3B8; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: .3rem; }
.page-title { font-size: 1.5rem; font-weight: 900; color: #1E293B; margin-top: .3rem; }
.btn-refund { background: #FEF2F2; color: #DC2626; border: 1.5px solid #FECACA; font-family: inherit; font-weight: 700; font-size: .88rem; padding: .65rem 1.4rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: .5rem; }
.btn-refund:disabled { opacity: .5; cursor: not-allowed; }

.detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.2rem; }
.card { background: white; border-radius: 16px; box-shadow: 0 1px 8px rgba(0,0,0,.05); padding: 1.5rem; }
.card-title { font-size: 1rem; font-weight: 800; color: #1E293B; display: flex; align-items: center; gap: .5rem; margin-bottom: 1.2rem; }
.card-title i { color: #38BDF8; }
.row { display: flex; justify-content: space-between; padding: .6rem 0; border-bottom: 1px solid #F8FAFC; font-size: .9rem; }
.row:last-child { border-bottom: none; }
.row span { color: #94A3B8; font-weight: 600; }
.row strong { color: #1E293B; font-weight: 700; }
.mono { font-family: monospace; font-size: .82rem; }
.gw { font-size: .75rem; font-weight: 800; padding: .2rem .7rem; border-radius: 50px; }
.gw.mada { background: #E0F4FF; color: #0E7490; }
.gw.tabby { background: #F0FDF4; color: #15803D; }
.gw.tamara { background: #FCE7F3; color: #9D174D; }
.status { font-size: .75rem; font-weight: 800; padding: .2rem .7rem; border-radius: 50px; }
.status.success { background: #F0FDF4; color: #15803D; }
.status.pending { background: #FEF3C7; color: #92400E; }
.status.failed { background: #FEF2F2; color: #DC2626; }
.status.refunded { background: #F1F5F9; color: #64748B; }
</style>
