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

// صور غلاف دورية + ألوان الشارات
const covers = [
  'https://www.amlood.net/images/interior.png',
  'https://www.amlood.net/images/child-activity.png',
  'https://www.amlood.net/images/workspace.jpg',
]
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
          رؤى عميقة ومقالات متخصصة حول الصحة النفسية وجودة الحياة
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
            <img :src="covers[i % covers.length]" class="w-full h-56 object-cover">
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
  background: linear-gradient(rgba(75,53,104,0.8), rgba(143,119,174,0.75)),
              url('https://www.amlood.net/images/interior.png');
  background-size: cover;
  background-position: center;
}
.article-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.article-card:hover {
  transform: translateY(-12px);
  box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}
</style>
