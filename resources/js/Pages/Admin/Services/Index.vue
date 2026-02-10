<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    services: {
        type: Array,
        required: true,
    },
});

const deleteService = (service) => {
    if (confirm('คุณแน่ใจว่าต้องการลบรายการบริการ "' + service.name + '" ใช่หรือไม่?')) {
        router.delete(route('admin.services.destroy', service.id));
    }
};
</script>

<template>
    <Head title="จัดการบริการ" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                จัดการบริการ
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-slate-900 border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-slate-900">รายการบริการทั้งหมด</h3>
                            <Link :href="route('admin.services.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                เพิ่มบริการใหม่
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-100 rounded-lg overflow-hidden">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            ชื่อบริการ
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            ราคา
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            สถานะ
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            การดำเนินการ
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="service in services" :key="service.id" class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-900 font-medium">
                                            {{ service.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-slate-700 font-bold">
                                            {{ Number(service.price).toLocaleString() }} ฿
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span v-if="service.is_active" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                เปิดใช้งาน
                                            </span>
                                            <span v-else class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                ปิดใช้งาน
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                            <Link :href="route('admin.services.edit', service.id)" class="text-indigo-600 hover:text-indigo-900 transition-colors">แก้ไข</Link>
                                            <button @click="deleteService(service)" class="text-red-600 hover:text-red-900 transition-colors">ลบ</button>
                                        </td>
                                    </tr>
                                    <tr v-if="services.length === 0">
                                        <td colspan="4" class="px-6 py-8 text-center text-slate-500 italic">
                                            ยังไม่มีรายการบริการ
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
