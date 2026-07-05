<script setup>
import { computed, ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import PublicShell from '@/Components/PublicShell.vue'

const props = defineProps({
  brand:  Object,
  footer: Object,
  posts:  { type: Array, default: () => [] },
})

// أقسام المقالات المتاحة (تُشتق من المقالات نفسها)
const categories = computed(() => {
  const set = [...new Set(props.posts.map(p => p.category).filter(Boolean))]
  return ['الكل', ...set]
})

const activeCat = ref('الكل')

const filteredPosts = computed(() =>
  activeCat.value === 'الكل'
    ? props.posts
    : props.posts.filter(p => p.category === activeCat.value)
)

// ألوان دورية لأغلفة البطاقات (متناسقة مع باليتة الموقع)
const covers = [
  { bg: 'linear-gradient(135deg,#38BDF8,#0E7490)', icon: 'fa-solid fa-rocket' },
  { bg: 'linear-gradient(135deg,#EC4899,#9D174D)', icon: 'fa-solid fa-lightbulb' },
  { bg: 'linear-gradient(135deg,#A855F7,#7E22CE)', icon: 'fa-solid fa-brain' },
  { bg: 'linear-gradient(135deg,#F59E0B,#B45309)', icon: 'fa-solid fa-coins' },
  { bg: 'linear-gradient(135deg,#34D399,#047857)', icon: 'fa-solid fa-heart-pulse' },
]
const coverFor = (i) => covers[i % covers.length]
</script>

<template>
  <Head title="المدونة — بارع" />
  <PublicShell :brand="brand" :footer="footer">
    <!-- HERO -->
    <section class="blog-hero">
      <div class="blog-hero-tag"><i class="fa-solid fa-newspaper"></i> مجتمع بارع</div>
      <h1>مدونة بارع</h1>
      <p>مقالات ورؤى تربوية تساعدك على إعداد جيل واعٍ ومبتكر لمهارات القرن الحادي والعشرين</p>
    </section>

    <div class="blog-wrap">
      <!-- فلاتر الأقسام -->
      <div v-if="categories.length > 2" class="cat-filters">
        <button
          v-for="c in categories"
          :key="c"
          class="cat-chip"
          :class="{ active: activeCat === c }"
          @click="activeCat = c"
        >{{ c }}</button>
      </div>

      <!-- شبكة المقالات -->
      <div v-if="filteredPosts.length" class="posts-grid">
        <Link
          v-for="(post, i) in filteredPosts"
          :key="post.slug"
          :href="route('blog.show', post.slug)"
          class="post-card"
        >
          <div class="post-cover" :style="{ background: coverFor(i).bg }">
            <i :class="coverFor(i).icon"></i>
          </div>
          <div class="post-body">
            <span v-if="post.category" class="post-cat">{{ post.category }}</span>
            <h3 class="post-title">{{ post.title }}</h3>
            <p class="post-excerpt">{{ post.excerpt }}</p>
            <div class="post-meta">
              <span v-if="post.published_at"><i class="fa-regular fa-calendar"></i> {{ post.published_at }}</span>
              <span class="read-more">اقرأ المقال <i class="fa-solid fa-arrow-left"></i></span>
            </div>
          </div>
        </Link>
      </div>

      <!-- حالة فارغة -->
      <div v-else class="blog-empty">
        <i class="fa-regular fa-newspaper"></i>
        <p>لا توجد مقالات منشورة بعد — ترقّبوا الجديد قريبًا!</p>
      </div>
    </div>
  </PublicShell>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@500;700;800&display=swap');

.blog-hero {
  text-align: center;
  padding: 4.5rem 2rem 5.5rem;
  background: linear-gradient(155deg, #FCE7F3 0%, #E0F4FF 50%, #F5F3FF 100%);
  position: relative;
  overflow: hidden;
}
.blog-hero-tag {
  display: inline-flex; align-items: center; gap: .5rem;
  background: #fff; border: 2px solid #38BDF8; border-radius: 50px;
  padding: .35rem 1.1rem; font-weight: 800; font-size: .85rem; color: #0E7490;
  margin-bottom: 1rem; box-shadow: 0 3px 0 #E0F4FF;
}
.blog-hero h1 {
  font-family: 'Baloo Bhaijaan 2', sans-serif;
  font-size: clamp(2rem, 4.5vw, 3rem); font-weight: 800; color: #1C1C2E; margin-bottom: .6rem;
}
.blog-hero p { color: #475569; font-size: 1.1rem; font-weight: 600; max-width: 620px; margin: 0 auto; line-height: 1.8; }

.blog-wrap { max-width: 1100px; margin: 0 auto; padding: 3rem 1.5rem 4.5rem; }

.cat-filters { display: flex; flex-wrap: wrap; gap: .6rem; justify-content: center; margin-bottom: 2.5rem; }
.cat-chip {
  border: 2px solid #E2E8F0; background: #fff; color: #475569;
  font-family: inherit; font-weight: 700; font-size: .88rem;
  padding: .5rem 1.2rem; border-radius: 50px; cursor: pointer; transition: all .2s;
}
.cat-chip:hover { border-color: #38BDF8; color: #0E7490; }
.cat-chip.active { background: #EC4899; border-color: #EC4899; color: #fff; box-shadow: 0 4px 0 #9D174D; }

.posts-grid {
  display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.6rem;
}
.post-card {
  display: flex; flex-direction: column; background: #fff; border-radius: 24px; overflow: hidden;
  border: 2px solid #F1F5F9; box-shadow: 0 4px 20px rgba(28,28,46,.05);
  text-decoration: none; color: inherit; transition: transform .3s, box-shadow .3s, border-color .3s;
}
.post-card:hover { transform: translateY(-6px); box-shadow: 0 16px 34px rgba(28,28,46,.1); border-color: #E0F4FF; }
.post-cover { height: 150px; display: flex; align-items: center; justify-content: center; color: #fff; }
.post-cover i { font-size: 2.6rem; opacity: .9; }
.post-body { padding: 1.4rem 1.5rem 1.6rem; display: flex; flex-direction: column; flex: 1; }
.post-cat {
  align-self: flex-start; background: #E0F4FF; color: #0E7490;
  font-size: .72rem; font-weight: 800; padding: .25rem .8rem; border-radius: 50px; margin-bottom: .7rem;
}
.post-title {
  font-family: 'Baloo Bhaijaan 2', sans-serif; font-weight: 800; font-size: 1.12rem;
  color: #1C1C2E; line-height: 1.5; margin-bottom: .6rem;
}
.post-excerpt { color: #6B7280; font-size: .9rem; line-height: 1.8; flex: 1; margin-bottom: 1.1rem; }
.post-meta { display: flex; align-items: center; justify-content: space-between; font-size: .8rem; color: #94A3B8; font-weight: 700; }
.read-more { color: #EC4899; display: inline-flex; align-items: center; gap: .35rem; }

.blog-empty { text-align: center; padding: 4rem 1rem; color: #94A3B8; }
.blog-empty i { font-size: 3rem; margin-bottom: 1rem; display: block; }
.blog-empty p { font-weight: 700; }
</style>
