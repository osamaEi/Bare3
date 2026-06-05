<template>
  <AdminLayout page-title="إدارة المستخدمين">

    <!-- HEADER -->
    <div class="page-header">
      <div>
        <h1 class="page-title">إدارة المستخدمين</h1>
        <p class="page-sub">إجمالي {{ totalUsers.toLocaleString() }} مستخدم مسجّل</p>
      </div>
      <button class="btn-add" @click="openModal('add')">
        <i class="fa-solid fa-user-plus"></i> إضافة مستخدم
      </button>
    </div>

    <!-- STATS ROW -->
    <div class="user-stats">
      <div v-for="s in userStats" :key="s.label" class="ustat-card" :style="{ borderColor: s.color }">
        <i :class="s.icon" :style="{ color: s.color }"></i>
        <div class="ustat-val">{{ s.value.toLocaleString() }}</div>
        <div class="ustat-label">{{ s.label }}</div>
      </div>
    </div>

    <!-- TABS -->
    <div class="card">
      <div class="tabs">
        <button v-for="tab in tabs" :key="tab.key"
          class="tab-btn" :class="{ active: activeTab === tab.key }"
          @click="activeTab = tab.key">
          <i :class="tab.icon"></i> {{ tab.label }}
          <span class="tab-count">{{ tab.count }}</span>
        </button>
      </div>

      <!-- SEARCH & FILTER -->
      <div class="table-toolbar">
        <div class="search-wrap">
          <i class="fa-solid fa-search search-ico"></i>
          <input v-model="search" type="text" placeholder="ابحث بالاسم أو البريد..." class="search-input" />
        </div>
        <div class="toolbar-actions">
          <select v-model="filterStatus" class="filter-sel">
            <option value="">كل الحالات</option>
            <option value="active">نشط</option>
            <option value="suspended">موقوف</option>
            <option value="pending">معلّق</option>
          </select>
          <button class="btn-export-sm"><i class="fa-solid fa-download"></i> تصدير Excel</button>
        </div>
      </div>

      <!-- TABLE -->
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th><input type="checkbox" @change="toggleAll" /></th>
              <th>المستخدم</th>
              <th>البريد</th>
              <th>النوع</th>
              <th v-if="activeTab === 'students'">المرحلة</th>
              <th v-if="activeTab === 'students'">المسار</th>
              <th>تاريخ التسجيل</th>
              <th>الحالة</th>
              <th>إجراءات</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in filteredUsers" :key="user.id">
              <td><input type="checkbox" :checked="selected.includes(user.id)" @change="toggleSelect(user.id)" /></td>
              <td>
                <div class="user-cell">
                  <div class="user-avatar" :style="{ background: avatarColor(user.name) }">{{ user.name[0] }}</div>
                  <span class="user-name">{{ user.name }}</span>
                </div>
              </td>
              <td class="text-gray">{{ user.email }}</td>
              <td><span class="role-badge" :class="user.role">{{ roleLabel(user.role) }}</span></td>
              <td v-if="activeTab === 'students'" class="text-gray">{{ user.level ?? '—' }}</td>
              <td v-if="activeTab === 'students'" class="text-gray">{{ user.path ?? '—' }}</td>
              <td class="text-gray">{{ user.joined }}</td>
              <td>
                <span class="status-dot" :class="user.status"></span>
                <span class="status-text" :class="user.status">{{ statusLabel(user.status) }}</span>
              </td>
              <td>
                <div class="action-btns">
                  <button class="act-btn view" title="عرض" @click="openModal('view', user)"><i class="fa-solid fa-eye"></i></button>
                  <button class="act-btn edit" title="تعديل" @click="openModal('edit', user)"><i class="fa-solid fa-pen"></i></button>
                  <button class="act-btn" :class="user.status === 'active' ? 'suspend' : 'activate'" :title="user.status === 'active' ? 'إيقاف' : 'تفعيل'" @click="toggleStatus(user)">
                    <i :class="user.status === 'active' ? 'fa-solid fa-ban' : 'fa-solid fa-check'"></i>
                  </button>
                  <button class="act-btn delete" title="حذف" @click="confirmDelete(user)"><i class="fa-solid fa-trash"></i></button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredUsers.length === 0">
              <td colspan="9" class="empty-row">لا توجد نتائج مطابقة</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div class="pagination">
        <span class="page-info">عرض {{ filteredUsers.length }} من {{ currentPaginator.total ?? filteredUsers.length }}</span>
        <div class="page-btns">
          <button class="page-btn" :disabled="!currentPaginator.prev_page_url" @click="goPage(currentPaginator.prev_page_url)">السابق</button>
          <button class="page-btn active">{{ currentPaginator.current_page ?? 1 }}</button>
          <button class="page-btn" :disabled="!currentPaginator.next_page_url" @click="goPage(currentPaginator.next_page_url)">التالي</button>
        </div>
      </div>
    </div>

    <!-- MODAL -->
    <div v-if="modal.open" class="modal-overlay" @click.self="modal.open = false">
      <div class="modal">
        <div class="modal-header">
          <h3>{{ modal.title }}</h3>
          <button class="modal-close" @click="modal.open = false"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
          <div class="form-grid">
            <div class="form-group">
              <label>الاسم الكامل</label>
              <input type="text" v-model="form.name" class="form-input" placeholder="الاسم الرباعي" />
            </div>
            <div class="form-group">
              <label>البريد الإلكتروني</label>
              <input type="email" v-model="form.email" class="form-input" placeholder="email@bare3.sa" />
            </div>
            <div class="form-group">
              <label>نوع الحساب</label>
              <select v-model="form.role" class="form-input">
                <option value="student">طالب</option>
                <option value="parent">ولي أمر</option>
                <option value="teacher">معلم</option>
              </select>
            </div>
            <div class="form-group" v-if="modal.type !== 'view'">
              <label>كلمة المرور</label>
              <input type="password" v-model="form.password" class="form-input" placeholder="••••••••" />
            </div>
            <div class="form-group" v-if="form.role === 'student'">
              <label>المرحلة الدراسية</label>
              <select v-model="form.level" class="form-input">
                <option value="primary">ابتدائي</option>
                <option value="middle">متوسط</option>
                <option value="high">ثانوي</option>
              </select>
            </div>
          </div>
          <div v-if="Object.keys(form.errors).length" class="form-errors">
            <p v-for="(msg, key) in form.errors" :key="key">{{ msg }}</p>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn-cancel" @click="modal.open = false">إلغاء</button>
          <button class="btn-save" v-if="modal.type !== 'view'" @click="saveUser">
            <i class="fa-solid fa-floppy-disk"></i> حفظ
          </button>
        </div>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  students: { type: Object, default: () => ({ data: [] }) },
  parents:  { type: Object, default: () => ({ data: [] }) },
  teachers: { type: Object, default: () => ({ data: [] }) },
  counts:   { type: Object, default: () => ({ students: 0, parents: 0, teachers: 0, active: 0 }) },
  filters:  { type: Object, default: () => ({}) },
})

const activeTab = ref('students')
const search = ref(props.filters.search ?? '')
const filterStatus = ref(props.filters.status ?? '')
const selected = ref([])

const modal = ref({ open: false, type: 'add', title: '' })
const form = useForm({ id: null, name: '', email: '', role: 'student', password: '', level: 'primary' })

// Map a User model to the shape the table expects
const mapUser = (u) => ({
  id: u.id,
  name: u.name,
  email: u.email,
  role: u.role,
  level: u.level ?? null,
  path: u.path ?? null,
  joined: u.created_at ? new Date(u.created_at).toLocaleDateString('ar-EG') : '—',
  status: u.is_active ? 'active' : 'suspended',
})

const paginatorFor = (key) => (key === 'students' ? props.students : key === 'parents' ? props.parents : props.teachers)
const currentPaginator = computed(() => paginatorFor(activeTab.value))
const currentTabUsers = computed(() => (currentPaginator.value?.data ?? []).map(mapUser))

const tabs = computed(() => [
  { key: 'students', label: 'الطلاب',        icon: 'fa-solid fa-graduation-cap',  count: props.counts.students },
  { key: 'parents',  label: 'أولياء الأمور', icon: 'fa-solid fa-people-roof',     count: props.counts.parents },
  { key: 'teachers', label: 'المعلمون',       icon: 'fa-solid fa-chalkboard-user', count: props.counts.teachers },
])

const userStats = computed(() => [
  { label: 'الطلاب',         value: props.counts.students, icon: 'fa-solid fa-graduation-cap',  color: '#38BDF8' },
  { label: 'أولياء الأمور', value: props.counts.parents,  icon: 'fa-solid fa-people-roof',     color: '#8B5CF6' },
  { label: 'المعلمون',       value: props.counts.teachers, icon: 'fa-solid fa-chalkboard-user', color: '#16A34A' },
  { label: 'النشطون',        value: props.counts.active,   icon: 'fa-solid fa-circle-check',    color: '#EC4899' },
])

const totalUsers = computed(() => props.counts.students + props.counts.parents + props.counts.teachers)

// Server already filters by search/status; show current page rows
const filteredUsers = computed(() => currentTabUsers.value)

// Debounced server-side search/filter
let searchTimer = null
watch([search, filterStatus], () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    router.get(route('admin.users.index'),
      { search: search.value || undefined, status: filterStatus.value || undefined },
      { preserveState: true, replace: true, preserveScroll: true })
  }, 350)
})

const goPage = (url) => { if (url) router.get(url, {}, { preserveState: true, preserveScroll: true }) }

const roleLabel  = (r) => ({ student: 'طالب', parent: 'ولي أمر', teacher: 'معلم' }[r] ?? r)
const statusLabel = (s) => ({ active: 'نشط', suspended: 'موقوف', pending: 'معلّق' }[s] ?? s)
const avatarColor = (name) => {
  const colors = ['#38BDF8','#EC4899','#84CC16','#F59E0B','#8B5CF6']
  return colors[name.charCodeAt(0) % colors.length]
}

const openModal = (type, user = null) => {
  modal.value = { open: true, type, title: type === 'add' ? 'إضافة مستخدم' : type === 'edit' ? 'تعديل المستخدم' : 'تفاصيل المستخدم' }
  form.clearErrors()
  if (user) {
    form.id = user.id; form.name = user.name; form.email = user.email
    form.role = user.role; form.password = ''; form.level = user.level ?? 'primary'
  } else {
    form.reset(); form.id = null
  }
}

const saveUser = () => {
  if (modal.value.type === 'add') {
    form.post(route('admin.users.store'), {
      preserveScroll: true,
      onSuccess: () => { modal.value.open = false; form.reset() },
    })
  } else {
    form.put(route('admin.users.update', form.id), {
      preserveScroll: true,
      onSuccess: () => { modal.value.open = false },
    })
  }
}

const toggleStatus = (user) => {
  router.patch(route('admin.users.toggle-status', user.id), {}, { preserveScroll: true })
}

const confirmDelete = (user) => {
  if (confirm(`هل تريد حذف "${user.name}"؟`)) {
    router.delete(route('admin.users.destroy', user.id), { preserveScroll: true })
  }
}

const toggleAll = (e) => {
  selected.value = e.target.checked ? filteredUsers.value.map(u => u.id) : []
}
const toggleSelect = (id) => {
  const idx = selected.value.indexOf(id)
  idx === -1 ? selected.value.push(id) : selected.value.splice(idx, 1)
}
</script>

<style scoped>
.page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
.page-title { font-size: 1.6rem; font-weight: 900; color: #1E293B; }
.page-sub { font-size: .88rem; color: #94A3B8; margin-top: .2rem; }
.btn-add { background: #38BDF8; color: white; font-family: inherit; font-weight: 700; font-size: .9rem; padding: .65rem 1.4rem; border-radius: 10px; border: none; cursor: pointer; display: flex; align-items: center; gap: .5rem; transition: opacity .2s; }
.btn-add:hover { opacity: .9; }

.user-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
.ustat-card { background: white; border-radius: 14px; padding: 1.2rem; text-align: center; border-top: 3px solid; box-shadow: 0 1px 8px rgba(0,0,0,.05); }
.ustat-card i { font-size: 1.4rem; margin-bottom: .5rem; }
.ustat-val { font-size: 1.8rem; font-weight: 900; color: #1E293B; }
.ustat-label { font-size: .8rem; color: #94A3B8; font-weight: 600; }

.card { background: white; border-radius: 16px; box-shadow: 0 1px 8px rgba(0,0,0,.05); overflow: hidden; }

.tabs { display: flex; border-bottom: 1px solid #F1F5F9; padding: 0 1rem; }
.tab-btn { display: flex; align-items: center; gap: .5rem; padding: 1rem 1.2rem; background: none; border: none; cursor: pointer; font-family: inherit; font-weight: 700; font-size: .9rem; color: #94A3B8; border-bottom: 2px solid transparent; margin-bottom: -1px; transition: color .2s; }
.tab-btn.active { color: #38BDF8; border-bottom-color: #38BDF8; }
.tab-count { background: #F1F5F9; color: #64748B; font-size: .72rem; font-weight: 800; padding: .1rem .5rem; border-radius: 50px; }
.tab-btn.active .tab-count { background: #E0F4FF; color: #0E7490; }

.table-toolbar { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.2rem; border-bottom: 1px solid #F8FAFC; flex-wrap: wrap; gap: .7rem; }
.search-wrap { position: relative; flex: 1; min-width: 200px; max-width: 360px; }
.search-ico { position: absolute; right: .9rem; top: 50%; transform: translateY(-50%); color: #CBD5E1; font-size: .85rem; }
.search-input { width: 100%; padding: .6rem 2.4rem .6rem .9rem; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; font-size: .88rem; color: #1E293B; transition: border-color .2s; }
.search-input:focus { outline: none; border-color: #38BDF8; }
.toolbar-actions { display: flex; gap: .6rem; align-items: center; }
.filter-sel { padding: .6rem .9rem; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; font-size: .85rem; color: #475569; background: white; cursor: pointer; }
.btn-export-sm { background: white; border: 1.5px solid #E2E8F0; color: #475569; font-family: inherit; font-weight: 700; font-size: .82rem; padding: .6rem 1rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: .4rem; }

.table-wrap { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { padding: .75rem 1rem; text-align: right; font-size: .78rem; font-weight: 700; color: #94A3B8; background: #FAFAFA; border-bottom: 1px solid #F1F5F9; white-space: nowrap; }
.data-table td { padding: .85rem 1rem; border-bottom: 1px solid #F8FAFC; font-size: .88rem; color: #1E293B; vertical-align: middle; }
.data-table tbody tr:hover { background: #FAFAFA; }

.user-cell { display: flex; align-items: center; gap: .7rem; }
.user-avatar { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: .85rem; flex-shrink: 0; }
.user-name { font-weight: 700; }
.text-gray { color: #64748B; }

.role-badge { font-size: .75rem; font-weight: 700; padding: .2rem .7rem; border-radius: 50px; }
.role-badge.student { background: #E0F4FF; color: #0E7490; }
.role-badge.parent  { background: #F5F3FF; color: #5B21B6; }
.role-badge.teacher { background: #F0FDF4; color: #15803D; }

.status-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; margin-left: .4rem; }
.status-dot.active    { background: #16A34A; }
.status-dot.suspended { background: #DC2626; }
.status-dot.pending   { background: #F59E0B; }
.status-text.active    { color: #16A34A; font-weight: 700; font-size: .82rem; }
.status-text.suspended { color: #DC2626; font-weight: 700; font-size: .82rem; }
.status-text.pending   { color: #F59E0B; font-weight: 700; font-size: .82rem; }

.action-btns { display: flex; gap: .3rem; }
.act-btn { width: 30px; height: 30px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: .78rem; transition: opacity .2s; }
.act-btn.view     { background: #EFF6FF; color: #2563EB; }
.act-btn.edit     { background: #FFF7ED; color: #EA580C; }
.act-btn.suspend  { background: #FEF2F2; color: #DC2626; }
.act-btn.activate { background: #F0FDF4; color: #16A34A; }
.act-btn.delete   { background: #FEF2F2; color: #DC2626; }
.act-btn:hover { opacity: .75; }

.empty-row { text-align: center; color: #94A3B8; padding: 3rem !important; }

.pagination { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.2rem; border-top: 1px solid #F1F5F9; }
.page-info { font-size: .82rem; color: #94A3B8; }
.page-btns { display: flex; gap: .4rem; }
.page-btn { padding: .4rem .9rem; border: 1.5px solid #E2E8F0; background: white; border-radius: 8px; cursor: pointer; font-family: inherit; font-size: .82rem; color: #475569; transition: border-color .2s; }
.page-btn.active { background: #38BDF8; color: white; border-color: #38BDF8; }
.page-btn:disabled { opacity: .4; cursor: not-allowed; }

/* MODAL */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 200; display: flex; align-items: center; justify-content: center; }
.modal { background: white; border-radius: 20px; width: 500px; max-width: 95vw; box-shadow: 0 20px 60px rgba(0,0,0,.15); }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1.2rem 1.5rem; border-bottom: 1px solid #F1F5F9; }
.modal-header h3 { font-size: 1.1rem; font-weight: 800; color: #1E293B; }
.modal-close { background: none; border: none; cursor: pointer; font-size: 1.1rem; color: #94A3B8; padding: .3rem; border-radius: 8px; }
.modal-body { padding: 1.5rem; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-group { display: flex; flex-direction: column; gap: .4rem; }
.form-group label { font-size: .82rem; font-weight: 700; color: #475569; }
.form-input { padding: .65rem .9rem; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; font-size: .9rem; color: #1E293B; transition: border-color .2s; }
.form-input:focus { outline: none; border-color: #38BDF8; }
.form-errors { margin-top: 1rem; background: #FEF2F2; border: 1.5px solid #FECACA; border-radius: 10px; padding: .7rem 1rem; }
.form-errors p { color: #DC2626; font-size: .82rem; font-weight: 700; margin: .1rem 0; }
.modal-footer { display: flex; gap: .7rem; justify-content: flex-end; padding: 1rem 1.5rem; border-top: 1px solid #F1F5F9; }
.btn-cancel { background: #F1F5F9; border: none; color: #475569; font-family: inherit; font-weight: 700; font-size: .9rem; padding: .65rem 1.4rem; border-radius: 10px; cursor: pointer; }
.btn-save { background: #38BDF8; border: none; color: white; font-family: inherit; font-weight: 700; font-size: .9rem; padding: .65rem 1.4rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: .5rem; }

@media (max-width: 768px) {
  .user-stats { grid-template-columns: repeat(2, 1fr); }
  .form-grid { grid-template-columns: 1fr; }
}
</style>
