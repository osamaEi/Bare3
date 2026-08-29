<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="تسجيل الدخول" />

    <div class="auth-page" dir="rtl">
        <!-- decorative blobs -->
        <div class="blob blob1"></div>
        <div class="blob blob2"></div>
        <div class="blob blob3"></div>

        <div class="auth-card">
            <Link href="/" class="auth-logo">
                <img src="/images/logo-horizontal.png" alt="بارع" />
            </Link>

            <h1 class="auth-title">أهلاً بعودتك! 👋</h1>
            <p class="auth-sub">سجّل دخولك وأكمل مغامرة التعلّم</p>

            <div v-if="status" class="auth-status">{{ status }}</div>

            <form @submit.prevent="submit">
                <div class="field">
                    <label for="email">البريد الإلكتروني</label>
                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="example@bare3.sa"
                    />
                    <span v-if="form.errors.email" class="field-error">{{ form.errors.email }}</span>
                </div>

                <div class="field">
                    <label for="password">كلمة المرور</label>
                    <input
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                    <span v-if="form.errors.password" class="field-error">{{ form.errors.password }}</span>
                </div>

                <div class="row">
                    <label class="remember">
                        <input type="checkbox" v-model="form.remember" />
                        تذكّرني
                    </label>
                    <Link v-if="canResetPassword" :href="route('password.request')" class="forgot">
                        نسيت كلمة المرور؟
                    </Link>
                </div>

                <button type="submit" class="btn-submit" :class="{ loading: form.processing }" :disabled="form.processing">
                    {{ form.processing ? 'جاري الدخول...' : 'دخول' }}
                </button>
            </form>

            <p class="auth-footer">
                ليس لديك حساب؟
                <Link :href="route('register')">أنشئ حسابك الآن</Link>
            </p>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@400;600;700;800&display=swap');

.auth-page {
    --sky: #38BDF8; --sky-dark: #0E7490; --sky-light: #E0F4FF;
    --pink: #EC4899; --pink-dark: #9D174D; --pink-light: #FCE7F3;
    --lime: #84CC16; --dark: #1C1C2E; --gray: #6B7280;
    min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
    padding: 1.5rem;
    font-family: 'Baloo Bhaijaan 2', cursive;
    background: linear-gradient(135deg, #E0F4FF 0%, #FCE7F3 50%, #F0FDF4 100%);
    position: relative; overflow: hidden;
}

.blob { position: absolute; border-radius: 50%; pointer-events: none; }
.blob1 { width: 380px; height: 380px; background: var(--sky); opacity: .14; top: -90px; right: -90px; }
.blob2 { width: 300px; height: 300px; background: var(--pink); opacity: .14; bottom: -80px; left: -80px; }
.blob3 { width: 200px; height: 200px; background: var(--lime); opacity: .12; top: 55%; left: 45%; }

.auth-card {
    position: relative; z-index: 1;
    background: #fff; width: 100%; max-width: 430px;
    border-radius: 28px; padding: 2.5rem 2.2rem;
    box-shadow: 0 20px 60px rgba(14,116,144,.18);
    border: 3px solid #fff;
}

.auth-logo { display: block; text-align: center; margin-bottom: 1.2rem; }
.auth-logo img { width: 120px; }

.auth-title { font-size: 1.7rem; font-weight: 800; text-align: center; color: var(--dark); margin-bottom: .3rem; }
.auth-sub { text-align: center; color: var(--gray); font-size: 1rem; margin-bottom: 1.8rem; }

.auth-status {
    background: #F0FDF4; color: #3F6212; border: 2px solid #BEF264;
    border-radius: 12px; padding: .6rem 1rem; font-size: .9rem; font-weight: 700;
    margin-bottom: 1.2rem; text-align: center;
}

.field { margin-bottom: 1.1rem; }
.field label { display: block; font-weight: 700; font-size: .92rem; color: var(--dark); margin-bottom: .45rem; }
.field input {
    width: 100%; padding: .85rem 1.1rem;
    border: 2px solid #E2E8F0; border-radius: 14px;
    font-family: inherit; font-size: 1rem; color: var(--dark);
    transition: border-color .2s, box-shadow .2s; outline: none;
    background: #F8FAFC;
}
.field input:focus { border-color: var(--sky); box-shadow: 0 0 0 4px var(--sky-light); background: #fff; }
.field input::placeholder { color: #94A3B8; }
.field-error { display: block; color: var(--pink-dark); font-size: .82rem; font-weight: 700; margin-top: .4rem; }

.row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
.remember { display: flex; align-items: center; gap: .5rem; font-size: .9rem; font-weight: 700; color: var(--gray); cursor: pointer; }
.remember input { width: 18px; height: 18px; accent-color: var(--sky); cursor: pointer; }
.forgot { font-size: .88rem; font-weight: 700; color: var(--sky-dark); text-decoration: none; }
.forgot:hover { text-decoration: underline; }

.btn-submit {
    width: 100%; padding: 1rem;
    background: var(--sky); color: #fff;
    font-family: inherit; font-weight: 800; font-size: 1.1rem;
    border: none; border-radius: 50px; cursor: pointer;
    box-shadow: 0 5px 0 var(--sky-dark); transition: transform .15s, box-shadow .15s;
}
.btn-submit:hover:not(:disabled) { transform: translateY(-2px); }
.btn-submit:active:not(:disabled) { transform: translateY(3px); box-shadow: 0 2px 0 var(--sky-dark); }
.btn-submit.loading, .btn-submit:disabled { opacity: .6; cursor: not-allowed; }

.auth-footer { text-align: center; margin-top: 1.6rem; color: var(--gray); font-weight: 700; font-size: .92rem; }
.auth-footer a { color: var(--pink); text-decoration: none; font-weight: 800; }
.auth-footer a:hover { text-decoration: underline; }

@media (max-width: 480px) { .auth-card { padding: 2rem 1.4rem; } }
</style>
