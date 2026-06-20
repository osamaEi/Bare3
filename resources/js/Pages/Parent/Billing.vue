<template>
  <Head title="الفواتير والاشتراكات" />
  <ParentLayout page-title="الفواتير والاشتراكات">
    <h1 class="title">الفواتير والاشتراكات</h1>

    <div v-if="billing.children.length === 0" class="card empty-card">
      <i class="fa-solid fa-children"></i>
      <p>لا يوجد أبناء مرتبطون بحسابك بعد.</p>
    </div>

    <!-- بطاقة لكل طالب -->
    <div v-for="child in billing.children" :key="child.id" class="child-block">
      <div class="child-head">
        <span class="avatar">{{ child.name.charAt(0) }}</span>
        <h2 class="child-name">{{ child.name }}</h2>
        <Link :href="route('payment.checkout')" class="btn-sub"><i class="fa-solid fa-credit-card"></i> اشترِ / جدّد</Link>
      </div>

      <!-- اشتراك الطالب -->
      <div class="card sub-card" :class="{ none: !child.subscription }">
        <div v-if="child.subscription">
          <div class="sub-top">
            <div>
              <div class="sub-label">الاشتراك الحالي</div>
              <div class="sub-plan">{{ child.subscription.plan }}</div>
            </div>
            <span class="sub-status" :class="child.subscription.status">{{ statusLabel(child.subscription.status) }}</span>
          </div>
          <div class="sub-meta">
            <span><i class="fa-solid fa-calendar-day"></i> من {{ child.subscription.starts_at }}</span>
            <span><i class="fa-solid fa-calendar-xmark"></i> إلى {{ child.subscription.ends_at }}</span>
            <span><i class="fa-solid fa-rotate"></i> {{ child.subscription.auto_renew ? 'تجديد تلقائي' : 'بدون تجديد' }}</span>
          </div>
        </div>
        <div v-else class="no-sub">
          <i class="fa-solid fa-circle-info"></i>
          <p>لا يوجد اشتراك نشط لهذا الطالب</p>
        </div>
      </div>

      <!-- مدفوعات الطالب -->
      <div class="card">
        <h3 class="card-title"><i class="fa-solid fa-receipt"></i> سجل المدفوعات</h3>
        <div v-if="child.transactions.length === 0" class="empty">لا توجد عمليات دفع</div>
        <table v-else class="tbl">
          <thead><tr><th>الباقة</th><th>المبلغ</th><th>البوابة</th><th>الحالة</th><th>التاريخ</th></tr></thead>
          <tbody>
            <tr v-for="t in child.transactions" :key="t.id">
              <td>{{ t.plan }}</td>
              <td class="amount">{{ t.amount.toLocaleString('ar-EG') }} <i class="fa-solid fa-saudi-riyal-symbol"></i></td>
              <td><span class="gw">{{ gwLabel(t.gateway) }}</span></td>
              <td><span class="status" :class="t.status">{{ txStatusLabel(t.status) }}</span></td>
              <td class="date">{{ t.date }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- مدفوعات أخرى (غير منسوبة لطالب) -->
    <div v-if="billing.other_transactions.length" class="card">
      <h3 class="card-title"><i class="fa-solid fa-receipt"></i> مدفوعات أخرى</h3>
      <table class="tbl">
        <thead><tr><th>الباقة</th><th>المبلغ</th><th>البوابة</th><th>الحالة</th><th>التاريخ</th></tr></thead>
        <tbody>
          <tr v-for="t in billing.other_transactions" :key="t.id">
            <td>{{ t.plan }}</td>
            <td class="amount">{{ t.amount.toLocaleString('ar-EG') }} <i class="fa-solid fa-saudi-riyal-symbol"></i></td>
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
import { Head, Link } from '@inertiajs/vue3'
import ParentLayout from '@/Layouts/ParentLayout.vue'

defineProps({ billing: { type: Object, required: true } })
const statusLabel = (s) => ({ active: 'نشط', expired: 'منتهٍ', cancelled: 'ملغى', trial: 'تجريبي' }[s] ?? s)
const txStatusLabel = (s) => ({ success: 'ناجحة', pending: 'معلّقة', failed: 'فاشلة', refunded: 'مستردّة' }[s] ?? s)
const gwLabel = (g) => ({ mada: 'مدى', tabby: 'تابي', tamara: 'تمارا', paytabs: 'PayTabs' }[g] ?? g)
</script>

<style scoped>
.title { font-size: 1.5rem; font-weight: 800; color: #1E293B; margin-bottom: 1.4rem; }

.child-block { margin-bottom: 2rem; }
.child-head { display: flex; align-items: center; gap: .8rem; margin-bottom: 1rem; }
.avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #EC4899, #38BDF8); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; }
.child-name { font-size: 1.25rem; font-weight: 800; color: #1E293B; flex: 1; }
.btn-sub { background: #38BDF8; color: #fff; font-weight: 700; font-size: .82rem; padding: .5rem 1.1rem; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: .4rem; }
.btn-sub:hover { background: #0EA5E9; }

.card { background: #fff; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 12px rgba(0,0,0,.05); margin-bottom: 1rem; }
.empty-card { text-align: center; color: #94A3B8; padding: 2.5rem; }
.empty-card i { font-size: 2.5rem; display: block; margin-bottom: .8rem; color: #CBD5E1; }
.empty-card p { font-weight: 700; }

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
