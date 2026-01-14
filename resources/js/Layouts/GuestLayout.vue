<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gradient-to-br from-teal-50 to-green-50 pt-6 sm:justify-center sm:pt-0"
    >
        <div class="mb-6 flex flex-col items-center">
            <Link href="/" class="flex flex-col items-center gap-2">
                <ApplicationLogo class="h-20 w-20 fill-current text-teal-600" />
                <span class="text-3xl font-bold tracking-tight text-teal-900">
                    Nava Clinic
                </span>
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-8 shadow-xl sm:max-w-md sm:rounded-2xl border border-teal-100"
        >
            <slot />
        </div>
    </div>
</template>
