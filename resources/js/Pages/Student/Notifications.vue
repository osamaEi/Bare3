<template>
  <StudentLayout page-title="الإشعارات">
    <div class="head">
      <h1 class="title">🔔 الإشعارات</h1>
      <button v-if="unread_count > 0" class="btn-all" @click="markAll">تعليم الكل كمقروء</button>
    </div>

    <div v-if="notifications.length === 0" class="empty">
      <i class="fa-solid fa-bell-slash"></i>
      <p>لا توجد إشعارات حالياً</p>
    </div>

    <div v-else class="list">
      <div v-for="n in notifications" :key="n.id" class="note" :class="[n.type, { unread: !n.read }]" @click="!n.read && markRead(n.id)">
        <div class="note-ic" :class="n.type">
          <i :class="iconFor(n.type)"></i>
        </div>
        <div class="note-body">
          <div class="note-top">
            <span class="note-title">{{ n.title }}</span>
            <span v-if="!n.read" class="dot"></span>
          </div>
          <p class="note-text">{{ n.body }}</p>
          <span class="note-time">{{ n.created_at }}<span v-if="n.sender"> · {{ n.sender }}</span></span>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

defineProps({
  notifications: { type: Array, default: () => [] },
  unread_count:  { type: Number, default: 0 },
})

const markRead = (id) => router.patch(route('student.notifications.read', id), {}, { preserveScroll: true })
const markAll = () => router.patch(route('student.notifications.readAll'), {}, { preserveScroll: true })

const iconFor = (t) => ({ info: 'fa-solid fa-circle-info', success: 'fa-solid fa-circle-check', warning: 'fa-solid fa-triangle-exclamation' }[t] ?? 'fa-solid fa-bell')
</script>

<style scoped>
.head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
.title { font-size: 1.6rem; font-weight: 900; color: #1e1b2e; }
.btn-all { background: #fff; border: 1.5px solid #f0eef7; color: #5f4398; font-weight: 700; font-size: .82rem; padding: .5rem 1.1rem; border-radius: 10px; cursor: pointer; }
.btn-all:hover { border-color: #7c5cbf; }

.empty { text-align: center; color: #94A3B8; padding: 4rem 1rem; }
.empty i { font-size: 3rem; margin-bottom: 1rem; display: block; opacity: .5; }
.empty p { font-weight: 700; }

.list { display: flex; flex-direction: column; gap: .8rem; }
.note { display: flex; gap: 1rem; background: #fff; border: 1.5px solid #f0eef7; border-radius: 14px; padding: 1rem 1.2rem; transition: box-shadow .2s; }
.note.unread { cursor: pointer; border-color: #c9b4f0; background: #faf8fe; }
.note.unread:hover { box-shadow: 0 4px 14px rgba(56,189,248,.15); }
.note-ic { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
.note-ic.info { background: #f3effb; color: #5f4398; }
.note-ic.success { background: #DCFCE7; color: #15803D; }
.note-ic.warning { background: #fdf3e3; color: #B45309; }
.note-body { flex: 1; }
.note-top { display: flex; align-items: center; gap: .5rem; }
.note-title { font-weight: 800; color: #1e1b2e; font-size: .98rem; }
.dot { width: 9px; height: 9px; border-radius: 50%; background: #7c5cbf; }
.note-text { color: #475569; font-size: .9rem; line-height: 1.7; margin: .3rem 0; }
.note-time { font-size: .76rem; color: #94A3B8; font-weight: 600; }
</style>
