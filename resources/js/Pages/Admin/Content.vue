<template>
  <AdminLayout page-title="إدارة المحتوى">

    <!-- HEADER -->
    <div class="page-header">
      <div>
        <h1 class="page-title">إدارة المحتوى (CMS)</h1>
        <p class="page-sub">رفع الفيديوهات وملفات SCORM وأسئلة الاختبارات</p>
      </div>
      <button class="btn-add" @click="openUpload">
        <i class="fa-solid fa-circle-plus"></i> إضافة محتوى جديد
      </button>
    </div>

    <!-- TABS -->
    <div class="tabs-outer">
      <button v-for="tab in tabs" :key="tab.key"
        class="tab-pill" :class="{ active: activeTab === tab.key }"
        @click="activeTab = tab.key">
        <i :class="tab.icon"></i> {{ tab.label }}
      </button>
    </div>

    <!-- ══════════════ VIDEOS TAB ══════════════ -->
    <div v-if="activeTab === 'videos'">
      <!-- Upload Zone -->
      <div class="upload-zone" @dragover.prevent @drop.prevent="handleDrop" @click="$refs.videoInput.click()">
        <input ref="videoInput" type="file" accept="video/*" class="hidden-input" @change="handleVideoUpload" />
        <i class="fa-solid fa-cloud-arrow-up upload-ico"></i>
        <p class="upload-title">اسحب الفيديو هنا أو اضغط للرفع</p>
        <p class="upload-hint">MP4, MKV, AVI — حجم أقصى ٢ جيجابايت</p>
      </div>

      <!-- Upload Progress -->
      <div v-if="uploading" class="upload-progress-wrap">
        <div class="upload-progress-info">
          <span>{{ uploadingFile }}</span>
          <span>{{ uploadProgress }}%</span>
        </div>
        <div class="progress-bar"><div class="progress-fill" :style="{ width: uploadProgress + '%' }"></div></div>
      </div>

      <!-- Videos List -->
      <div class="content-grid">
        <div v-for="video in videos" :key="video.id" class="content-card">
          <div class="content-thumb video-thumb">
            <i class="fa-solid fa-play-circle thumb-play"></i>
            <span class="content-duration">{{ video.duration }}</span>
          </div>
          <div class="content-body">
            <div class="content-path-badge" :style="{ background: pathColor(video.path) + '20', color: pathColor(video.path) }">{{ video.path }}</div>
            <h4 class="content-title">{{ video.title }}</h4>
            <div class="content-meta">
              <span><i class="fa-solid fa-layer-group"></i> {{ video.level }}</span>
              <span><i class="fa-solid fa-eye"></i> {{ video.views }} مشاهدة</span>
              <span><i class="fa-solid fa-calendar"></i> {{ video.date }}</span>
            </div>
          </div>
          <div class="content-actions">
            <button class="act-sm edit" @click="editContent(video)"><i class="fa-solid fa-pen"></i></button>
            <button class="act-sm delete" @click="deleteContent(video, 'videos')"><i class="fa-solid fa-trash"></i></button>
          </div>
        </div>
      </div>
    </div>

    <!-- ══════════════ SCORM TAB ══════════════ -->
    <div v-if="activeTab === 'scorm'">
      <div class="upload-zone scorm-zone" @dragover.prevent @drop.prevent @click="$refs.scormInput.click()">
        <input ref="scormInput" type="file" accept=".zip" class="hidden-input" @change="handleScormUpload" />
        <i class="fa-solid fa-file-zipper upload-ico" style="color:#8B5CF6"></i>
        <p class="upload-title">ارفع ملف SCORM (.zip)</p>
        <p class="upload-hint">يدعم SCORM 1.2 و SCORM 2004 — سيُفكّ الضغط تلقائياً</p>
      </div>

      <!-- SCORM Items -->
      <div class="scorm-list">
        <div v-for="item in scormFiles" :key="item.id" class="scorm-row">
          <div class="scorm-icon">
            <i class="fa-solid fa-gamepad"></i>
          </div>
          <div class="scorm-info">
            <div class="scorm-name">{{ item.name }}</div>
            <div class="scorm-meta">
              <span class="scorm-version">{{ item.version }}</span>
              <span>{{ item.path }}</span>
              <span>{{ item.level }}</span>
              <span>{{ item.size }}</span>
            </div>
          </div>
          <div class="scorm-status">
            <span class="scorm-badge" :class="item.status">
              {{ item.status === 'active' ? 'مفعّل' : 'معطّل' }}
            </span>
          </div>
          <div class="scorm-actions">
            <button class="act-sm preview" title="معاينة" @click="previewScorm(item)"><i class="fa-solid fa-eye"></i></button>
            <button class="act-sm edit"    title="تعديل"  @click="editContent(item)"><i class="fa-solid fa-pen"></i></button>
            <button class="act-sm delete"  title="حذف"    @click="deleteContent(item, 'scormFiles')"><i class="fa-solid fa-trash"></i></button>
          </div>
        </div>
      </div>
    </div>

    <!-- ══════════════ QUIZZES TAB ══════════════ -->
    <div v-if="activeTab === 'quizzes'">
      <!-- Quiz Builder -->
      <div class="quiz-builder card">
        <div class="card-header">
          <h3 class="card-title"><i class="fa-solid fa-clipboard-question"></i> بناء اختبار جديد</h3>
        </div>
        <div class="quiz-meta">
          <div class="form-group">
            <label>المسار</label>
            <select v-model="newQuiz.path" class="form-input">
              <option v-for="p in paths" :key="p" :value="p">{{ p }}</option>
            </select>
          </div>
          <div class="form-group">
            <label>المرحلة</label>
            <select v-model="newQuiz.level" class="form-input">
              <option value="ابتدائي">ابتدائي</option>
              <option value="متوسط">متوسط</option>
              <option value="ثانوي">ثانوي</option>
            </select>
          </div>
          <div class="form-group">
            <label>نسبة النجاح (%)</label>
            <input type="number" v-model="newQuiz.passMark" class="form-input" min="50" max="100" />
          </div>
          <div class="form-group">
            <label>عدد المحاولات</label>
            <input type="number" v-model="newQuiz.attempts" class="form-input" min="1" max="5" />
          </div>
        </div>

        <!-- Questions -->
        <div class="questions-list">
          <div v-for="(q, qi) in newQuiz.questions" :key="qi" class="question-card">
            <div class="q-header">
              <span class="q-num">سؤال {{ qi + 1 }}</span>
              <button class="q-delete" @click="removeQuestion(qi)"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <input v-model="q.text" class="form-input q-input" :placeholder="`نص السؤال ${qi + 1}`" />
            <div class="options-grid">
              <div v-for="(opt, oi) in q.options" :key="oi" class="option-row">
                <input type="radio" :name="`q${qi}`" :value="oi" v-model="q.correct" class="opt-radio" />
                <input v-model="q.options[oi]" class="form-input opt-input" :placeholder="`الخيار ${oi + 1}`" />
                <span class="opt-label" :class="{ correct: q.correct === oi }">
                  {{ q.correct === oi ? '✓ الإجابة الصحيحة' : '' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="quiz-footer">
          <button class="btn-add-q" @click="addQuestion">
            <i class="fa-solid fa-plus"></i> إضافة سؤال
          </button>
          <button class="btn-save-quiz" @click="saveQuiz" :disabled="newQuiz.questions.length === 0">
            <i class="fa-solid fa-floppy-disk"></i> حفظ الاختبار
          </button>
        </div>
      </div>

      <!-- Saved Quizzes -->
      <div class="card mt-card">
        <div class="card-header">
          <h3 class="card-title"><i class="fa-solid fa-list-check"></i> الاختبارات المحفوظة</h3>
        </div>
        <div class="quiz-saved-list">
          <div v-for="quiz in savedQuizzes" :key="quiz.id" class="quiz-saved-row">
            <div class="quiz-saved-icon"><i class="fa-solid fa-clipboard-question"></i></div>
            <div class="quiz-saved-info">
              <div class="quiz-saved-title">{{ quiz.title }}</div>
              <div class="quiz-saved-meta">
                <span>{{ quiz.questions }} سؤال</span>
                <span>نجاح {{ quiz.passMark }}%</span>
                <span>{{ quiz.level }}</span>
              </div>
            </div>
            <div class="quiz-saved-stat">
              <div class="stat-mini">
                <span class="stat-mini-val">{{ quiz.attempts }}</span>
                <span class="stat-mini-label">محاولة</span>
              </div>
              <div class="stat-mini">
                <span class="stat-mini-val">{{ quiz.passRate }}%</span>
                <span class="stat-mini-label">نسبة النجاح</span>
              </div>
            </div>
            <div class="quiz-saved-actions">
              <button class="act-sm edit"><i class="fa-solid fa-pen"></i></button>
              <button class="act-sm delete"><i class="fa-solid fa-trash"></i></button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- SCORM Preview Modal -->
    <div v-if="previewModal" class="modal-overlay" @click.self="previewModal = false">
      <div class="preview-modal">
        <div class="modal-header">
          <h3>معاينة: {{ previewItem?.name }}</h3>
          <button class="modal-close" @click="previewModal = false"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="scorm-preview-frame">
          <div class="scorm-placeholder">
            <i class="fa-solid fa-gamepad" style="font-size:3rem;color:#8B5CF6;margin-bottom:1rem"></i>
            <p>سيُعرض ملف SCORM هنا في الإنتاج</p>
            <p style="font-size:.82rem;color:#94A3B8">{{ previewItem?.name }}</p>
          </div>
        </div>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const activeTab = ref('videos')
const uploading = ref(false)
const uploadingFile = ref('')
const uploadProgress = ref(0)
const previewModal = ref(false)
const previewItem = ref(null)

const tabs = [
  { key: 'videos',  label: 'الفيديوهات', icon: 'fa-solid fa-video' },
  { key: 'scorm',   label: 'ملفات SCORM', icon: 'fa-solid fa-gamepad' },
  { key: 'quizzes', label: 'الاختبارات', icon: 'fa-solid fa-clipboard-question' },
]

const paths = ['مهارة الإبداع والابتكار', 'مهارة التواصل والإلقاء', 'مهارة الوعي المالي', 'الذكاء العاطفي والاجتماعي', 'المهارة التقنية والمواطنة الرقمية']

const pathColors = { 'مهارة الإبداع والابتكار': '#38BDF8', 'مهارة التواصل والإلقاء': '#EC4899', 'مهارة الوعي المالي': '#84CC16', 'الذكاء العاطفي والاجتماعي': '#F59E0B', 'المهارة التقنية والمواطنة الرقمية': '#8B5CF6' }
const pathColor = (p) => pathColors[p] ?? '#38BDF8'

const videos = ref([
  { id: 1, title: 'مقدمة في الابتكار وأنواعه',         path: 'مهارة الإبداع والابتكار',   level: 'ابتدائي', duration: '٨:٣٢', views: 342, date: '٢٠٢٦/٠٢/١٠' },
  { id: 2, title: 'كيف تلقي خطاباً بثقة',               path: 'مهارة التواصل والإلقاء',  level: 'متوسط',   duration: '١٢:١٥', views: 218, date: '٢٠٢٦/٠٢/٢٠' },
  { id: 3, title: 'الادخار وأهميته في حياتنا',           path: 'مهارة الوعي المالي',       level: 'ابتدائي', duration: '٩:٤٥', views: 415, date: '٢٠٢٦/٠٣/٠١' },
  { id: 4, title: 'التعاطف وبناء العلاقات الإيجابية',   path: 'الذكاء العاطفي والاجتماعي',level: 'متوسط',   duration: '١١:٢٠', views: 189, date: '٢٠٢٦/٠٣/١٥' },
  { id: 5, title: 'الأمان الرقمي وحماية البيانات',       path: 'المهارة التقنية والمواطنة الرقمية', level: 'ثانوي', duration: '١٤:٠٥', views: 276, date: '٢٠٢٦/٠٤/٠٢' },
  { id: 6, title: 'أدوات التفكير الإبداعي',              path: 'مهارة الإبداع والابتكار',   level: 'ثانوي',   duration: '١٠:٥٠', views: 301, date: '٢٠٢٦/٠٤/١٠' },
])

const scormFiles = ref([
  { id: 1, name: 'نشاط الخريطة الذهنية',      version: 'SCORM 1.2', path: 'مهارة الإبداع والابتكار',   level: 'ابتدائي', size: '٤.٢ MB', status: 'active' },
  { id: 2, name: 'محاكاة تقديم الخطاب',        version: 'SCORM 2004', path: 'مهارة التواصل والإلقاء',  level: 'متوسط',   size: '٦.٨ MB', status: 'active' },
  { id: 3, name: 'لعبة الميزانية الشهرية',     version: 'SCORM 1.2', path: 'مهارة الوعي المالي',       level: 'ابتدائي', size: '٣.١ MB', status: 'active' },
  { id: 4, name: 'سيناريو إدارة المشاعر',      version: 'SCORM 2004', path: 'الذكاء العاطفي والاجتماعي',level: 'متوسط',   size: '٥.٥ MB', status: 'active' },
  { id: 5, name: 'تمرين الأمن الرقمي',         version: 'SCORM 1.2', path: 'المهارة التقنية والمواطنة الرقمية', level: 'ثانوي', size: '٧.٣ MB', status: 'active' },
])

const newQuiz = reactive({
  path: 'مهارة الوعي المالي',
  level: 'ابتدائي',
  passMark: 70,
  attempts: 3,
  questions: [
    { text: '', options: ['', '', '', ''], correct: 0 }
  ]
})

const savedQuizzes = ref([
  { id: 1, title: 'اختبار مهارة الوعي المالي - ابتدائي',       questions: 10, passMark: 70, level: 'ابتدائي', attempts: 1240, passRate: 78 },
  { id: 2, title: 'اختبار مهارة التواصل - متوسط',              questions: 8,  passMark: 70, level: 'متوسط',   attempts: 890,  passRate: 82 },
  { id: 3, title: 'اختبار الذكاء العاطفي - ثانوي',             questions: 12, passMark: 75, level: 'ثانوي',   attempts: 620,  passRate: 71 },
  { id: 4, title: 'اختبار المواطنة الرقمية - ثانوي',           questions: 10, passMark: 70, level: 'ثانوي',   attempts: 445,  passRate: 85 },
])

const addQuestion = () => {
  newQuiz.questions.push({ text: '', options: ['', '', '', ''], correct: 0 })
}
const removeQuestion = (i) => newQuiz.questions.splice(i, 1)

const saveQuiz = () => {
  savedQuizzes.value.push({
    id: Date.now(),
    title: `اختبار ${newQuiz.path} - ${newQuiz.level}`,
    questions: newQuiz.questions.length,
    passMark: newQuiz.passMark,
    level: newQuiz.level,
    attempts: 0,
    passRate: 0
  })
  newQuiz.questions = [{ text: '', options: ['', '', '', ''], correct: 0 }]
  alert('تم حفظ الاختبار بنجاح!')
}

const handleVideoUpload = (e) => {
  const file = e.target.files[0]
  if (!file) return
  simulateUpload(file.name)
}
const handleScormUpload = (e) => {
  const file = e.target.files[0]
  if (!file) return
  simulateUpload(file.name)
}
const handleDrop = (e) => {
  const file = e.dataTransfer.files[0]
  if (file) simulateUpload(file.name)
}

const simulateUpload = (name) => {
  uploading.value = true
  uploadingFile.value = name
  uploadProgress.value = 0
  const interval = setInterval(() => {
    uploadProgress.value += Math.floor(Math.random() * 15) + 5
    if (uploadProgress.value >= 100) {
      uploadProgress.value = 100
      clearInterval(interval)
      setTimeout(() => { uploading.value = false }, 1000)
    }
  }, 200)
}

const previewScorm = (item) => {
  previewItem.value = item
  previewModal.value = true
}

const editContent = (item) => alert(`تعديل: ${item.title ?? item.name}`)
const deleteContent = (item, list) => {
  if (confirm(`حذف "${item.title ?? item.name}"؟`)) {
    const arr = list === 'videos' ? videos : scormFiles
    const i = arr.value.findIndex(x => x.id === item.id)
    if (i !== -1) arr.value.splice(i, 1)
  }
}
const openUpload = () => {
  if (activeTab.value === 'videos') document.querySelector('.hidden-input')?.click()
}
</script>

<style scoped>
.page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
.page-title { font-size: 1.6rem; font-weight: 900; color: #1E293B; }
.page-sub { font-size: .88rem; color: #94A3B8; margin-top: .2rem; }
.btn-add { background: #38BDF8; color: white; font-family: inherit; font-weight: 700; font-size: .9rem; padding: .65rem 1.4rem; border-radius: 10px; border: none; cursor: pointer; display: flex; align-items: center; gap: .5rem; }

.tabs-outer { display: flex; gap: .6rem; margin-bottom: 1.4rem; flex-wrap: wrap; }
.tab-pill { display: flex; align-items: center; gap: .5rem; padding: .65rem 1.2rem; border-radius: 50px; border: 1.5px solid #E2E8F0; background: white; font-family: inherit; font-weight: 700; font-size: .88rem; color: #64748B; cursor: pointer; transition: all .2s; }
.tab-pill.active { background: #38BDF8; border-color: #38BDF8; color: white; }

/* UPLOAD ZONE */
.upload-zone { border: 2px dashed #CBD5E1; border-radius: 16px; padding: 3rem 2rem; text-align: center; cursor: pointer; transition: border-color .2s, background .2s; background: white; margin-bottom: 1.5rem; }
.upload-zone:hover { border-color: #38BDF8; background: #F0F9FF; }
.scorm-zone:hover { border-color: #8B5CF6; background: #F5F3FF; }
.upload-ico { font-size: 3rem; color: #CBD5E1; margin-bottom: 1rem; }
.upload-zone:hover .upload-ico { color: #38BDF8; }
.scorm-zone:hover .upload-ico { color: #8B5CF6; }
.upload-title { font-size: 1.1rem; font-weight: 800; color: #1E293B; margin-bottom: .4rem; }
.upload-hint { font-size: .85rem; color: #94A3B8; }
.hidden-input { display: none; }

.upload-progress-wrap { background: white; border-radius: 12px; padding: 1rem 1.2rem; margin-bottom: 1rem; box-shadow: 0 1px 8px rgba(0,0,0,.05); }
.upload-progress-info { display: flex; justify-content: space-between; font-size: .85rem; font-weight: 700; color: #475569; margin-bottom: .5rem; }
.progress-bar { background: #F1F5F9; border-radius: 50px; height: 8px; overflow: hidden; }
.progress-fill { height: 100%; background: linear-gradient(90deg, #38BDF8, #0E7490); border-radius: 50px; transition: width .2s; }

/* VIDEO CARDS */
.content-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; }
.content-card { background: white; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 8px rgba(0,0,0,.05); transition: transform .2s; }
.content-card:hover { transform: translateY(-3px); }
.content-thumb { height: 140px; background: linear-gradient(135deg, #1C1C2E 0%, #2E2E42 100%); display: flex; align-items: center; justify-content: center; position: relative; }
.thumb-play { font-size: 2.5rem; color: rgba(255,255,255,.7); }
.content-duration { position: absolute; bottom: .5rem; left: .5rem; background: rgba(0,0,0,.7); color: white; font-size: .72rem; font-weight: 700; padding: .15rem .5rem; border-radius: 4px; }
.content-body { padding: 1rem; }
.content-path-badge { display: inline-block; font-size: .72rem; font-weight: 700; padding: .2rem .7rem; border-radius: 50px; margin-bottom: .5rem; }
.content-title { font-size: .95rem; font-weight: 800; color: #1E293B; margin-bottom: .6rem; line-height: 1.4; }
.content-meta { display: flex; gap: .8rem; flex-wrap: wrap; }
.content-meta span { font-size: .75rem; color: #94A3B8; display: flex; align-items: center; gap: .25rem; }
.content-actions { display: flex; gap: .4rem; padding: .6rem 1rem 1rem; }

/* SCORM LIST */
.scorm-list { display: flex; flex-direction: column; gap: .7rem; }
.scorm-row { background: white; border-radius: 14px; padding: 1.1rem 1.3rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 1px 8px rgba(0,0,0,.05); }
.scorm-icon { width: 44px; height: 44px; border-radius: 12px; background: #F5F3FF; display: flex; align-items: center; justify-content: center; color: #8B5CF6; font-size: 1.2rem; flex-shrink: 0; }
.scorm-info { flex: 1; min-width: 0; }
.scorm-name { font-weight: 800; color: #1E293B; margin-bottom: .3rem; }
.scorm-meta { display: flex; gap: .8rem; flex-wrap: wrap; }
.scorm-meta span { font-size: .78rem; color: #94A3B8; }
.scorm-version { background: #F5F3FF; color: #5B21B6; font-weight: 700; padding: .1rem .5rem; border-radius: 4px; }
.scorm-badge { font-size: .75rem; font-weight: 700; padding: .2rem .7rem; border-radius: 50px; }
.scorm-badge.active { background: #F0FDF4; color: #15803D; }
.scorm-badge.inactive { background: #FEF2F2; color: #DC2626; }
.scorm-actions { display: flex; gap: .4rem; }

/* QUIZ BUILDER */
.card { background: white; border-radius: 16px; box-shadow: 0 1px 8px rgba(0,0,0,.05); overflow: hidden; }
.mt-card { margin-top: 1.2rem; }
.card-header { padding: 1.2rem 1.4rem; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between; }
.card-title { font-size: 1rem; font-weight: 800; color: #1E293B; display: flex; align-items: center; gap: .5rem; }
.card-title i { color: #38BDF8; }

.quiz-meta { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; padding: 1.2rem 1.4rem; border-bottom: 1px solid #F8FAFC; }
.form-group { display: flex; flex-direction: column; gap: .4rem; }
.form-group label { font-size: .8rem; font-weight: 700; color: #475569; }
.form-input { padding: .6rem .9rem; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; font-size: .88rem; color: #1E293B; }
.form-input:focus { outline: none; border-color: #38BDF8; }

.questions-list { padding: 1.2rem 1.4rem; display: flex; flex-direction: column; gap: 1rem; max-height: 420px; overflow-y: auto; }
.question-card { background: #F8FAFC; border-radius: 12px; padding: 1rem 1.2rem; border: 1.5px solid #E2E8F0; }
.q-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: .7rem; }
.q-num { font-size: .82rem; font-weight: 800; color: #38BDF8; }
.q-delete { background: none; border: none; cursor: pointer; color: #DC2626; font-size: .9rem; }
.q-input { width: 100%; margin-bottom: .8rem; }
.options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .5rem; }
.option-row { display: flex; align-items: center; gap: .5rem; }
.opt-radio { accent-color: #38BDF8; flex-shrink: 0; }
.opt-input { flex: 1; padding: .5rem .7rem; font-size: .83rem; }
.opt-label { font-size: .72rem; font-weight: 700; color: #16A34A; white-space: nowrap; min-width: 80px; }
.opt-label:not(.correct) { color: transparent; }

.quiz-footer { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.4rem; border-top: 1px solid #F1F5F9; }
.btn-add-q { background: none; border: 1.5px dashed #38BDF8; color: #0E7490; font-family: inherit; font-weight: 700; font-size: .88rem; padding: .6rem 1.2rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: .4rem; }
.btn-save-quiz { background: #38BDF8; border: none; color: white; font-family: inherit; font-weight: 700; font-size: .9rem; padding: .65rem 1.6rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: .5rem; }
.btn-save-quiz:disabled { opacity: .5; cursor: not-allowed; }

.quiz-saved-list { padding: .5rem 0; }
.quiz-saved-row { display: flex; align-items: center; gap: 1rem; padding: .9rem 1.4rem; border-bottom: 1px solid #F8FAFC; }
.quiz-saved-icon { width: 40px; height: 40px; border-radius: 10px; background: #F0F9FF; display: flex; align-items: center; justify-content: center; color: #38BDF8; font-size: 1rem; flex-shrink: 0; }
.quiz-saved-info { flex: 1; min-width: 0; }
.quiz-saved-title { font-weight: 800; color: #1E293B; font-size: .92rem; margin-bottom: .25rem; }
.quiz-saved-meta { display: flex; gap: .7rem; }
.quiz-saved-meta span { font-size: .78rem; color: #94A3B8; }
.quiz-saved-stat { display: flex; gap: 1rem; }
.stat-mini { text-align: center; }
.stat-mini-val { display: block; font-size: 1rem; font-weight: 900; color: #1E293B; }
.stat-mini-label { font-size: .7rem; color: #94A3B8; }
.quiz-saved-actions { display: flex; gap: .4rem; }

/* COMMON ACTION BUTTONS */
.act-sm { width: 30px; height: 30px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: .78rem; }
.act-sm.edit    { background: #FFF7ED; color: #EA580C; }
.act-sm.delete  { background: #FEF2F2; color: #DC2626; }
.act-sm.preview { background: #EFF6FF; color: #2563EB; }

/* SCORM PREVIEW MODAL */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 200; display: flex; align-items: center; justify-content: center; }
.preview-modal { background: white; border-radius: 20px; width: 700px; max-width: 95vw; box-shadow: 0 20px 60px rgba(0,0,0,.2); overflow: hidden; }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1.2rem 1.5rem; border-bottom: 1px solid #F1F5F9; }
.modal-header h3 { font-size: 1rem; font-weight: 800; color: #1E293B; }
.modal-close { background: none; border: none; cursor: pointer; font-size: 1.1rem; color: #94A3B8; }
.scorm-preview-frame { height: 400px; display: flex; align-items: center; justify-content: center; background: #F8FAFC; }
.scorm-placeholder { text-align: center; color: #64748B; }

@media (max-width: 768px) {
  .quiz-meta { grid-template-columns: 1fr 1fr; }
  .options-grid { grid-template-columns: 1fr; }
  .content-grid { grid-template-columns: 1fr; }
}
</style>
