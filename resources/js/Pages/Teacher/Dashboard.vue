<template>
  <Head title="بارع - المعلم" />
  <TeacherLayout page-title="لوحة التحكم">
    <h1 class="title">مرحباً بك في بوابة المعلم</h1>
    <p class="sub">أدِر فصولك وتابع تقدّم طلابك في المهارات.</p>
    <div class="grid">
      <div class="card"><i class="fa-solid fa-chalkboard-user"></i><div class="num">٠</div><div class="lbl">الفصول</div></div>
      <div class="card"><i class="fa-solid fa-users"></i><div class="num">٠</div><div class="lbl">الطلاب</div></div>
      <div class="card"><i class="fa-solid fa-circle-check"></i><div class="num">٠</div><div class="lbl">مهارات مكتملة</div></div>
    </div>
  </TeacherLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import TeacherLayout from '@/Layouts/TeacherLayout.vue'
</script>

<style scoped>
.title { font-size: 1.5rem; font-weight: 800; color: #1E293B; margin-bottom: .3rem; }
.sub { color: #64748B; margin-bottom: 1.5rem; }
.grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; }
.card { background: #fff; border-radius: 16px; padding: 1.5rem; text-align: center; box-shadow: 0 2px 12px rgba(0,0,0,.05); }
.card i { font-size: 1.8rem; color: #16A34A; margin-bottom: .5rem; }
.card .num { font-size: 2rem; font-weight: 800; color: #1E293B; }
.card .lbl { color: #64748B; font-weight: 600; }
</style>
