<script setup>
import { computed, ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Bare3Layout from '@/Layouts/Bare3Layout.vue'

const props = defineProps({
  brand:  Object,
  footer: Object,
  posts:  { type: Array, default: () => [] },
})

// التصنيفات تُشتق من المقالات الفعلية
const categories = computed(() => {
  const set = [...new Set(props.posts.map(p => p.category).filter(Boolean))]
  return ['الكل', ...set]
})
const activeCat = ref('الكل')
const filteredPosts = computed(() =>
  activeCat.value === 'الكل' ? props.posts : props.posts.filter(p => p.category === activeCat.value)
)

// أغلفة gradient مولّدة بالكود (هوية بارع) + ألوان الشارات
const covers = [
  'linear-gradient(135deg,#7c5cbf,#5f4398)',
  'linear-gradient(135deg,#4bb5a8,#2f8a7e)',
  'linear-gradient(135deg,#f0806a,#d95f4a)',
  'linear-gradient(135deg,#f2b866,#d99236)',
]
const coverIcons = ['fa-brain', 'fa-heart-pulse', 'fa-coins', 'fa-microchip']
const badgeColors = ['bg-purple-100 text-purple-700', 'bg-teal-100 text-teal-700', 'bg-orange-100 text-orange-700']
</script>

<template>
  <Head title="المدونة | Bare3" />
  <Bare3Layout active="blog">

    <!-- HERO -->
    <section class="blog-hero min-h-[65vh] flex items-center text-white relative">
      <div class="max-w-5xl mx-auto px-6 text-center">
        <h1 class="text-5xl md:text-6xl font-black mb-6" data-aos="fade-up">المدونة</h1>
        <p class="text-2xl text-purple-100 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
          رؤى عميقة ومقالات متخصصة حول مهارات القرن الحادي والعشرين ومهارات المستقبل
        </p>
      </div>
    </section>

    <!-- BLOG SECTION -->
    <section class="py-20">
      <div class="max-w-7xl mx-auto px-6">

        <!-- Categories Filter -->
        <div v-if="categories.length > 1" class="flex flex-wrap justify-center gap-3 mb-12" data-aos="fade-up">
          <button v-for="cat in categories" :key="cat" @click="activeCat = cat"
                  class="px-6 py-2 rounded-full text-sm font-medium transition"
                  :class="activeCat === cat ? 'bg-purple-700 text-white' : 'hover:bg-purple-100'">
            {{ cat }}
          </button>
        </div>

        <div v-if="filteredPosts.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <Link v-for="(post, i) in filteredPosts" :key="post.slug" :href="route('blog.show', post.slug)"
                class="article-card bg-white rounded-3xl overflow-hidden shadow-md block">
            <div class="w-full h-48 flex items-center justify-center text-white text-4xl" :style="{ background: covers[i % covers.length] }">
              <i class="fa-solid" :class="coverIcons[i % coverIcons.length]"></i>
            </div>
            <div class="p-7">
              <span v-if="post.category" class="text-xs px-4 py-1.5 rounded-full" :class="badgeColors[i % badgeColors.length]">
                {{ post.category }}
              </span>
              <h3 class="font-bold text-xl mt-5 leading-tight">{{ post.title }}</h3>
              <p class="text-gray-600 mt-4 line-clamp-3">{{ post.excerpt }}</p>
              <div class="mt-6 flex items-center justify-between text-sm text-gray-500">
                <span>{{ post.published_at }}</span>
                <span class="text-purple-700 font-medium">اقرأ المقال ←</span>
              </div>
            </div>
          </Link>
        </div>

        <div v-else class="text-center py-16 text-gray-400">
          <i class="fa-regular fa-newspaper text-5xl mb-4"></i>
          <p class="font-medium">لا توجد مقالات منشورة بعد.</p>
        </div>

      </div>
    </section>
  </Bare3Layout>
</template>

<style scoped>
.blog-hero {
  background:
    radial-gradient(circle at 25% 35%, rgba(124,92,191,.4), transparent 45%),
    radial-gradient(circle at 75% 65%, rgba(75,181,168,.3), transparent 45%),
    linear-gradient(135deg, #5f4398, #7c5cbf);
}
.article-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.article-card:hover {
  transform: translateY(-12px);
  box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}
</style>
