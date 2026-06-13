<template>
  <AdminLayout :page-title="`تقدم الطالب: ${student.name}`">
    <div class="spd-page">

      <!-- Back -->
      <Link :href="route('admin.students.progress')" class="back-link">
        <i class="fa-solid fa-arrow-right"></i> العودة للقائمة
      </Link>

      <!-- Student Card -->
      <div class="student-header">
        <div class="student-av-lg">{{ student.name?.[0] }}</div>
        <div class="student-info">
          <h1 class="student-name">{{ student.name }}</h1>
          <div class="student-meta">
            <span><i class="fa-solid fa-envelope"></i> {{ student.email }}</span>
            <span v-if="student.phone"><i class="fa-solid fa-phone"></i> {{ student.phone }}</span>
            <span><i class="fa-regular fa-calendar"></i> تسجيل: {{ student.created_at }}</span>
            <span class="status-chip" :class="student.is_active ? 'active' : 'inactive'">
              {{ student.is_active ? 'نشط' : 'موقوف' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Summary Stats -->
      <div class="summary-grid">
        <div class="sum-card" style="--c:#38BDF8;--bg:#E0F4FF">
          <i class="fa-solid fa-book-open"></i>
          <div class="sum-val">{{ summary.total_enrollments }}</div>
          <div class="sum-lbl">مسارات مسجّلة</div>
        </div>
        <div class="sum-card" style="--c:#22C55E;--bg:#F0FDF4">
          <i class="fa-solid fa-circle-check"></i>
          <div class="sum-val">{{ summary.completed_enrollments }}</div>
          <div class="sum-lbl">مسارات مكتملة</div>
        </div>
        <div class="sum-card" style="--c:#EC4899;--bg:#FDF2F8">
          <i class="fa-solid fa-list-check"></i>
          <div class="sum-val">{{ summary.completed_lessons }} / {{ summary.total_lessons }}</div>
          <div class="sum-lbl">دروس مكتملة</div>
        </div>
        <div class="sum-card" style="--c:#F59E0B;--bg:#FFFBEB">
          <i class="fa-solid fa-pen-to-square"></i>
          <div class="sum-val">{{ summary.quiz_passed }} / {{ summary.quiz_attempts }}</div>
          <div class="sum-lbl">اختبارات ناجحة</div>
        </div>
        <div class="sum-card" style="--c:#A855F7;--bg:#F5F3FF">
          <i class="fa-solid fa-medal"></i>
          <div class="sum-val">{{ summary.badges_count }}</div>
          <div class="sum-lbl">شارات</div>
        </div>
        <div class="sum-card" style="--c:#0EA5E9;--bg:#E0F4FF">
          <i class="fa-solid fa-certificate"></i>
          <div class="sum-val">{{ summary.certificates_count }}</div>
          <div class="sum-lbl">شهادات</div>
        </div>
      </div>

      <!-- Enrollments -->
      <div class="section-title">
        <i class="fa-solid fa-map"></i> المسارات المسجّلة
      </div>

      <div v-if="enrollments.length === 0" class="empty-box">
        <i class="fa-solid fa-book-open"></i>
        <p>لم يسجّل الطالب في أي مسار بعد</p>
      </div>

      <div v-else class="enrollments-list">
        <div
          v-for="en in enrollments"
          :key="en.id"
          class="enrollment-card"
        >
          <!-- Enrollment Header -->
          <div class="en-header" @click="toggle(en.id)">
            <div class="en-title-row">
              <div class="en-color-dot" :style="{ background: en.path_color }"></div>
              <div>
                <div class="en-path-name">{{ en.path_title }}</div>
                <div class="en-meta">
                  <span>{{ gradeLabel(en.grade_level) }}</span>
                  <span>·</span>
                  <span>بدأ: {{ en.started_at }}</span>
                  <span v-if="en.completed_at">· اكتمل: {{ en.completed_at }}</span>
                </div>
              </div>
            </div>
            <div class="en-right">
              <span class="en-status" :class="en.status">{{ statusLabel(en.status) }}</span>
              <div class="progress-wrap">
                <div class="progress-bar">
                  <div class="progress-fill" :style="{ width: en.progress + '%', background: en.path_color }"></div>
                </div>
                <span class="progress-pct">{{ en.progress }}%</span>
              </div>
              <div class="en-counts">
                <span>{{ en.completed_lessons }} / {{ en.total_lessons }} درس</span>
              </div>
              <i class="fa-solid toggle-ico" :class="expanded.has(en.id) ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </div>
          </div>

          <!-- Expanded Details -->
          <div v-if="expanded.has(en.id)" class="en-details">

            <!-- Lessons Table -->
            <div class="detail-section">
              <div class="detail-title"><i class="fa-solid fa-list"></i> تفاصيل الدروس</div>
              <table class="detail-table">
                <thead>
                  <tr>
                    <th>الدرس</th>
                    <th>الحالة</th>
                    <th>الفيديو</th>
                    <th>SCORM</th>
                    <th>الاختبار</th>
                    <th>تاريخ الإتمام</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="lp in en.lessons" :key="lp.id">
                    <td class="lesson-name">{{ lp.lesson_title }}</td>
                    <td>
                      <span class="lp-status" :class="lp.status">{{ lpStatusLabel(lp.status) }}</span>
                    </td>
                    <td><i class="fa-solid" :class="lp.video_completed ? 'fa-circle-check c-green' : 'fa-circle-xmark c-gray'"></i></td>
                    <td><i class="fa-solid" :class="lp.scorm_completed ? 'fa-circle-check c-green' : 'fa-circle-xmark c-gray'"></i></td>
                    <td><i class="fa-solid" :class="lp.quiz_passed ? 'fa-circle-check c-green' : 'fa-circle-xmark c-gray'"></i></td>
                    <td class="date-cell">{{ lp.completed_at ? formatDt(lp.completed_at) : '—' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Quiz Attempts -->
            <div v-if="en.quiz_attempts.length > 0" class="detail-section">
              <div class="detail-title"><i class="fa-solid fa-pen-to-square"></i> محاولات الاختبارات</div>
              <table class="detail-table">
                <thead>
                  <tr>
                    <th>الاختبار</th>
                    <th>الدرس</th>
                    <th>المحاولة</th>
                    <th>الدرجة</th>
                    <th>النتيجة</th>
                    <th>التاريخ</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="qa in en.quiz_attempts" :key="qa.id">
                    <td>{{ qa.quiz_title }}</td>
                    <td class="date-cell">{{ qa.lesson }}</td>
                    <td class="center">#{{ qa.attempt_num }}</td>
                    <td class="center">
                      <span class="score-chip" :class="qa.passed ? 'pass' : 'fail'">
                        {{ qa.score }}%
                      </span>
                    </td>
                    <td class="center">
                      <span class="result-chip" :class="qa.passed ? 'pass' : 'fail'">
                        {{ qa.passed ? 'ناجح' : 'راسب' }}
                      </span>
                    </td>
                    <td class="date-cell">{{ qa.finished_at ? formatDt(qa.finished_at) : '—' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>

      <!-- Badges & Certificates Row -->
      <div class="two-col">
        <!-- Badges -->
        <div class="info-card">
          <div class="section-title"><i class="fa-solid fa-medal"></i> الشارات المكتسبة</div>
          <div v-if="badges.length === 0" class="empty-sm">لا توجد شارات بعد</div>
          <div v-else class="badges-grid">
            <div v-for="b in badges" :key="b.id" class="badge-item">
              <div class="badge-icon">{{ b.icon || '🏅' }}</div>
              <div class="badge-name">{{ b.name }}</div>
              <div class="badge-date">{{ b.earned_at }}</div>
            </div>
          </div>
        </div>

        <!-- Certificates -->
        <div class="info-card">
          <div class="section-title"><i class="fa-solid fa-certificate"></i> الشهادات</div>
          <div v-if="certificates.length === 0" class="empty-sm">لا توجد شهادات بعد</div>
          <div v-else class="certs-list">
            <div v-for="c in certificates" :key="c.id" class="cert-item">
              <i class="fa-solid fa-certificate cert-ico"></i>
              <div>
                <div class="cert-path">{{ c.path_title }}</div>
                <div class="cert-num">{{ c.cert_number }}</div>
                <div class="cert-date">{{ c.issued_at }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
  student:      Object,
  enrollments:  Array,
  badges:       Array,
  certificates: Array,
  summary:      Object,
})

const expanded = reactive(new Set())
function toggle(id) {
  expanded.has(id) ? expanded.delete(id) : expanded.add(id)
}

function gradeLabel(g) {
  return { primary: 'ابتدائي', middle: 'إعدادي', high: 'ثانوي' }[g] ?? g
}
function statusLabel(s) {
  return { active: 'نشط', completed: 'مكتمل', paused: 'موقوف' }[s] ?? s
}
function lpStatusLabel(s) {
  return { locked: 'مغلق', in_progress: 'جاري', completed: 'مكتمل' }[s] ?? s
}
function formatDt(d) {
  return new Date(d).toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric' })
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap');
* { box-sizing: border-box; margin: 0; padding: 0; }
.spd-page { font-family: 'Cairo', sans-serif; direction: rtl; }

.back-link { display: inline-flex; align-items: center; gap: .5rem; color: #64748B; font-weight: 700; font-size: .88rem; text-decoration: none; margin-bottom: 1.2rem; transition: color .2s; }
.back-link:hover { color: #38BDF8; }

/* Student Header */
.student-header { background: white; border-radius: 16px; padding: 1.5rem 1.8rem; display: flex; align-items: center; gap: 1.4rem; margin-bottom: 1.5rem; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
.student-av-lg { width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #38BDF8, #EC4899); color: white; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.6rem; flex-shrink: 0; }
.student-name { font-size: 1.3rem; font-weight: 900; color: #1E293B; margin-bottom: .5rem; }
.student-meta { display: flex; align-items: center; gap: 1.2rem; flex-wrap: wrap; font-size: .84rem; color: #64748B; }
.student-meta i { margin-left: .3rem; }

.status-chip { font-size: .75rem; font-weight: 700; border-radius: 20px; padding: .25rem .75rem; }
.status-chip.active   { background: #F0FDF4; color: #16A34A; }
.status-chip.inactive { background: #FEF2F2; color: #DC2626; }

/* Summary */
.summary-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: .9rem; margin-bottom: 1.8rem; }
.sum-card { background: white; border-radius: 14px; padding: 1.1rem 1.2rem; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,.05); border-top: 3px solid var(--c); }
.sum-card i { font-size: 1.4rem; color: var(--c); margin-bottom: .4rem; display: block; }
.sum-val { font-size: 1.35rem; font-weight: 900; color: #1E293B; }
.sum-lbl { font-size: .78rem; color: #94A3B8; font-weight: 600; margin-top: .15rem; }

/* Section */
.section-title { font-size: 1rem; font-weight: 800; color: #1E293B; margin-bottom: 1rem; display: flex; align-items: center; gap: .6rem; }
.section-title i { color: #38BDF8; }

.empty-box { background: white; border-radius: 14px; padding: 3rem 2rem; text-align: center; color: #94A3B8; margin-bottom: 1.5rem; }
.empty-box i { font-size: 2rem; margin-bottom: .6rem; display: block; }

/* Enrollments */
.enrollments-list { display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem; }
.enrollment-card { background: white; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,.06); }

.en-header { display: flex; align-items: center; justify-content: space-between; padding: 1.1rem 1.4rem; cursor: pointer; transition: background .2s; gap: 1rem; flex-wrap: wrap; }
.en-header:hover { background: #F8FAFC; }

.en-title-row { display: flex; align-items: center; gap: .8rem; }
.en-color-dot { width: 14px; height: 14px; border-radius: 50%; flex-shrink: 0; }
.en-path-name { font-weight: 800; color: #1E293B; font-size: .95rem; }
.en-meta { font-size: .78rem; color: #94A3B8; margin-top: .2rem; display: flex; gap: .4rem; flex-wrap: wrap; }

.en-right { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
.en-status { font-size: .75rem; font-weight: 700; border-radius: 20px; padding: .25rem .75rem; }
.en-status.active    { background: #E0F4FF; color: #0284C7; }
.en-status.completed { background: #F0FDF4; color: #16A34A; }
.en-status.paused    { background: #FEF9C3; color: #CA8A04; }

.progress-wrap { display: flex; align-items: center; gap: .6rem; }
.progress-bar { width: 100px; height: 6px; background: #F1F5F9; border-radius: 99px; overflow: hidden; }
.progress-fill { height: 100%; border-radius: 99px; transition: width .4s; }
.progress-pct { font-weight: 800; font-size: .82rem; color: #1E293B; min-width: 35px; }
.en-counts { font-size: .8rem; color: #64748B; font-weight: 600; white-space: nowrap; }
.toggle-ico { color: #94A3B8; font-size: .85rem; }

/* Expanded Details */
.en-details { border-top: 1px solid #F1F5F9; padding: 1.2rem 1.4rem; display: flex; flex-direction: column; gap: 1.5rem; background: #FAFBFC; }

.detail-section {}
.detail-title { font-size: .88rem; font-weight: 800; color: #64748B; margin-bottom: .7rem; display: flex; align-items: center; gap: .5rem; }
.detail-title i { color: #38BDF8; }

.detail-table { width: 100%; border-collapse: collapse; font-size: .83rem; background: white; border-radius: 10px; overflow: hidden; }
.detail-table th { padding: .65rem .9rem; text-align: right; font-weight: 800; color: #64748B; font-size: .77rem; background: #F8FAFC; border-bottom: 1px solid #F1F5F9; white-space: nowrap; }
.detail-table td { padding: .65rem .9rem; border-bottom: 1px solid #F8FAFC; vertical-align: middle; }
.detail-table tr:last-child td { border-bottom: none; }
.lesson-name { font-weight: 700; color: #1E293B; }
.date-cell { color: #94A3B8; font-size: .78rem; white-space: nowrap; }
.center { text-align: center; }

.lp-status { font-size: .72rem; font-weight: 700; border-radius: 20px; padding: .2rem .6rem; }
.lp-status.locked      { background: #F1F5F9; color: #94A3B8; }
.lp-status.in_progress { background: #FEF9C3; color: #CA8A04; }
.lp-status.completed   { background: #F0FDF4; color: #16A34A; }

.c-green { color: #22C55E; font-size: .95rem; }
.c-gray  { color: #CBD5E1; font-size: .95rem; }

.score-chip { font-size: .78rem; font-weight: 800; border-radius: 20px; padding: .2rem .6rem; }
.score-chip.pass { background: #F0FDF4; color: #16A34A; }
.score-chip.fail { background: #FEF2F2; color: #DC2626; }
.result-chip { font-size: .78rem; font-weight: 700; border-radius: 20px; padding: .2rem .65rem; }
.result-chip.pass { background: #F0FDF4; color: #16A34A; }
.result-chip.fail { background: #FEF2F2; color: #DC2626; }

/* Two col */
.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: .5rem; }
@media (max-width: 768px) { .two-col { grid-template-columns: 1fr; } }
.info-card { background: white; border-radius: 14px; padding: 1.4rem; box-shadow: 0 2px 10px rgba(0,0,0,.06); }
.empty-sm { color: #94A3B8; font-size: .85rem; font-weight: 600; padding: .5rem 0; }

/* Badges */
.badges-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: .75rem; margin-top: .5rem; }
.badge-item { text-align: center; background: #F8FAFC; border-radius: 10px; padding: .8rem .5rem; }
.badge-icon { font-size: 1.6rem; margin-bottom: .3rem; }
.badge-name { font-weight: 700; font-size: .78rem; color: #1E293B; }
.badge-date { font-size: .7rem; color: #94A3B8; margin-top: .2rem; }

/* Certs */
.certs-list { display: flex; flex-direction: column; gap: .75rem; margin-top: .5rem; }
.cert-item { display: flex; align-items: flex-start; gap: .8rem; padding: .75rem; background: #F8FAFC; border-radius: 10px; }
.cert-ico { color: #F59E0B; font-size: 1.3rem; margin-top: .1rem; }
.cert-path { font-weight: 700; font-size: .88rem; color: #1E293B; }
.cert-num  { font-size: .75rem; color: #94A3B8; font-family: monospace; margin-top: .2rem; }
.cert-date { font-size: .75rem; color: #94A3B8; margin-top: .1rem; }
</style>
