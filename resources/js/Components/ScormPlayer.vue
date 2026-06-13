<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'

const props = defineProps({
  scormId: { type: [Number, String], required: true },
  launchUrl: { type: String, required: true },
  version: { type: String, default: '1.2' }, // '1.2' | '2004'
})

const emit = defineEmits(['completed'])

const iframe = ref(null)
const started = ref(false)

// ذاكرة مؤقتة تخزّن كل ما تكتبه الحزمة (cmi.*)
const cmi = {}

// استخراج الحالة والدرجة من بيانات الحزمة (تختلف بين 1.2 و 2004)
function readStatus() {
  // 2004: cmi.completion_status / cmi.success_status ، 1.2: cmi.core.lesson_status
  return cmi['cmi.completion_status']
    || cmi['cmi.success_status']
    || cmi['cmi.core.lesson_status']
    || 'incomplete'
}
function readScore() {
  // 2004: cmi.score.scaled (0..1) ، 1.2: cmi.core.score.raw (0..100)
  if (cmi['cmi.score.scaled'] != null) return Math.round(parseFloat(cmi['cmi.score.scaled']) * 100)
  if (cmi['cmi.core.score.raw'] != null) return parseFloat(cmi['cmi.core.score.raw'])
  if (cmi['cmi.score.raw'] != null) return parseFloat(cmi['cmi.score.raw'])
  return null
}
function readSessionTime() {
  return cmi['cmi.core.session_time'] || cmi['cmi.session_time'] || null
}

// إرسال النتائج إلى السيرفر (صندوق البريد)
function commitToServer() {
  const status = readStatus()
  axios.post(route('student.lesson.scorm'), {
    scorm_id: props.scormId,
    status,
    score: readScore(),
    session_time: readSessionTime(),
    raw_data: cmi,
  }).then(({ data }) => {
    if (data.scorm_completed) emit('completed', data)
  }).catch(() => {})
}

// ── جسر SCORM 1.2 — الحزمة تبحث عن window.API ──
const api12 = {
  LMSInitialize: () => 'true',
  LMSFinish: () => { commitToServer(); return 'true' },
  LMSGetValue: (k) => cmi[k] ?? '',
  LMSSetValue: (k, v) => { cmi[k] = v; return 'true' },
  LMSCommit: () => { commitToServer(); return 'true' },
  LMSGetLastError: () => '0',
  LMSGetErrorString: () => 'No error',
  LMSGetDiagnostic: () => '',
}

// ── جسر SCORM 2004 — الحزمة تبحث عن window.API_1484_11 ──
const api2004 = {
  Initialize: () => 'true',
  Terminate: () => { commitToServer(); return 'true' },
  GetValue: (k) => cmi[k] ?? '',
  SetValue: (k, v) => { cmi[k] = v; return 'true' },
  Commit: () => { commitToServer(); return 'true' },
  GetLastError: () => '0',
  GetErrorString: () => 'No error',
  GetDiagnostic: () => '',
}

function installBridge() {
  // نركّب الجسر على النافذة الأم قبل تحميل الحزمة، فالحزمة تجده وتناديه
  if (props.version === '2004') {
    window.API_1484_11 = api2004
  } else {
    window.API = api12
  }
}

function removeBridge() {
  if (props.version === '2004') delete window.API_1484_11
  else delete window.API
}

function launch() {
  started.value = true
  installBridge()
}

onMounted(() => installBridge())
onBeforeUnmount(() => removeBridge())

defineExpose({ launch })
</script>

<template>
  <div class="scorm-player">
    <div v-if="!started" class="scorm-launch">
      <span class="mi big">extension</span>
      <p>نشاط تفاعلي ({{ version === '2004' ? 'SCORM 2004' : 'SCORM 1.2' }})</p>
      <button class="btn-launch" @click="launch"><span class="mi">play_arrow</span> ابدأ النشاط</button>
    </div>
    <div v-else class="scorm-frame-wrap">
      <iframe ref="iframe" :src="launchUrl" class="scorm-frame" allowfullscreen></iframe>
      <p class="scorm-hint">أكمل النشاط حتى النهاية — ستُسجَّل نتيجتك تلقائياً</p>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/icon?family=Material+Icons+Round');
.mi { font-family:'Material Icons Round'; font-style:normal; vertical-align:middle; }
.scorm-player { width:100%; }
.scorm-launch { text-align:center; padding:2rem; }
.scorm-launch .big { font-size:3rem; color:#8B5CF6; display:block; margin-bottom:.6rem; }
.scorm-launch p { color:#64748B; font-weight:700; margin-bottom:1.2rem; }
.btn-launch { background:#8B5CF6; color:#fff; border:none; font-family:inherit; font-weight:800; font-size:1rem; padding:.8rem 2rem; border-radius:50px; cursor:pointer; display:inline-flex; align-items:center; gap:.5rem; box-shadow:0 4px 0 #6D28D9; }
.btn-launch:hover { transform:translateY(-2px); }
.scorm-frame-wrap { display:flex; flex-direction:column; gap:.6rem; }
.scorm-frame { width:100%; height:460px; border:none; border-radius:14px; background:#fff; box-shadow:0 2px 12px rgba(0,0,0,.08); }
.scorm-hint { font-size:.82rem; color:#94A3B8; font-weight:600; text-align:center; }
</style>
