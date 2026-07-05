<script setup>
import { Head, Link } from '@inertiajs/vue3'
import PublicShell from '@/Components/PublicShell.vue'

defineProps({
  brand:   Object,
  footer:  Object,
  post:    { type: Object, default: () => ({}) },
  related: { type: Array, default: () => [] },
})
</script>

<template>
  <Head :title="`${post.title} — مدونة بارع`" />
  <PublicShell :brand="brand" :footer="footer">
    <article class="article">
      <!-- HEADER -->
      <header class="article-head">
        <Link :href="route('blog')" class="back-link">
          <i class="fa-solid fa-arrow-right"></i> كل المقالات
        </Link>
        <span v-if="post.category" class="article-cat">{{ post.category }}</span>
        <h1>{{ post.title }}</h1>
        <div class="article-meta">
          <span v-if="post.author"><i class="fa-solid fa-user-pen"></i> {{ post.author }}</span>
          <span v-if="post.published_at"><i class="fa-regular fa-calendar"></i> {{ post.published_at }}</span>
          <span><i class="fa-regular fa-eye"></i> {{ post.views }} مشاهدة</span>
        </div>
      </header>

      <!-- BODY -->
      <div class="article-body" v-html="post.content"></div>

      <!-- SHARE / CTA -->
      <div class="article-cta">
        <p>هل أعجبك المقال؟ ابدأ رحلة طفلك مع بارع اليوم</p>
        <Link :href="route('subscribe')" class="cta-btn">ابدأ المغامرة <i class="fa-solid fa-arrow-left"></i></Link>
      </div>
    </article>

    <!-- RELATED -->
    <section v-if="related.length" class="related">
      <div class="related-inner">
        <h2>مقالات ذات صلة</h2>
        <div class="related-grid">
          <Link
            v-for="r in related"
            :key="r.slug"
            :href="route('blog.show', r.slug)"
            class="related-card"
          >
            <h3>{{ r.title }}</h3>
            <p>{{ r.excerpt }}</p>
            <span class="related-more">اقرأ المزيد <i class="fa-solid fa-arrow-left"></i></span>
          </Link>
        </div>
      </div>
    </section>
  </PublicShell>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@500;700;800&display=swap');

.article { max-width: 760px; margin: 0 auto; padding: 3rem 1.5rem 3.5rem; }

.back-link {
  display: inline-flex; align-items: center; gap: .4rem;
  color: #0E7490; font-weight: 700; font-size: .9rem; text-decoration: none; margin-bottom: 1.4rem;
}
.back-link:hover { color: #EC4899; }

.article-cat {
  display: inline-block; background: #FCE7F3; color: #BE185D;
  font-size: .78rem; font-weight: 800; padding: .3rem .9rem; border-radius: 50px; margin-bottom: 1rem;
}
.article-head h1 {
  font-family: 'Baloo Bhaijaan 2', sans-serif; font-size: clamp(1.7rem, 3.6vw, 2.4rem);
  font-weight: 800; color: #1C1C2E; line-height: 1.5; margin-bottom: 1.1rem;
}
.article-meta {
  display: flex; flex-wrap: wrap; gap: 1.3rem; color: #94A3B8; font-size: .85rem; font-weight: 700;
  padding-bottom: 1.6rem; border-bottom: 2px solid #F1F5F9; margin-bottom: 2rem;
}
.article-meta i { color: #38BDF8; margin-inline-end: .25rem; }

/* محتوى المقال (v-html) */
.article-body { color: #334155; font-size: 1.08rem; line-height: 2.1; }
.article-body :deep(h2) {
  font-family: 'Baloo Bhaijaan 2', sans-serif; font-size: 1.5rem; font-weight: 800; color: #0E7490;
  margin: 2.2rem 0 1rem;
}
.article-body :deep(h3) {
  font-family: 'Baloo Bhaijaan 2', sans-serif; font-size: 1.2rem; font-weight: 700; color: #BE185D;
  margin: 1.8rem 0 .8rem;
}
.article-body :deep(p) { margin-bottom: 1.2rem; }
.article-body :deep(ul) { margin: 0 0 1.3rem; padding-inline-start: 1.4rem; }
.article-body :deep(li) { margin-bottom: .6rem; }
.article-body :deep(strong) { color: #1C1C2E; }
.article-body :deep(blockquote) {
  border-inline-start: 4px solid #EC4899; background: #FDF2F8;
  padding: 1rem 1.4rem; border-radius: 12px; margin: 1.5rem 0; color: #9D174D; font-weight: 600;
}

.article-cta {
  margin-top: 3rem; text-align: center; background: linear-gradient(135deg,#EC4899,#38BDF8);
  border-radius: 24px; padding: 2.4rem 2rem; color: #fff;
}
.article-cta p { font-family: 'Baloo Bhaijaan 2', sans-serif; font-weight: 700; font-size: 1.2rem; margin-bottom: 1.3rem; }
.cta-btn {
  display: inline-flex; align-items: center; gap: .5rem; background: #fff; color: #BE185D;
  font-weight: 800; padding: .8rem 2rem; border-radius: 50px; text-decoration: none;
  box-shadow: 0 5px 0 rgba(0,0,0,.12); transition: transform .15s;
}
.cta-btn:hover { transform: translateY(-2px); }

.related { background: #fff; padding: 3.5rem 1.5rem 4.5rem; border-top: 2px solid #F1F5F9; }
.related-inner { max-width: 1040px; margin: 0 auto; }
.related-inner h2 {
  font-family: 'Baloo Bhaijaan 2', sans-serif; font-size: 1.6rem; font-weight: 800;
  color: #1C1C2E; text-align: center; margin-bottom: 2rem;
}
.related-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.4rem; }
.related-card {
  background: #FAFBFF; border: 2px solid #F1F5F9; border-radius: 20px; padding: 1.5rem;
  text-decoration: none; color: inherit; transition: transform .25s, border-color .25s, box-shadow .25s;
}
.related-card:hover { transform: translateY(-4px); border-color: #E0F4FF; box-shadow: 0 12px 26px rgba(28,28,46,.08); }
.related-card h3 {
  font-family: 'Baloo Bhaijaan 2', sans-serif; font-weight: 700; font-size: 1.05rem;
  color: #1C1C2E; line-height: 1.5; margin-bottom: .6rem;
}
.related-card p { color: #6B7280; font-size: .88rem; line-height: 1.8; margin-bottom: 1rem;
  display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.related-more { color: #EC4899; font-weight: 700; font-size: .85rem; display: inline-flex; align-items: center; gap: .35rem; }

@media (max-width: 600px) {
  .article-meta { gap: .8rem; }
}
</style>
