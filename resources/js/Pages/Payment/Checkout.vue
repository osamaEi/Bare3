<template>
  <component :is="layout" page-title="الاشتراك والدفع">
    <div class="head">
      <h1 class="title">💳 اختر باقتك وادفع</h1>
      <p class="sub">اشترك بأمان عبر بوابة PayTabs</p>
    </div>

    <div v-if="flashError" class="flash err"><i class="fa-solid fa-circle-exclamation"></i> {{ flashError }}</div>
    <div v-if="!gateway_ready" class="flash warn"><i class="fa-solid fa-triangle-exclamation"></i> بوابة الدفع غير مُهيّأة حالياً. تواصل مع الإدارة.</div>

    <div v-if="current" class="current-sub">
      <i class="fa-solid fa-circle-check"></i>
      اشتراكك الحالي: <b>{{ current.plan?.name }}</b> — ينتهي في {{ formatDate(current.ends_at) }}
    </div>

    <!-- اختيار الابن المستفيد (لولي الأمر) -->
    <div v-if="isParent" class="child-pick">
      <label class="child-label"><i class="fa-solid fa-child"></i> اختر الابن المستفيد من الاشتراك</label>
      <div v-if="children.length === 0" class="no-children">لا يوجد أبناء مرتبطون بحسابك. أضف ابنًا أولاً من «أبنائي».</div>
      <div v-else class="child-options">
        <button v-for="ch in children" :key="ch.id" type="button"
                class="child-chip" :class="{ active: childId === ch.id }" @click="childId = ch.id">
          <span class="ch-avatar">{{ ch.name.charAt(0) }}</span> {{ ch.name }}
        </button>
      </div>
    </div>

    <div class="plans-grid">
      <div v-for="p in plans" :key="p.id" class="plan" :class="{ selected: selected === p.id }" @click="selected = p.id">
        <div class="radio"><span v-if="selected === p.id"></span></div>
        <div class="plan-name">{{ p.name }}</div>
        <div class="plan-price">{{ p.price }} <span>{{ p.currency }} / {{ cycleLabel(p.cycle) }}</span></div>
        <ul class="plan-feats">
          <li v-for="(f, i) in p.features" :key="i"><i class="fa-solid fa-check"></i> {{ f }}</li>
        </ul>
      </div>
    </div>

    <div class="pay-bar">
      <button class="btn-pay" :disabled="!canPay || processing" @click="pay">
        <i class="fa-solid fa-lock"></i> {{ processing ? 'جاري التحويل...' : 'ادفع الآن بأمان' }}
      </button>
      <p class="secure-note"><i class="fa-solid fa-shield-halved"></i> الدفع يتم على صفحة PayTabs الآمنة</p>
    </div>
  </component>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import ParentLayout from '@/Layouts/ParentLayout.vue'

const props = defineProps({
  plans:         { type: Array, default: () => [] },
  current:       { type: Object, default: null },
  gateway_ready: { type: Boolean, default: false },
  children:      { type: Array, default: () => [] },
})

const page = usePage()
const role = computed(() => page.props.auth?.user?.role)
const isParent = computed(() => role.value === 'parent')
const layout = computed(() => (isParent.value ? ParentLayout : StudentLayout))
const flashError = computed(() => page.props.flash?.error)

const selected = ref(props.plans[0]?.id ?? null)
const childId = ref(props.children[0]?.id ?? null)
const processing = ref(false)

// للوالد: لازم يختار ابنًا. لغيره: غير مطلوب.
const canPay = computed(() => selected.value && props.gateway_ready && (!isParent.value || childId.value))

const pay = () => {
  if (!canPay.value) return
  processing.value = true
  router.post(route('payment.pay'), { plan_id: selected.value, child_id: isParent.value ? childId.value : null }, {
    onFinish: () => { processing.value = false },
  })
}

const cycleLabel = (c) => ({ monthly: 'شهري', yearly: 'سنوي' }[c] ?? c)
const formatDate = (d) => d ? new Date(d).toLocaleDateString('ar-EG') : '—'
</script>

<style scoped>
.head { margin-bottom: 1.5rem; }
.title { font-size: 1.6rem; font-weight: 900; color: #1E293B; }
.sub { font-size: .9rem; color: #94A3B8; margin-top: .2rem; }

.flash { border-radius: 12px; padding: .85rem 1.1rem; font-weight: 700; margin-bottom: 1.2rem; display: flex; align-items: center; gap: .5rem; font-size: .9rem; }
.flash.err { background: #FEF2F2; border: 1.5px solid #FECACA; color: #DC2626; }
.flash.warn { background: #FEF3C7; border: 1.5px solid #FDE68A; color: #B45309; }
.current-sub { background: #F0FDF4; border: 1.5px solid #BBF7D0; color: #15803D; border-radius: 12px; padding: .85rem 1.1rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: .5rem; font-size: .9rem; }

.child-pick { background: #fff; border: 1.5px solid #E2E8F0; border-radius: 14px; padding: 1.2rem; margin-bottom: 1.5rem; }
.child-label { display: flex; align-items: center; gap: .5rem; font-weight: 800; color: #1E293B; font-size: .95rem; margin-bottom: .9rem; }
.child-label i { color: #38BDF8; }
.no-children { color: #94A3B8; font-size: .88rem; font-weight: 600; }
.child-options { display: flex; flex-wrap: wrap; gap: .7rem; }
.child-chip { display: inline-flex; align-items: center; gap: .5rem; background: #F8FAFC; border: 2px solid #E2E8F0; border-radius: 50px; padding: .45rem 1rem .45rem .5rem; font-family: inherit; font-weight: 700; font-size: .88rem; color: #475569; cursor: pointer; transition: all .2s; }
.child-chip:hover { border-color: #BAE6FD; }
.child-chip.active { border-color: #38BDF8; background: #F0F9FF; color: #0E7490; }
.ch-avatar { width: 28px; height: 28px; border-radius: 50%; background: #38BDF8; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: .85rem; }

.plans-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.2rem; margin-bottom: 2rem; }
.plan { background: #fff; border: 2px solid #E2E8F0; border-radius: 18px; padding: 1.5rem; cursor: pointer; transition: border-color .2s, box-shadow .2s; position: relative; }
.plan:hover { border-color: #BAE6FD; }
.plan.selected { border-color: #38BDF8; box-shadow: 0 4px 18px rgba(56,189,248,.18); }
.radio { position: absolute; top: 1.3rem; left: 1.3rem; width: 20px; height: 20px; border: 2px solid #CBD5E1; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.plan.selected .radio { border-color: #38BDF8; }
.radio span { width: 10px; height: 10px; border-radius: 50%; background: #38BDF8; }
.plan-name { font-size: 1.2rem; font-weight: 900; color: #1E293B; margin-bottom: .5rem; }
.plan-price { font-size: 1.8rem; font-weight: 900; color: #38BDF8; margin-bottom: 1rem; }
.plan-price span { font-size: .8rem; font-weight: 700; color: #94A3B8; }
.plan-feats { list-style: none; padding: 0; margin: 0; }
.plan-feats li { display: flex; align-items: center; gap: .5rem; font-size: .85rem; color: #475569; padding: .25rem 0; }
.plan-feats li i { color: #16A34A; font-size: .72rem; }

.pay-bar { text-align: center; }
.btn-pay { background: #16A34A; color: #fff; border: none; font-family: inherit; font-weight: 800; font-size: 1.1rem; padding: 1rem 3rem; border-radius: 50px; cursor: pointer; box-shadow: 0 5px 0 #15803D; transition: transform .15s; display: inline-flex; align-items: center; gap: .6rem; }
.btn-pay:hover:not(:disabled) { transform: translateY(-2px); }
.btn-pay:disabled { opacity: .5; cursor: not-allowed; box-shadow: 0 5px 0 #94A3B8; }
.secure-note { font-size: .8rem; color: #94A3B8; font-weight: 600; margin-top: .9rem; display: flex; align-items: center; justify-content: center; gap: .4rem; }
</style>
