<template>
  <Head title="الفواتير والاشتراكات" />
  <ParentLayout page-title="الفواتير والاشتراكات">
    <h1 class="title">الفواتير والاشتراكات</h1>

    <!-- Subscription -->
    <div class="card sub-card" :class="{ none: !billing.subscription }">
      <div v-if="billing.subscription">
        <div class="sub-top">
          <div>
            <div class="sub-label">الاشتراك الحالي</div>
            <div class="sub-plan">{{ billing.subscription.plan }}</div>
          </div>
          <span class="sub-status" :class="billing.subscription.status">{{ statusLabel(billing.subscription.status) }}</span>
        </div>
        <div class="sub-meta">
          <span><i class="fa-solid fa-calendar-day"></i> من {{ billing.subscription.starts_at }}</span>
          <span><i class="fa-solid fa-calendar-xmark"></i> إلى {{ billing.subscription.ends_at }}</span>
          <span><i class="fa-solid fa-rotate"></i> {{ billing.subscription.auto_renew ? 'تجديد تلقائي' : 'بدون تجديد' }}</span>
        </div>
      </div>
      <div v-else class="no-sub">
        <i class="fa-solid fa-circle-info"></i>
        <p>لا يوجد اشتراك نشط حالياً</p>
      </div>
    </div>

    <!-- Transactions -->
    <div class="card">
      <h3 class="card-title"><i class="fa-solid fa-receipt"></i> سجل المدفوعات</h3>
      <div v-if="billing.transactions.length === 0" class="empty">لا توجد عمليات دفع</div>
      <table v-else class="tbl">
        <thead><tr><th>الباقة</th><th>المبلغ</th><th>البوابة</th><th>الحالة</th><th>التاريخ</th></tr></thead>
        <tbody>
          <tr v-for="t in billing.transactions" :key="t.id">
            <td>{{ t.plan }}</td>
            <td class="amount">{{ t.amount.toLocaleString('ar-EG') }} ر.س</td>
            <td><span class="gw">{{ gwLabel(t.gateway) }}</span></td>
            <td><span class="status" :class="t.status">{{ txStatusLabel(t.status) }}</span></td>
            <td class="date">{{ t.date }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </ParentLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import ParentLayout from '@/Layouts/ParentLayout.vue'

defineProps({ billing: { type: Object, required: true } })
const statusLabel = (s) => ({ active: 'نشط', expired: 'منتهٍ', cancelled: 'ملغى', trial: 'تجريبي' }[s] ?? s)
const txStatusLabel = (s) => ({ success: 'ناجحة', pending: 'معلّقة', failed: 'فاشلة', refunded: 'مستردّة' }[s] ?? s)
const gwLabel = (g) => ({ mada: 'مدى', tabby: 'تابي', tamara: 'تمارا' }[g] ?? g)
</script>

<style scoped>
.title { font-size: 1.5rem; font-weight: 800; color: #1E293B; margin-bottom: 1.4rem; }
.card { background: #fff; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 12px rgba(0,0,0,.05); margin-bottom: 1.4rem; }
.sub-card { background: linear-gradient(135deg, #EC4899, #38BDF8); color: #fff; }
.sub-card.none { background: #fff; color: inherit; }
.sub-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem; }
.sub-label { font-size: .8rem; opacity: .8; }
.sub-plan { font-size: 1.4rem; font-weight: 800; }
.sub-status { font-size: .76rem; font-weight: 800; padding: .25rem .8rem; border-radius: 50px; background: rgba(255,255,255,.25); }
.sub-meta { display: flex; gap: 1.5rem; font-size: .85rem; font-weight: 600; opacity: .9; flex-wrap: wrap; }
.no-sub { text-align: center; padding: 1.5rem; color: #94A3B8; font-weight: 600; }
.no-sub i { font-size: 2rem; display: block; margin-bottom: .6rem; color: #CBD5E1; }

.card-title { font-size: 1rem; font-weight: 800; color: #1E293B; display: flex; align-items: center; gap: .5rem; margin-bottom: 1.2rem; }
.card-title i { color: #EC4899; }
.empty { color: #94A3B8; text-align: center; padding: 1.5rem; font-weight: 600; }

.tbl { width: 100%; border-collapse: collapse; }
.tbl th { text-align: right; padding: .7rem; font-size: .78rem; font-weight: 700; color: #94A3B8; background: #FAFAFA; border-bottom: 1px solid #F1F5F9; }
.tbl td { padding: .8rem .7rem; border-bottom: 1px solid #F8FAFC; font-size: .88rem; color: #1E293B; }
.amount { font-weight: 800; }
.date { color: #94A3B8; }
.gw { font-size: .74rem; font-weight: 700; padding: .15rem .6rem; border-radius: 50px; background: #F1F5F9; color: #475569; }
.status { font-size: .74rem; font-weight: 800; padding: .15rem .6rem; border-radius: 50px; }
.status.success { background: #F0FDF4; color: #15803D; }
.status.pending { background: #FEF3C7; color: #92400E; }
.status.failed { background: #FEF2F2; color: #DC2626; }
.status.refunded { background: #F1F5F9; color: #64748B; }
</style>
