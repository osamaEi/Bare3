<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({ email: '', password: '', remember: false });
const submit = () => form.post(route('login'), { onFinish: () => form.reset('password') });
</script>

<template>
    <Head title="دخول المعلم" />
    <div class="auth-page teacher" dir="rtl">
        <div class="blob blob1"></div>
        <div class="blob blob2"></div>

        <div class="auth-card">
            <Link href="/" class="auth-logo"><img src="/images/logo.png" alt="بارع" /></Link>
            <div class="role-chip"><span class="mi">chalkboard_teacher</span> بوابة المعلم</div>
            <h1 class="auth-title">مرحباً أستاذنا 👨‍🏫</h1>
            <p class="auth-sub">سجّل دخولك لإدارة فصولك ومتابعة طلابك</p>

            <form @submit.prevent="submit">
                <div class="field">
                    <label>البريد الإلكتروني</label>
                    <input type="email" v-model="form.email" required autofocus placeholder="teacher@bare3.sa" />
                    <span v-if="form.errors.email" class="field-error">{{ form.errors.email }}</span>
                </div>
                <div class="field">
                    <label>كلمة المرور</label>
                    <input type="password" v-model="form.password" required placeholder="••••••••" />
                    <span v-if="form.errors.password" class="field-error">{{ form.errors.password }}</span>
                </div>
                <label class="remember"><input type="checkbox" v-model="form.remember" /> تذكّرني</label>
                <button type="submit" class="btn-submit" :disabled="form.processing">
                    {{ form.processing ? 'جاري الدخول...' : 'دخول' }}
                </button>
            </form>

            <div class="other-portals">
                <Link :href="route('login.student')">دخول الطالب</Link>
                <span>·</span>
                <Link :href="route('login.admin')">دخول الإدارة</Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@400;600;700;800&display=swap');
@import url('https://fonts.googleapis.com/icon?family=Material+Icons+Round');
.mi { font-family:'Material Icons Round'; font-style:normal; vertical-align:middle; font-size:1.1rem; }

.auth-page { --c:#16A34A; --c-dark:#15803D; --c-light:#F0FDF4;
    min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1.5rem;
    font-family:'Baloo Bhaijaan 2', cursive; position:relative; overflow:hidden;
    background:linear-gradient(135deg,#F0FDF4 0%,#E0F4FF 100%); }
.blob { position:absolute; border-radius:50%; pointer-events:none; }
.blob1 { width:380px; height:380px; background:var(--c); opacity:.12; top:-90px; right:-90px; }
.blob2 { width:300px; height:300px; background:#38BDF8; opacity:.12; bottom:-80px; left:-80px; }

.auth-card { position:relative; z-index:1; background:#fff; width:100%; max-width:430px; border-radius:28px; padding:2.5rem 2.2rem; box-shadow:0 20px 60px rgba(21,128,61,.18); border:3px solid #fff; }
.auth-logo { display:block; text-align:center; margin-bottom:1rem; }
.auth-logo img { width:110px; }
.role-chip { display:inline-flex; align-items:center; gap:.4rem; background:var(--c-light); color:var(--c-dark); font-weight:800; font-size:.82rem; padding:.35rem 1rem; border-radius:50px; margin:0 auto 1rem; }
.auth-title { font-size:1.6rem; font-weight:800; text-align:center; color:#1C1C2E; margin-bottom:.3rem; }
.auth-sub { text-align:center; color:#6B7280; margin-bottom:1.6rem; }
.field { margin-bottom:1.1rem; }
.field label { display:block; font-weight:700; font-size:.9rem; margin-bottom:.45rem; }
.field input { width:100%; padding:.85rem 1.1rem; border:2px solid #E2E8F0; border-radius:14px; font-family:inherit; font-size:1rem; background:#F8FAFC; outline:none; transition:border-color .2s, box-shadow .2s; }
.field input:focus { border-color:var(--c); box-shadow:0 0 0 4px var(--c-light); background:#fff; }
.field-error { display:block; color:#9D174D; font-size:.82rem; font-weight:700; margin-top:.4rem; }
.remember { display:flex; align-items:center; gap:.5rem; font-size:.9rem; font-weight:700; color:#6B7280; cursor:pointer; margin-bottom:1.3rem; }
.remember input { width:18px; height:18px; accent-color:var(--c); }
.btn-submit { width:100%; padding:1rem; background:var(--c); color:#fff; font-family:inherit; font-weight:800; font-size:1.1rem; border:none; border-radius:50px; cursor:pointer; box-shadow:0 5px 0 var(--c-dark); transition:transform .15s; }
.btn-submit:hover:not(:disabled) { transform:translateY(-2px); }
.btn-submit:active:not(:disabled) { transform:translateY(3px); box-shadow:0 2px 0 var(--c-dark); }
.btn-submit:disabled { opacity:.6; cursor:not-allowed; }
.other-portals { text-align:center; margin-top:1.6rem; font-weight:700; font-size:.85rem; color:#94A3B8; display:flex; gap:.6rem; justify-content:center; }
.other-portals a { color:var(--c-dark); text-decoration:none; }
.other-portals a:hover { text-decoration:underline; }
@media (max-width:480px){ .auth-card{ padding:2rem 1.4rem; } }
</style>
