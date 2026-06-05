<template>
  <Head :title="data.lesson.title + ' — بارع'" />
  <StudentLayout>
    <div class="lesson-wrap">
      <Link :href="route('student.dashboard')" class="back"><span class="mi">arrow_forward_ios</span> لوحتي</Link>
      <h1 class="lesson-title">{{ data.lesson.title }}</h1>

      <!-- Tabs -->
      <div class="tabs">
        <button class="tab" :class="{ active: tab === 'video', done: prog.video_completed }" @click="tab = 'video'">
          <span class="mi">play_circle</span> الفيديو
          <span v-if="prog.video_completed" class="mi check">check_circle</span>
        </button>
        <button class="tab" :class="{ active: tab === 'scorm', done: prog.scorm_completed }"
                :disabled="!prog.video_completed" @click="prog.video_completed && (tab = 'scorm')">
          <span class="mi">{{ prog.video_completed ? 'extension' : 'lock' }}</span> التفاعل
          <span v-if="prog.scorm_completed" class="mi check">check_circle</span>
        </button>
        <button class="tab" :class="{ active: tab === 'quiz', done: prog.quiz_passed }"
                :disabled="!prog.scorm_completed" @click="prog.scorm_completed && (tab = 'quiz')">
          <span class="mi">{{ prog.scorm_completed ? 'quiz' : 'lock' }}</span> الاختبار
          <span v-if="prog.quiz_passed" class="mi check">check_circle</span>
        </button>
      </div>

      <!-- VIDEO -->
      <div v-show="tab === 'video'" class="panel">
        <div v-if="data.video">
          <video ref="videoEl" class="video-js vjs-big-play-centered" controls playsinline></video>
          <div class="video-foot">
            <div class="watch-info">
              <span class="mi">visibility</span>
              شاهدت {{ watchPercent }}٪ — مطلوب {{ data.video.watch_threshold }}٪ لإكمال الفيديو
            </div>
            <button class="btn next" :disabled="!prog.video_completed" @click="tab = 'scorm'">
              التالي <span class="mi">arrow_back</span>
            </button>
          </div>
        </div>
        <div v-else class="empty">لا يوجد فيديو لهذا الدرس <button class="btn next" @click="tab='scorm'">تخطّي</button></div>
      </div>

      <!-- SCORM (stub for Phase 1; real runtime in Phase 5) -->
      <div v-show="tab === 'scorm'" class="panel">
        <div class="scorm-box">
          <span class="mi big">extension</span>
          <h3>{{ data.scorm ? data.scorm.title : 'النشاط التفاعلي' }}</h3>
          <p>افتح النشاط التفاعلي وأكمله للمتابعة للاختبار</p>
          <button v-if="!prog.scorm_completed" class="btn start" :disabled="scormBusy" @click="finishScorm">
            <span class="mi">touch_app</span> {{ scormBusy ? '...' : 'بدء النشاط وإكماله' }}
          </button>
          <div v-else class="done-note"><span class="mi">check_circle</span> أكملت النشاط!</div>
          <button v-if="prog.scorm_completed" class="btn next" @click="tab = 'quiz'">التالي <span class="mi">arrow_back</span></button>
        </div>
      </div>

      <!-- QUIZ -->
      <div v-show="tab === 'quiz'" class="panel">
        <div v-if="!data.quiz" class="empty">لا يوجد اختبار لهذا الدرس</div>
        <div v-else-if="quizResult && quizResult.passed" class="quiz-result pass">
          <span class="mi big">emoji_events</span>
          <h3>أحسنت! نجحت بنسبة {{ quizResult.score }}٪</h3>
          <Link :href="route('student.dashboard')" class="btn start">عودة للوحتي</Link>
        </div>
        <div v-else>
          <div v-if="quizResult && !quizResult.passed" class="quiz-banner fail">
            <span class="mi">sentiment_dissatisfied</span>
            حصلت على {{ quizResult.score }}٪ — تحتاج {{ data.quiz.pass_mark }}٪. حاول مرة أخرى!
          </div>
          <div class="quiz-meta">المحاولات: {{ attemptsUsed }} / {{ data.quiz.max_attempts }}</div>

          <div v-for="(q, qi) in data.quiz.questions" :key="q.id" class="question">
            <div class="q-text">{{ qi + 1 }}. {{ q.text }}</div>
            <label v-for="o in q.options" :key="o.id" class="option" :class="{ sel: answers[q.id] === o.id }">
              <input type="radio" :name="'q'+q.id" :value="o.id" v-model="answers[q.id]" />
              {{ o.text }}
            </label>
          </div>

          <button class="btn start submit" :disabled="!allAnswered || quizBusy || attemptsExhausted" @click="submitQuiz">
            <span class="mi">send</span> {{ quizBusy ? 'جاري التصحيح...' : 'إرسال الإجابات' }}
          </button>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onBeforeUnmount } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import axios from 'axios'
import confetti from 'canvas-confetti'
import videojs from 'video.js'
import 'video.js/dist/video-js.css'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const props = defineProps({ data: { type: Object, required: true } })

const tab = ref('video')
const prog = reactive({ ...props.data.progress })
const watchPercent = ref(props.data.video?.watch_percent ?? 0)

const videoEl = ref(null)
let player = null
let lastSent = 0

onMounted(() => {
  if (props.data.video?.src) {
    player = videojs(videoEl.value, {
      sources: [{ src: props.data.video.src }],
      playbackRates: [0.5, 1, 1.5, 2],
      fluid: true,
    })
    player.on('timeupdate', onTimeUpdate)
  }
})

onBeforeUnmount(() => { if (player) player.dispose() })

function onTimeUpdate() {
  const dur = player.duration()
  if (!dur) return
  const pct = Math.floor((player.currentTime() / dur) * 100)
  if (pct > watchPercent.value) watchPercent.value = pct

  // throttle server updates to every ~5%
  if (pct - lastSent >= 5 || pct >= 100) {
    lastSent = pct
    axios.post(route('student.lesson.video'), {
      video_id: props.data.video.id,
      watch_percent: pct,
      position: Math.floor(player.currentTime()),
    }).then(({ data }) => {
      if (data.video_completed) prog.video_completed = true
    }).catch(() => {})
  }
}

// SCORM stub
const scormBusy = ref(false)
function finishScorm() {
  if (!props.data.scorm) { prog.scorm_completed = true; tab.value = 'quiz'; return }
  scormBusy.value = true
  axios.post(route('student.lesson.scorm'), { scorm_id: props.data.scorm.id })
    .then(({ data }) => { if (data.scorm_completed) prog.scorm_completed = true })
    .finally(() => { scormBusy.value = false })
}

// Quiz
const answers = reactive({})
const quizResult = ref(null)
const quizBusy = ref(false)
const attemptsUsed = ref(props.data.quiz?.attempts_used ?? 0)
const allAnswered = computed(() => props.data.quiz && props.data.quiz.questions.every(q => answers[q.id]))
const attemptsExhausted = computed(() => props.data.quiz && attemptsUsed.value >= props.data.quiz.max_attempts)

function submitQuiz() {
  quizBusy.value = true
  axios.post(route('student.lesson.quiz'), {
    quiz_id: props.data.quiz.id,
    answers: { ...answers },
  }).then(({ data }) => {
    if (data.error) { alert(data.error); return }
    quizResult.value = data
    attemptsUsed.value = data.attempts_used
    if (data.passed) {
      prog.quiz_passed = true
      celebrate()
      if (data.completion?.path_completed) {
        setTimeout(() => router.visit(route('student.certificates')), 2500)
      }
    }
  }).finally(() => { quizBusy.value = false })
}

function celebrate() {
  const end = Date.now() + 1500
  const colors = ['#38BDF8', '#EC4899', '#84CC16', '#F59E0B']
  ;(function frame() {
    confetti({ particleCount: 4, angle: 60, spread: 55, origin: { x: 0 }, colors })
    confetti({ particleCount: 4, angle: 120, spread: 55, origin: { x: 1 }, colors })
    if (Date.now() < end) requestAnimationFrame(frame)
  })()
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/icon?family=Material+Icons+Round');
* { box-sizing:border-box; }
.mi { font-family:'Material Icons Round'; font-style:normal; line-height:1; display:inline-flex; align-items:center; justify-content:center; vertical-align:middle; }

.lesson-wrap { max-width:780px; margin:0 auto; }
.back { display:inline-flex; align-items:center; gap:.3rem; color:#64748B; font-weight:700; font-size:.85rem; text-decoration:none; }
.back .mi { font-size:.9rem; }
.lesson-title { font-size:1.5rem; font-weight:800; color:#0F172A; margin:.6rem 0 1.2rem; }

.tabs { display:flex; gap:.5rem; margin-bottom:1.2rem; }
.tab { flex:1; display:flex; align-items:center; justify-content:center; gap:.4rem; padding:.9rem; border-radius:16px;
  border:2px solid #E2E8F0; background:#fff; font-family:inherit; font-weight:800; font-size:.92rem; color:#64748B; cursor:pointer; transition:all .2s; }
.tab:disabled { opacity:.5; cursor:not-allowed; }
.tab.active { border-color:#38BDF8; background:#E0F4FF; color:#0E7490; }
.tab.done { border-color:#84CC16; }
.tab .mi { font-size:1.2rem; }
.tab .check { color:#84CC16; font-size:1rem; }

.panel { background:#fff; border-radius:24px; padding:1.6rem; border:1.5px solid #E2E8F0; box-shadow:0 2px 12px rgba(15,23,42,.06); }
.video-foot { display:flex; align-items:center; justify-content:space-between; margin-top:1rem; flex-wrap:wrap; gap:.8rem; }
.watch-info { font-size:.85rem; color:#64748B; font-weight:700; display:flex; align-items:center; gap:.4rem; }
.watch-info .mi { font-size:1.1rem; color:#38BDF8; }

.btn { display:inline-flex; align-items:center; justify-content:center; gap:.4rem; padding:.75rem 1.6rem; border-radius:50px;
  font-family:inherit; font-weight:800; font-size:.92rem; border:none; cursor:pointer; text-decoration:none; transition:transform .15s; }
.btn:hover:not(:disabled) { transform:translateY(-2px); }
.btn:disabled { opacity:.5; cursor:not-allowed; }
.btn.next { background:#38BDF8; color:#fff; box-shadow:0 4px 0 #0E7490; }
.btn.start { background:#84CC16; color:#fff; box-shadow:0 4px 0 #3F6212; }
.btn .mi { font-size:1.1rem; }

.scorm-box { text-align:center; padding:1.5rem; }
.scorm-box .big { font-size:3rem; color:#8B5CF6; margin-bottom:.6rem; }
.scorm-box h3 { font-size:1.2rem; font-weight:800; color:#0F172A; margin-bottom:.4rem; }
.scorm-box p { color:#64748B; font-weight:600; margin-bottom:1.2rem; }
.done-note { color:#3F6212; font-weight:800; display:flex; align-items:center; justify-content:center; gap:.4rem; margin-bottom:1rem; }

.empty { text-align:center; color:#64748B; font-weight:700; padding:1.5rem; display:flex; flex-direction:column; gap:1rem; align-items:center; }

.quiz-meta { font-size:.82rem; color:#64748B; font-weight:700; margin-bottom:1rem; }
.quiz-banner { padding:.9rem 1.2rem; border-radius:14px; font-weight:800; margin-bottom:1rem; display:flex; align-items:center; gap:.5rem; }
.quiz-banner.fail { background:#FCE7F3; color:#9D174D; }
.question { margin-bottom:1.4rem; }
.q-text { font-weight:800; color:#0F172A; margin-bottom:.7rem; }
.option { display:flex; align-items:center; gap:.6rem; padding:.7rem 1rem; border:2px solid #E2E8F0; border-radius:12px;
  margin-bottom:.5rem; cursor:pointer; font-weight:700; color:#1C1C2E; transition:all .15s; }
.option:hover { border-color:#38BDF8; }
.option.sel { border-color:#38BDF8; background:#E0F4FF; }
.option input { accent-color:#38BDF8; }
.submit { width:100%; margin-top:.5rem; }

.quiz-result { text-align:center; padding:2rem; }
.quiz-result .big { font-size:3.5rem; color:#F59E0B; margin-bottom:.6rem; }
.quiz-result h3 { font-size:1.4rem; font-weight:800; color:#0F172A; margin-bottom:1.2rem; }
</style>
