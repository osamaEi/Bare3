<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
import axios from 'axios'

const props = defineProps({
  scormId:   { type: [Number, String], required: true },
  launchUrl: { type: String, required: true },
  version:   { type: String, default: '1.2' }, // '1.2' | '2004'
})

const emit = defineEmits(['completed'])

const iframe = ref(null)
const started   = ref(false)
const completed = ref(false)

// ذاكرة مؤقتة تخزّن كل ما تكتبه الحزمة (cmi.*)
const cmi = {}

function readStatus() {
  return cmi['cmi.completion_status']
    || cmi['cmi.success_status']
    || cmi['cmi.core.lesson_status']
    || 'incomplete'
}
function readScore() {
  if (cmi['cmi.score.scaled'] != null) return Math.round(parseFloat(cmi['cmi.score.scaled']) * 100)
  if (cmi['cmi.core.score.raw'] != null) return parseFloat(cmi['cmi.core.score.raw'])
  if (cmi['cmi.score.raw'] != null) return parseFloat(cmi['cmi.score.raw'])
  return null
}
function readSessionTime() {
  return cmi['cmi.core.session_time'] || cmi['cmi.session_time'] || null
}

function commitToServer() {
  const status = readStatus()
  axios.post(route('student.lesson.scorm'), {
    scorm_id:     props.scormId,
    status,
    score:        readScore(),
    session_time: readSessionTime(),
    raw_data:     cmi,
  }).then(({ data }) => {
    if (data.scorm_completed && !completed.value) {
      completed.value = true
      emit('completed', data)
    }
  }).catch(() => {})
}

// ── جسر SCORM 1.2 ──
const api12 = {
  LMSInitialize:   ()      => 'true',
  LMSFinish:       ()      => { commitToServer(); return 'true' },
  LMSGetValue:     (k)     => cmi[k] ?? '',
  LMSSetValue:     (k, v)  => { cmi[k] = v; return 'true' },
  LMSCommit:       ()      => { commitToServer(); return 'true' },
  LMSGetLastError: ()      => '0',
  LMSGetErrorString: ()    => 'No error',
  LMSGetDiagnostic:  ()    => '',
}

// ── جسر SCORM 2004 ──
const api2004 = {
  Initialize:        ()      => 'true',
  Terminate:         ()      => { commitToServer(); return 'true' },
  GetValue:          (k)     => cmi[k] ?? '',
  SetValue:          (k, v)  => { cmi[k] = v; return 'true' },
  Commit:            ()      => { commitToServer(); return 'true' },
  GetLastError:      ()      => '0',
  GetErrorString:    ()      => 'No error',
  GetDiagnostic:     ()      => '',
}

function apiObj() {
  return props.version === '2004' ? api2004 : api12
}
function apiKey() {
  return props.version === '2004' ? 'API_1484_11' : 'API'
}

function installBridge() {
  // نركّب الجسر على هذه النافذة وعلى الـ parent (للحالتين)
  window[apiKey()] = apiObj()
  try { window.parent[apiKey()] = apiObj() } catch (_) {}
}

function injectBridgeIntoIframe() {
  // بعد تحميل الـ iframe نحقن الجسر داخله مباشرة
  if (!iframe.value) return
  try {
    const iw = iframe.value.contentWindow
    if (iw) {
      iw[apiKey()] = apiObj()
      // بعض الحزم بتدور على parent
      try { iw.parent[apiKey()] = apiObj() } catch (_) {}
    }
  } catch (_) {
    // cross-origin — الجسر على window.parent كافٍ
  }
}

function removeBridge() {
  try { delete window[apiKey()] } catch (_) {}
  try { delete window.parent[apiKey()] } catch (_) {}
}

async function launch() {
  installBridge()
  started.value = true
  // ننتظر الـ iframe يظهر في الـ DOM ثم نحقن فيه
  await nextTick()
  if (iframe.value) {
    iframe.value.addEventListener('load', injectBridgeIntoIframe)
  }
}

onMounted(() => installBridge())
onBeforeUnmount(() => {
  removeBridge()
  if (iframe.value) iframe.value.removeEventListener('load', injectBridgeIntoIframe)
})

defineExpose({ launch })
</script>

<template>
  <div class="scorm-player">
    <div v-if="!started" class="scorm-launch">
      <div class="launch-icon">
        <i class="fa-solid fa-gamepad"></i>
      </div>
      <p class="launch-label">نشاط تفاعلي — {{ version === '2004' ? 'SCORM 2004' : 'SCORM 1.2' }}</p>
      <button class="btn-launch" @click="launch">
        <i class="fa-solid fa-play"></i> ابدأ النشاط
      </button>
    </div>

    <div v-else class="scorm-frame-wrap">
      <div v-if="completed" class="completed-banner">
        <i class="fa-solid fa-circle-check"></i> أكملت النشاط بنجاح — سيُسجَّل تقدمك تلقائياً
      </div>
      <iframe
        ref="iframe"
        :src="launchUrl"
        class="scorm-frame"
        allowfullscreen
        allow="fullscreen"
        sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-top-navigation"
      ></iframe>
      <p class="scorm-hint">
        <i class="fa-solid fa-circle-info"></i>
        أكمل النشاط حتى النهاية — ستُسجَّل نتيجتك تلقائياً
      </p>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap');
.scorm-player { width: 100%; font-family: 'Cairo', sans-serif; direction: rtl; }

.scorm-launch {
  text-align: center;
  padding: 3rem 2rem;
  background: linear-gradient(135deg, #F5F3FF, #EEF2FF);
  border-radius: 16px;
  border: 2px dashed #C4B5FD;
}
.launch-icon {
  width: 72px; height: 72px; border-radius: 50%;
  background: linear-gradient(135deg, #8B5CF6, #6D28D9);
  color: white; font-size: 1.8rem;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 1rem;
  box-shadow: 0 8px 24px rgba(109,40,217,.3);
}
.launch-label {
  color: #6D28D9; font-weight: 700; font-size: .95rem; margin-bottom: 1.4rem;
}
.btn-launch {
  background: linear-gradient(135deg, #8B5CF6, #6D28D9);
  color: #fff; border: none;
  font-family: 'Cairo', sans-serif;
  font-weight: 800; font-size: 1rem;
  padding: .85rem 2.4rem; border-radius: 50px; cursor: pointer;
  display: inline-flex; align-items: center; gap: .6rem;
  box-shadow: 0 6px 0 #4C1D95;
  transition: transform .15s;
}
.btn-launch:hover { transform: translateY(-2px); }
.btn-launch i { font-size: 1rem; }

.scorm-frame-wrap { display: flex; flex-direction: column; gap: .75rem; }
.completed-banner {
  background: #F0FDF4; border: 1.5px solid #BBF7D0; color: #16A34A;
  border-radius: 10px; padding: .7rem 1rem;
  font-weight: 700; font-size: .9rem;
  display: flex; align-items: center; gap: .5rem;
}
.scorm-frame {
  width: 100%; height: 520px; border: none; border-radius: 14px;
  background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,.1);
}
.scorm-hint {
  font-size: .8rem; color: #94A3B8; font-weight: 600;
  text-align: center; display: flex; align-items: center; justify-content: center; gap: .4rem;
}
</style>
