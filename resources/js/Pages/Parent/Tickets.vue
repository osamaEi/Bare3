<template>
  <ParentLayout page-title="تذاكر الدعم">
    <div class="tickets-page">

      <!-- Header -->
      <div class="page-header">
        <div>
          <h1 class="page-title">تذاكر الدعم</h1>
          <p class="page-sub">تواصل مع فريق الدعم وتابع ردودهم</p>
        </div>
        <button class="btn-primary" @click="showNew = true">
          <i class="fa-solid fa-plus"></i> تذكرة جديدة
        </button>
      </div>

      <!-- Flash -->
      <div v-if="$page.props.flash?.success" class="flash-success">
        <i class="fa-solid fa-circle-check"></i> {{ $page.props.flash.success }}
      </div>

      <!-- Tickets List -->
      <div v-if="tickets.length === 0" class="empty-state">
        <i class="fa-solid fa-ticket"></i>
        <p>لا توجد تذاكر بعد. أنشئ تذكرتك الأولى!</p>
      </div>

      <div v-else class="ticket-list">
        <div
          v-for="ticket in tickets"
          :key="ticket.id"
          class="ticket-card"
          :class="'status-' + ticket.status"
          @click="openTicket(ticket)"
        >
          <div class="ticket-top">
            <span class="ticket-subject">{{ ticket.subject }}</span>
            <span class="ticket-badge" :class="ticket.status">{{ statusLabel(ticket.status) }}</span>
          </div>
          <p class="ticket-preview">{{ ticket.body.slice(0, 100) }}{{ ticket.body.length > 100 ? '...' : '' }}</p>
          <div class="ticket-meta">
            <span><i class="fa-regular fa-clock"></i> {{ formatDate(ticket.created_at) }}</span>
            <span><i class="fa-solid fa-comments"></i> {{ ticket.replies.length }} رد</span>
            <span v-if="ticket.rating" class="ticket-rating">
              <i v-for="s in 5" :key="s" class="fa-solid fa-star" :class="s <= ticket.rating ? 'star-on' : 'star-off'"></i>
            </span>
          </div>
        </div>
      </div>

      <!-- New Ticket Modal -->
      <div v-if="showNew" class="modal-overlay" @click.self="showNew = false">
        <div class="modal">
          <div class="modal-header">
            <h2>تذكرة جديدة</h2>
            <button class="modal-close" @click="showNew = false"><i class="fa-solid fa-xmark"></i></button>
          </div>
          <form @submit.prevent="submitNew">
            <div class="form-group">
              <label>الموضوع</label>
              <input v-model="newForm.subject" type="text" required placeholder="اكتب موضوع التذكرة" />
            </div>
            <div class="form-group">
              <label>التفاصيل</label>
              <textarea v-model="newForm.body" rows="5" required placeholder="اشرح مشكلتك أو استفسارك..."></textarea>
            </div>
            <div class="form-actions">
              <button type="button" class="btn-secondary" @click="showNew = false">إلغاء</button>
              <button type="submit" class="btn-primary" :disabled="newForm.processing">
                <i class="fa-solid fa-paper-plane"></i> إرسال
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Ticket Detail Modal -->
      <div v-if="activeTicket" class="modal-overlay" @click.self="activeTicket = null">
        <div class="modal modal-wide">
          <div class="modal-header">
            <div>
              <h2>{{ activeTicket.subject }}</h2>
              <span class="ticket-badge" :class="activeTicket.status">{{ statusLabel(activeTicket.status) }}</span>
            </div>
            <button class="modal-close" @click="activeTicket = null"><i class="fa-solid fa-xmark"></i></button>
          </div>

          <!-- Conversation -->
          <div class="conversation">
            <!-- Original message -->
            <div class="msg msg-parent">
              <div class="msg-avatar parent-av">أ</div>
              <div class="msg-bubble">
                <div class="msg-meta">أنت · {{ formatDate(activeTicket.created_at) }}</div>
                <p>{{ activeTicket.body }}</p>
              </div>
            </div>

            <!-- Replies -->
            <div
              v-for="reply in activeTicket.replies"
              :key="reply.id"
              class="msg"
              :class="reply.user_id === activeTicket.user_id ? 'msg-parent' : 'msg-admin'"
            >
              <div class="msg-avatar" :class="reply.user_id === activeTicket.user_id ? 'parent-av' : 'admin-av'">
                {{ reply.user_id === activeTicket.user_id ? 'أ' : 'م' }}
              </div>
              <div class="msg-bubble">
                <div class="msg-meta">
                  {{ reply.user_id === activeTicket.user_id ? 'أنت' : 'فريق الدعم' }} · {{ formatDate(reply.created_at) }}
                </div>
                <p>{{ reply.body }}</p>
              </div>
            </div>
          </div>

          <!-- Reply form (not closed) -->
          <form v-if="activeTicket.status !== 'closed'" class="reply-form" @submit.prevent="submitReply">
            <textarea v-model="replyBody" rows="3" placeholder="اكتب ردك هنا..." required></textarea>
            <button type="submit" class="btn-primary" :disabled="replyProcessing">
              <i class="fa-solid fa-paper-plane"></i> إرسال الرد
            </button>
          </form>

          <!-- Review form (replied and no rating yet) -->
          <div v-if="activeTicket.status === 'replied' && !activeTicket.rating" class="review-box">
            <h3>قيّم تجربتك مع الدعم</h3>
            <div class="stars-input">
              <button
                v-for="s in 5"
                :key="s"
                type="button"
                @click="reviewForm.rating = s"
                class="star-btn"
                :class="s <= reviewForm.rating ? 'star-on' : 'star-off'"
              >
                <i class="fa-solid fa-star"></i>
              </button>
            </div>
            <textarea v-model="reviewForm.comment" rows="2" placeholder="تعليق اختياري..."></textarea>
            <button class="btn-primary" :disabled="!reviewForm.rating || reviewProcessing" @click="submitReview">
              <i class="fa-solid fa-check"></i> إرسال التقييم
            </button>
          </div>

          <!-- Existing review -->
          <div v-if="activeTicket.rating" class="existing-review">
            <i class="fa-solid fa-circle-check"></i>
            <span>تقييمك: </span>
            <i v-for="s in 5" :key="s" class="fa-solid fa-star" :class="s <= activeTicket.rating ? 'star-on' : 'star-off'"></i>
            <span v-if="activeTicket.review_comment" class="review-comment">· {{ activeTicket.review_comment }}</span>
          </div>
        </div>
      </div>

    </div>
  </ParentLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import ParentLayout from '@/Layouts/ParentLayout.vue'

const props = defineProps({ tickets: Array })

const showNew = ref(false)
const activeTicket = ref(null)
const replyBody = ref('')
const replyProcessing = ref(false)
const reviewProcessing = ref(false)

const newForm = useForm({ subject: '', body: '' })
const reviewForm = reactive({ rating: 0, comment: '' })

function statusLabel(s) {
  return { open: 'مفتوحة', replied: 'تم الرد', closed: 'مغلقة' }[s] ?? s
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function openTicket(ticket) {
  activeTicket.value = ticket
  replyBody.value = ''
  reviewForm.rating = 0
  reviewForm.comment = ''
}

function submitNew() {
  newForm.post(route('parent.tickets.store'), {
    onSuccess: () => { showNew.value = false; newForm.reset() }
  })
}

function submitReply() {
  replyProcessing.value = true
  router.post(route('parent.tickets.reply', activeTicket.value.id), { body: replyBody.value }, {
    onSuccess: () => { replyBody.value = '' },
    onFinish: () => { replyProcessing.value = false }
  })
}

function submitReview() {
  reviewProcessing.value = true
  router.post(route('parent.tickets.review', activeTicket.value.id), {
    rating: reviewForm.rating,
    review_comment: reviewForm.comment,
  }, {
    onSuccess: () => { activeTicket.value = null },
    onFinish: () => { reviewProcessing.value = false }
  })
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap');
* { box-sizing: border-box; margin: 0; padding: 0; }
.tickets-page { font-family: 'Cairo', sans-serif; direction: rtl; }

.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
.page-title { font-size: 1.5rem; font-weight: 800; color: #1E293B; }
.page-sub { color: #64748B; font-size: .88rem; margin-top: .2rem; }

.flash-success { background: #F0FDF4; border: 1px solid #86EFAC; color: #16A34A; border-radius: 10px; padding: .75rem 1rem; margin-bottom: 1rem; display: flex; align-items: center; gap: .6rem; font-weight: 700; }

.empty-state { text-align: center; padding: 4rem 2rem; color: #94A3B8; }
.empty-state i { font-size: 3rem; margin-bottom: 1rem; display: block; }

.ticket-list { display: flex; flex-direction: column; gap: 1rem; }
.ticket-card { background: white; border-radius: 14px; padding: 1.2rem 1.4rem; cursor: pointer; border: 2px solid #E2E8F0; transition: border-color .2s, box-shadow .2s; }
.ticket-card:hover { border-color: #EC4899; box-shadow: 0 4px 16px rgba(236,72,153,.1); }
.ticket-card.status-replied { border-right: 4px solid #38BDF8; }
.ticket-card.status-closed { opacity: .75; }

.ticket-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: .5rem; }
.ticket-subject { font-weight: 800; color: #1E293B; font-size: 1rem; }
.ticket-badge { font-size: .75rem; font-weight: 700; border-radius: 20px; padding: .25rem .75rem; }
.ticket-badge.open { background: #FEF9C3; color: #CA8A04; }
.ticket-badge.replied { background: #E0F4FF; color: #0284C7; }
.ticket-badge.closed { background: #F1F5F9; color: #64748B; }

.ticket-preview { color: #64748B; font-size: .88rem; margin-bottom: .75rem; line-height: 1.6; }
.ticket-meta { display: flex; align-items: center; gap: 1.5rem; font-size: .8rem; color: #94A3B8; }
.ticket-meta i { margin-left: .3rem; }
.ticket-rating { display: flex; gap: .1rem; }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 200; display: flex; align-items: center; justify-content: center; padding: 1rem; }
.modal { background: white; border-radius: 18px; width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.2); }
.modal-wide { max-width: 680px; }
.modal-header { display: flex; align-items: flex-start; justify-content: space-between; padding: 1.4rem 1.6rem; border-bottom: 1px solid #F1F5F9; gap: 1rem; }
.modal-header h2 { font-size: 1.1rem; font-weight: 800; color: #1E293B; }
.modal-close { background: none; border: none; cursor: pointer; color: #94A3B8; font-size: 1.1rem; padding: .2rem; border-radius: 6px; }
.modal-close:hover { color: #1E293B; background: #F1F5F9; }

.form-group { padding: 0 1.6rem; margin-top: 1.2rem; }
.form-group label { display: block; font-weight: 700; font-size: .88rem; color: #374151; margin-bottom: .4rem; }
.form-group input, .form-group textarea { width: 100%; border: 1.5px solid #E2E8F0; border-radius: 10px; padding: .75rem 1rem; font-family: 'Cairo', sans-serif; font-size: .9rem; color: #1E293B; outline: none; resize: vertical; }
.form-group input:focus, .form-group textarea:focus { border-color: #EC4899; }

.form-actions { display: flex; justify-content: flex-end; gap: .75rem; padding: 1.2rem 1.6rem 1.6rem; }

/* Conversation */
.conversation { padding: 1.2rem 1.6rem; display: flex; flex-direction: column; gap: 1rem; max-height: 360px; overflow-y: auto; }
.msg { display: flex; gap: .8rem; align-items: flex-start; }
.msg-parent { flex-direction: row-reverse; }
.msg-avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: .9rem; color: white; flex-shrink: 0; }
.parent-av { background: linear-gradient(135deg, #EC4899, #F472B6); }
.admin-av { background: linear-gradient(135deg, #38BDF8, #0284C7); }
.msg-bubble { background: #F8FAFC; border-radius: 12px; padding: .8rem 1rem; max-width: 75%; }
.msg-parent .msg-bubble { background: #FDF2F8; }
.msg-meta { font-size: .75rem; color: #94A3B8; margin-bottom: .3rem; font-weight: 600; }
.msg-bubble p { font-size: .9rem; color: #1E293B; line-height: 1.6; white-space: pre-wrap; }

/* Reply form */
.reply-form { padding: 0 1.6rem 1.2rem; display: flex; flex-direction: column; gap: .75rem; border-top: 1px solid #F1F5F9; margin-top: .5rem; padding-top: 1rem; }
.reply-form textarea { border: 1.5px solid #E2E8F0; border-radius: 10px; padding: .75rem 1rem; font-family: 'Cairo', sans-serif; font-size: .9rem; resize: vertical; outline: none; }
.reply-form textarea:focus { border-color: #EC4899; }
.reply-form .btn-primary { align-self: flex-end; }

/* Review */
.review-box { margin: 0 1.6rem 1.4rem; border: 1.5px solid #FDE68A; background: #FFFBEB; border-radius: 12px; padding: 1rem 1.2rem; display: flex; flex-direction: column; gap: .7rem; }
.review-box h3 { font-size: .95rem; font-weight: 800; color: #92400E; }
.stars-input { display: flex; gap: .3rem; }
.star-btn { background: none; border: none; cursor: pointer; font-size: 1.5rem; padding: 0; transition: transform .1s; }
.star-btn:hover { transform: scale(1.2); }
.review-box textarea { border: 1.5px solid #FDE68A; border-radius: 8px; padding: .6rem .8rem; font-family: 'Cairo', sans-serif; font-size: .88rem; resize: vertical; outline: none; background: white; }
.existing-review { padding: .75rem 1.6rem 1.2rem; display: flex; align-items: center; gap: .4rem; font-size: .88rem; color: #16A34A; font-weight: 700; }
.existing-review i.fa-circle-check { font-size: 1rem; }
.review-comment { color: #64748B; font-weight: 600; }

/* Buttons */
.btn-primary { background: #EC4899; color: white; border: none; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 700; font-size: .9rem; border-radius: 10px; padding: .65rem 1.4rem; display: inline-flex; align-items: center; gap: .5rem; transition: background .2s; }
.btn-primary:hover:not(:disabled) { background: #DB2777; }
.btn-primary:disabled { opacity: .6; cursor: default; }
.btn-secondary { background: #F1F5F9; color: #64748B; border: none; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 700; font-size: .9rem; border-radius: 10px; padding: .65rem 1.2rem; transition: background .2s; }
.btn-secondary:hover { background: #E2E8F0; }

/* Stars */
.star-on { color: #FBBF24; }
.star-off { color: #E2E8F0; }
</style>
