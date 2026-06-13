<template>
  <AdminLayout page-title="تذاكر الدعم">
    <div class="tickets-page">

      <!-- Header & filters -->
      <div class="page-header">
        <div>
          <h1 class="page-title">تذاكر الدعم</h1>
          <p class="page-sub">{{ tickets.length }} تذكرة إجمالاً</p>
        </div>
        <div class="filters">
          <button
            v-for="f in filters"
            :key="f.value"
            class="filter-btn"
            :class="{ active: activeFilter === f.value }"
            @click="activeFilter = f.value"
          >
            {{ f.label }}
            <span class="filter-count">{{ countByStatus(f.value) }}</span>
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-card">
        <div v-if="filteredTickets.length === 0" class="empty-state">
          <i class="fa-solid fa-ticket"></i>
          <p>لا توجد تذاكر في هذه الفئة</p>
        </div>
        <table v-else class="tickets-table">
          <thead>
            <tr>
              <th>#</th>
              <th>ولي الأمر</th>
              <th>الموضوع</th>
              <th>الحالة</th>
              <th>التقييم</th>
              <th>التاريخ</th>
              <th>إجراء</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ticket in filteredTickets" :key="ticket.id" @click="openTicket(ticket)" class="ticket-row">
              <td class="td-id">#{{ ticket.id }}</td>
              <td class="td-user">
                <div class="user-cell">
                  <div class="user-av">{{ ticket.user?.name?.[0] ?? 'أ' }}</div>
                  <span>{{ ticket.user?.name }}</span>
                </div>
              </td>
              <td class="td-subject">{{ ticket.subject }}</td>
              <td><span class="badge" :class="ticket.status">{{ statusLabel(ticket.status) }}</span></td>
              <td>
                <span v-if="ticket.rating" class="rating-stars">
                  <i v-for="s in 5" :key="s" class="fa-solid fa-star" :class="s <= ticket.rating ? 'star-on' : 'star-off'"></i>
                </span>
                <span v-else class="no-rating">—</span>
              </td>
              <td class="td-date">{{ formatDate(ticket.created_at) }}</td>
              <td @click.stop>
                <button class="action-btn" @click="openTicket(ticket)">
                  <i class="fa-solid fa-eye"></i>
                </button>
                <button
                  v-if="ticket.status !== 'closed'"
                  class="action-btn close-btn"
                  title="إغلاق"
                  @click="closeTicket(ticket)"
                >
                  <i class="fa-solid fa-xmark"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Ticket Detail Modal -->
      <div v-if="activeTicket" class="modal-overlay" @click.self="activeTicket = null">
        <div class="modal">
          <div class="modal-header">
            <div>
              <h2>{{ activeTicket.subject }}</h2>
              <div class="modal-meta">
                <span class="badge" :class="activeTicket.status">{{ statusLabel(activeTicket.status) }}</span>
                <span class="meta-user"><i class="fa-solid fa-user"></i> {{ activeTicket.user?.name }}</span>
                <span class="meta-date"><i class="fa-regular fa-clock"></i> {{ formatDate(activeTicket.created_at) }}</span>
              </div>
            </div>
            <button class="modal-close" @click="activeTicket = null"><i class="fa-solid fa-xmark"></i></button>
          </div>

          <!-- Conversation -->
          <div class="conversation">
            <div class="msg msg-parent">
              <div class="msg-avatar parent-av">{{ activeTicket.user?.name?.[0] ?? 'أ' }}</div>
              <div class="msg-bubble">
                <div class="msg-meta">{{ activeTicket.user?.name }} · {{ formatDate(activeTicket.created_at) }}</div>
                <p>{{ activeTicket.body }}</p>
              </div>
            </div>

            <div
              v-for="reply in activeTicket.replies"
              :key="reply.id"
              class="msg"
              :class="reply.user_id === activeTicket.user_id ? 'msg-parent' : 'msg-admin'"
            >
              <div class="msg-avatar" :class="reply.user_id === activeTicket.user_id ? 'parent-av' : 'admin-av'">
                {{ reply.user_id === activeTicket.user_id ? (activeTicket.user?.name?.[0] ?? 'أ') : 'م' }}
              </div>
              <div class="msg-bubble">
                <div class="msg-meta">
                  {{ reply.user_id === activeTicket.user_id ? activeTicket.user?.name : 'فريق الدعم' }} · {{ formatDate(reply.created_at) }}
                </div>
                <p>{{ reply.body }}</p>
              </div>
            </div>
          </div>

          <!-- Review (if exists) -->
          <div v-if="activeTicket.rating" class="review-display">
            <i class="fa-solid fa-star-half-stroke"></i>
            <span>تقييم ولي الأمر:</span>
            <i v-for="s in 5" :key="s" class="fa-solid fa-star" :class="s <= activeTicket.rating ? 'star-on' : 'star-off'"></i>
            <span v-if="activeTicket.review_comment" class="review-comment">· {{ activeTicket.review_comment }}</span>
          </div>

          <!-- Reply form -->
          <form v-if="activeTicket.status !== 'closed'" class="reply-form" @submit.prevent="submitReply">
            <textarea v-model="replyBody" rows="3" placeholder="اكتب ردك على ولي الأمر..." required></textarea>
            <div class="reply-actions">
              <button type="submit" class="btn-primary" :disabled="replyProcessing">
                <i class="fa-solid fa-paper-plane"></i> إرسال الرد
              </button>
              <button type="button" class="btn-close" @click="closeTicket(activeTicket)">
                <i class="fa-solid fa-lock"></i> إغلاق التذكرة
              </button>
            </div>
          </form>
          <div v-else class="closed-notice">
            <i class="fa-solid fa-lock"></i> هذه التذكرة مغلقة
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ tickets: Array })

const activeFilter = ref('all')
const activeTicket = ref(null)
const replyBody = ref('')
const replyProcessing = ref(false)

const filters = [
  { value: 'all',     label: 'الكل' },
  { value: 'open',    label: 'مفتوحة' },
  { value: 'replied', label: 'تم الرد' },
  { value: 'closed',  label: 'مغلقة' },
]

const filteredTickets = computed(() =>
  activeFilter.value === 'all'
    ? props.tickets
    : props.tickets.filter(t => t.status === activeFilter.value)
)

function countByStatus(s) {
  return s === 'all' ? props.tickets.length : props.tickets.filter(t => t.status === s).length
}

function statusLabel(s) {
  return { open: 'مفتوحة', replied: 'تم الرد', closed: 'مغلقة' }[s] ?? s
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function openTicket(ticket) {
  activeTicket.value = ticket
  replyBody.value = ''
}

function submitReply() {
  replyProcessing.value = true
  router.post(route('admin.tickets.reply', activeTicket.value.id), { body: replyBody.value }, {
    onSuccess: () => { replyBody.value = '' },
    onFinish: () => { replyProcessing.value = false }
  })
}

function closeTicket(ticket) {
  router.patch(route('admin.tickets.close', ticket.id), {}, {
    onSuccess: () => { activeTicket.value = null }
  })
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap');
* { box-sizing: border-box; margin: 0; padding: 0; }
.tickets-page { font-family: 'Cairo', sans-serif; direction: rtl; }

.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
.page-title { font-size: 1.5rem; font-weight: 800; color: #1E293B; }
.page-sub { color: #64748B; font-size: .88rem; margin-top: .2rem; }

.filters { display: flex; gap: .5rem; flex-wrap: wrap; }
.filter-btn { background: white; border: 1.5px solid #E2E8F0; color: #64748B; font-family: 'Cairo', sans-serif; font-weight: 700; font-size: .85rem; border-radius: 10px; padding: .45rem 1rem; cursor: pointer; transition: all .2s; display: flex; align-items: center; gap: .5rem; }
.filter-btn:hover { border-color: #38BDF8; color: #0284C7; }
.filter-btn.active { background: #38BDF8; border-color: #38BDF8; color: white; }
.filter-count { background: rgba(255,255,255,.3); border-radius: 20px; padding: .1rem .4rem; font-size: .75rem; }
.filter-btn:not(.active) .filter-count { background: #F1F5F9; color: #94A3B8; }

.table-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
.empty-state { text-align: center; padding: 4rem 2rem; color: #94A3B8; }
.empty-state i { font-size: 2.5rem; margin-bottom: .75rem; display: block; }

.tickets-table { width: 100%; border-collapse: collapse; font-size: .88rem; }
.tickets-table thead { background: #F8FAFC; }
.tickets-table th { padding: .9rem 1rem; text-align: right; font-weight: 800; color: #64748B; font-size: .8rem; border-bottom: 1px solid #F1F5F9; white-space: nowrap; }
.ticket-row { cursor: pointer; transition: background .15s; }
.ticket-row:hover { background: #F8FAFC; }
.tickets-table td { padding: .85rem 1rem; border-bottom: 1px solid #F8FAFC; color: #1E293B; vertical-align: middle; }
.td-id { color: #94A3B8; font-weight: 700; }
.td-subject { max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 700; }
.td-date { color: #94A3B8; font-size: .8rem; white-space: nowrap; }

.user-cell { display: flex; align-items: center; gap: .6rem; }
.user-av { width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, #EC4899, #F472B6); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: .8rem; flex-shrink: 0; }

.badge { font-size: .75rem; font-weight: 700; border-radius: 20px; padding: .25rem .75rem; }
.badge.open { background: #FEF9C3; color: #CA8A04; }
.badge.replied { background: #E0F4FF; color: #0284C7; }
.badge.closed { background: #F1F5F9; color: #64748B; }

.rating-stars { display: flex; gap: .1rem; }
.no-rating { color: #CBD5E1; }
.star-on { color: #FBBF24; }
.star-off { color: #E2E8F0; }

.action-btn { background: none; border: 1px solid #E2E8F0; color: #64748B; cursor: pointer; border-radius: 8px; width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; font-size: .85rem; margin-left: .3rem; transition: all .2s; }
.action-btn:hover { background: #F1F5F9; color: #1E293B; }
.close-btn:hover { border-color: #FCA5A5; color: #DC2626; background: #FEF2F2; }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 200; display: flex; align-items: center; justify-content: center; padding: 1rem; }
.modal { background: white; border-radius: 18px; width: 100%; max-width: 660px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.2); }
.modal-header { display: flex; align-items: flex-start; justify-content: space-between; padding: 1.4rem 1.6rem; border-bottom: 1px solid #F1F5F9; gap: 1rem; }
.modal-header h2 { font-size: 1.05rem; font-weight: 800; color: #1E293B; margin-bottom: .4rem; }
.modal-meta { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
.meta-user, .meta-date { font-size: .8rem; color: #94A3B8; display: flex; align-items: center; gap: .3rem; }
.modal-close { background: none; border: none; cursor: pointer; color: #94A3B8; font-size: 1.1rem; padding: .2rem; border-radius: 6px; flex-shrink: 0; }
.modal-close:hover { color: #1E293B; background: #F1F5F9; }

.conversation { padding: 1.2rem 1.6rem; display: flex; flex-direction: column; gap: 1rem; max-height: 340px; overflow-y: auto; }
.msg { display: flex; gap: .8rem; align-items: flex-start; }
.msg-parent { flex-direction: row-reverse; }
.msg-avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: .9rem; color: white; flex-shrink: 0; }
.parent-av { background: linear-gradient(135deg, #EC4899, #F472B6); }
.admin-av { background: linear-gradient(135deg, #38BDF8, #0284C7); }
.msg-bubble { background: #F8FAFC; border-radius: 12px; padding: .8rem 1rem; max-width: 75%; }
.msg-parent .msg-bubble { background: #FDF2F8; }
.msg-meta { font-size: .75rem; color: #94A3B8; margin-bottom: .3rem; font-weight: 600; }
.msg-bubble p { font-size: .9rem; color: #1E293B; line-height: 1.6; white-space: pre-wrap; }

.review-display { margin: 0 1.6rem .5rem; padding: .75rem 1rem; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; display: flex; align-items: center; gap: .4rem; font-size: .85rem; color: #92400E; font-weight: 700; flex-wrap: wrap; }
.review-comment { color: #64748B; font-weight: 600; }

.reply-form { padding: 1rem 1.6rem 1.4rem; display: flex; flex-direction: column; gap: .75rem; border-top: 1px solid #F1F5F9; }
.reply-form textarea { border: 1.5px solid #E2E8F0; border-radius: 10px; padding: .75rem 1rem; font-family: 'Cairo', sans-serif; font-size: .9rem; resize: vertical; outline: none; }
.reply-form textarea:focus { border-color: #38BDF8; }
.reply-actions { display: flex; gap: .75rem; }

.btn-primary { background: #38BDF8; color: white; border: none; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 700; font-size: .9rem; border-radius: 10px; padding: .65rem 1.4rem; display: inline-flex; align-items: center; gap: .5rem; transition: background .2s; }
.btn-primary:hover:not(:disabled) { background: #0284C7; }
.btn-primary:disabled { opacity: .6; cursor: default; }
.btn-close { background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 700; font-size: .9rem; border-radius: 10px; padding: .65rem 1.2rem; display: inline-flex; align-items: center; gap: .5rem; transition: all .2s; }
.btn-close:hover { background: #DC2626; color: white; }

.closed-notice { padding: 1rem 1.6rem 1.4rem; color: #94A3B8; font-weight: 700; display: flex; align-items: center; gap: .5rem; font-size: .9rem; }
</style>
