<template>
  <AdminLayout page-title="رسائل التواصل">
    <div class="page-header">
      <div>
        <h1 class="page-title">رسائل التواصل</h1>
        <p class="page-sub">الرسائل الواردة من صفحة «تواصل معنا»</p>
      </div>
      <div class="hs"><span class="hs-val">{{ unread }}</span><span class="hs-lbl">غير مقروء</span></div>
    </div>

    <div class="card">
      <div v-if="messages.length === 0" class="empty"><i class="fa-solid fa-inbox"></i><p>لا توجد رسائل بعد</p></div>
      <div v-else class="list">
        <div v-for="m in messages" :key="m.id" class="msg" :class="{ unread: !m.is_read }" @click="open(m)">
          <div class="msg-main">
            <div class="msg-top">
              <span class="msg-name">{{ m.name }}</span>
              <span v-if="!m.is_read" class="dot"></span>
              <span class="msg-date">{{ m.created_at }}</span>
            </div>
            <div class="msg-subject" v-if="m.subject">{{ m.subject }}</div>
            <div class="msg-preview">{{ m.message }}</div>
            <div class="msg-contact"><i class="fa-solid fa-envelope"></i> {{ m.email }}<template v-if="m.phone"> · <i class="fa-solid fa-phone"></i> {{ m.phone }}</template></div>
          </div>
          <button class="btn-del" @click.stop="del(m.id)"><i class="fa-solid fa-trash"></i></button>
        </div>
      </div>
    </div>

    <!-- Detail modal -->
    <div v-if="active" class="modal-bg" @click.self="active = null">
      <div class="modal">
        <h3 class="modal-title">{{ active.subject || 'رسالة جديدة' }}</h3>
        <div class="m-row"><b>الاسم:</b> {{ active.name }}</div>
        <div class="m-row"><b>البريد:</b> {{ active.email }}</div>
        <div class="m-row" v-if="active.phone"><b>الهاتف:</b> {{ active.phone }}</div>
        <div class="m-row"><b>التاريخ:</b> {{ active.created_at }}</div>
        <div class="m-body">{{ active.message }}</div>
        <div class="modal-actions">
          <a :href="`mailto:${active.email}`" class="btn-reply"><i class="fa-solid fa-reply"></i> الرد بالبريد</a>
          <button class="btn-close" @click="active = null">إغلاق</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
  messages: { type: Array, default: () => [] },
  unread:   { type: Number, default: 0 },
})

const active = ref(null)

const open = (m) => {
  active.value = m
  if (!m.is_read) router.patch(route('admin.contact-messages.read', m.id), {}, { preserveScroll: true, preserveState: true })
}
const del = (id) => {
  if (confirm('حذف هذه الرسالة؟')) router.delete(route('admin.contact-messages.destroy', id), { preserveScroll: true })
}
</script>

<style scoped>
.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
.page-title { font-size: 1.6rem; font-weight: 900; color: #1E293B; }
.page-sub { font-size: .88rem; color: #94A3B8; margin-top: .2rem; }
.hs { background: #fff; border: 1.5px solid #E2E8F0; border-radius: 12px; padding: .6rem 1.2rem; text-align: center; }
.hs-val { display: block; font-size: 1.4rem; font-weight: 900; color: #38BDF8; }
.hs-lbl { font-size: .75rem; color: #94A3B8; font-weight: 700; }

.card { background: white; border-radius: 16px; box-shadow: 0 1px 8px rgba(0,0,0,.05); padding: 1.2rem; }
.empty { text-align: center; color: #94A3B8; padding: 3rem; }
.empty i { font-size: 2.5rem; margin-bottom: .8rem; display: block; opacity: .5; }
.empty p { font-weight: 700; }

.list { display: flex; flex-direction: column; gap: .7rem; }
.msg { display: flex; gap: 1rem; align-items: flex-start; background: #fff; border: 1.5px solid #E2E8F0; border-radius: 12px; padding: 1rem 1.2rem; cursor: pointer; transition: box-shadow .2s; }
.msg:hover { box-shadow: 0 4px 14px rgba(0,0,0,.07); }
.msg.unread { border-color: #BAE6FD; background: #F0F9FF; }
.msg-main { flex: 1; }
.msg-top { display: flex; align-items: center; gap: .5rem; }
.msg-name { font-weight: 800; color: #1E293B; }
.dot { width: 8px; height: 8px; border-radius: 50%; background: #38BDF8; }
.msg-date { margin-right: auto; font-size: .76rem; color: #94A3B8; }
.msg-subject { font-weight: 700; color: #475569; font-size: .88rem; margin-top: .3rem; }
.msg-preview { color: #64748B; font-size: .85rem; margin: .3rem 0; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
.msg-contact { font-size: .76rem; color: #94A3B8; }
.btn-del { background: #FEF2F2; color: #DC2626; border: none; width: 34px; height: 34px; border-radius: 8px; cursor: pointer; flex-shrink: 0; }
.btn-del:hover { background: #FEE2E2; }

.modal-bg { position: fixed; inset: 0; background: rgba(15,23,42,.5); display: flex; align-items: center; justify-content: center; z-index: 200; padding: 1rem; }
.modal { background: #fff; border-radius: 16px; padding: 1.6rem; width: 100%; max-width: 480px; box-shadow: 0 20px 60px rgba(0,0,0,.2); }
.modal-title { font-size: 1.2rem; font-weight: 900; color: #1E293B; margin-bottom: 1rem; }
.m-row { font-size: .88rem; color: #475569; margin-bottom: .4rem; }
.m-row b { color: #1E293B; }
.m-body { background: #F8FAFC; border-radius: 10px; padding: 1rem; color: #334155; line-height: 1.8; font-size: .9rem; margin: 1rem 0; white-space: pre-wrap; }
.modal-actions { display: flex; justify-content: flex-end; gap: .6rem; }
.btn-reply { background: #38BDF8; color: #fff; font-weight: 700; font-size: .85rem; padding: .6rem 1.3rem; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: .4rem; }
.btn-close { background: #F1F5F9; color: #475569; border: none; font-family: inherit; font-weight: 700; font-size: .85rem; padding: .6rem 1.3rem; border-radius: 10px; cursor: pointer; }
</style>
