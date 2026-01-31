<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import BodyPartSelector from '@/Components/BodyPartSelector.vue';

const props = defineProps({
    booking: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    status: '',
});

import { computed, ref } from 'vue';
import Modal from '@/Components/Modal.vue';

const showConfirmModal = ref(false);
const pendingStatus = ref(null);

const openConfirmModal = (status) => {
    pendingStatus.value = status;
    showConfirmModal.value = true;
};

const closeConfirmModal = () => {
    showConfirmModal.value = false;
    pendingStatus.value = null;
};

const confirmAction = () => {
    if (pendingStatus.value) {
        form.status = pendingStatus.value;
        form.patch(route('admin.bookings.update-status', props.booking.id), {
            onSuccess: () => closeConfirmModal(),
            onFinish: () => closeConfirmModal(),
        });
    }
};

const modalContent = computed(() => {
    switch (pendingStatus.value) {
        case 'confirmed':
            return {
                title: 'ยืนยันการจอง',
                message: 'คุณต้องการยืนยันการจองนี้ใช่หรือไม่?',
                description: 'การยืนยันการจองจะแจ้งเตือนผู้ป่วยว่าการจองได้รับการอนุมัติแล้ว',
                color: 'emerald',
                icon: 'check'
            };
        case 'completed':
            return {
                title: 'เสร็จสิ้นการรักษา',
                message: 'คุณต้องการบันทึกว่าการรักษานี้เสร็จสิ้นแล้วใช่หรือไม่?',
                description: 'สถานะการจองจะเปลี่ยนเป็น "เสร็จสิ้น" และบันทึกประวัติการรักษา',
                color: 'blue',
                icon: 'check-circle'
            };
        case 'cancelled':
            return {
                title: 'ยกเลิกการจอง',
                message: 'คุณต้องการยกเลิกการจองนี้ใช่หรือไม่?',
                description: 'การยกเลิกการจองไม่สามารถย้อนกลับได้ โปรดตรวจสอบความถูกต้อง',
                color: 'rose',
                icon: 'x-circle'
            };
        default:
            return {
                title: 'ยืนยันการเปลี่ยนสถานะ',
                message: `คุณต้องการเปลี่ยนสถานะเป็น ${pendingStatus.value} ใช่หรือไม่?`,
                description: '',
                color: 'slate',
                icon: 'question-mark-circle'
            };
    }
});

const getStatusClass = (status) => {
    switch (status) {
        case 'confirmed':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'pending':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'cancelled':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        case 'completed':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
        default:
            return 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300';

    }
};

const getStatusLabel = (status) => {
    switch (status) {
        case 'confirmed': return 'ยืนยันแล้ว';
        case 'pending': return 'รอดำเนินการ';
        case 'cancelled': return 'ยกเลิก';
        case 'completed': return 'เสร็จสิ้น';
        default: return status;
    }
};

const getPainAreaNames = (areas) => {
    if (!Array.isArray(areas)) return [];
    if (areas.length === 0) return [];
    if (typeof areas[0] === 'string') return areas;
    return areas.map(a => a.area);
};

const isDetailedPainArea = (areas) => {
    return Array.isArray(areas) && areas.length > 0 && typeof areas[0] !== 'string';
};

// ... (props)



// ... (existing functions)

</script>

<template>
    <Head title="รายละเอียดการจอง" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                    การจองเลขที่ #{{ booking.id }}
                </h2>
                <Link :href="route('admin.dashboard')" class="text-sm text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                    &larr; กลับไปหน้าแดชบอร์ด
                </Link>
            </div>

        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-blue-100">
                    <div class="p-8">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div>
                                <h3 class="text-lg font-bold mb-6 pb-3 border-b border-indigo-100 text-indigo-900 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-indigo-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                    ข้อมูลผู้ป่วย
                                </h3>
                                <div class="space-y-3 text-slate-600">
                                    <p><strong class="text-slate-800">ชื่อ:</strong> {{ booking.user ? booking.user.name : (booking.customer_name || 'Guest') }}</p>
                                    <p><strong class="text-slate-800">ข้อมูลติดต่อ:</strong> {{ booking.user ? booking.user.phone_number : (booking.customer_phone || '-') }}</p>
                                    <div class="mt-4 pt-2">
                                         <Link v-if="booking.user" :href="route('admin.patients.show', booking.user.id)" class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline">
                                            ดูประวัติผู้ป่วย
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                                <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                                            </svg>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold mb-6 pb-3 border-b border-indigo-100 text-indigo-900 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-indigo-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>
                                    รายละเอียดการนัดหมาย
                                </h3>
                                <div class="space-y-3 text-slate-600">
                                    <p><strong class="text-slate-800">แพทย์:</strong> {{ booking.doctor?.name }}</p>
                                    <p><strong class="text-slate-800">วันที่:</strong> {{ booking.appointment_date }}</p>
                                    <p><strong class="text-slate-800">เวลา:</strong> {{ booking.start_time }} ({{ booking.duration_minutes }} นาที)</p>
                                    <p><strong class="text-slate-800">อาการเบื้องต้น:</strong> {{ booking.symptoms }}</p>
                                    <p class="mt-2 flex items-center gap-2">
                                        <strong class="text-slate-800">สถานะ:</strong>
                                        <span :class="getStatusClass(booking.status)" class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide border border-current opacity-90">
                                            {{ getStatusLabel(booking.status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 border-t border-slate-100 pt-8">
                            <h3 class="text-lg font-bold mb-4 text-slate-800">การจัดการ</h3>
                            <div class="flex flex-wrap gap-4">
                                <button
                                    v-if="booking.status !== 'confirmed' && booking.status !== 'completed' && booking.status !== 'cancelled'"
                                    @click="openConfirmModal('confirmed')"
                                    class="btn bg-emerald-600 hover:bg-emerald-700 text-white border-none shadow-md hover:shadow-lg transition-all"
                                    :disabled="form.processing"
                                >
                                    ยืนยันการจอง
                                </button>
                                <button
                                    v-if="booking.status !== 'completed' && booking.status !== 'pending' && booking.status !== 'cancelled'"
                                    @click="openConfirmModal('completed')"
                                    class="btn bg-blue-600 hover:bg-blue-700 text-white border-none shadow-md hover:shadow-lg transition-all"
                                    :disabled="form.processing"
                                >
                                    เสร็จสิ้นการรักษา
                                </button>
                                <button
                                    v-if="booking.status !== 'cancelled' && booking.status !== 'completed'"
                                    @click="openConfirmModal('cancelled')"
                                    class="btn bg-rose-600 hover:bg-rose-700 text-white border-none shadow-md hover:shadow-lg transition-all"
                                    :disabled="form.processing"
                                >
                                    ยกเลิกการจอง
                                </button>

                                <Link
                                    :href="route('admin.bookings.edit', booking.id)"
                                    class="btn bg-slate-600 hover:bg-slate-700 text-white border-none shadow-md hover:shadow-lg transition-all"
                                >
                                    แก้ไขข้อมูลการนัด
                                </Link>
                            </div>
                        </div>

                    </div>
                    </div>





                <!-- Medical Record Section -->
                <div v-if="booking.treatment_record" class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-indigo-100">
                    <div class="px-6 py-4 border-b border-indigo-50 flex justify-between items-center bg-indigo-50/30">
                        <h3 class="font-bold text-indigo-900 text-lg flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-indigo-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            บันทึกเวชระเบียน
                            <span class="text-[10px] font-normal text-indigo-400 border border-indigo-200 px-2 py-0.5 rounded-full">Medical Record</span>
                        </h3>
                        <Link :href="route('admin.treatment.create', booking.id)" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:underline flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            แก้ไขบันทึก
                        </Link>
                    </div>

                    <div class="p-6">
                        <!-- 4-Column Grid: Vitals/Clinical (1) | PE (1) | Body Map (2) -->
                        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 mb-8">
                            
                            <!-- Column 1: Vitals & Clinical Assessment -->
                            <div class="space-y-6">
                                <!-- Vitals -->
                                <div>
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3 text-slate-400">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                        </svg>
                                        สัญญาณชีพ (VITAL SIGNS)
                                    </h4>
                                    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
                                        <div class="grid grid-cols-2 gap-y-4 gap-x-6">
                                            <div class="flex flex-col items-center p-2 rounded-lg hover:bg-slate-50">
                                                <span class="text-[10px] text-slate-400 font-bold uppercase">ความดัน (BP)</span>
                                                <div class="font-bold text-indigo-600 text-lg">{{ booking.treatment_record.blood_pressure || '-' }}</div>
                                                <span class="text-[10px] text-slate-400">mmHg</span>
                                            </div>
                                            <div class="flex flex-col items-center p-2 rounded-lg hover:bg-slate-50">
                                                <span class="text-[10px] text-slate-400 font-bold uppercase">อุณหภูมิ (TEMP)</span>
                                                <div class="font-bold text-rose-500 text-lg">{{ booking.treatment_record.temperature || '-' }}</div>
                                                <span class="text-[10px] text-slate-400">°C</span>
                                            </div>
                                            <div class="flex flex-col items-center p-2 rounded-lg hover:bg-slate-50">
                                                <span class="text-[10px] text-slate-400 font-bold uppercase">ชีพจร (PULSE)</span>
                                                <div class="font-bold text-blue-500 text-lg">{{ booking.treatment_record.pulse_rate || '-' }}</div>
                                                <span class="text-[10px] text-slate-400">bpm</span>
                                            </div>
                                            <div class="flex flex-col items-center p-2 rounded-lg hover:bg-slate-50">
                                                <span class="text-[10px] text-slate-400 font-bold uppercase">หายใจ (RESP)</span>
                                                <div class="font-bold text-emerald-500 text-lg">{{ booking.treatment_record.respiratory_rate || '-' }}</div>
                                                <span class="text-[10px] text-slate-400">bpm</span>
                                            </div>
                                            <div class="col-span-2 flex items-center justify-center gap-8 pt-2 mt-2 border-t border-slate-100">
                                                <div class="flex items-baseline gap-2">
                                                    <span class="text-[10px] font-bold text-slate-400 uppercase">BW</span>
                                                    <span class="font-bold text-slate-700">{{ booking.treatment_record.weight || '-' }}</span> <span class="text-[10px] text-slate-400">kg</span>
                                                </div>
                                                <div class="flex items-baseline gap-2">
                                                    <span class="text-[10px] font-bold text-slate-400 uppercase">HT</span>
                                                    <span class="font-bold text-slate-700">{{ booking.treatment_record.height || '-' }}</span> <span class="text-[10px] text-slate-400">cm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Clinical -->
                                <div>
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3 text-slate-400">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
                                        </svg>
                                        การตรวจประเมิน (CLINICAL)
                                    </h4>
                                    <div class="space-y-3">
                                        <div class="bg-amber-50 p-4 rounded-xl border border-amber-100/50">
                                            <div class="text-[10px] font-bold text-amber-600 uppercase mb-1">อาการสำคัญ (CC)</div>
                                            <p class="text-sm text-slate-800 font-medium leading-relaxed">{{ booking.treatment_record.chief_complaint || '-' }}</p>
                                        </div>
                                        <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100/50">
                                            <div class="text-[10px] font-bold text-indigo-600 uppercase mb-1">การวินิจฉัย (Dx)</div>
                                            <p class="text-sm text-indigo-900 font-medium leading-relaxed">{{ booking.treatment_record.diagnosis || '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Column 2: Physical Exam -->
                            <div class="flex flex-col">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3 flex items-center gap-2">
                                    การตรวจร่างกาย (PE)
                                </h4>
                                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex-1">
                                    <div v-if="booking.treatment_record.physical_exam" class="whitespace-pre-wrap text-sm text-slate-700 leading-relaxed">
                                        {{ booking.treatment_record.physical_exam }}
                                    </div>
                                    <div v-else class="h-full flex flex-col items-center justify-center text-slate-400 italic text-sm">
                                        ไม่มีข้อมูลการตรวจร่างกาย
                                    </div>
                                </div>
                            </div>

                            <!-- Column 3: Body Map (Pain Areas) - Spans 2 cols -->
                            <div class="flex flex-col xl:col-span-2">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3 flex items-center gap-2">
                                    ตำแหน่งที่ปวด
                                </h4>
                                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-full">
                                    <!-- Body Selector -->
                                    <div class="p-4 bg-slate-50/30 flex items-center justify-center min-h-[500px]">
                                         <div v-if="booking.treatment_record.pain_areas && booking.treatment_record.pain_areas.length > 0" class="w-full">
                                            <BodyPartSelector :model-value="booking.treatment_record.pain_areas" :readonly="true"/>
                                        </div>
                                        <div v-else class="flex flex-col items-center justify-center text-slate-300 gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 opacity-20">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                            </svg>
                                            <span class="text-xs">ไม่ระบุตำแหน่ง</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Pain Details List -->
                                    <div v-if="isDetailedPainArea(booking.treatment_record.pain_areas)" class="border-t border-slate-100 bg-slate-50/50 p-4 max-h-60 overflow-y-auto custom-scrollbar">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div v-for="(item, idx) in booking.treatment_record.pain_areas" :key="idx" class="bg-white border-l-4 border-rose-400 shadow-sm rounded-r-xl p-3 flex flex-col gap-2 group hover:shadow-md transition-all">
                                                <div class="flex justify-between items-start">
                                                    <span class="font-bold text-slate-700 text-sm border-b border-transparent group-hover:border-rose-200 transition-colors">{{ item.area }}</span>
                                                    <div v-if="item.pain_level || item.pain_level_after" class="flex items-center gap-1.5 bg-slate-100 px-2 py-1 rounded-md text-[10px] font-bold border border-slate-200">
                                                        <div class="flex items-center gap-1">
                                                            <span class="text-rose-500">Pain:</span>
                                                            <span class="text-slate-700 text-sm">{{ item.pain_level || '-' }}</span>
                                                        </div>
                                                        <template v-if="item.pain_level_after">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3 text-slate-400">
                                                                <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414-1.414L14.586 11H3a1 1 0 1 1 0-2h11.586l-2.293-2.293a1 1 0 0 1 0-1.414Z" clip-rule="evenodd" />
                                                            </svg>
                                                            <span class="text-emerald-600 text-sm">{{ item.pain_level_after }}</span>
                                                        </template>
                                                    </div>
                                                </div>
                                                <p class="text-xs text-slate-500 leading-relaxed font-medium bg-slate-50 p-2 rounded-lg" v-if="item.symptom">{{ item.symptom }}</p>
                                                <p class="text-xs text-slate-300 italic" v-else>ไม่มีรายละเอียดอาการ</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Section: Treatment Plan -->
                         <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3 flex items-center gap-2">
                                แผนการรักษา (TREATMENT PLAN)
                            </h4>
                            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 relative overflow-hidden">
                                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                                    <div class="lg:col-span-3">
                                        <div class="prose prose-sm max-w-none text-slate-700 font-medium leading-relaxed">
                                            {{ booking.treatment_record.treatment_details || 'ระบุรายละเอียดการรักษา...' }}
                                        </div>
                                        
                                        <!-- Footer Info -->
                                        <div class="mt-6 flex flex-wrap gap-4 items-center">
                                            <div class="px-4 py-3 bg-slate-50 rounded-xl border border-slate-100 flex flex-col items-center min-w-[100px]">
                                                <span class="text-[10px] font-bold text-slate-400 uppercase">น้ำหนักมือ</span>
                                                <span class="capitalize font-bold text-slate-700">{{ booking.treatment_record.massage_weight || '-' }}</span>
                                            </div>
                                            
                                            <div class="px-4 py-3 bg-rose-50 rounded-xl border border-rose-100 flex flex-col items-center min-w-[100px]">
                                                <span class="text-[10px] font-bold text-rose-400 uppercase">Pain (Before)</span>
                                                <span class="font-bold text-rose-600">-</span>
                                            </div>
                                            
                                            <div class="px-4 py-3 bg-emerald-50 rounded-xl border border-emerald-100 flex flex-col items-center min-w-[100px]">
                                                <span class="text-[10px] font-bold text-emerald-400 uppercase">Pain (After)</span>
                                                <span class="font-bold text-emerald-600">-</span>
                                            </div>
                                        </div>
                                    
                                        <!-- Notes -->
                                        <div v-if="booking.treatment_record.notes" class="mt-4 text-sm text-slate-500 italic border-l-2 border-slate-200 pl-3">
                                            หมายเหตุ: {{ booking.treatment_record.notes }}
                                        </div>
                                    </div>

                                    <!-- Total Bill Highlight -->
                                    <div class="lg:col-span-1 bg-indigo-50/50 rounded-xl flex flex-col items-center justify-center p-6 border border-indigo-100 text-center">
                                        <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-1">ยอดรวมค่ารักษา (TOTAL BILL)</span>
                                        <div class="text-3xl font-black text-indigo-600 tracking-tight">
                                           {{ booking.price ? Number(booking.price).toLocaleString() : '0' }} <span class="text-base font-bold text-indigo-400">฿</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <Modal :show="showConfirmModal" @close="closeConfirmModal" maxWidth="md">
            <div class="p-6">
                <!-- Icon -->
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full transition-colors"
                    :class="{
                        'bg-emerald-100 text-emerald-600': modalContent.color === 'emerald',
                        'bg-blue-100 text-blue-600': modalContent.color === 'blue',
                        'bg-rose-100 text-rose-600': modalContent.color === 'rose',
                        'bg-slate-100 text-slate-600': modalContent.color === 'slate'
                    }"
                >
                    <svg v-if="modalContent.icon === 'check'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9.135-9.135" />
                    </svg>
                    <svg v-else-if="modalContent.icon === 'check-circle'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <svg v-else-if="modalContent.icon === 'x-circle'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                    </svg>
                </div>

                <!-- Text Content -->
                <div class="text-center mb-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">{{ modalContent.title }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">{{ modalContent.message }}</p>
                    <p class="text-xs text-slate-400 mt-1" v-if="modalContent.description">{{ modalContent.description }}</p>
                </div>

                <!-- Booking Summary Card -->
                <div class="bg-slate-50 rounded-xl p-4 mb-6 border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-white rounded-bl-full opacity-50"></div>
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3 text-center border-b border-slate-200 pb-2">รายละเอียดการจอง</h4>
                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 text-xs">ผู้ป่วย</span>
                            <span class="font-bold text-slate-800">{{ booking.user ? booking.user.name : (booking.customer_name || 'Guest') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 text-xs">วันที่นัดหมาย</span>
                            <span class="font-medium text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md">{{ booking.appointment_date }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 text-xs">เวลานัดหมาย</span>
                            <span class="font-medium text-slate-800">{{ booking.start_time }}</span>
                        </div>
                         <div class="flex justify-between items-center">
                            <span class="text-slate-500 text-xs">แพทย์ผู้รักษา</span>
                            <span class="font-medium text-slate-800">{{ booking.doctor?.name || '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button
                        @click="closeConfirmModal"
                        class="flex-1 w-full justify-center rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-bold text-slate-600 shadow-sm hover:bg-slate-50 hover:text-slate-800 transition-all sm:w-auto"
                    >
                        ยกเลิก
                    </button>
                    <button
                        @click="confirmAction"
                        :class="{
                            'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200': modalContent.color === 'emerald',
                            'bg-blue-600 hover:bg-blue-700 shadow-blue-200': modalContent.color === 'blue',
                            'bg-rose-600 hover:bg-rose-700 shadow-rose-200': modalContent.color === 'rose',
                            'bg-slate-600 hover:bg-slate-700 shadow-slate-200': modalContent.color === 'slate'
                        }"
                        class="flex-1 w-full justify-center rounded-xl px-3 py-2.5 text-sm font-bold text-white shadow-lg transition-all transform hover:scale-[1.02] sm:w-auto"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing" class="flex items-center gap-2 justify-center">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            กำลังดำเนินการ...
                        </span>
                        <span v-else>ยืนยันดำเนินการ</span>
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
