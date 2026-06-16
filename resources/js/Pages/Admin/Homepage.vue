<template>
  <AdminLayout page-title="الصفحة الرئيسية">
    <div class="page-header">
      <div>
        <h1 class="page-title">محتوى الصفحة الرئيسية</h1>
        <p class="page-sub">عدّل النصوص والصور والروابط التي تظهر في صفحة الهبوط</p>
      </div>
      <a href="/" target="_blank" class="btn-view"><i class="fa-solid fa-arrow-up-right-from-square"></i> معاينة الصفحة</a>
    </div>

    <!-- Tabs -->
    <div class="tabs">
      <button v-for="t in tabs" :key="t.key" class="tab" :class="{ active: tab === t.key }" @click="tab = t.key">
        <i :class="t.icon"></i> {{ t.label }}
      </button>
    </div>

    <!-- ══════════ HERO ══════════ -->
    <div v-show="tab === 'hero'" class="card">
      <div class="card-title"><i class="fa-solid fa-star"></i> القسم الرئيسي (Hero)</div>
      <div class="grid2">
        <Field label="الشارة العلوية" v-model="form.hero.badge" />
        <Field label="الوصف" v-model="form.hero.desc" />
        <Field label="السطر الأول" v-model="form.hero.title_line1" />
        <Field label="السطر الثاني" v-model="form.hero.title_line2" />
        <Field label="الكلمة المميّزة (أزرق)" v-model="form.hero.title_hl1" />
        <Field label="الكلمة المميّزة (وردي)" v-model="form.hero.title_hl2" />
        <Field label="زر رئيسي" v-model="form.hero.btn_primary" />
        <Field label="زر ثانوي" v-model="form.hero.btn_secondary" />
      </div>
      <ImageField label="صورة الـ Hero" path="hero.image" :current="form.hero.image" @file="onFile" />

      <div class="sub-title">الشعار</div>
      <ImageField label="شعار المنصة" path="brand.logo" :current="form.brand.logo" @file="onFile" />

      <div class="sub-title">إحصائيات الـ Hero</div>
      <div v-for="(s, i) in form.stats" :key="i" class="row-item">
        <Field label="أيقونة (FontAwesome)" v-model="s.icon" small />
        <Field label="اللون (sky/pink/lime)" v-model="s.color" small />
        <Field label="النص" v-model="s.text" small />
        <button class="btn-del" @click="form.stats.splice(i, 1)"><i class="fa-solid fa-trash"></i></button>
      </div>
      <button class="btn-add" @click="form.stats.push({ icon: 'fa-solid fa-star', color: 'sky', text: '' })"><i class="fa-solid fa-plus"></i> إضافة إحصائية</button>

      <div class="sub-title">روابط القائمة العلوية</div>
      <div v-for="(n, i) in form.nav" :key="i" class="row-item">
        <Field label="النص" v-model="n.label" small />
        <Field label="الرابط" v-model="n.href" small />
        <button class="btn-del" @click="form.nav.splice(i, 1)"><i class="fa-solid fa-trash"></i></button>
      </div>
      <button class="btn-add" @click="form.nav.push({ label: '', href: '#' })"><i class="fa-solid fa-plus"></i> إضافة رابط</button>
    </div>

    <!-- ══════════ FEATURES ══════════ -->
    <div v-show="tab === 'features'" class="card">
      <div class="card-title"><i class="fa-solid fa-stars"></i> قسم المميزات</div>
      <div class="grid3">
        <Field label="الوسم" v-model="form.features_header.tag" />
        <Field label="العنوان" v-model="form.features_header.title" />
        <Field label="الوصف" v-model="form.features_header.sub" />
      </div>
      <div class="sub-title">بطاقات المميزات</div>
      <div v-for="(f, i) in form.features" :key="i" class="block-item">
        <div class="block-head"><span>ميزة {{ i + 1 }}</span><button class="btn-del" @click="form.features.splice(i, 1)"><i class="fa-solid fa-trash"></i></button></div>
        <div class="grid2">
          <Field label="الأيقونة (FontAwesome)" v-model="f.icon" small />
          <Field label="العنوان" v-model="f.title" small />
        </div>
        <Field label="الوصف" v-model="f.desc" small />
        <ImageField label="الصورة" :path="`features.${i}.image`" :current="f.image" @file="onFile" />
      </div>
      <button class="btn-add" @click="form.features.push({ icon: 'fa-solid fa-star', title: '', desc: '', image: '' })"><i class="fa-solid fa-plus"></i> إضافة ميزة</button>
    </div>

    <!-- ══════════ PRICING ══════════ -->
    <div v-show="tab === 'pricing'" class="card">
      <div class="card-title"><i class="fa-solid fa-rocket"></i> قسم الباقات</div>
      <div class="grid3">
        <Field label="الوسم" v-model="form.pricing_header.tag" />
        <Field label="العنوان" v-model="form.pricing_header.title" />
        <Field label="الوصف" v-model="form.pricing_header.sub" />
      </div>
      <div class="sub-title">الباقات</div>
      <div v-for="(p, i) in form.pricing" :key="i" class="block-item">
        <div class="block-head"><span>باقة {{ i + 1 }}</span><button class="btn-del" @click="form.pricing.splice(i, 1)"><i class="fa-solid fa-trash"></i></button></div>
        <div class="grid2">
          <Field label="الشارة" v-model="p.badge" small />
          <Field label="اسم الباقة" v-model="p.name" small />
          <Field label="السعر" v-model="p.amount" small />
          <Field label="الوحدة" v-model="p.unit" small />
          <Field label="نص الزر" v-model="p.btn" small />
          <label class="toggle"><input type="checkbox" v-model="p.featured" /> الأكثر شعبية</label>
        </div>
        <div class="sub-mini">المميزات</div>
        <div v-for="(feat, fi) in p.features" :key="fi" class="row-item">
          <Field label="" v-model="p.features[fi]" small />
          <button class="btn-del" @click="p.features.splice(fi, 1)"><i class="fa-solid fa-trash"></i></button>
        </div>
        <button class="btn-add sm" @click="p.features.push('')"><i class="fa-solid fa-plus"></i> ميزة</button>
      </div>
      <button class="btn-add" @click="form.pricing.push({ badge: '', amount: '', unit: '', name: '', featured: false, features: [], btn: '' })"><i class="fa-solid fa-plus"></i> إضافة باقة</button>
      <div class="sub-title">ملاحظة الباقات</div>
      <Field label="سطر 1" v-model="form.pricing_note.line1" />
      <Field label="سطر 2" v-model="form.pricing_note.line2" />
    </div>

    <!-- ══════════ PARENTS ══════════ -->
    <div v-show="tab === 'parents'" class="card">
      <div class="card-title"><i class="fa-solid fa-people-group"></i> قسم الآباء</div>
      <div class="grid3">
        <Field label="الوسم" v-model="form.parents_header.tag" />
        <Field label="العنوان" v-model="form.parents_header.title" />
        <Field label="الوصف" v-model="form.parents_header.sub" />
      </div>
      <div class="sub-title">البطاقات</div>
      <div v-for="(p, i) in form.parents" :key="i" class="block-item">
        <div class="block-head"><span>بطاقة {{ i + 1 }}</span><button class="btn-del" @click="form.parents.splice(i, 1)"><i class="fa-solid fa-trash"></i></button></div>
        <div class="grid2">
          <Field label="الأيقونة" v-model="p.icon" small />
          <Field label="اللون (sky/pink/lime)" v-model="p.color" small />
        </div>
        <Field label="العنوان" v-model="p.title" small />
        <Field label="الوصف" v-model="p.desc" small />
      </div>
      <button class="btn-add" @click="form.parents.push({ color: 'sky', icon: 'fa-solid fa-star', title: '', desc: '' })"><i class="fa-solid fa-plus"></i> إضافة بطاقة</button>
    </div>

    <!-- ══════════ TESTIMONIALS ══════════ -->
    <div v-show="tab === 'reviews'" class="card">
      <div class="card-title"><i class="fa-solid fa-comment-dots"></i> قسم الآراء</div>
      <div class="grid3">
        <Field label="الوسم" v-model="form.reviews_header.tag" />
        <Field label="العنوان" v-model="form.reviews_header.title" />
        <Field label="الوصف" v-model="form.reviews_header.sub" />
      </div>
      <div class="sub-title">الآراء</div>
      <div v-for="(t, i) in form.testimonials" :key="i" class="block-item">
        <div class="block-head"><span>رأي {{ i + 1 }}</span><button class="btn-del" @click="form.testimonials.splice(i, 1)"><i class="fa-solid fa-trash"></i></button></div>
        <div class="grid2">
          <Field label="نوع الرأي" v-model="t.chip" small />
          <Field label="العنوان" v-model="t.title" small />
          <Field label="الاسم" v-model="t.name" small />
          <Field label="المكان" v-model="t.loc" small />
        </div>
        <Field label="التعليق" v-model="t.comment" small />
        <label class="toggle"><input type="checkbox" v-model="t.featured" /> رأي مميّز</label>
        <ImageField label="الصورة الرمزية" :path="`testimonials.${i}.avatar`" :current="t.avatar" @file="onFile" />
      </div>
      <button class="btn-add" @click="form.testimonials.push({ chip: '', featured: false, title: '', comment: '', avatar: '', name: '', loc: '' })"><i class="fa-solid fa-plus"></i> إضافة رأي</button>
    </div>

    <!-- ══════════ FAQ ══════════ -->
    <div v-show="tab === 'faq'" class="card">
      <div class="card-title"><i class="fa-solid fa-circle-question"></i> الأسئلة الشائعة</div>
      <div class="grid2">
        <Field label="الوسم" v-model="form.faq_header.tag" />
        <Field label="العنوان" v-model="form.faq_header.title" />
      </div>
      <div class="sub-title">الأسئلة</div>
      <div v-for="(item, i) in form.faq" :key="i" class="block-item">
        <div class="block-head"><span>سؤال {{ i + 1 }}</span><button class="btn-del" @click="form.faq.splice(i, 1)"><i class="fa-solid fa-trash"></i></button></div>
        <Field label="السؤال" v-model="item.q" small />
        <Field label="الإجابة" v-model="item.a" small />
      </div>
      <button class="btn-add" @click="form.faq.push({ q: '', a: '' })"><i class="fa-solid fa-plus"></i> إضافة سؤال</button>
    </div>

    <!-- ══════════ CTA ══════════ -->
    <div v-show="tab === 'cta'" class="card">
      <div class="card-title"><i class="fa-solid fa-bullhorn"></i> قسم الدعوة (CTA)</div>
      <Field label="العنوان" v-model="form.cta.title" />
      <Field label="الوصف" v-model="form.cta.desc" />
      <Field label="نص الزر" v-model="form.cta.btn" />
      <div class="grid2">
        <ImageField label="الصورة اليسرى" path="cta.image_left" :current="form.cta.image_left" @file="onFile" />
        <ImageField label="الصورة اليمنى" path="cta.image_right" :current="form.cta.image_right" @file="onFile" />
      </div>
    </div>

    <!-- ══════════ FOOTER ══════════ -->
    <div v-show="tab === 'footer'" class="card">
      <div class="card-title"><i class="fa-solid fa-shoe-prints"></i> التذييل (Footer)</div>
      <ImageField label="الشعار" path="footer.logo" :current="form.footer.logo" @file="onFile" />
      <Field label="الوصف (يدعم HTML)" v-model="form.footer.desc" />

      <div class="sub-title">روابط التواصل الاجتماعي</div>
      <div v-for="(s, i) in form.footer.socials" :key="i" class="row-item">
        <Field label="الأيقونة" v-model="s.icon" small />
        <Field label="الرابط" v-model="s.href" small />
        <button class="btn-del" @click="form.footer.socials.splice(i, 1)"><i class="fa-solid fa-trash"></i></button>
      </div>
      <button class="btn-add" @click="form.footer.socials.push({ icon: 'fab fa-facebook-f', href: '#' })"><i class="fa-solid fa-plus"></i> إضافة رابط</button>

      <div class="sub-title">عمود الصفحات</div>
      <Field label="العنوان" v-model="form.footer.col_pages_title" />
      <div v-for="(l, i) in form.footer.col_pages" :key="i" class="row-item">
        <Field label="النص" v-model="l.label" small />
        <Field label="الرابط" v-model="l.href" small />
        <button class="btn-del" @click="form.footer.col_pages.splice(i, 1)"><i class="fa-solid fa-trash"></i></button>
      </div>
      <button class="btn-add" @click="form.footer.col_pages.push({ label: '', href: '#' })"><i class="fa-solid fa-plus"></i> إضافة</button>

      <div class="sub-title">عمود المساعدة</div>
      <Field label="العنوان" v-model="form.footer.col_help_title" />
      <div v-for="(l, i) in form.footer.col_help" :key="i" class="row-item">
        <Field label="النص" v-model="l.label" small />
        <Field label="الرابط" v-model="l.href" small />
        <button class="btn-del" @click="form.footer.col_help.splice(i, 1)"><i class="fa-solid fa-trash"></i></button>
      </div>
      <button class="btn-add" @click="form.footer.col_help.push({ label: '', href: '#' })"><i class="fa-solid fa-plus"></i> إضافة</button>

      <div class="sub-title">التواصل</div>
      <div class="grid2">
        <Field label="عنوان العمود" v-model="form.footer.col_contact_title" />
        <Field label="الموقع" v-model="form.footer.contact_location" />
        <Field label="الهاتف" v-model="form.footer.contact_phone" />
        <Field label="البريد" v-model="form.footer.contact_email" />
      </div>
      <Field label="حقوق النشر" v-model="form.footer.copyright" />
    </div>

    <!-- Save bar -->
    <div class="save-bar">
      <span v-if="saved" class="saved">✓ تم الحفظ</span>
      <button class="btn-save" :disabled="processing" @click="save">
        <i class="fa-solid fa-floppy-disk"></i> {{ processing ? 'جاري الحفظ...' : 'حفظ كل التغييرات' }}
      </button>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, h } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ content: { type: Object, required: true } })

// نسخة قابلة للتعديل من المحتوى
const form = reactive(JSON.parse(JSON.stringify(props.content)))

const tab = ref('hero')
const tabs = [
  { key: 'hero',     label: 'الرئيسي',   icon: 'fa-solid fa-star' },
  { key: 'features', label: 'المميزات',  icon: 'fa-solid fa-stars' },
  { key: 'pricing',  label: 'الباقات',   icon: 'fa-solid fa-rocket' },
  { key: 'parents',  label: 'الآباء',    icon: 'fa-solid fa-people-group' },
  { key: 'reviews',  label: 'الآراء',    icon: 'fa-solid fa-comment-dots' },
  { key: 'faq',      label: 'الأسئلة',   icon: 'fa-solid fa-circle-question' },
  { key: 'cta',      label: 'الدعوة',    icon: 'fa-solid fa-bullhorn' },
  { key: 'footer',   label: 'التذييل',   icon: 'fa-solid fa-shoe-prints' },
]

// الصور المرفوعة: dot-path => File
const images = reactive({})
const onFile = ({ path, file }) => { images[path] = file }

const processing = ref(false)
const saved = ref(false)

const save = () => {
  const fd = new FormData()
  fd.append('content', JSON.stringify(form))
  for (const path in images) {
    if (images[path]) fd.append('image__' + path, images[path])
  }
  processing.value = true
  saved.value = false
  router.post(route('admin.homepage.update'), fd, {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => { saved.value = true; for (const k in images) delete images[k] },
    onFinish: () => { processing.value = false },
  })
}

// ── Inline components ──
const Field = {
  props: { label: String, modelValue: String, small: Boolean },
  emits: ['update:modelValue'],
  setup(p, { emit }) {
    return () => h('div', { class: ['field', { sm: p.small }] }, [
      p.label ? h('label', p.label) : null,
      h('input', { class: 'inp', value: p.modelValue, onInput: e => emit('update:modelValue', e.target.value) }),
    ])
  },
}

const ImageField = {
  props: { label: String, path: String, current: String },
  emits: ['file'],
  setup(p, { emit }) {
    const preview = ref(p.current)
    const pick = e => {
      const f = e.target.files[0]
      if (!f) return
      emit('file', { path: p.path, file: f })
      preview.value = URL.createObjectURL(f)
    }
    return () => h('div', { class: 'img-field' }, [
      h('label', p.label),
      h('div', { class: 'img-row' }, [
        preview.value ? h('img', { src: preview.value, class: 'img-prev' }) : h('div', { class: 'img-empty' }, 'لا صورة'),
        h('label', { class: 'btn-upload' }, [
          h('i', { class: 'fa-solid fa-cloud-arrow-up' }),
          ' رفع صورة',
          h('input', { type: 'file', accept: 'image/*', class: 'hidden', onChange: pick }),
        ]),
      ]),
    ])
  },
}
</script>

<style scoped>
.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.2rem; gap: 1rem; flex-wrap: wrap; }
.page-title { font-size: 1.6rem; font-weight: 900; color: #1E293B; }
.page-sub { font-size: .88rem; color: #94A3B8; margin-top: .2rem; }
.btn-view { background: #fff; border: 1.5px solid #E2E8F0; color: #475569; font-weight: 700; font-size: .85rem; padding: .55rem 1.1rem; border-radius: 10px; display: inline-flex; align-items: center; gap: .5rem; text-decoration: none; }
.btn-view:hover { border-color: #38BDF8; color: #0E7490; }

.tabs { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1.2rem; }
.tab { background: #fff; border: 1.5px solid #E2E8F0; color: #64748B; font-family: inherit; font-weight: 700; font-size: .85rem; padding: .55rem 1.1rem; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: .45rem; }
.tab.active { background: #38BDF8; border-color: #38BDF8; color: #fff; }

.card { background: white; border-radius: 16px; box-shadow: 0 1px 8px rgba(0,0,0,.05); padding: 1.5rem; margin-bottom: 1.2rem; }
.card-title { font-size: 1.05rem; font-weight: 800; color: #1E293B; display: flex; align-items: center; gap: .5rem; margin-bottom: 1.2rem; }
.card-title i { color: #38BDF8; }

.grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.grid3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
.field { display: flex; flex-direction: column; gap: .35rem; margin-bottom: .8rem; }
.field label { font-size: .8rem; font-weight: 700; color: #475569; }
.inp { padding: .6rem .85rem; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; font-size: .88rem; color: #1E293B; width: 100%; }
.inp:focus { outline: none; border-color: #38BDF8; }

.sub-title { font-size: .95rem; font-weight: 800; color: #334155; margin: 1.4rem 0 .8rem; padding-top: 1rem; border-top: 1px dashed #E2E8F0; }
.sub-mini { font-size: .8rem; font-weight: 700; color: #64748B; margin: .6rem 0 .4rem; }

.row-item { display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: .6rem; align-items: end; margin-bottom: .6rem; }
.row-item .field { margin-bottom: 0; }
.block-item { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; }
.block-head { display: flex; justify-content: space-between; align-items: center; font-weight: 800; color: #334155; font-size: .9rem; margin-bottom: .8rem; }

.toggle { display: flex; align-items: center; gap: .5rem; font-weight: 700; font-size: .85rem; color: #475569; cursor: pointer; }
.toggle input { width: 17px; height: 17px; accent-color: #38BDF8; }

.btn-add { background: #EFF6FF; color: #0E7490; border: 1.5px dashed #7DD3F8; font-family: inherit; font-weight: 700; font-size: .82rem; padding: .5rem 1.1rem; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: .4rem; }
.btn-add.sm { font-size: .78rem; padding: .35rem .8rem; }
.btn-del { background: #FEF2F2; color: #DC2626; border: none; width: 36px; height: 36px; border-radius: 9px; cursor: pointer; flex-shrink: 0; }
.btn-del:hover { background: #FEE2E2; }

.img-field { margin-bottom: .8rem; }
.img-field > label { font-size: .8rem; font-weight: 700; color: #475569; display: block; margin-bottom: .4rem; }
.img-row { display: flex; align-items: center; gap: 1rem; }
.img-prev { width: 80px; height: 80px; object-fit: contain; border-radius: 10px; border: 1px solid #E2E8F0; background: #fff; }
.img-empty { width: 80px; height: 80px; border-radius: 10px; border: 1px dashed #CBD5E1; display: flex; align-items: center; justify-content: center; font-size: .7rem; color: #94A3B8; }
.btn-upload { background: #38BDF8; color: #fff; font-weight: 700; font-size: .82rem; padding: .5rem 1rem; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: .45rem; }
.hidden { display: none; }

.save-bar { position: sticky; bottom: 0; background: rgba(255,255,255,.95); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: flex-end; gap: 1rem; padding: 1rem 0; border-top: 1px solid #E2E8F0; }
.saved { color: #16A34A; font-weight: 800; font-size: .88rem; }
.btn-save { background: #16A34A; color: white; border: none; font-family: inherit; font-weight: 700; font-size: .92rem; padding: .75rem 1.8rem; border-radius: 12px; cursor: pointer; display: flex; align-items: center; gap: .5rem; }
.btn-save:disabled { opacity: .5; cursor: not-allowed; }

@media (max-width: 768px) { .grid2, .grid3, .row-item { grid-template-columns: 1fr; } }
</style>
