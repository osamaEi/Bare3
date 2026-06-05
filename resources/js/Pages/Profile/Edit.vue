<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import ParentLayout from '@/Layouts/ParentLayout.vue';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});

const page = usePage();
const user = page.props.auth.user;

const layout = computed(() => ({
    student: StudentLayout,
    parent: ParentLayout,
    teacher: TeacherLayout,
    admin: AdminLayout,
}[user.role] ?? AuthenticatedLayout));

// ── Profile info form ──
const infoForm = useForm({ name: user.name, email: user.email });

// ── Password form ──
const passForm = useForm({ current_password: '', password: '', password_confirmation: '' });
const updatePassword = () => {
    passForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => passForm.reset(),
        onError: () => {
            if (passForm.errors.password) passForm.reset('password', 'password_confirmation');
            if (passForm.errors.current_password) passForm.reset('current_password');
        },
    });
};

// ── Delete form ──
const deleteForm = useForm({ password: '' });
const confirmDelete = () => {
    if (confirm('هل أنت متأكد من حذف حسابك؟ لا يمكن التراجع عن هذا الإجراء.')) {
        deleteForm.delete(route('profile.destroy'), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="الملف الشخصي" />

    <component :is="layout">
        <div class="profile-wrap" dir="rtl">
            <div class="profile-head">
                <h1>الملف الشخصي ⚙️</h1>
                <p>أدِر معلومات حسابك وكلمة المرور</p>
            </div>

            <!-- Profile info -->
            <div class="card">
                <div class="card-title"><span class="mi">person</span> المعلومات الشخصية</div>
                <p class="card-sub">حدّث اسمك وبريدك الإلكتروني</p>

                <form @submit.prevent="infoForm.patch(route('profile.update'))">
                    <div class="field">
                        <label>الاسم</label>
                        <input type="text" v-model="infoForm.name" required autocomplete="name" />
                        <span v-if="infoForm.errors.name" class="err">{{ infoForm.errors.name }}</span>
                    </div>
                    <div class="field">
                        <label>البريد الإلكتروني</label>
                        <input type="email" v-model="infoForm.email" required autocomplete="username" />
                        <span v-if="infoForm.errors.email" class="err">{{ infoForm.errors.email }}</span>
                    </div>

                    <div v-if="mustVerifyEmail && user.email_verified_at === null" class="verify-note">
                        بريدك غير موثّق.
                        <Link :href="route('verification.send')" method="post" as="button" class="link-btn">
                            إعادة إرسال رابط التوثيق
                        </Link>
                        <div v-show="status === 'verification-link-sent'" class="ok-note">
                            تم إرسال رابط توثيق جديد إلى بريدك.
                        </div>
                    </div>

                    <div class="actions">
                        <button class="btn save" :disabled="infoForm.processing">حفظ</button>
                        <Transition enter-active-class="transition" enter-from-class="opacity-0" leave-active-class="transition" leave-to-class="opacity-0">
                            <span v-if="infoForm.recentlySuccessful" class="saved">✓ تم الحفظ</span>
                        </Transition>
                    </div>
                </form>
            </div>

            <!-- Password -->
            <div class="card">
                <div class="card-title"><span class="mi">lock</span> كلمة المرور</div>
                <p class="card-sub">استخدم كلمة مرور قوية للحفاظ على أمان حسابك</p>

                <form @submit.prevent="updatePassword">
                    <div class="field">
                        <label>كلمة المرور الحالية</label>
                        <input type="password" v-model="passForm.current_password" autocomplete="current-password" />
                        <span v-if="passForm.errors.current_password" class="err">{{ passForm.errors.current_password }}</span>
                    </div>
                    <div class="field">
                        <label>كلمة المرور الجديدة</label>
                        <input type="password" v-model="passForm.password" autocomplete="new-password" />
                        <span v-if="passForm.errors.password" class="err">{{ passForm.errors.password }}</span>
                    </div>
                    <div class="field">
                        <label>تأكيد كلمة المرور</label>
                        <input type="password" v-model="passForm.password_confirmation" autocomplete="new-password" />
                        <span v-if="passForm.errors.password_confirmation" class="err">{{ passForm.errors.password_confirmation }}</span>
                    </div>
                    <div class="actions">
                        <button class="btn save" :disabled="passForm.processing">تحديث</button>
                        <Transition enter-active-class="transition" enter-from-class="opacity-0" leave-active-class="transition" leave-to-class="opacity-0">
                            <span v-if="passForm.recentlySuccessful" class="saved">✓ تم التحديث</span>
                        </Transition>
                    </div>
                </form>
            </div>

            <!-- Delete -->
            <div class="card danger">
                <div class="card-title danger-t"><span class="mi">warning</span> حذف الحساب</div>
                <p class="card-sub">بمجرد حذف حسابك، ستُحذف جميع بياناتك نهائيًا.</p>

                <div class="field">
                    <label>أدخل كلمة المرور للتأكيد</label>
                    <input type="password" v-model="deleteForm.password" autocomplete="current-password" placeholder="كلمة المرور" />
                    <span v-if="deleteForm.errors.password" class="err">{{ deleteForm.errors.password }}</span>
                </div>
                <button class="btn delete" :disabled="deleteForm.processing" @click="confirmDelete">حذف حسابي</button>
            </div>
        </div>
    </component>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/icon?family=Material+Icons+Round');

.profile-wrap { max-width: 640px; margin: 0 auto; font-family: 'Baloo Bhaijaan 2', cursive; }
.mi { font-family:'Material Icons Round'; font-style:normal; line-height:1; display:inline-flex; align-items:center; justify-content:center; vertical-align:middle; }

.profile-head { margin-bottom: 1.6rem; }
.profile-head h1 { font-size: 1.6rem; font-weight: 800; color: #0F172A; margin-bottom: .3rem; }
.profile-head p { color: #64748B; font-weight: 600; }

.card { background:#fff; border-radius:24px; padding:1.8rem; border:1.5px solid #E2E8F0; box-shadow:0 2px 12px rgba(15,23,42,.06); margin-bottom:1.4rem; }
.card.danger { border-color:#FCE7F3; }
.card-title { font-size:1.1rem; font-weight:800; color:#0F172A; display:flex; align-items:center; gap:.5rem; margin-bottom:.3rem; }
.card-title .mi { color:#38BDF8; font-size:1.3rem; }
.card-title.danger-t .mi { color:#EC4899; }
.card-sub { color:#64748B; font-weight:600; font-size:.9rem; margin-bottom:1.4rem; }

.field { margin-bottom:1.1rem; }
.field label { display:block; font-weight:700; font-size:.9rem; color:#1C1C2E; margin-bottom:.45rem; }
.field input { width:100%; padding:.8rem 1.1rem; border:2px solid #E2E8F0; border-radius:14px; font-family:inherit; font-size:1rem; color:#1C1C2E; background:#F8FAFC; outline:none; transition:border-color .2s, box-shadow .2s; }
.field input:focus { border-color:#38BDF8; box-shadow:0 0 0 4px #E0F4FF; background:#fff; }
.err { display:block; color:#9D174D; font-size:.82rem; font-weight:700; margin-top:.4rem; }

.actions { display:flex; align-items:center; gap:1rem; margin-top:.4rem; }
.btn { padding:.75rem 2rem; border-radius:50px; font-family:inherit; font-weight:800; font-size:.95rem; border:none; cursor:pointer; transition:transform .15s; }
.btn:hover:not(:disabled) { transform:translateY(-2px); }
.btn:disabled { opacity:.5; cursor:not-allowed; }
.btn.save { background:#38BDF8; color:#fff; box-shadow:0 4px 0 #0E7490; }
.btn.delete { background:#EC4899; color:#fff; box-shadow:0 4px 0 #9D174D; }
.saved { color:#3F6212; font-weight:800; font-size:.9rem; }

.verify-note { background:#FEF3C7; border-radius:12px; padding:.9rem 1.1rem; font-size:.88rem; font-weight:700; color:#92400E; margin-bottom:1.1rem; }
.link-btn { background:none; border:none; color:#0E7490; font-family:inherit; font-weight:800; cursor:pointer; text-decoration:underline; padding:0; }
.ok-note { color:#3F6212; font-weight:700; margin-top:.5rem; }
</style>
