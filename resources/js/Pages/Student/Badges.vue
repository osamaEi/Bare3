<template>
  <Head title="شاراتي — بارع" />
  <StudentLayout>
    <div class="page-head">
      <h1>سلة شاراتي 🏆</h1>
      <p>اكسبت {{ earnedCount }} من {{ totalCount }} شارة — استمر لتجمعها كلها!</p>
    </div>

    <div class="badges-grid">
      <div v-for="b in badges" :key="b.id" class="badge-tile" :class="{ earned: b.earned, locked: !b.earned }">
        <div class="badge-icon">
          <img v-if="b.earned && b.image" :src="b.image" :alt="b.name" class="badge-img" />
          <span v-else class="mi">{{ b.earned ? b.icon : 'lock' }}</span>
        </div>
        <div class="badge-name">{{ b.name }}</div>
        <div class="badge-path">{{ b.path }}</div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

defineProps({
  badges:       { type: Array,  default: () => [] },
  earnedCount:  { type: Number, default: 0 },
  totalCount:   { type: Number, default: 0 },
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/icon?family=Material+Icons+Round');
* { box-sizing:border-box; }
.mi { font-family:'Material Icons Round'; font-style:normal; line-height:1; display:inline-flex; align-items:center; justify-content:center; }

.page-head { margin-bottom:1.6rem; }
.page-head h1 { font-size:1.6rem; font-weight:800; color:#1e1b2e; margin-bottom:.3rem; }
.page-head p { color:#6B7280; font-weight:600; }

.badges-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(150px, 1fr)); gap:1rem; }
.badge-tile { text-align:center; padding:1.6rem 1rem; border-radius:20px; border:1.5px solid #f0eef7; background:#fff; transition:transform .2s; }
.badge-tile:hover { transform:translateY(-5px) rotate(2deg); }
.badge-tile.earned { background:#fdf3e3; border-color:#f2b866; }
.badge-tile.locked { filter:grayscale(.8); opacity:.5; }
.badge-icon { width:60px; height:60px; border-radius:18px; display:flex; align-items:center; justify-content:center; margin:0 auto .7rem; }
.badge-icon .mi { font-size:2rem; }
.badge-img { width:100%; height:100%; object-fit:contain; border-radius:14px; }
.badge-tile.earned .badge-icon { background:rgba(245,158,11,.18); color:#f2b866; }
.badge-tile:not(.earned) .badge-icon { background:#f5f4f9; color:#94A3B8; }
.badge-name { font-weight:800; color:#1e1b2e; font-size:.92rem; margin-bottom:.2rem; }
.badge-tile.earned .badge-name { color:#a06a1f; }
.badge-path { font-size:.75rem; color:#94A3B8; font-weight:700; }
</style>
