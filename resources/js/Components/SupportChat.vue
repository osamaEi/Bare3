<template>
  <!-- Chat Widget Container -->
  <div class="support-chat-container">
    <!-- Chat Bubble Button -->
    <button
  class="chat-bubble"
  @click="isOpen = !isOpen"
  title="الدعم الفني"
>
  <i class="fa-solid fa-headset"></i>

  <span class="unread-badge" v-if="unreadCount > 0">
    {{ unreadCount }}
  </span>
</button>

    <!-- Chat Window -->
    <transition name="chat-slide">
      <div v-if="isOpen" class="chat-window">
        <!-- Header -->
        <div class="chat-header">
          <div class="chat-header-icon">
            <i class="fa-solid fa-graduation-cap"></i>
          </div>
          <div class="chat-header-content">
            <h3>دعم المعهد</h3>
            <p class="chat-status">
              <span class="status-dot"></span>
              نحن هنا للمساعدة
            </p>
          </div>
          <button class="btn-close" @click="isOpen = false" title="إغلاق">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <!-- Messages Area -->
        <div class="chat-messages" ref="messagesContainer">
          <!-- Greeting Message -->
          <div class="message bot-message">
            <div class="message-avatar">
              <i class="fa-solid fa-robot"></i>
            </div>
            <div class="message-content">
              <p>مرحباً! 👋 كيف يمكننا مساعدتك اليوم؟</p>
              <span class="message-time">الآن</span>
            </div>
          </div>

          <!-- Messages Loop -->
          <div v-for="(msg, idx) in messages" :key="idx" class="message" :class="msg.type">
            <div v-if="msg.type === 'bot-message'" class="message-avatar">
              <i class="fa-solid fa-robot"></i>
            </div>
            <div class="message-content">
              <p>{{ msg.text }}</p>
              <span class="message-time">{{ msg.time }}</span>
            </div>
          </div>

          <!-- Typing Indicator -->
          <div v-if="isTyping" class="message bot-message">
            <div class="message-avatar">
              <i class="fa-solid fa-robot"></i>
            </div>
            <div class="message-content">
              <div class="typing-indicator">
                <span></span>
                <span></span>
                <span></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Options -->
        <div class="quick-options">
          <button
            v-for="option in quickOptions"
            :key="option.id"
            class="option-btn"
            @click="selectOption(option)"
          >
            <i :class="option.icon"></i>
            <span>{{ option.text }}</span>
          </button>
        </div>

        <!-- Input Area -->
        <div class="chat-input-area">
          <input
            v-model="inputMessage"
            type="text"
            class="chat-input"
            placeholder="اكتب رسالتك..."
            @keyup.enter="sendMessage"
          />
          <button class="btn-send" @click="sendMessage" :disabled="!inputMessage.trim()">
            <i class="fa-solid fa-paper-plane"></i>
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue'

const isOpen = ref(false)
const inputMessage = ref('')
const messages = ref([])
const isTyping = ref(false)
const unreadCount = ref(2)
const messagesContainer = ref(null)

const quickOptions = [
  { id: 1, icon: 'fa-solid fa-question-circle', text: 'أسئلة شائعة' },
  { id: 2, icon: 'fa-solid fa-calendar-check', text: 'حجز استشارة' },
  { id: 3, icon: 'fa-solid fa-envelope', text: 'راسلنا' },
  { id: 4, icon: 'fa-solid fa-phone', text: 'اتصل بنا' },
]

// Preset bot responses
const botResponses = {
  faq: 'يمكنك زيارة صفحة الأسئلة الشائعة لمعرفة المزيد. هل تريد مساعدة في شيء آخر؟',
  booking: 'يمكنك حجز استشارة مباشرة من خلال الموقع. هل تريد رابط الحجز؟',
  contact: 'يمكنك التواصل معنا عبر البريد الإلكتروني أو الهاتف. سيسعدنا سماعك!',
  phone: 'رقم الاتصال: 📞 +966 XX XXX XXXX يمكنك الاتصال بنا من 9 صباحاً إلى 6 مساءً',
}

const getCurrentTime = () => {
  const now = new Date()
  return now.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' })
}

const scrollToBottom = async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const sendMessage = async () => {
  if (!inputMessage.value.trim()) return

  // Add user message
  messages.value.push({
    type: 'user-message',
    text: inputMessage.value,
    time: getCurrentTime(),
  })

  const userMsg = inputMessage.value
  inputMessage.value = ''
  unreadCount.value = 0

  await scrollToBottom()

  // Simulate bot typing
  isTyping.value = true
  await new Promise(resolve => setTimeout(resolve, 1000))

  // Add bot response based on keywords
  let response = 'شكراً على رسالتك! سيتم الرد عليك قريباً من فريق الدعم.'

  if (
    userMsg.includes('سؤال') ||
    userMsg.includes('استفسار') ||
    userMsg.includes('كيف')
  ) {
    response = botResponses.faq
  } else if (userMsg.includes('حجز') || userMsg.includes('استشارة')) {
    response = botResponses.booking
  } else if (
    userMsg.includes('اتصل') ||
    userMsg.includes('رقم') ||
    userMsg.includes('هاتف')
  ) {
    response = botResponses.phone
  } else if (userMsg.includes('بريد') || userMsg.includes('ايميل')) {
    response = botResponses.contact
  }

  isTyping.value = false

  messages.value.push({
    type: 'bot-message',
    text: response,
    time: getCurrentTime(),
  })

  await scrollToBottom()
}

const selectOption = async (option) => {
  inputMessage.value = option.text
  await sendMessage()
}
</script>

<style scoped>
.support-chat-container {
  /* بدل الألوان الثابتة، بنسحب هوية الصفحة نفسها */
  --ink: var(--dark, #1C1C2E);
  --ink-soft: var(--dark-mid, #2E2E42);
  --gold: var(--lime, #A855F7);          /* البنفسجي بقى هو لون التمييز */
  --gold-light: var(--lime-light, #F5F3FF);
  --paper: var(--light, #F8FAFC);
  --bot-bubble: var(--sky-light, #FCE7F3);
  --text-main: var(--dark, #1C1C2E);
  --text-muted: var(--gray, #6B7280);

  position: fixed;
  bottom: 2rem;
  left: 2rem;
  z-index: 999;
  font-family: 'Baloo Bhaijaan 2', 'Segoe UI', Tahoma, sans-serif;
}

/* Chat Bubble — جراديانت الصفحة بدل الكحلي الصرف */
.chat-bubble {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: var(--grad-cta, linear-gradient(135deg, #EC4899 0%, #38BDF8 100%));
  color: white;
  border: none;
  cursor: pointer;
  font-size: 1.4rem;
  box-shadow: 0 10px 28px rgba(236, 72, 153, 0.35);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.chat-bubble:hover {
  transform: scale(1.08) translateY(-3px);
  box-shadow: 0 14px 34px rgba(56, 189, 248, 0.45);
}

.chat-bubble:active {
  transform: scale(1.03) translateY(-1px);
}

.unread-badge {
  position: absolute;
  top: -4px;
  right: -4px;
  background: var(--sky-dark, #9D174D);
  color: white;
  border-radius: 50%;
  width: 22px;
  height: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  font-weight: 800;
  border: 2px solid var(--paper);
}

/* Chat Window */
.chat-window {
  position: absolute;
  bottom: 60px;
  left: 0;
  width: 330px;
  height: 350px;
  max-height: calc(100vh - 7rem);
  background: var(--paper);
  border-radius: 20px;
  box-shadow: 0 20px 50px rgba(124, 58, 237, 0.18);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  border: 1px solid rgba(168, 85, 247, 0.12);
}

/* Chat Header — جراديانت وردي/سماوي زي الـ hero */
.chat-header {
  background: var(--grad-cta, linear-gradient(135deg, #EC4899 0%, #38BDF8 100%));
  color: white;
  padding: 1rem 1.1rem;
  display: flex;
  align-items: center;
  gap: 0.7rem;
  flex-shrink: 0;
  border-bottom: none;
}

.chat-header-icon {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.18);
  border: 1px solid rgba(255, 255, 255, 0.4);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  flex-shrink: 0;
}

.chat-header-content {
  flex: 1;
  min-width: 0;
}

.chat-header-content h3 {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 800;
  letter-spacing: 0.2px;
}

.chat-status {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  margin: 0.25rem 0 0;
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.85);
}

.status-dot {
  width: 7px;
  height: 7px;
  background: #4ade80;
  border-radius: 50%;
  animation: pulse 2s infinite;
  flex-shrink: 0;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.45; }
}

.btn-close {
  background: rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.btn-close:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: white;
}

/* Messages Area */
.chat-messages {
  flex: 1;
  min-height: 0;
  overflow-y: auto;
  padding: 1.1rem;
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
  background: var(--paper);
}

.chat-messages::-webkit-scrollbar { width: 6px; }
.chat-messages::-webkit-scrollbar-track { background: transparent; }
.chat-messages::-webkit-scrollbar-thumb {
  background: var(--pink-mid, #7DD3F8);
  border-radius: 10px;
}

.message {
  display: flex;
  gap: 0.6rem;
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

.message.user-message { justify-content: flex-end; }

.message-avatar {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: var(--grad-cta, linear-gradient(135deg, #EC4899 0%, #38BDF8 100%));
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 0.85rem;
  flex-shrink: 0;
}

.message-content { max-width: 280px; }

.bot-message .message-content p {
  background: var(--bot-bubble);
  color: var(--text-main);
  padding: 0.75rem 1rem;
  border-radius: 12px 12px 12px 4px;
  margin: 0;
  font-size: 0.93rem;
  line-height: 1.55;
}

.user-message .message-content p {
  background: var(--grad-cta, linear-gradient(135deg, #EC4899 0%, #38BDF8 100%));
  color: white;
  padding: 0.75rem 1rem;
  border-radius: 12px 12px 4px 12px;
  margin: 0;
  font-size: 0.93rem;
  line-height: 1.55;
}

.message-time {
  display: block;
  font-size: 0.72rem;
  color: var(--text-muted);
  margin-top: 0.3rem;
  margin-left: 0.5rem;
}

.user-message .message-time { text-align: left; }

/* Typing Indicator */
.typing-indicator {
  display: flex;
  gap: 4px;
  padding: 0.8rem 1rem;
  background: var(--bot-bubble);
  border-radius: 12px 12px 12px 4px;
  width: fit-content;
}

.typing-indicator span {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--gold);
  animation: typing 1.4s infinite;
}

.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
  0%, 60%, 100% { opacity: 0.4; transform: translateY(0); }
  30% { opacity: 1; transform: translateY(-6px); }
}

/* Quick Options */
.quick-options {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  border-top: 1px solid rgba(168, 85, 247, 0.1);
  background: white;
  flex-shrink: 0;
}

.option-btn {
  flex: 1;
  min-width: 50px;
  padding: 0.50rem 0.5rem;
  background: var(--paper);
  border: 1px solid rgba(168, 85, 247, 0.15);
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--ink);
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.35rem;
}

.option-btn:hover {
  background: var(--grad-cta, linear-gradient(135deg, #EC4899 0%, #38BDF8 100%));
  color: white;
  border-color: transparent;
  transform: translateY(-2px);
}

.option-btn i { font-size: 0.8rem; color: var(--gold); }
.option-btn:hover i { color: white; }

/* Input Area */
.chat-input-area {
  display: flex;
  gap: 0.6rem;
  padding: 0.9rem 1rem;
  border-top: 1px solid rgba(168, 85, 247, 0.1);
  background: white;
  flex-shrink: 0;
}

.chat-input {
  flex: 1;
  border: 1px solid rgba(168, 85, 247, 0.2);
  border-radius: 24px;
  padding: 0.6rem 1rem;
  font-family: inherit;
  font-size: 0.93rem;
  outline: none;
  transition: all 0.2s;
  background: var(--paper);
  color: var(--text-main);
}

.chat-input::placeholder { color: var(--text-muted); }

.chat-input:focus {
  border-color: var(--lime, #A855F7);
  box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.15);
  background: white;
}

.btn-send {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--grad-cta, linear-gradient(135deg, #EC4899 0%, #38BDF8 100%));
  color: white;
  border: none;
  cursor: pointer;
  font-size: 0.95rem;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.btn-send:hover:not(:disabled) {
  transform: scale(1.06);
  box-shadow: 0 6px 16px rgba(236, 72, 153, 0.35);
}

.btn-send:disabled { opacity: 0.45; cursor: not-allowed; }

/* باقي الـ animations والـ responsive زي ما هما بالظبط */


/* ---------------------------------------------------
   Responsive
   FIX: same viewport-bound max-height logic applies on
   mobile, and bottom offset is reduced so the header
   stays on screen on short/landscape viewports.
   --------------------------------------------------- */
@media (max-width: 480px) {
  .support-chat-container {
    bottom: 1rem;
    left: 1rem;
  }

  .chat-window {
    width: calc(100vw - 2rem);
    bottom: 74px;
    height: 540px;
    max-height: calc(100vh - 5.5rem);
    max-width: 100%;
  }

  .quick-options {
    gap: 0.4rem;
  }

  .message-content {
    max-width: 200px;
  }
}

@media (max-height: 700px) {
  .chat-window {
    bottom: 70px;
    max-height: calc(100vh - 5rem);
  }
}
</style>