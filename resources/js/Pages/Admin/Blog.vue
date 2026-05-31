<template>
  <AdminLayout page-title="إدارة المدونة">

    <div class="blog-shell" :class="{ editing: view === 'editor' }">

      <!-- ══════════ LIST VIEW ══════════ -->
      <div v-if="view === 'list'">

        <!-- HEADER -->
        <div class="page-header">
          <div>
            <h1 class="page-title">مجتمع بارع — المدونة</h1>
            <p class="page-sub">{{ posts.length }} مقالة منشورة</p>
          </div>
          <button class="btn-new" @click="newPost">
            <i class="fa-solid fa-pen-nib"></i> مقالة جديدة
          </button>
        </div>

        <!-- FILTERS -->
        <div class="list-toolbar">
          <div class="search-wrap">
            <i class="fa-solid fa-search search-ico"></i>
            <input v-model="search" type="text" placeholder="ابحث في المقالات..." class="search-input" />
          </div>
          <div class="filter-group">
            <select v-model="filterCat" class="filter-sel">
              <option value="">كل الأقسام</option>
              <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
            </select>
            <select v-model="filterStatus" class="filter-sel">
              <option value="">كل الحالات</option>
              <option value="published">منشورة</option>
              <option value="draft">مسودة</option>
            </select>
          </div>
        </div>

        <!-- POSTS GRID -->
        <div class="posts-grid">
          <div v-for="post in filteredPosts" :key="post.id" class="post-card">
            <!-- Thumbnail -->
            <div class="post-thumb" :style="{ background: post.thumbBg }">
              <i :class="post.thumbIcon" class="thumb-ico"></i>
              <span class="post-status-badge" :class="post.status">
                {{ post.status === 'published' ? 'منشورة' : 'مسودة' }}
              </span>
            </div>
            <div class="post-body">
              <div class="post-cat">{{ post.category }}</div>
              <h3 class="post-title">{{ post.title }}</h3>
              <p class="post-excerpt">{{ post.excerpt }}</p>
              <div class="post-meta">
                <span><i class="fa-solid fa-user"></i> {{ post.author }}</span>
                <span><i class="fa-solid fa-calendar"></i> {{ post.date }}</span>
                <span><i class="fa-solid fa-eye"></i> {{ post.views }}</span>
              </div>
            </div>
            <div class="post-footer">
              <div class="post-tags">
                <span v-for="tag in post.tags.slice(0,2)" :key="tag" class="post-tag"># {{ tag }}</span>
              </div>
              <div class="post-actions">
                <button class="act-sm preview" title="معاينة" @click="previewPost(post)"><i class="fa-solid fa-eye"></i></button>
                <button class="act-sm edit" title="تعديل" @click="editPost(post)"><i class="fa-solid fa-pen"></i></button>
                <button class="act-sm" :class="post.status === 'published' ? 'unpublish' : 'publish'"
                  :title="post.status === 'published' ? 'إلغاء النشر' : 'نشر'"
                  @click="togglePublish(post)">
                  <i :class="post.status === 'published' ? 'fa-solid fa-eye-slash' : 'fa-solid fa-paper-plane'"></i>
                </button>
                <button class="act-sm delete" title="حذف" @click="deletePost(post)"><i class="fa-solid fa-trash"></i></button>
              </div>
            </div>
          </div>

          <!-- EMPTY STATE -->
          <div v-if="filteredPosts.length === 0" class="empty-state">
            <i class="fa-solid fa-newspaper"></i>
            <p>لا توجد مقالات مطابقة</p>
          </div>
        </div>

      </div>

      <!-- ══════════ EDITOR VIEW ══════════ -->
      <div v-if="view === 'editor'" class="editor-wrap">

        <!-- Editor Header -->
        <div class="editor-topbar">
          <button class="btn-back" @click="view = 'list'">
            <i class="fa-solid fa-arrow-right"></i> العودة للمدونة
          </button>
          <div class="editor-status">
            <span class="autosave-indicator" v-if="autoSaved">
              <i class="fa-solid fa-circle-check" style="color:#16A34A"></i> تم الحفظ التلقائي
            </span>
          </div>
          <div class="editor-btns">
            <button class="btn-draft" @click="saveDraft">
              <i class="fa-solid fa-floppy-disk"></i> حفظ كمسودة
            </button>
            <button class="btn-publish" @click="publishPost">
              <i class="fa-solid fa-paper-plane"></i> نشر المقالة
            </button>
          </div>
        </div>

        <div class="editor-layout">

          <!-- MAIN EDITOR -->
          <div class="editor-main">
            <!-- Title -->
            <input
              v-model="currentPost.title"
              class="post-title-input"
              placeholder="عنوان المقالة..."
            />

            <!-- Toolbar -->
            <div class="editor-toolbar">
              <button v-for="btn in toolbarBtns" :key="btn.cmd"
                class="tool-btn" :title="btn.label"
                @click="execCmd(btn.cmd, btn.val)">
                <i :class="btn.icon"></i>
              </button>
              <div class="tool-sep"></div>
              <button class="tool-btn" title="إضافة صورة" @click="$refs.imgInput.click()">
                <i class="fa-solid fa-image"></i>
              </button>
              <input ref="imgInput" type="file" accept="image/*" class="hidden-input" @change="insertImage" />
              <button class="tool-btn" title="رابط" @click="insertLink"><i class="fa-solid fa-link"></i></button>
              <div class="tool-sep"></div>
              <select class="tool-select" @change="execCmd('formatBlock', $event.target.value)">
                <option value="p">فقرة</option>
                <option value="h2">عنوان رئيسي</option>
                <option value="h3">عنوان فرعي</option>
                <option value="blockquote">اقتباس</option>
              </select>
            </div>

            <!-- Content Editable -->
            <div
              ref="editorRef"
              class="editor-content"
              contenteditable="true"
              dir="rtl"
              @input="onEditorInput"
              v-html="currentPost.content"
            ></div>

            <!-- Word Count -->
            <div class="editor-footer-bar">
              <span>{{ wordCount }} كلمة</span>
              <span>وقت القراءة: {{ readTime }} دقيقة</span>
            </div>
          </div>

          <!-- SIDEBAR -->
          <div class="editor-sidebar">

            <!-- Publish Settings -->
            <div class="sidebar-card">
              <div class="sc-header"><i class="fa-solid fa-gear"></i> إعدادات النشر</div>
              <div class="sc-body">
                <div class="form-group">
                  <label>القسم</label>
                  <select v-model="currentPost.category" class="form-input">
                    <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>الكاتب</label>
                  <input v-model="currentPost.author" type="text" class="form-input" />
                </div>
                <div class="form-group">
                  <label>وصف مختصر (للبطاقة)</label>
                  <textarea v-model="currentPost.excerpt" class="form-input" rows="3" placeholder="ملخص يظهر في قائمة المقالات..."></textarea>
                </div>
              </div>
            </div>

            <!-- Featured Image -->
            <div class="sidebar-card">
              <div class="sc-header"><i class="fa-solid fa-image"></i> صورة المقالة</div>
              <div class="sc-body">
                <div class="feat-img-zone" @click="$refs.featImgInput.click()" :style="{ backgroundImage: currentPost.featuredImg ? `url(${currentPost.featuredImg})` : 'none' }">
                  <input ref="featImgInput" type="file" accept="image/*" class="hidden-input" @change="setFeaturedImg" />
                  <div v-if="!currentPost.featuredImg" class="feat-img-placeholder">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>اضغط لرفع الصورة</span>
                  </div>
                </div>
                <button v-if="currentPost.featuredImg" class="btn-remove-img" @click="currentPost.featuredImg = ''">
                  <i class="fa-solid fa-xmark"></i> إزالة الصورة
                </button>
              </div>
            </div>

            <!-- Tags -->
            <div class="sidebar-card">
              <div class="sc-header"><i class="fa-solid fa-tags"></i> الوسوم</div>
              <div class="sc-body">
                <div class="tags-input-wrap">
                  <input v-model="tagInput" class="form-input" placeholder="أضف وسماً واضغط Enter..."
                    @keydown.enter.prevent="addTag" />
                </div>
                <div class="tags-list">
                  <span v-for="(tag, i) in currentPost.tags" :key="tag" class="tag-chip">
                    {{ tag }}
                    <button @click="currentPost.tags.splice(i, 1)" class="tag-remove">×</button>
                  </span>
                </div>
              </div>
            </div>

            <!-- SEO -->
            <div class="sidebar-card">
              <div class="sc-header"><i class="fa-solid fa-magnifying-glass"></i> SEO</div>
              <div class="sc-body">
                <div class="form-group">
                  <label>عنوان SEO</label>
                  <input v-model="currentPost.seoTitle" class="form-input" placeholder="عنوان للمحركات..." />
                  <div class="char-count" :class="{ warn: currentPost.seoTitle.length > 60 }">
                    {{ currentPost.seoTitle.length }}/60
                  </div>
                </div>
                <div class="form-group">
                  <label>وصف SEO</label>
                  <textarea v-model="currentPost.seoDesc" class="form-input" rows="2" placeholder="وصف للمحركات..."></textarea>
                  <div class="char-count" :class="{ warn: currentPost.seoDesc.length > 160 }">
                    {{ currentPost.seoDesc.length }}/160
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>

    <!-- POST PREVIEW MODAL -->
    <div v-if="previewModal" class="modal-overlay" @click.self="previewModal = false">
      <div class="preview-modal">
        <div class="preview-modal-header">
          <h3>{{ previewData?.title }}</h3>
          <button @click="previewModal = false"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="preview-body" dir="rtl">
          <div class="preview-meta">
            <span>{{ previewData?.author }}</span> · <span>{{ previewData?.date }}</span>
            <span class="preview-cat">{{ previewData?.category }}</span>
          </div>
          <p class="preview-excerpt">{{ previewData?.excerpt }}</p>
          <div class="preview-content" v-html="previewData?.content"></div>
        </div>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const view = ref('list')
const search = ref('')
const filterCat = ref('')
const filterStatus = ref('')
const autoSaved = ref(false)
const tagInput = ref('')
const editorRef = ref(null)
const previewModal = ref(false)
const previewData = ref(null)

const categories = ['تطوير الذات', 'التعليم', 'المهارات الحياتية', 'الوعي المالي', 'التقنية والرقمنة', 'الذكاء العاطفي']

const emptyPost = () => ({
  id: null, title: '', content: '<p>ابدأ كتابة مقالتك هنا...</p>',
  excerpt: '', category: 'التعليم', author: 'فريق بارع',
  tags: [], featuredImg: '', seoTitle: '', seoDesc: '',
  status: 'draft', date: '', views: 0,
  thumbBg: 'linear-gradient(135deg,#E0F4FF,#FCE7F3)',
  thumbIcon: 'fa-solid fa-newspaper'
})

const currentPost = ref(emptyPost())

const posts = ref([
  { id: 1, title: 'لماذا تعليم الوعي المالي ضروري لأبنائنا؟',    category: 'الوعي المالي',       author: 'أ. سعد المالكي',  date: '٢٠٢٦/٠٥/٢٠', views: 1240, status: 'published', excerpt: 'نستعرض في هذا المقال أهمية تعليم الأطفال مفاهيم المال والادخار منذ الصغر...', tags: ['وعي مالي', 'تعليم', 'ادخار'], thumbBg: 'linear-gradient(135deg,#F0FDF4,#BEF264)', thumbIcon: 'fa-solid fa-sack-dollar', content: '<h2>أهمية الوعي المالي</h2><p>...</p>' },
  { id: 2, title: 'كيف تنمّي مهارة الإلقاء عند طفلك؟',           category: 'المهارات الحياتية',  author: 'د. نورة الغامدي', date: '٢٠٢٦/٠٥/١٥', views: 890,  status: 'published', excerpt: 'الإلقاء الجيد مهارة تُكتسب لا موهبة تُولد بها — إليك ٧ خطوات عملية...', tags: ['إلقاء', 'تواصل', 'ثقة'],     thumbBg: 'linear-gradient(135deg,#FCE7F3,#F9A8D4)', thumbIcon: 'fa-solid fa-microphone', content: '<h2>مقدمة</h2><p>...</p>' },
  { id: 3, title: '٥ تطبيقات تساعد طفلك على تعلم البرمجة',       category: 'التقنية والرقمنة',   author: 'فريق بارع',       date: '٢٠٢٦/٠٥/١٠', views: 2100, status: 'published', excerpt: 'اخترنا لك أفضل التطبيقات المجانية لتعليم البرمجة للأطفال من سن ٦ إلى ١٤...', tags: ['برمجة', 'تقنية', 'أطفال'],  thumbBg: 'linear-gradient(135deg,#F5F3FF,#DDD6FE)', thumbIcon: 'fa-solid fa-laptop-code', content: '<h2>مقدمة</h2><p>...</p>' },
  { id: 4, title: 'كيف يؤثر الذكاء العاطفي على نجاح طفلك؟',     category: 'الذكاء العاطفي',     author: 'أ. سعد المالكي',  date: '٢٠٢٦/٠٥/٠٥', views: 650,  status: 'draft',     excerpt: 'الأطفال ذوو الذكاء العاطفي المرتفع يتمتعون بعلاقات أفضل ونتائج أكاديمية أعلى...', tags: ['ذكاء عاطفي', 'نجاح'],       thumbBg: 'linear-gradient(135deg,#FFF7ED,#FED7AA)', thumbIcon: 'fa-solid fa-heart', content: '<h2>مقدمة</h2><p>...</p>' },
])

const filteredPosts = computed(() =>
  posts.value.filter(p => {
    const s = !search.value     || p.title.includes(search.value) || p.excerpt.includes(search.value)
    const c = !filterCat.value  || p.category === filterCat.value
    const st= !filterStatus.value || p.status === filterStatus.value
    return s && c && st
  })
)

const wordCount = computed(() => {
  const text = currentPost.value.content.replace(/<[^>]+>/g, ' ').trim()
  return text ? text.split(/\s+/).length : 0
})
const readTime = computed(() => Math.max(1, Math.ceil(wordCount.value / 200)))

const toolbarBtns = [
  { cmd: 'bold',          val: null, icon: 'fa-solid fa-bold',          label: 'عريض' },
  { cmd: 'italic',        val: null, icon: 'fa-solid fa-italic',        label: 'مائل' },
  { cmd: 'underline',     val: null, icon: 'fa-solid fa-underline',     label: 'تسطير' },
  { cmd: 'insertUnorderedList', val: null, icon: 'fa-solid fa-list-ul', label: 'قائمة' },
  { cmd: 'insertOrderedList',   val: null, icon: 'fa-solid fa-list-ol', label: 'قائمة مرقّمة' },
  { cmd: 'justifyRight',  val: null, icon: 'fa-solid fa-align-right',   label: 'يميناً' },
  { cmd: 'justifyCenter', val: null, icon: 'fa-solid fa-align-center',  label: 'وسطاً' },
  { cmd: 'justifyLeft',   val: null, icon: 'fa-solid fa-align-left',    label: 'يساراً' },
]

const execCmd = (cmd, val = null) => {
  document.execCommand(cmd, false, val)
  editorRef.value?.focus()
}

const onEditorInput = () => {
  currentPost.value.content = editorRef.value?.innerHTML ?? ''
  triggerAutoSave()
}

let autoSaveTimer = null
const triggerAutoSave = () => {
  clearTimeout(autoSaveTimer)
  autoSaveTimer = setTimeout(() => {
    autoSaved.value = true
    setTimeout(() => autoSaved.value = false, 3000)
  }, 1500)
}

const insertImage = (e) => {
  const file = e.target.files[0]
  if (!file) return
  const reader = new FileReader()
  reader.onload = (ev) => {
    execCmd('insertImage', ev.target.result)
  }
  reader.readAsDataURL(file)
}

const insertLink = () => {
  const url = prompt('أدخل الرابط:')
  if (url) execCmd('createLink', url)
}

const setFeaturedImg = (e) => {
  const file = e.target.files[0]
  if (!file) return
  const reader = new FileReader()
  reader.onload = (ev) => { currentPost.value.featuredImg = ev.target.result }
  reader.readAsDataURL(file)
}

const addTag = () => {
  const tag = tagInput.value.trim()
  if (tag && !currentPost.value.tags.includes(tag)) {
    currentPost.value.tags.push(tag)
  }
  tagInput.value = ''
}

const newPost = () => {
  currentPost.value = emptyPost()
  view.value = 'editor'
}

const editPost = (post) => {
  currentPost.value = { ...post }
  view.value = 'editor'
}

const saveDraft = () => {
  saveCurrentPost('draft')
  alert('تم حفظ المسودة!')
}

const publishPost = () => {
  saveCurrentPost('published')
  alert('تم نشر المقالة!')
  view.value = 'list'
}

const saveCurrentPost = (status) => {
  currentPost.value.status = status
  currentPost.value.date = new Date().toLocaleDateString('ar-SA')
  if (currentPost.value.id) {
    const idx = posts.value.findIndex(p => p.id === currentPost.value.id)
    if (idx !== -1) posts.value[idx] = { ...currentPost.value }
  } else {
    currentPost.value.id = Date.now()
    posts.value.unshift({ ...currentPost.value })
  }
}

const togglePublish = (post) => {
  post.status = post.status === 'published' ? 'draft' : 'published'
}

const deletePost = (post) => {
  if (confirm(`حذف "${post.title}"؟`)) {
    const idx = posts.value.findIndex(p => p.id === post.id)
    if (idx !== -1) posts.value.splice(idx, 1)
  }
}

const previewPost = (post) => {
  previewData.value = post
  previewModal.value = true
}
</script>

<style scoped>
/* PAGE */
.page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
.page-title { font-size: 1.6rem; font-weight: 900; color: #1E293B; }
.page-sub { font-size: .88rem; color: #94A3B8; margin-top: .2rem; }
.btn-new { background: #EC4899; color: white; font-family: inherit; font-weight: 700; font-size: .9rem; padding: .65rem 1.4rem; border-radius: 10px; border: none; cursor: pointer; display: flex; align-items: center; gap: .5rem; }

/* FILTERS */
.list-toolbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: .7rem; }
.search-wrap { position: relative; flex: 1; min-width: 200px; max-width: 340px; }
.search-ico { position: absolute; right: .9rem; top: 50%; transform: translateY(-50%); color: #CBD5E1; font-size: .85rem; }
.search-input { width: 100%; padding: .65rem 2.4rem .65rem .9rem; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; font-size: .88rem; color: #1E293B; }
.search-input:focus { outline: none; border-color: #EC4899; }
.filter-group { display: flex; gap: .5rem; }
.filter-sel { padding: .6rem .9rem; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; font-size: .85rem; color: #475569; background: white; cursor: pointer; }

/* POST CARDS */
.posts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.2rem; }
.post-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 8px rgba(0,0,0,.05); transition: transform .2s, box-shadow .2s; display: flex; flex-direction: column; }
.post-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
.post-thumb { height: 130px; display: flex; align-items: center; justify-content: center; position: relative; }
.thumb-ico { font-size: 2.5rem; color: rgba(0,0,0,.15); }
.post-status-badge { position: absolute; top: .7rem; right: .7rem; font-size: .7rem; font-weight: 800; padding: .2rem .6rem; border-radius: 50px; }
.post-status-badge.published { background: rgba(22,163,74,.15); color: #15803D; }
.post-status-badge.draft     { background: rgba(148,163,184,.15); color: #64748B; }
.post-body { padding: 1rem 1.1rem; flex: 1; }
.post-cat { font-size: .72rem; font-weight: 700; color: #EC4899; margin-bottom: .4rem; }
.post-title { font-size: .98rem; font-weight: 800; color: #1E293B; margin-bottom: .4rem; line-height: 1.45; }
.post-excerpt { font-size: .82rem; color: #64748B; line-height: 1.6; margin-bottom: .7rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.post-meta { display: flex; gap: .7rem; flex-wrap: wrap; }
.post-meta span { font-size: .73rem; color: #94A3B8; display: flex; align-items: center; gap: .25rem; }
.post-footer { display: flex; align-items: center; justify-content: space-between; padding: .7rem 1.1rem; border-top: 1px solid #F8FAFC; }
.post-tags { display: flex; gap: .3rem; flex-wrap: wrap; }
.post-tag { font-size: .68rem; font-weight: 700; background: #F1F5F9; color: #64748B; padding: .15rem .5rem; border-radius: 4px; }
.post-actions { display: flex; gap: .3rem; }

.act-sm { width: 28px; height: 28px; border-radius: 7px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: .75rem; transition: opacity .2s; }
.act-sm.preview   { background: #EFF6FF; color: #2563EB; }
.act-sm.edit      { background: #FFF7ED; color: #EA580C; }
.act-sm.publish   { background: #F0FDF4; color: #15803D; }
.act-sm.unpublish { background: #F1F5F9; color: #64748B; }
.act-sm.delete    { background: #FEF2F2; color: #DC2626; }
.act-sm:hover { opacity: .75; }

.empty-state { grid-column: 1/-1; text-align: center; padding: 4rem; color: #CBD5E1; }
.empty-state i { font-size: 3rem; margin-bottom: 1rem; display: block; }

/* EDITOR */
.editor-wrap { display: flex; flex-direction: column; height: calc(100vh - 100px); }
.editor-topbar { display: flex; align-items: center; justify-content: space-between; padding: .8rem 0 1rem; margin-bottom: 1rem; border-bottom: 1px solid #E2E8F0; flex-wrap: wrap; gap: .6rem; }
.btn-back { background: none; border: 1.5px solid #E2E8F0; color: #475569; font-family: inherit; font-weight: 700; font-size: .88rem; padding: .55rem 1.1rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: .5rem; }
.editor-status { font-size: .82rem; color: #16A34A; display: flex; align-items: center; gap: .4rem; }
.editor-btns { display: flex; gap: .6rem; }
.btn-draft { background: #F1F5F9; border: none; color: #475569; font-family: inherit; font-weight: 700; font-size: .88rem; padding: .6rem 1.2rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: .4rem; }
.btn-publish { background: #EC4899; border: none; color: white; font-family: inherit; font-weight: 700; font-size: .88rem; padding: .6rem 1.4rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: .4rem; }

.editor-layout { display: grid; grid-template-columns: 1fr 280px; gap: 1.2rem; flex: 1; overflow: hidden; }
.editor-main { display: flex; flex-direction: column; background: white; border-radius: 16px; box-shadow: 0 1px 8px rgba(0,0,0,.05); overflow: hidden; }
.post-title-input { padding: 1.2rem 1.4rem; border: none; border-bottom: 1px solid #F1F5F9; font-family: inherit; font-size: 1.4rem; font-weight: 800; color: #1E293B; width: 100%; }
.post-title-input:focus { outline: none; }
.post-title-input::placeholder { color: #CBD5E1; }

.editor-toolbar { display: flex; align-items: center; gap: .2rem; padding: .6rem 1rem; border-bottom: 1px solid #F1F5F9; flex-wrap: wrap; background: #FAFAFA; }
.tool-btn { width: 32px; height: 32px; border-radius: 8px; border: none; background: none; cursor: pointer; color: #475569; font-size: .88rem; display: flex; align-items: center; justify-content: center; transition: background .15s, color .15s; }
.tool-btn:hover { background: #E2E8F0; color: #1E293B; }
.tool-sep { width: 1px; height: 20px; background: #E2E8F0; margin: 0 .2rem; }
.tool-select { border: 1px solid #E2E8F0; border-radius: 8px; padding: .3rem .6rem; font-family: inherit; font-size: .82rem; color: #475569; background: white; cursor: pointer; }
.hidden-input { display: none; }

.editor-content { flex: 1; padding: 1.4rem; outline: none; font-size: 1rem; line-height: 1.9; color: #1E293B; overflow-y: auto; min-height: 300px; direction: rtl; text-align: right; }
.editor-content:empty::before { content: 'ابدأ الكتابة...'; color: #CBD5E1; }
.editor-footer-bar { display: flex; gap: 1rem; padding: .6rem 1.4rem; border-top: 1px solid #F1F5F9; font-size: .78rem; color: #94A3B8; background: #FAFAFA; }

/* EDITOR SIDEBAR */
.editor-sidebar { display: flex; flex-direction: column; gap: .8rem; overflow-y: auto; }
.sidebar-card { background: white; border-radius: 14px; box-shadow: 0 1px 8px rgba(0,0,0,.05); overflow: hidden; }
.sc-header { padding: .8rem 1rem; border-bottom: 1px solid #F1F5F9; font-size: .85rem; font-weight: 800; color: #1E293B; display: flex; align-items: center; gap: .4rem; }
.sc-header i { color: #EC4899; }
.sc-body { padding: .8rem 1rem; display: flex; flex-direction: column; gap: .7rem; }
.form-group { display: flex; flex-direction: column; gap: .3rem; }
.form-group label { font-size: .75rem; font-weight: 700; color: #64748B; }
.form-input { padding: .55rem .8rem; border: 1.5px solid #E2E8F0; border-radius: 8px; font-family: inherit; font-size: .85rem; color: #1E293B; width: 100%; resize: vertical; }
.form-input:focus { outline: none; border-color: #EC4899; }
.char-count { font-size: .72rem; color: #94A3B8; text-align: left; }
.char-count.warn { color: #DC2626; }

.feat-img-zone { height: 120px; border: 2px dashed #E2E8F0; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; background-size: cover; background-position: center; transition: border-color .2s; }
.feat-img-zone:hover { border-color: #EC4899; }
.feat-img-placeholder { display: flex; flex-direction: column; align-items: center; gap: .4rem; color: #CBD5E1; font-size: .82rem; }
.feat-img-placeholder i { font-size: 1.5rem; }
.btn-remove-img { margin-top: .4rem; background: none; border: none; cursor: pointer; color: #DC2626; font-size: .78rem; font-weight: 700; display: flex; align-items: center; gap: .3rem; }

.tags-list { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .4rem; }
.tag-chip { background: #FCE7F3; color: #9D174D; font-size: .75rem; font-weight: 700; padding: .2rem .6rem; border-radius: 50px; display: flex; align-items: center; gap: .3rem; }
.tag-remove { background: none; border: none; cursor: pointer; color: #9D174D; font-size: .9rem; line-height: 1; padding: 0; }

/* PREVIEW MODAL */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 200; display: flex; align-items: center; justify-content: center; }
.preview-modal { background: white; border-radius: 20px; width: 720px; max-width: 95vw; max-height: 85vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.2); }
.preview-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1.2rem 1.5rem; border-bottom: 1px solid #F1F5F9; position: sticky; top: 0; background: white; z-index: 1; }
.preview-modal-header h3 { font-size: 1.1rem; font-weight: 800; color: #1E293B; flex: 1; }
.preview-modal-header button { background: none; border: none; cursor: pointer; font-size: 1.2rem; color: #94A3B8; }
.preview-body { padding: 1.5rem; }
.preview-meta { font-size: .82rem; color: #94A3B8; margin-bottom: 1rem; display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
.preview-cat { background: #FCE7F3; color: #9D174D; font-weight: 700; padding: .15rem .6rem; border-radius: 4px; font-size: .75rem; }
.preview-excerpt { font-size: 1rem; color: #475569; line-height: 1.7; margin-bottom: 1rem; font-style: italic; border-right: 3px solid #EC4899; padding-right: .8rem; }
.preview-content { font-size: .95rem; line-height: 1.9; color: #1E293B; }

@media (max-width: 900px) {
  .editor-layout { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
  .posts-grid { grid-template-columns: 1fr; }
}
</style>
