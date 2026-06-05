<template>
  <Head title="بارع - ولي الأمر" />
  <ParentLayout page-title="لوحة التحكم">
    <h1 class="title">مرحباً بك في بوابة ولي الأمر</h1>
    <p class="sub">تابع تقدّم أبنائك وأدِر اشتراكاتك من مكان واحد.</p>
    <div class="grid">
      <div class="card"><i class="fa-solid fa-children"></i><div class="num">٠</div><div class="lbl">الأبناء</div></div>
      <div class="card"><i class="fa-solid fa-graduation-cap"></i><div class="num">٠</div><div class="lbl">مسارات نشطة</div></div>
      <div class="card"><i class="fa-solid fa-receipt"></i><div class="num">٠</div><div class="lbl">فواتير</div></div>
    </div>
  </ParentLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import ParentLayout from '@/Layouts/ParentLayout.vue'
</script>

<style scoped>
.title { font-size: 1.5rem; font-weight: 800; color: #1E293B; margin-bottom: .3rem; }
.sub { color: #64748B; margin-bottom: 1.5rem; }
.grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; }
.card { background: #fff; border-radius: 16px; padding: 1.5rem; text-align: center; box-shadow: 0 2px 12px rgba(0,0,0,.05); }
.card i { font-size: 1.8rem; color: #EC4899; margin-bottom: .5rem; }
.card .num { font-size: 2rem; font-weight: 800; color: #1E293B; }
.card .lbl { color: #64748B; font-weight: 600; }
</style>
