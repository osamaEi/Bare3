<template>
  <AdminLayout page-title="إدارة المحتوى">
    <div class="page-header">
      <div>
        <h1 class="page-title">إدارة المحتوى (CMS)</h1>
        <p class="page-sub">المسارات والدروس والفيديوهات وملفات SCORM والاختبارات</p>
      </div>
      <button class="btn-add" @click="openPathModal()"><i class="fa-solid fa-circle-plus"></i> مسار جديد</button>
    </div>

    <!-- PATHS -->
    <div class="paths-accordion">
      <div v-for="p in paths" :key="p.id" class="path-block">
        <div class="path-head" @click="toggle(p.id)">
          <div class="path-head-info">
            <span class="path-dot" :style="{ background: p.color || '#38BDF8' }"></span>
            <span class="path-name">{{ p.title }}</span>
            <span class="path-count">{{ p.lessons?.length ?? 0 }} درس</span>
          </div>
          <div class="path-head-actions">
            <button class="mini-btn" @click.stop="openLessonModal(p)"><i class="fa-solid fa-plus"></i> درس</button>
            <button class="mini-btn ghost" @click.stop="openPathModal(p)"><i class="fa-solid fa-pen"></i></button>
            <i class="fa-solid fa-chevron-down chev" :class="{ open: expanded === p.id }"></i>
          </div>
        </div>

        <div v-if="expanded === p.id" class="lessons">
          <div v-if="!p.lessons?.length" class="empty-lessons">لا توجد دروس بعد — أضف أول درس</div>
          <div v-for="l in p.lessons" :key="l.id" class="lesson-row">
            <div class="lesson-main">
              <span class="lesson-order">{{ l.sort_order }}</span>
              <span class="lesson-title">{{ l.title }}</span>
              <span class="grade-badge">{{ gradeLabel(l.grade_level) }}</span>
            </div>
            <div class="lesson-parts">
              <span class="part" :class="{ on: l.video }" @click="l.video ? openDetail('video', l) : openVideoModal(l)">
                <i class="fa-solid fa-video"></i> فيديو
                <i v-if="l.video" class="fa-solid fa-circle-check tick"></i>
              </span>
              <span class="part" :class="{ on: l.scorm_package }" @click="l.scorm_package ? openDetail('scorm', l) : openScormModal(l)">
                <i class="fa-solid fa-gamepad"></i> SCORM
                <i v-if="l.scorm_package" class="fa-solid fa-circle-check tick"></i>
              </span>
              <span class="part" :class="{ on: l.quiz }" @click="l.quiz ? openDetail('quiz', l) : openQuizModal(l)">
                <i class="fa-solid fa-clipboard-question"></i> اختبار
                <i v-if="l.quiz" class="fa-solid fa-circle-check tick"></i>
              </span>
              <button class="mini-btn ghost danger" title="حذف الدرس" @click="deleteLesson(l)"><i class="fa-solid fa-trash"></i></button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="!paths.length" class="empty-lessons" style="background:#fff;border-radius:12px">لا توجد مسارات بعد</div>
    </div>

    <!-- DETAIL MODAL (view a part) -->
    <Modal :open="modal === 'detail'" :title="detailTitle" @close="modal = null">
      <!-- Video detail -->
      <template v-if="detail.type === 'video'">
        <div class="detail-preview">
          <video v-if="detail.item.url || detail.item.file_path" controls class="detail-video"
                 :src="detail.item.url || ('/storage/' + detail.item.file_path)"></video>
          <div v-else class="no-media"><i class="fa-solid fa-video-slash"></i> لا يوجد ملف فيديو</div>
        </div>
        <div class="detail-rows">
          <div class="d-row"><span>العنوان</span><strong>{{ detail.item.title }}</strong></div>
          <div class="d-row"><span>المدة</span><strong>{{ fmtDuration(detail.item.duration_seconds) }}</strong></div>
          <div class="d-row"><span>حد المشاهدة</span><strong>{{ detail.item.watch_threshold }}٪</strong></div>
          <div class="d-row" v-if="detail.item.url"><span>الرابط</span><a :href="detail.item.url" target="_blank" class="d-link">{{ detail.item.url }}</a></div>
        </div>
      </template>

      <!-- SCORM detail -->
      <template v-else-if="detail.type === 'scorm'">
        <div class="detail-icon-box scorm"><i class="fa-solid fa-gamepad"></i></div>
        <div class="detail-rows">
          <div class="d-row"><span>العنوان</span><strong>{{ detail.item.title }}</strong></div>
          <div class="d-row"><span>الإصدار</span><strong>SCORM {{ detail.item.version }}</strong></div>
          <div class="d-row"><span>نقطة الدخول</span><strong>{{ detail.item.entry_point }}</strong></div>
          <div class="d-row"><span>الحجم</span><strong>{{ ((detail.item.file_size_kb||0)/1024).toFixed(1) }} MB</strong></div>
        </div>
      </template>

      <!-- Quiz detail -->
      <template v-else-if="detail.type === 'quiz'">
        <div class="detail-icon-box quiz"><i class="fa-solid fa-clipboard-question"></i></div>
        <div class="detail-rows">
          <div class="d-row"><span>العنوان</span><strong>{{ detail.item.title }}</strong></div>
          <div class="d-row"><span>نسبة النجاح</span><strong>{{ detail.item.pass_mark }}٪</strong></div>
          <div class="d-row"><span>المحاولات</span><strong>{{ detail.item.max_attempts }}</strong></div>
          <div class="d-row"><span>عدد الأسئلة</span><strong>{{ detail.item.questions?.length ?? '—' }}</strong></div>
        </div>
      </template>

      <template #footer>
        <button class="btn-ghost danger" @click="deletePart"><i class="fa-solid fa-trash"></i> حذف</button>
        <button class="btn-save" @click="editFromDetail"><i class="fa-solid fa-pen"></i> تعديل</button>
      </template>
    </Modal>

    <!-- PATH MODAL -->
    <Modal :open="modal === 'path'" :title="pathForm.id ? 'تعديل المسار' : 'مسار جديد'" @close="modal = null">
      <div class="fg"><label>العنوان</label><input v-model="pathForm.title" class="inp" /></div>
      <div class="fg" v-if="!pathForm.id"><label>المعرّف (slug)</label><input v-model="pathForm.slug" class="inp" placeholder="creativity" /></div>
      <div class="grid2">
        <div class="fg"><label>أيقونة (FA)</label><input v-model="pathForm.icon" class="inp" placeholder="fa-solid fa-lightbulb" /></div>
        <div class="fg"><label>اللون</label><input type="color" v-model="pathForm.color" class="inp color" /></div>
      </div>
      <div class="fg"><label>الوصف</label><textarea v-model="pathForm.description" class="inp" rows="2"></textarea></div>
      <Errors :form="pathForm" />
      <template #footer><button class="btn-save" :disabled="pathForm.processing" @click="savePath">حفظ</button></template>
    </Modal>

    <!-- LESSON MODAL -->
    <Modal :open="modal === 'lesson'" title="درس جديد" @close="modal = null">
      <div class="fg"><label>عنوان الدرس</label><input v-model="lessonForm.title" class="inp" /></div>
      <div class="grid2">
        <div class="fg"><label>المرحلة</label>
          <select v-model="lessonForm.grade_level" class="inp">
            <option value="all">الكل</option><option value="primary">ابتدائي</option>
            <option value="middle">متوسط</option><option value="high">ثانوي</option>
          </select>
        </div>
        <div class="fg"><label>الترتيب</label><input type="number" v-model.number="lessonForm.sort_order" class="inp" min="0" /></div>
      </div>
      <Errors :form="lessonForm" />
      <template #footer><button class="btn-save" :disabled="lessonForm.processing" @click="saveLesson">حفظ</button></template>
    </Modal>

    <!-- VIDEO MODAL -->
    <Modal :open="modal === 'video'" title="فيديو الدرس" @close="modal = null">
      <div class="fg"><label>العنوان</label><input v-model="videoForm.title" class="inp" /></div>

      <!-- Source switch -->
      <div class="src-switch">
        <button type="button" class="src-tab" :class="{ active: videoSource === 'upload' }" @click="videoSource = 'upload'">
          <i class="fa-solid fa-cloud-arrow-up"></i> رفع ملف
        </button>
        <button type="button" class="src-tab" :class="{ active: videoSource === 'url' }" @click="videoSource = 'url'">
          <i class="fa-solid fa-link"></i> رابط خارجي
        </button>
      </div>

      <!-- Upload -->
      <div v-if="videoSource === 'upload'" class="fg">
        <label>ملف الفيديو</label>
        <div class="upload-drop" @click="$refs.vfile.click()" @dragover.prevent @drop.prevent="onVideoDrop">
          <input ref="vfile" type="file" accept="video/*" class="hidden" @change="onVideoPick" />
          <template v-if="videoForm.video_file">
            <i class="fa-solid fa-file-video up-ic"></i>
            <span class="up-name">{{ videoForm.video_file.name }}</span>
            <span class="up-size">{{ (videoForm.video_file.size / 1048576).toFixed(1) }} MB</span>
          </template>
          <template v-else>
            <i class="fa-solid fa-cloud-arrow-up up-ic"></i>
            <span class="up-title">اسحب الفيديو هنا أو اضغط للاختيار</span>
            <span class="up-hint">MP4, MKV, WebM — حد أقصى ٥٠٠ ميجابايت</span>
          </template>
        </div>
        <p v-if="currentVideoFilePath && !videoForm.video_file" class="up-existing">
          <i class="fa-solid fa-circle-check"></i> يوجد ملف محفوظ — اختر ملفاً جديداً لاستبداله
        </p>
      </div>

      <!-- URL -->
      <div v-else class="fg">
        <label>رابط الفيديو (URL)</label>
        <input v-model="videoForm.url" class="inp" placeholder="https://..." />
      </div>

      <!-- Upload progress -->
      <div v-if="videoForm.processing && videoSource === 'upload'" class="up-progress">
        <div class="up-progress-info"><span>جاري الرفع...</span><span>{{ videoForm.progress?.percentage ?? 0 }}%</span></div>
        <div class="up-bar"><div class="up-bar-fill" :style="{ width: (videoForm.progress?.percentage ?? 0) + '%' }"></div></div>
      </div>

      <div class="grid2">
        <div class="fg"><label>المدة (ثانية)</label><input type="number" v-model.number="videoForm.duration_seconds" class="inp" min="0" /></div>
        <div class="fg"><label>حد المشاهدة (٪)</label><input type="number" v-model.number="videoForm.watch_threshold" class="inp" min="50" max="100" /></div>
      </div>
      <Errors :form="videoForm" />
      <template #footer><button class="btn-save" :disabled="videoForm.processing" @click="saveVideo">{{ videoForm.processing ? 'جاري الحفظ...' : 'حفظ' }}</button></template>
    </Modal>

    <!-- SCORM MODAL -->
    <Modal :open="modal === 'scorm'" title="ملف SCORM" @close="modal = null">
      <div class="fg"><label>العنوان</label><input v-model="scormForm.title" class="inp" /></div>
      <div class="grid2">
        <div class="fg"><label>الإصدار</label>
          <select v-model="scormForm.version" class="inp"><option value="1.2">SCORM 1.2</option><option value="2004">SCORM 2004</option></select>
        </div>
        <div class="fg"><label>نقطة الدخول</label><input v-model="scormForm.entry_point" class="inp" placeholder="index.html" /></div>
      </div>
      <div class="fg"><label>ملف الحزمة (.zip)</label><input type="file" accept=".zip" class="inp" @change="scormForm.scorm_file = $event.target.files[0]" /></div>
      <Errors :form="scormForm" />
      <template #footer><button class="btn-save" :disabled="scormForm.processing" @click="saveScorm">رفع</button></template>
    </Modal>

    <!-- QUIZ MODAL -->
    <Modal :open="modal === 'quiz'" :title="quizForm.id ? 'تعديل الاختبار' : 'اختبار جديد'" @close="modal = null">
      <div class="fg"><label>العنوان</label><input v-model="quizForm.title" class="inp" /></div>
      <div class="grid2">
        <div class="fg"><label>نسبة النجاح (٪)</label><input type="number" v-model.number="quizForm.pass_mark" class="inp" min="50" max="100" /></div>
        <div class="fg"><label>عدد المحاولات</label><input type="number" v-model.number="quizForm.max_attempts" class="inp" min="1" max="10" /></div>
      </div>

      <div class="section-label">الأسئلة</div>
      <div class="questions">
        <div v-for="(q, qi) in quizForm.questions" :key="qi" class="q-card">
          <div class="q-top"><span>سؤال {{ qi + 1 }}</span><button class="x" @click="quizForm.questions.splice(qi,1)">×</button></div>
          <input v-model="q.text" class="inp" :placeholder="`نص السؤال ${qi+1}`" />
          <div v-for="(o, oi) in q.options" :key="oi" class="opt">
            <input type="radio" :name="`q${qi}`" :value="oi" v-model="q.correct" />
            <input v-model="q.options[oi]" class="inp" :placeholder="`خيار ${oi+1}`" />
          </div>
        </div>
      </div>
      <button class="mini-btn" @click="addQuestion"><i class="fa-solid fa-plus"></i> سؤال</button>

      <Errors :form="quizForm" />
      <template #footer>
        <button class="btn-save" :disabled="quizForm.processing || (!quizForm.id && !quizForm.questions.length)" @click="saveQuiz">
          {{ quizForm.id ? 'حفظ التعديلات' : 'حفظ الاختبار' }}
        </button>
      </template>
    </Modal>

  </AdminLayout>
</template>

<script setup>
import { ref, computed, defineComponent, h, onMounted, onBeforeUnmount } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  paths:       { type: Array, default: () => [] },
  scorm_files: { type: Array, default: () => [] },
})

const expanded = ref(props.paths[0]?.id ?? null)
const modal = ref(null)
const toggle = (id) => { expanded.value = expanded.value === id ? null : id }

// Close any open modal with the Escape key
const onKeydown = (e) => { if (e.key === 'Escape' && modal.value) modal.value = null }
onMounted(() => document.addEventListener('keydown', onKeydown))
onBeforeUnmount(() => document.removeEventListener('keydown', onKeydown))
const gradeLabel = (g) => ({ all: 'الكل', primary: 'ابتدائي', middle: 'متوسط', high: 'ثانوي' }[g] ?? g)
const fmtDuration = (s) => { s = s || 0; const m = Math.floor(s / 60); const r = s % 60; return `${m}:${String(r).padStart(2, '0')}` }

// ── Detail view ──
const detail = ref({ type: null, lesson: null, item: null })
const detailTitle = computed(() => ({ video: 'تفاصيل الفيديو', scorm: 'تفاصيل SCORM', quiz: 'تفاصيل الاختبار' }[detail.value.type] ?? ''))
const openDetail = (type, lesson) => {
  const item = type === 'video' ? lesson.video : type === 'scorm' ? lesson.scorm_package : lesson.quiz
  detail.value = { type, lesson, item }
  modal.value = 'detail'
}
const editFromDetail = () => {
  const { type, lesson } = detail.value
  if (type === 'video') openVideoModal(lesson)
  else if (type === 'scorm') openScormModal(lesson)
  else openQuizModal(lesson)
}
const deletePart = () => {
  const { type, item } = detail.value
  const label = { video: 'الفيديو', scorm: 'ملف SCORM', quiz: 'الاختبار' }[type]
  if (!confirm(`حذف ${label}؟`)) return
  const routes = {
    video: route('admin.content.videos.destroy', item.id),
    scorm: route('admin.content.scorm.destroy', item.id),
    quiz: route('admin.content.quizzes.destroy', item.id),
  }
  router.delete(routes[type], { preserveScroll: true, onSuccess: () => { modal.value = null } })
}

// ── Path ──
const pathForm = useForm({ id: null, title: '', slug: '', icon: '', color: '#38BDF8', description: '' })
const openPathModal = (p = null) => {
  pathForm.clearErrors()
  if (p) { pathForm.id = p.id; pathForm.title = p.title; pathForm.icon = p.icon ?? ''; pathForm.color = p.color ?? '#38BDF8'; pathForm.description = p.description ?? '' }
  else { pathForm.reset(); pathForm.id = null }
  modal.value = 'path'
}
const savePath = () => {
  const opts = { preserveScroll: true, onSuccess: () => { modal.value = null } }
  pathForm.id ? pathForm.put(route('admin.content.paths.update', pathForm.id), opts)
              : pathForm.post(route('admin.content.paths.store'), opts)
}

// ── Lesson ──
const lessonForm = useForm({ path_id: null, title: '', grade_level: 'all', sort_order: 0 })
const openLessonModal = (p) => { lessonForm.reset(); lessonForm.path_id = p.id; lessonForm.sort_order = (p.lessons?.length ?? 0) + 1; lessonForm.clearErrors(); modal.value = 'lesson' }
const saveLesson = () => lessonForm.post(route('admin.content.lessons.store'), { preserveScroll: true, onSuccess: () => { modal.value = null } })
const deleteLesson = (l) => { if (confirm(`حذف الدرس "${l.title}"؟`)) router.delete(route('admin.content.lessons.destroy', l.id), { preserveScroll: true }) }

// ── Video ──
const videoSource = ref('upload')
const currentVideoFilePath = ref(null)
const videoForm = useForm({ id: null, lesson_id: null, title: '', url: '', video_file: null, duration_seconds: 0, watch_threshold: 80 })
const openVideoModal = (l) => {
  videoForm.reset(); videoForm.lesson_id = l.id
  currentVideoFilePath.value = null
  videoSource.value = 'upload'
  if (l.video) {
    videoForm.id = l.video.id; videoForm.title = l.video.title; videoForm.url = l.video.url ?? ''
    videoForm.duration_seconds = l.video.duration_seconds; videoForm.watch_threshold = l.video.watch_threshold
    currentVideoFilePath.value = l.video.file_path
    videoSource.value = l.video.url ? 'url' : 'upload'
  }
  videoForm.clearErrors(); modal.value = 'video'
}
const onVideoPick = (e) => { videoForm.video_file = e.target.files[0] ?? null }
const onVideoDrop = (e) => { videoForm.video_file = e.dataTransfer.files[0] ?? null }
const saveVideo = () => {
  // Only send the field relevant to the chosen source
  if (videoSource.value === 'upload') videoForm.url = ''
  else videoForm.video_file = null

  const opts = { preserveScroll: true, forceFormData: true, onSuccess: () => { modal.value = null } }
  if (videoForm.id) {
    videoForm.transform((d) => ({ ...d, _method: 'put' })).post(route('admin.content.videos.update', videoForm.id), opts)
  } else {
    videoForm.post(route('admin.content.videos.store'), opts)
  }
}

// ── SCORM ──
const scormForm = useForm({ lesson_id: null, title: '', version: '1.2', entry_point: 'index.html', scorm_file: null })
const openScormModal = (l) => { scormForm.reset(); scormForm.lesson_id = l.id; if (l.scorm_package) { scormForm.title = l.scorm_package.title; scormForm.version = l.scorm_package.version } scormForm.clearErrors(); modal.value = 'scorm' }
const saveScorm = () => scormForm.post(route('admin.content.scorm.store'), { forceFormData: true, preserveScroll: true, onSuccess: () => { modal.value = null } })

// ── Quiz ──
const quizForm = useForm({ id: null, lesson_id: null, title: '', pass_mark: 70, max_attempts: 3, questions: [] })
const openQuizModal = (l) => {
  quizForm.reset(); quizForm.lesson_id = l.id
  if (l.quiz) {
    quizForm.id = l.quiz.id
    quizForm.title = l.quiz.title
    quizForm.pass_mark = l.quiz.pass_mark
    quizForm.max_attempts = l.quiz.max_attempts
    quizForm.questions = (l.quiz.questions ?? []).map((q) => ({
      text: q.text,
      options: (q.options ?? []).sort((a, b) => a.sort_order - b.sort_order).map((o) => o.text),
      correct: (q.options ?? []).findIndex((o) => o.is_correct),
    }))
  } else {
    quizForm.questions = []
  }
  quizForm.clearErrors(); modal.value = 'quiz'
}
const addQuestion = () => quizForm.questions.push({ text: '', options: ['', '', '', ''], correct: 0 })
const saveQuiz = () => {
  const opts = { preserveScroll: true, onSuccess: () => { modal.value = null } }
  if (quizForm.id) {
    quizForm.put(route('admin.content.quizzes.update', quizForm.id), opts)
  } else {
    quizForm.post(route('admin.content.quizzes.store'), opts)
  }
}

// ── Inline tiny components ──
const Modal = defineComponent({
  props: ['open', 'title'],
  emits: ['close'],
  setup(p, { slots, emit }) {
    const close = () => emit('close')
    return () => p.open ? h('div', {
      class: 'modal-overlay',
      onClick: (e) => { if (e.target.classList.contains('modal-overlay')) close() },
    }, [
      h('div', { class: 'modal' }, [
        h('div', { class: 'modal-header' }, [
          h('h3', p.title),
          h('button', { class: 'modal-close', onClick: close }, '×'),
        ]),
        h('div', { class: 'modal-body' }, slots.default?.()),
        h('div', { class: 'modal-footer' }, slots.footer?.()),
      ]),
    ]) : null
  },
})

const Errors = (p) => {
  const errs = Object.values(p.form.errors)
  return errs.length ? h('div', { class: 'form-errors' }, errs.map((e) => h('p', e))) : null
}
Errors.props = ['form']
</script>

<style scoped>
.page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
.page-title { font-size: 1.6rem; font-weight: 900; color: #1E293B; }
.page-sub { font-size: .88rem; color: #94A3B8; margin-top: .2rem; }
.btn-add { background: #8B5CF6; color: white; font-family: inherit; font-weight: 700; font-size: .9rem; padding: .65rem 1.4rem; border-radius: 10px; border: none; cursor: pointer; display: flex; align-items: center; gap: .5rem; }

.paths-accordion { display: flex; flex-direction: column; gap: .8rem; }
.path-block { background: white; border-radius: 14px; box-shadow: 0 1px 8px rgba(0,0,0,.05); overflow: hidden; }
.path-head { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.3rem; cursor: pointer; }
.path-head:hover { background: #FAFAFA; }
.path-head-info { display: flex; align-items: center; gap: .7rem; }
.path-dot { width: 12px; height: 12px; border-radius: 50%; }
.path-name { font-weight: 800; color: #1E293B; }
.path-count { font-size: .75rem; color: #64748B; background: #F1F5F9; padding: .15rem .6rem; border-radius: 50px; }
.path-head-actions { display: flex; align-items: center; gap: .5rem; }
.chev { color: #94A3B8; transition: transform .2s; }
.chev.open { transform: rotate(180deg); }

.mini-btn { background: #EEF2FF; color: #4F46E5; border: none; font-family: inherit; font-weight: 700; font-size: .78rem; padding: .35rem .8rem; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: .3rem; }
.mini-btn.ghost { background: #F1F5F9; color: #64748B; }
.mini-btn.danger { background: #FEF2F2; color: #DC2626; }

.lessons { border-top: 1px solid #F1F5F9; padding: .8rem 1.3rem 1rem; }
.empty-lessons { color: #94A3B8; text-align: center; padding: 1.2rem; font-size: .88rem; }
.lesson-row { display: flex; align-items: center; justify-content: space-between; padding: .7rem .9rem; border: 1px solid #F1F5F9; border-radius: 10px; margin-bottom: .5rem; flex-wrap: wrap; gap: .6rem; }
.lesson-main { display: flex; align-items: center; gap: .7rem; }
.lesson-order { width: 26px; height: 26px; border-radius: 8px; background: #F1F5F9; color: #64748B; font-weight: 800; font-size: .8rem; display: flex; align-items: center; justify-content: center; }
.lesson-title { font-weight: 700; color: #1E293B; font-size: .9rem; }
.grade-badge { font-size: .72rem; font-weight: 700; color: #0E7490; background: #E0F4FF; padding: .15rem .6rem; border-radius: 50px; }
.lesson-parts { display: flex; align-items: center; gap: .4rem; }
.part { font-size: .76rem; font-weight: 700; color: #94A3B8; background: #F8FAFC; border: 1.5px dashed #E2E8F0; padding: .3rem .7rem; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: .3rem; }
.part.on { color: #15803D; background: #F0FDF4; border-style: solid; border-color: #BBF7D0; }
.part:hover { border-color: #8B5CF6; color: #8B5CF6; }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); z-index: 200; display: flex; align-items: center; justify-content: center; padding: 1rem; animation: fade .15s ease; }
@keyframes fade { from { opacity: 0 } to { opacity: 1 } }
.modal { background: white; border-radius: 22px; width: 540px; max-width: 95vw; max-height: 90vh; overflow-y: auto; box-shadow: 0 24px 70px rgba(0,0,0,.25); animation: pop .18s cubic-bezier(.22,1,.36,1); }
@keyframes pop { from { transform: scale(.96); opacity: 0 } to { transform: scale(1); opacity: 1 } }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1.2rem 1.5rem; border-bottom: 1px solid #F1F5F9; position: sticky; top: 0; background: white; z-index: 1; }
.modal-header h3 { font-size: 1.1rem; font-weight: 800; color: #1E293B; }
.modal-close { background: #F1F5F9; border: none; cursor: pointer; font-size: 1.2rem; color: #64748B; line-height: 1; width: 32px; height: 32px; border-radius: 50%; transition: background .2s, color .2s; }
.modal-close:hover { background: #FEE2E2; color: #DC2626; }
.modal-body { padding: 1.5rem; }
.modal-footer { padding: 1rem 1.5rem; border-top: 1px solid #F1F5F9; display: flex; justify-content: flex-end; gap: .6rem; }
.btn-save { background: #8B5CF6; color: white; border: none; font-family: inherit; font-weight: 700; font-size: .9rem; padding: .7rem 1.7rem; border-radius: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: .5rem; box-shadow: 0 4px 12px rgba(139,92,246,.3); transition: transform .15s; }
.btn-save:hover:not(:disabled) { transform: translateY(-2px); }
.btn-save:disabled { opacity: .5; cursor: not-allowed; box-shadow: none; }
.btn-ghost { background: #F1F5F9; color: #64748B; border: none; font-family: inherit; font-weight: 700; font-size: .9rem; padding: .7rem 1.4rem; border-radius: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: .5rem; transition: background .2s; }
.btn-ghost:hover { background: #E2E8F0; }
.btn-ghost.danger { background: #FEF2F2; color: #DC2626; }
.btn-ghost.danger:hover { background: #FEE2E2; }

/* Detail view */
.tick { color: #16A34A; font-size: .7rem; margin-right: .15rem; }
.detail-preview { margin-bottom: 1.2rem; }
.detail-video { width: 100%; border-radius: 14px; background: #000; max-height: 280px; }
.no-media { background: #F8FAFC; border: 1.5px dashed #E2E8F0; border-radius: 14px; padding: 2.5rem; text-align: center; color: #94A3B8; font-weight: 700; }
.no-media i { display: block; font-size: 2rem; margin-bottom: .5rem; }
.detail-icon-box { width: 80px; height: 80px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 0 auto 1.4rem; }
.detail-icon-box.scorm { background: #F5F3FF; color: #8B5CF6; }
.detail-icon-box.quiz { background: #FFF7ED; color: #EA580C; }
.detail-rows { display: flex; flex-direction: column; gap: .2rem; }
.d-row { display: flex; justify-content: space-between; align-items: center; padding: .75rem .2rem; border-bottom: 1px solid #F1F5F9; font-size: .9rem; }
.d-row:last-child { border-bottom: none; }
.d-row span { color: #94A3B8; font-weight: 600; }
.d-row strong { color: #1E293B; font-weight: 700; }
.d-link { color: #8B5CF6; font-size: .82rem; max-width: 60%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.section-label { font-size: .82rem; font-weight: 800; color: #64748B; margin: 1.2rem 0 .6rem; }
.edit-note { background: #EFF6FF; color: #1E40AF; border-radius: 10px; padding: .7rem 1rem; font-size: .82rem; font-weight: 600; margin-top: 1rem; display: flex; align-items: center; gap: .5rem; }

.fg { display: flex; flex-direction: column; gap: .4rem; margin-bottom: 1rem; }
.fg label { font-size: .82rem; font-weight: 700; color: #475569; }
.inp { padding: .6rem .9rem; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; font-size: .9rem; color: #1E293B; width: 100%; }
.inp:focus { outline: none; border-color: #8B5CF6; }
.inp.color { height: 42px; padding: .2rem; }
.grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: .8rem; }

.questions { display: flex; flex-direction: column; gap: .8rem; margin: 1rem 0; }
.q-card { border: 1.5px solid #F1F5F9; border-radius: 12px; padding: .9rem; }
.q-top { display: flex; justify-content: space-between; font-weight: 800; font-size: .82rem; color: #475569; margin-bottom: .5rem; }
.q-top .x { background: none; border: none; cursor: pointer; font-size: 1.2rem; color: #DC2626; }
.opt { display: flex; align-items: center; gap: .5rem; margin-top: .4rem; }
.opt input[type=radio] { accent-color: #16A34A; flex-shrink: 0; }

.form-errors { margin-top: 1rem; background: #FEF2F2; border: 1.5px solid #FECACA; border-radius: 10px; padding: .7rem 1rem; }
.form-errors p { color: #DC2626; font-size: .82rem; font-weight: 700; margin: .1rem 0; }

@media (max-width: 640px) { .grid2 { grid-template-columns: 1fr; } }
</style>

<!-- Non-scoped: the Modal/forms render via h() and don't inherit scoped data attributes -->
<style>
.content-modal-overlay,
.modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); z-index: 200; display: flex; align-items: center; justify-content: center; padding: 1rem; animation: cm-fade .15s ease; }
@keyframes cm-fade { from { opacity: 0 } to { opacity: 1 } }
.modal-overlay .modal { background: #fff; border-radius: 22px; width: 540px; max-width: 95vw; max-height: 90vh; overflow-y: auto; box-shadow: 0 24px 70px rgba(0,0,0,.25); animation: cm-pop .18s cubic-bezier(.22,1,.36,1); }
@keyframes cm-pop { from { transform: scale(.96); opacity: 0 } to { transform: scale(1); opacity: 1 } }
.modal-overlay .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1.2rem 1.5rem; border-bottom: 1px solid #F1F5F9; position: sticky; top: 0; background: #fff; z-index: 1; }
.modal-overlay .modal-header h3 { font-size: 1.1rem; font-weight: 800; color: #1E293B; }
.modal-overlay .modal-close { background: #F1F5F9; border: none; cursor: pointer; font-size: 1.2rem; color: #64748B; line-height: 1; width: 32px; height: 32px; border-radius: 50%; transition: background .2s, color .2s; }
.modal-overlay .modal-close:hover { background: #FEE2E2; color: #DC2626; }
.modal-overlay .modal-body { padding: 1.5rem; }
.modal-overlay .modal-footer { padding: 1rem 1.5rem; border-top: 1px solid #F1F5F9; display: flex; justify-content: flex-end; gap: .6rem; }

.modal-overlay .btn-save { background: #8B5CF6; color: #fff; border: none; font-family: inherit; font-weight: 700; font-size: .9rem; padding: .7rem 1.7rem; border-radius: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: .5rem; box-shadow: 0 4px 12px rgba(139,92,246,.3); transition: transform .15s; }
.modal-overlay .btn-save:hover:not(:disabled) { transform: translateY(-2px); }
.modal-overlay .btn-save:disabled { opacity: .5; cursor: not-allowed; box-shadow: none; }
.modal-overlay .btn-ghost { background: #F1F5F9; color: #64748B; border: none; font-family: inherit; font-weight: 700; font-size: .9rem; padding: .7rem 1.4rem; border-radius: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: .5rem; transition: background .2s; }
.modal-overlay .btn-ghost:hover { background: #E2E8F0; }
.modal-overlay .btn-ghost.danger { background: #FEF2F2; color: #DC2626; }
.modal-overlay .btn-ghost.danger:hover { background: #FEE2E2; }

.modal-overlay .detail-preview { margin-bottom: 1.2rem; }
.modal-overlay .detail-video { width: 100%; border-radius: 14px; background: #000; max-height: 280px; }
.modal-overlay .no-media { background: #F8FAFC; border: 1.5px dashed #E2E8F0; border-radius: 14px; padding: 2.5rem; text-align: center; color: #94A3B8; font-weight: 700; }
.modal-overlay .no-media i { display: block; font-size: 2rem; margin-bottom: .5rem; }
.modal-overlay .detail-icon-box { width: 80px; height: 80px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 0 auto 1.4rem; }
.modal-overlay .detail-icon-box.scorm { background: #F5F3FF; color: #8B5CF6; }
.modal-overlay .detail-icon-box.quiz { background: #FFF7ED; color: #EA580C; }
.modal-overlay .detail-rows { display: flex; flex-direction: column; gap: .2rem; }
.modal-overlay .d-row { display: flex; justify-content: space-between; align-items: center; padding: .75rem .2rem; border-bottom: 1px solid #F1F5F9; font-size: .9rem; }
.modal-overlay .d-row:last-child { border-bottom: none; }
.modal-overlay .d-row span { color: #94A3B8; font-weight: 600; }
.modal-overlay .d-row strong { color: #1E293B; font-weight: 700; }
.modal-overlay .d-link { color: #8B5CF6; font-size: .82rem; max-width: 60%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.modal-overlay .section-label { font-size: .82rem; font-weight: 800; color: #64748B; margin: 1.2rem 0 .6rem; }
.modal-overlay .edit-note { background: #EFF6FF; color: #1E40AF; border-radius: 10px; padding: .7rem 1rem; font-size: .82rem; font-weight: 600; margin-top: 1rem; display: flex; align-items: center; gap: .5rem; }

.modal-overlay .fg { display: flex; flex-direction: column; gap: .4rem; margin-bottom: 1rem; }
.modal-overlay .fg label { font-size: .82rem; font-weight: 700; color: #475569; }
.modal-overlay .inp { padding: .6rem .9rem; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; font-size: .9rem; color: #1E293B; width: 100%; }
.modal-overlay .inp:focus { outline: none; border-color: #8B5CF6; }
.modal-overlay .inp.color { height: 42px; padding: .2rem; }
.modal-overlay .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: .8rem; }

.modal-overlay .mini-btn { background: #EEF2FF; color: #4F46E5; border: none; font-family: inherit; font-weight: 700; font-size: .78rem; padding: .35rem .8rem; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: .3rem; }
.modal-overlay .questions { display: flex; flex-direction: column; gap: .8rem; margin: 1rem 0; }
.modal-overlay .q-card { border: 1.5px solid #F1F5F9; border-radius: 12px; padding: .9rem; }
.modal-overlay .q-top { display: flex; justify-content: space-between; font-weight: 800; font-size: .82rem; color: #475569; margin-bottom: .5rem; }
.modal-overlay .q-top .x { background: none; border: none; cursor: pointer; font-size: 1.2rem; color: #DC2626; }
.modal-overlay .opt { display: flex; align-items: center; gap: .5rem; margin-top: .4rem; }
.modal-overlay .opt input[type=radio] { accent-color: #16A34A; flex-shrink: 0; }
.modal-overlay .form-errors { margin-top: 1rem; background: #FEF2F2; border: 1.5px solid #FECACA; border-radius: 10px; padding: .7rem 1rem; }
.modal-overlay .form-errors p { color: #DC2626; font-size: .82rem; font-weight: 700; margin: .1rem 0; }

@media (max-width: 640px) { .modal-overlay .grid2 { grid-template-columns: 1fr; } }

/* Video upload */
.modal-overlay .hidden { display: none; }
.modal-overlay .src-switch { display: flex; gap: .5rem; margin-bottom: 1rem; background: #F1F5F9; padding: .3rem; border-radius: 12px; }
.modal-overlay .src-tab { flex: 1; background: none; border: none; cursor: pointer; font-family: inherit; font-weight: 700; font-size: .85rem; color: #64748B; padding: .55rem; border-radius: 9px; display: inline-flex; align-items: center; justify-content: center; gap: .4rem; transition: all .2s; }
.modal-overlay .src-tab.active { background: #fff; color: #8B5CF6; box-shadow: 0 2px 6px rgba(0,0,0,.08); }
.modal-overlay .upload-drop { border: 2px dashed #CBD5E1; border-radius: 14px; padding: 1.8rem 1rem; text-align: center; cursor: pointer; transition: border-color .2s, background .2s; display: flex; flex-direction: column; align-items: center; gap: .4rem; }
.modal-overlay .upload-drop:hover { border-color: #8B5CF6; background: #FAF5FF; }
.modal-overlay .up-ic { font-size: 2.2rem; color: #8B5CF6; }
.modal-overlay .up-title { font-weight: 700; color: #475569; font-size: .9rem; }
.modal-overlay .up-hint { font-size: .76rem; color: #94A3B8; }
.modal-overlay .up-name { font-weight: 700; color: #1E293B; font-size: .9rem; word-break: break-all; }
.modal-overlay .up-size { font-size: .76rem; color: #94A3B8; }
.modal-overlay .up-existing { font-size: .8rem; color: #15803D; font-weight: 700; margin-top: .5rem; display: flex; align-items: center; gap: .4rem; }
.modal-overlay .up-progress { margin: .2rem 0 1rem; }
.modal-overlay .up-progress-info { display: flex; justify-content: space-between; font-size: .8rem; font-weight: 700; color: #64748B; margin-bottom: .35rem; }
.modal-overlay .up-bar { height: 8px; background: #E2E8F0; border-radius: 99px; overflow: hidden; }
.modal-overlay .up-bar-fill { height: 100%; background: linear-gradient(90deg, #8B5CF6, #6D28D9); border-radius: 99px; transition: width .2s; }
</style>
