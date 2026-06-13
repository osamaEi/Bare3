<template>
  <AdminLayout page-title="تقدم الطلبة">
    <div class="sp-page">

      <!-- Header -->
      <div class="page-header">
        <div>
          <h1 class="page-title">تقدم الطلبة</h1>
          <p class="page-sub">متابعة تفصيلية لأداء جميع الطلبة</p>
        </div>
      </div>

      <!-- Summary Stats -->
      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon" style="background:#E0F4FF;color:#38BDF8"><i class="fa-solid fa-user-graduate"></i></div>
          <div class="stat-body">
            <div class="stat-val">{{ total_stats.students.toLocaleString() }}</div>
            <div class="stat-lbl">إجمالي الطلبة</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:#FDF2F8;color:#EC4899"><i class="fa-solid fa-book-open"></i></div>
          <div class="stat-body">
            <div class="stat-val">{{ total_stats.enrollments.toLocaleString() }}</div>
            <div class="stat-lbl">إجمالي التسجيلات</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:#F0FDF4;color:#22C55E"><i class="fa-solid fa-circle-check"></i></div>
          <div class="stat-body">
            <div class="stat-val">{{ total_stats.completed.toLocaleString() }}</div>
            <div class="stat-lbl">مسارات مكتملة</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:#FFFBEB;color:#F59E0B"><i class="fa-solid fa-star"></i></div>
          <div class="stat-body">
            <div class="stat-val">{{ total_stats.quiz_passed.toLocaleString() }}</div>
            <div class="stat-lbl">اختبارات ناجحة</div>
          </div>
        </div>
      </div>

      <!-- Search -->
      <div class="search-bar">
        <i class="fa-solid fa-search"></i>
        <input
          :value="filters.search"
          @input="doSearch($event.target.value)"
          type="text"
          placeholder="ابحث باسم الطالب أو البريد الإلكتروني..."
        />
      </div>

      <!-- Table -->
      <div class="table-card">
        <div v-if="students.data.length === 0" class="empty-state">
          <i class="fa-solid fa-user-graduate"></i>
          <p>لا يوجد طلبة يطابقون البحث</p>
        </div>
        <table v-else class="sp-table">
          <thead>
            <tr>
              <th>الطالب</th>
              <th>التسجيلات</th>
              <th>مكتملة</th>
              <th>الشارات</th>
              <th>الشهادات</th>
              <th>الحالة</th>
              <th>التفاصيل</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="s in students.data" :key="s.id">
              <td>
                <div class="student-cell">
                  <div class="student-av">{{ s.name?.[0] }}</div>
                  <div>
                    <div class="student-name">{{ s.name }}</div>
                    <div class="student-email">{{ s.email }}</div>
                  </div>
                </div>
              </td>
              <td>
                <span class="badge-num sky">{{ s.enrollments_count }}</span>
              </td>
              <td>
                <span class="badge-num green">{{ s.completed_enrollments_count }}</span>
              </td>
              <td>
                <span class="badge-num yellow">{{ s.badges_count }}</span>
              </td>
              <td>
                <span class="badge-num purple">{{ s.certificates_count }}</span>
              </td>
              <td>
                <span class="status-chip" :class="s.is_active ? 'active' : 'inactive'">
                  {{ s.is_active ? 'نشط' : 'موقوف' }}
                </span>
              </td>
              <td>
                <Link :href="route('admin.students.progress.show', s.id)" class="btn-detail">
                  <i class="fa-solid fa-eye"></i> عرض
                </Link>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div v-if="students.last_page > 1" class="pagination">
          <Link
            v-for="link in students.links"
            :key="link.label"
            :href="link.url || '#'"
            class="page-btn"
            :class="{ active: link.active, disabled: !link.url }"
            v-html="link.label"
          />
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  students:    Object,
  filters:     Object,
  total_stats: Object,
})

let searchTimer = null
function doSearch(val) {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    router.get(route('admin.students.progress'), { search: val }, { preserveState: true, replace: true })
  }, 400)
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap');
* { box-sizing: border-box; margin: 0; padding: 0; }
.sp-page { font-family: 'Cairo', sans-serif; direction: rtl; }

.page-header { margin-bottom: 1.5rem; }
.page-title { font-size: 1.5rem; font-weight: 800; color: #1E293B; }
.page-sub { color: #64748B; font-size: .88rem; margin-top: .2rem; }

.stats-row { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
.stat-card { background: white; border-radius: 14px; padding: 1.2rem 1.4rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,.05); }
.stat-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
.stat-val { font-size: 1.5rem; font-weight: 900; color: #1E293B; }
.stat-lbl { font-size: .8rem; color: #94A3B8; font-weight: 600; margin-top: .1rem; }

.search-bar { background: white; border-radius: 12px; padding: .75rem 1rem; display: flex; align-items: center; gap: .75rem; margin-bottom: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,.05); }
.search-bar i { color: #94A3B8; }
.search-bar input { border: none; outline: none; font-family: 'Cairo', sans-serif; font-size: .9rem; flex: 1; background: transparent; color: #1E293B; }

.table-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
.empty-state { text-align: center; padding: 4rem 2rem; color: #94A3B8; }
.empty-state i { font-size: 2.5rem; margin-bottom: .75rem; display: block; }

.sp-table { width: 100%; border-collapse: collapse; font-size: .88rem; }
.sp-table thead { background: #F8FAFC; }
.sp-table th { padding: .9rem 1rem; text-align: right; font-weight: 800; color: #64748B; font-size: .8rem; border-bottom: 1px solid #F1F5F9; white-space: nowrap; }
.sp-table td { padding: .85rem 1rem; border-bottom: 1px solid #F8FAFC; color: #1E293B; vertical-align: middle; }
.sp-table tr:last-child td { border-bottom: none; }

.student-cell { display: flex; align-items: center; gap: .7rem; }
.student-av { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #38BDF8, #EC4899); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: .9rem; flex-shrink: 0; }
.student-name { font-weight: 700; color: #1E293B; font-size: .9rem; }
.student-email { font-size: .78rem; color: #94A3B8; margin-top: .1rem; }

.badge-num { font-weight: 800; font-size: .85rem; border-radius: 20px; padding: .2rem .7rem; display: inline-block; }
.badge-num.sky    { background: #E0F4FF; color: #0284C7; }
.badge-num.green  { background: #F0FDF4; color: #16A34A; }
.badge-num.yellow { background: #FFFBEB; color: #D97706; }
.badge-num.purple { background: #F5F3FF; color: #7C3AED; }

.status-chip { font-size: .75rem; font-weight: 700; border-radius: 20px; padding: .25rem .75rem; }
.status-chip.active   { background: #F0FDF4; color: #16A34A; }
.status-chip.inactive { background: #FEF2F2; color: #DC2626; }

.btn-detail { background: #38BDF8; color: white; border-radius: 8px; padding: .4rem .9rem; font-size: .82rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: .4rem; transition: background .2s; font-family: 'Cairo', sans-serif; }
.btn-detail:hover { background: #0284C7; }

.pagination { display: flex; align-items: center; justify-content: center; gap: .4rem; padding: 1rem; flex-wrap: wrap; }
.page-btn { background: white; border: 1px solid #E2E8F0; color: #64748B; border-radius: 8px; padding: .4rem .75rem; font-size: .82rem; font-weight: 700; text-decoration: none; transition: all .2s; font-family: 'Cairo', sans-serif; }
.page-btn.active { background: #38BDF8; border-color: #38BDF8; color: white; }
.page-btn.disabled { opacity: .4; pointer-events: none; }
</style>
