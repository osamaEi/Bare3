<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Bare3Layout from '@/Layouts/Bare3Layout.vue'

defineProps({
  brand:   Object,
  footer:  Object,
  post:    { type: Object, default: () => ({}) },
  related: { type: Array, default: () => [] },
})
</script>

<template>
  <Head :title="`${post.title} | مدونة بارع`" />
  <Bare3Layout active="blog">

    <!-- HERO -->
    <section class="post-hero py-24 text-white text-center">
      <div class="max-w-4xl mx-auto px-6">
        <span v-if="post.category" class="inline-block bg-white/15 text-white text-sm px-4 py-1.5 rounded-full mb-5">{{ post.category }}</span>
        <h1 class="text-3xl md:text-5xl font-black leading-tight mb-6" data-aos="fade-up">{{ post.title }}</h1>
        <div class="flex flex-wrap justify-center gap-6 text-purple-100 text-sm">
          <span v-if="post.author"><i class="fa-solid fa-user-pen ml-1"></i> {{ post.author }}</span>
          <span v-if="post.published_at"><i class="fa-regular fa-calendar ml-1"></i> {{ post.published_at }}</span>
          <span><i class="fa-regular fa-eye ml-1"></i> {{ post.views }} مشاهدة</span>
        </div>
      </div>
    </section>

    <!-- BODY -->
    <article class="max-w-3xl mx-auto px-6 py-16">
      <Link :href="route('blog')" class="inline-flex items-center gap-2 text-purple-700 font-medium mb-10 hover:text-purple-900">
        <i class="fa-solid fa-arrow-right"></i> كل المقالات
      </Link>
      <div class="post-body" v-html="post.content"></div>

      <div class="mt-16 rounded-3xl bg-gradient-to-l from-purple-700 to-purple-500 text-white text-center p-10">
        <p class="text-xl font-bold mb-6">هل أعجبك المقال؟ استكشف مساراتنا</p>
        <Link :href="route('courses')" class="inline-flex items-center gap-2 bg-white text-purple-800 font-bold px-8 py-3.5 rounded-full hover:bg-purple-50 transition">
          تصفّح المسارات <i class="fa-solid fa-arrow-left"></i>
        </Link>
      </div>
    </article>

    <!-- RELATED -->
    <section v-if="related.length" class="bg-purple-50 py-16">
      <div class="max-w-6xl mx-auto px-6">
        <h2 class="text-2xl font-bold text-center text-gray-900 mb-10">مقالات ذات صلة</h2>
        <div class="grid md:grid-cols-3 gap-6">
          <Link v-for="r in related" :key="r.slug" :href="route('blog.show', r.slug)"
                class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition block">
            <h3 class="font-bold text-lg text-gray-900 mb-3 leading-tight">{{ r.title }}</h3>
            <p class="text-gray-600 text-sm line-clamp-3 mb-4">{{ r.excerpt }}</p>
            <span class="text-purple-700 font-medium text-sm">اقرأ المزيد ←</span>
          </Link>
        </div>
      </div>
    </section>
  </Bare3Layout>
</template>

<style scoped>
.post-hero { background: linear-gradient(135deg, #4b3568, #8f77ae); }

.post-body { color: #334155; font-size: 1.08rem; line-height: 2.1; }
.post-body :deep(h2) { font-size: 1.5rem; font-weight: 800; color: #4b3568; margin: 2.2rem 0 1rem; }
.post-body :deep(h3) { font-size: 1.2rem; font-weight: 700; color: #7a5ea0; margin: 1.8rem 0 .8rem; }
.post-body :deep(p) { margin-bottom: 1.2rem; }
.post-body :deep(ul) { margin: 0 0 1.3rem; padding-inline-start: 1.4rem; list-style: disc; }
.post-body :deep(li) { margin-bottom: .6rem; }
.post-body :deep(strong) { color: #1c1c2e; }
</style>
