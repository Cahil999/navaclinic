<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="สมัครสมาชิก" />

        <div class="mb-6 text-center animate-fade-in-up" style="animation-delay: 0.3s;">
            <h2 class="text-3xl font-bold text-blue-900 mb-2">สร้างบัญชีใหม่</h2>
            <p class="text-sm text-slate-500">สมัครสมาชิกกับนวคลินิกวันนี้</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4 animate-fade-in-up" style="animation-delay: 0.4s;">
            <div class="group">
                <InputLabel for="name" value="ชื่อ-นามสกุล" class="text-slate-700 mb-1.5" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full bg-slate-50 border-slate-200 text-slate-900 focus:bg-white focus:border-blue-400 focus:ring-blue-400 transition-all duration-300 shadow-sm"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="สมชาย ใจดี"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="group">
                <InputLabel for="email" value="อีเมล" class="text-slate-700 mb-1.5" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-slate-50 border-slate-200 text-slate-900 focus:bg-white focus:border-blue-400 focus:ring-blue-400 transition-all duration-300 shadow-sm"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="name@example.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="group">
                <InputLabel for="password" value="รหัสผ่าน" class="text-slate-700 mb-1.5" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full bg-slate-50 border-slate-200 text-slate-900 focus:bg-white focus:border-blue-400 focus:ring-blue-400 transition-all duration-300 shadow-sm"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="group">
                <InputLabel
                    for="password_confirmation"
                    value="ยืนยันรหัสผ่าน"
                    class="text-slate-700 mb-1.5"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full bg-slate-50 border-slate-200 text-slate-900 focus:bg-white focus:border-blue-400 focus:ring-blue-400 transition-all duration-300 shadow-sm"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="pt-4">
                <PrimaryButton
                    class="w-full justify-center py-3.5 text-base font-semibold bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/30 border-0 ring-0 transform hover:-translate-y-0.5 transition-all duration-300"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                     <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    สมัครสมาชิก
                </PrimaryButton>
            </div>

            <div class="text-center text-sm text-slate-500 pt-4 border-t border-slate-100">
                มีบัญชีอยู่แล้ว?
                <Link
                    :href="route('login')"
                    class="font-medium text-blue-600 hover:text-blue-800 ml-1 transition-colors hover:underline"
                >
                    เข้าสู่ระบบ
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out backwards;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out backwards;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
