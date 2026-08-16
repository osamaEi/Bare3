<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({ email: '', password: '', remember: false });
const submit = () => form.post(route('login'), { onFinish: () => form.reset('password') });
</script>

<template>
    <Head title="دخول ولي الأمر" />
    <div class="auth-page bare3" dir="rtl">
        <!-- أشكال 3D تجريدية — نفس هوية الصفحة الرئيسية -->
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>

        <div class="auth-shell">
            <!-- شخصية بارع -->
            <div class="auth-char-wrap">
                <div class="auth-char-glow"></div>
                <img src="/images/characters/09.png" alt="شخصية بارع" class="auth-char" />
            </div>

            <div class="auth-card">
                <Link href="/" class="auth-logo"><img src="/images/logo.png" alt="بارع" /></Link>

                <div class="role-chip">
                    <i class="fa-solid fa-user-shield"></i> بوابة ولي الأمر
                </div>

                <h1 class="auth-title">أهلاً بك</h1>
                <p class="auth-sub">سجّل دخولك لمتابعة تقدّم أبنائك</p>

                <form @submit.prevent="submit">
                    <div class="field">
                        <label>البريد الإلكتروني</label>
                        <input type="email" v-model="form.email" required autofocus placeholder="parent@bare3.sa" dir="ltr" />
                        <span v-if="form.errors.email" class="field-error">{{ form.errors.email }}</span>
                    </div>
                    <div class="field">
                        <label>كلمة المرور</label>
                        <input type="password" v-model="form.password" required placeholder="••••••••" dir="ltr" />
                        <span v-if="form.errors.password" class="field-error">{{ form.errors.password }}</span>
                    </div>
                    <label class="remember"><input type="checkbox" v-model="form.remember" /> تذكّرني</label>
                    <button type="submit" class="btn-submit" :disabled="form.processing">
                        <span>{{ form.processing ? 'جاري الدخول...' : 'دخول' }}</span>
                        <i v-if="!form.processing" class="fa-solid fa-arrow-left"></i>
                    </button>
                </form>

                <div class="other-portals">
                    <Link :href="route('login.student')">دخول الطالب</Link>
                    <span>·</span>
                    <Link :href="route('login.admin')">دخول الإدارة</Link>
                </div>

                <Link href="/" class="back-home">
                    <i class="fa-solid fa-arrow-right"></i> العودة للرئيسية
                </Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* ── هوية بارع — نفس متغيّرات الصفحة الرئيسية ── */
.auth-page {
    --brand:#7c5cbf; --brand-dark:#5f4398; --brand-soft:#f3effb; --ink:#1e1b2e;
    --coral:#f0806a; --teal:#4bb5a8; --amber:#f2b866; --navy:#3d6ea5;
    min-height:100vh; display:flex; align-items:center; justify-content:center;
    padding:2rem 1.5rem; position:relative; overflow:hidden;
    background:#FAFAFB; color:var(--ink);
    font-family:'Tajawal','Poppins',sans-serif;
}
.auth-page :deep(i[class*="fa-"]) { font-family:"Font Awesome 6 Free" !important; font-weight:900 !important; }

/* أشكال 3D تجريدية (blobs) */
.blob { position:absolute; border-radius:50%; filter:blur(60px); opacity:.5; pointer-events:none; }
.blob-1 { width:420px; height:420px; background:radial-gradient(circle,#c9b4f0,#7c5cbf); top:-120px; right:-80px; }
.blob-2 { width:340px; height:340px; background:radial-gradient(circle,#ffd9b0,#f0806a); bottom:-100px; left:-60px; opacity:.35; }
.blob-3 { width:260px; height:260px; background:radial-gradient(circle,#b8ede6,#4bb5a8); top:45%; left:12%; opacity:.3; }

/* التخطيط: شخصية + بطاقة */
.auth-shell {
    position:relative; z-index:1; width:100%; max-width:960px;
    display:grid; grid-template-columns:1fr; gap:2.5rem; align-items:center;
}
@media (min-width:1024px) { .auth-shell { grid-template-columns:.85fr 1fr; } }

.auth-char-wrap { position:relative; display:none; justify-content:center; }
@media (min-width:1024px) { .auth-char-wrap { display:flex; } }
.auth-char {
    position:relative; z-index:2; width:100%; max-width:330px; height:auto;
    filter:drop-shadow(0 24px 40px rgba(30,27,46,.18));
    animation:float-char 6s ease-in-out infinite;
}
.auth-char-glow {
    position:absolute; z-index:1; bottom:6%; left:50%; transform:translateX(-50%);
    width:78%; aspect-ratio:1/1; border-radius:50%;
    background:radial-gradient(circle, rgba(124,92,191,.30), transparent 68%); filter:blur(26px);
}
@keyframes float-char { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-16px);} }
@media (prefers-reduced-motion:reduce) { .auth-char { animation:none; } }

/* بطاقة زجاجية — نفس أسلوب الصفحة الرئيسية */
.auth-card {
    background:rgba(255,255,255,.7); backdrop-filter:blur(16px);
    border:1px solid #f0eef7; border-radius:28px;
    box-shadow:0 12px 40px rgba(30,27,46,.07);
    padding:2.6rem 2.2rem; width:100%; max-width:460px; margin:0 auto;
}
.auth-logo { display:block; text-align:center; margin-bottom:1.1rem; }
.auth-logo img { width:190px; max-width:70%; }

.role-chip {
    display:flex; align-items:center; justify-content:center; gap:.5rem; width:fit-content;
    background:#fff; color:var(--brand); font-weight:700; font-size:.85rem;
    padding:.5rem 1.1rem; border-radius:9999px; margin:0 auto 1.1rem;
    border:1px solid var(--brand-soft); box-shadow:0 4px 20px rgba(124,92,191,.12);
}
.auth-title { font-size:1.9rem; font-weight:900; text-align:center; color:var(--ink); margin-bottom:.4rem; }
.auth-sub { text-align:center; color:#6B7280; margin-bottom:1.8rem; }

.field { margin-bottom:1.1rem; }
.field label { display:block; font-weight:700; font-size:.9rem; margin-bottom:.45rem; color:var(--ink); }
.field input {
    width:100%; padding:14px 18px; border-radius:16px; border:2px solid #f0eef7;
    background:#fafafb; font-family:inherit; font-size:1rem; color:var(--ink);
    outline:none; transition:border-color .2s ease, background .2s ease;
}
.field input:focus { border-color:var(--brand); background:#fff; }
.field-error { display:block; color:#dc2626; font-size:.78rem; font-weight:700; margin-top:.4rem; }

.remember { display:flex; align-items:center; gap:.5rem; font-size:.9rem; font-weight:700; color:#6B7280; cursor:pointer; margin-bottom:1.4rem; }
.remember input { width:18px; height:18px; accent-color:var(--brand); }

.btn-submit {
    display:inline-flex; align-items:center; justify-content:center; gap:10px; width:100%;
    background:var(--brand); color:#fff; font-family:inherit; font-weight:700; font-size:1.05rem;
    padding:15px 34px; border:none; border-radius:9999px; cursor:pointer;
    box-shadow:0 8px 24px rgba(124,92,191,.28); transition:all .25s ease;
}
.btn-submit:hover:not(:disabled) { background:var(--brand-dark); transform:translateY(-2px); }
.btn-submit:disabled { opacity:.6; cursor:not-allowed; }

.other-portals {
    text-align:center; margin-top:1.7rem; font-weight:700; font-size:.85rem;
    color:#94A3B8; display:flex; gap:.6rem; justify-content:center;
}
.other-portals a { color:var(--brand); text-decoration:none; }
.other-portals a:hover { color:var(--brand-dark); text-decoration:underline; }

.back-home {
    display:flex; align-items:center; justify-content:center; gap:.5rem;
    margin-top:1.1rem; font-size:.82rem; font-weight:700; color:#94A3B8; text-decoration:none;
    transition:color .2s ease;
}
.back-home:hover { color:var(--brand); }

@media (max-width:480px) { .auth-card { padding:2rem 1.4rem; } }
</style>
