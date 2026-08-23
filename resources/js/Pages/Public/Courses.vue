<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import Bare3Layout from '@/Layouts/Bare3Layout.vue'

defineProps({ brand: Object, footer: Object, paths: { type: Array, default: () => [] } })

const open = ref(null)
const toggle = (slug) => { open.value = open.value === slug ? null : slug }
</script>

<template>
  <Head title="المسارات | Bare3" />
  <Bare3Layout active="courses">

    <!-- HERO -->
    <section class="courses-hero min-h-[70vh] flex items-center text-white overflow-hidden">
      <div class="max-w-6xl mx-auto px-6 grid lg:grid-cols-[1fr_auto] items-center gap-8 w-full">
        <div class="text-center lg:text-right">
          <h1 class="text-5xl md:text-6xl font-black mb-6" data-aos="fade-up">المسارات</h1>
          <p class="text-2xl text-purple-100 max-w-2xl mx-auto lg:mx-0" data-aos="fade-up" data-aos-delay="200">
            مسارات مهارية متكاملة.. تبني الشخصية وتصنع قادة المستقبل
          </p>
        </div>
        <img src="/images/characters/05.png" alt="شخصية بارع"
             class="courses-hero-char hidden lg:block" data-aos="fade-left" data-aos-delay="300" />
      </div>
    </section>

    <!-- PATHS GRID -->
    <section class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-6">
        <div v-if="!paths.length" class="text-center text-gray-500 py-16">
          لا توجد مسارات متاحة حالياً
        </div>

        <div v-else class="grid md:grid-cols-2 gap-10">
          <div v-for="(p, i) in paths" :key="p.slug" class="course-card bg-white rounded-3xl overflow-hidden shadow-xl"
               data-aos="fade-up" :data-aos-delay="i * 100">

            <!-- صورة المسار -->
            <div class="cover-wrap">
              <img v-if="p.cover" :src="p.cover" :alt="p.title" class="cover-img" loading="lazy" />
              <div v-else class="cover-img cover-fallback" :style="{ background: p.color }">
                <img src="/images/icons/paths.svg" alt="" class="cover-fallback-icon" aria-hidden="true" />
              </div>
              <span class="cover-badge" :style="{ background: p.color }">
                {{ String(i + 1).padStart(2, '0') }}
              </span>
            </div>

            <div class="p-10">
              <div class="flex justify-between items-start gap-4">
                <h3 class="text-3xl font-bold" :style="{ color: p.color }">{{ p.title }}</h3>
                <i class="fa-solid text-4xl shrink-0" :class="p.icon || 'fa-graduation-cap'"
                   :style="{ color: p.color + '33' }"></i>
              </div>

              <p v-if="p.description" class="mt-6 text-gray-600 leading-relaxed">{{ p.description }}</p>

              <div class="mt-8 flex items-center gap-3 text-sm text-gray-500">
                <i class="fa-solid fa-book-open" :style="{ color: p.color }"></i>
                {{ p.lessons_count }} درس
              </div>

              <!-- مواضيع المسار مقسّمة حسب المرحلة -->
              <div v-if="p.stages?.length" class="topics">
                <button class="topics-toggle" :style="{ color: p.color }" @click="toggle(p.slug)">
                  <i class="fa-solid" :class="open === p.slug ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                  {{ open === p.slug ? 'إخفاء المواضيع' : 'عرض مواضيع المسار' }}
                </button>

                <div v-if="open === p.slug" class="topics-body">
                  <div v-for="s in p.stages" :key="s.key" class="stage">
                    <div class="stage-label" :style="{ background: p.color }">{{ s.label }}</div>
                    <ul class="topic-list">
                      <li v-for="(t, ti) in s.topics" :key="ti">
                        <i class="fa-solid fa-circle-check" :style="{ color: p.color }"></i>
                        <span>{{ t }}</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <a :href="route('subscribe')"
                 class="mt-10 block w-full text-center text-white py-4 rounded-2xl font-medium transition hover:brightness-90"
                 :style="{ background: p.color }">
                اكتشف المسار
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </Bare3Layout>
</template>

<style scoped>
.courses-hero {
  background:
    radial-gradient(circle at 15% 40%, rgba(242,184,102,.3), transparent 45%),
    radial-gradient(circle at 85% 60%, rgba(61,110,165,.3), transparent 45%),
    linear-gradient(135deg, #5f4398, #7c5cbf);
}
.course-card { transition: all 0.4s ease; }
.course-card:hover { transform: translateY(-15px); box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.15); }

.cover-wrap { position: relative; }
.cover-img {
  width: 100%; height: 240px; object-fit: cover; display: block;
  transition: transform .5s ease;
}
.course-card:hover .cover-img { transform: scale(1.05); }
.cover-fallback { display: flex; align-items: center; justify-content: center; }
.cover-fallback-icon { width: 96px; height: 96px; filter: brightness(0) invert(1); opacity: .45; }
.courses-hero-char { width: 300px; height: auto; filter: drop-shadow(0 24px 40px rgb(0 0 0 / .28)); }

/* ── مواضيع المسار ── */
.topics { margin-top: 1.25rem; border-top: 1px solid #f0eef7; padding-top: 1rem; }
.topics-toggle {
  display: inline-flex; align-items: center; gap: .5rem; background: none; border: none;
  font-family: inherit; font-weight: 800; font-size: .9rem; cursor: pointer; padding: 0;
}
.topics-toggle:hover { text-decoration: underline; }
.topics-body { margin-top: 1rem; display: flex; flex-direction: column; gap: 1.1rem; }
.stage-label {
  display: inline-block; color: #fff; font-weight: 800; font-size: .75rem;
  padding: .3rem .9rem; border-radius: 9999px; margin-bottom: .6rem;
}
.topic-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: .5rem; }
.topic-list li { display: flex; align-items: flex-start; gap: .55rem; font-size: .88rem; color: #4b5563; line-height: 1.7; }
.topic-list li i { font-size: .8rem; margin-top: .35rem; flex-shrink: 0; }
.cover-badge {
  position: absolute; top: 1rem; inset-inline-start: 1rem;
  color: #fff; font-weight: 800; font-size: 1rem;
  padding: .35rem .8rem; border-radius: 999px;
  box-shadow: 0 4px 12px rgb(0 0 0 / .2);
}
</style>
