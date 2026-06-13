<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({ email: '', password: '', remember: false });
const submit = () => form.post(route('login'), { onFinish: () => form.reset('password') });
</script>

<template>
    <Head title="دخول الإدارة — بارع" />
    <div class="admin-auth" dir="rtl">

        <!-- Brand panel -->
        <div class="brand-panel">
            <div class="grid-overlay"></div>
            <div class="glow glow1"></div>
            <div class="glow glow2"></div>

            <div class="brand-top">
                <img src="/images/logo.png" alt="بارع" class="brand-logo" />
            </div>

            <div class="brand-mid">
                <div class="brand-badge"><span class="mi">verified_user</span> منطقة محمية</div>
                <h2>لوحة تحكم الإدارة</h2>
                <p>أدِر المنصّة بالكامل من مكان واحد — المستخدمون، المحتوى، المدفوعات، والتقارير.</p>

                <ul class="brand-features">
                    <li><span class="mi">groups</span> إدارة الطلاب والمعلمين وأولياء الأمور</li>
                    <li><span class="mi">video_library</span> رفع المحتوى والفيديوهات وحزم SCORM</li>
                    <li><span class="mi">payments</span> متابعة المدفوعات والاشتراكات</li>
                    <li><span class="mi">insights</span> تقارير وإحصاءات مباشرة</li>
                </ul>
            </div>

            <div class="brand-foot">© ٢٠٢٦ بارع — جميع الحقوق محفوظة</div>
        </div>

        <!-- Form panel -->
        <div class="form-panel">
            <div class="form-box">
                <div class="role-chip"><span class="mi">shield_person</span> دخول المسؤولين</div>
                <h1>أهلاً بعودتك 👋</h1>
                <p class="sub">سجّل دخولك للوصول إلى لوحة التحكم</p>

                <form @submit.prevent="submit">
                    <div class="field">
                        <label>البريد الإلكتروني</label>
                        <div class="input-wrap">
                            <span class="mi in-ic">mail</span>
                            <input type="email" v-model="form.email" required autofocus placeholder="admin@bare3.sa" />
                        </div>
                        <span v-if="form.errors.email" class="field-error">{{ form.errors.email }}</span>
                    </div>

                    <div class="field">
                        <label>كلمة المرور</label>
                        <div class="input-wrap">
                            <span class="mi in-ic">lock</span>
                            <input type="password" v-model="form.password" required placeholder="••••••••" />
                        </div>
                        <span v-if="form.errors.password" class="field-error">{{ form.errors.password }}</span>
                    </div>

                    <div class="row">
                        <label class="remember"><input type="checkbox" v-model="form.remember" /> تذكّرني</label>
                        <Link :href="route('password.request')" class="forgot">نسيت كلمة المرور؟</Link>
                    </div>

                    <button type="submit" class="btn-submit" :disabled="form.processing">
                        <span class="mi">login</span> {{ form.processing ? 'جاري الدخول...' : 'تسجيل الدخول' }}
                    </button>
                </form>

                <div class="other-portals">
                    <span>بوابات أخرى:</span>
                    <Link :href="route('login.student')">الطالب</Link>
                    <span class="dot">·</span>
                    <Link :href="route('login.teacher')">المعلم</Link>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap');
@import url('https://fonts.googleapis.com/icon?family=Material+Icons+Round');
.mi { font-family:'Material Icons Round'; font-style:normal; vertical-align:middle; }

.admin-auth { min-height:100vh; display:grid; grid-template-columns:1fr 1fr; font-family:'Cairo', sans-serif; }

/* ── Brand panel ── */
.brand-panel { position:relative; overflow:hidden; background:linear-gradient(155deg,#0F172A 0%,#1E293B 60%,#0E2A3B 100%);
    color:#fff; padding:2.5rem; display:flex; flex-direction:column; justify-content:space-between; }
.grid-overlay { position:absolute; inset:0; pointer-events:none;
    background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);
    background-size:40px 40px; mask-image:radial-gradient(circle at 40% 40%, #000 0%, transparent 75%); }
.glow { position:absolute; border-radius:50%; filter:blur(90px); pointer-events:none; }
.glow1 { width:420px; height:420px; background:#38BDF8; opacity:.22; top:-120px; left:-100px; }
.glow2 { width:360px; height:360px; background:#8B5CF6; opacity:.20; bottom:-120px; right:-80px; }

.brand-top { position:relative; z-index:1; }
.brand-logo { width:130px; filter:brightness(0) invert(1); }
.brand-mid { position:relative; z-index:1; }
.brand-badge { display:inline-flex; align-items:center; gap:.4rem; background:rgba(56,189,248,.15); color:#7DD3F8;
    border:1px solid rgba(56,189,248,.3); font-weight:800; font-size:.8rem; padding:.4rem 1rem; border-radius:50px; margin-bottom:1.4rem; }
.brand-badge .mi { font-size:1rem; }
.brand-mid h2 { font-size:2.1rem; font-weight:900; margin-bottom:.7rem; line-height:1.3; }
.brand-mid > p { color:#94A3B8; font-size:1rem; line-height:1.8; max-width:420px; margin-bottom:2rem; }
.brand-features { list-style:none; padding:0; display:flex; flex-direction:column; gap:1rem; }
.brand-features li { display:flex; align-items:center; gap:.8rem; font-weight:600; font-size:.95rem; color:#CBD5E1; }
.brand-features .mi { width:38px; height:38px; border-radius:11px; background:rgba(255,255,255,.06); color:#7DD3F8;
    display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
.brand-foot { position:relative; z-index:1; font-size:.8rem; color:#475569; }

/* ── Form panel ── */
.form-panel { display:flex; align-items:center; justify-content:center; padding:2.5rem 2rem; background:#F8FAFC; }
.form-box { width:100%; max-width:400px; }
.role-chip { display:inline-flex; align-items:center; gap:.4rem; background:#E0F4FF; color:#0E7490; font-weight:800; font-size:.8rem; padding:.35rem 1rem; border-radius:50px; margin-bottom:1.2rem; }
.role-chip .mi { font-size:1rem; }
.form-box h1 { font-size:1.7rem; font-weight:900; color:#0F172A; margin-bottom:.3rem; }
.sub { color:#64748B; margin-bottom:2rem; }

.field { margin-bottom:1.2rem; }
.field label { display:block; font-weight:700; font-size:.88rem; margin-bottom:.5rem; color:#334155; }
.input-wrap { position:relative; }
.in-ic { position:absolute; right:1rem; top:50%; transform:translateY(-50%); color:#94A3B8; font-size:1.2rem; }
.field input { width:100%; padding:.9rem 2.8rem .9rem 1rem; border:1.5px solid #E2E8F0; border-radius:12px; font-family:inherit; font-size:1rem; color:#0F172A; background:#fff; outline:none; transition:border-color .2s, box-shadow .2s; }
.field input:focus { border-color:#38BDF8; box-shadow:0 0 0 4px rgba(56,189,248,.12); }
.field input::placeholder { color:#CBD5E1; }
.field-error { display:block; color:#DC2626; font-size:.82rem; font-weight:700; margin-top:.4rem; }

.row { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.6rem; }
.remember { display:flex; align-items:center; gap:.5rem; font-size:.88rem; font-weight:700; color:#64748B; cursor:pointer; }
.remember input { width:17px; height:17px; accent-color:#38BDF8; }
.forgot { font-size:.85rem; font-weight:700; color:#0E7490; text-decoration:none; }
.forgot:hover { text-decoration:underline; }

.btn-submit { width:100%; padding:1rem; background:linear-gradient(135deg,#0E7490,#38BDF8); color:#fff; font-family:inherit; font-weight:800; font-size:1.05rem; border:none; border-radius:12px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:.5rem; box-shadow:0 6px 20px rgba(14,116,144,.3); transition:transform .15s, box-shadow .2s; }
.btn-submit:hover:not(:disabled) { transform:translateY(-2px); box-shadow:0 10px 28px rgba(14,116,144,.4); }
.btn-submit:disabled { opacity:.6; cursor:not-allowed; }

.other-portals { text-align:center; margin-top:1.8rem; font-weight:700; font-size:.85rem; color:#94A3B8; display:flex; gap:.5rem; justify-content:center; align-items:center; }
.other-portals a { color:#0E7490; text-decoration:none; }
.other-portals a:hover { text-decoration:underline; }
.other-portals .dot { color:#CBD5E1; }

@media (max-width:880px) {
    .admin-auth { grid-template-columns:1fr; }
    .brand-panel { display:none; }
    .form-panel { min-height:100vh; }
}
</style>
