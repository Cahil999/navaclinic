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

const updateStatus = (status) => {
    let confirmMessage = '';
    switch (status) {
        case 'confirmed':
            confirmMessage = 'คุณแน่ใจหรือไม่ที่จะยืนยันการจองนี้?';
            break;
        case 'completed':
            confirmMessage = 'คุณแน่ใจหรือไม่ที่จะระบุว่าการจองนี้เสร็จสิ้นแล้ว?';
            break;
        case 'cancelled':
            confirmMessage = 'คุณแน่ใจหรือไม่ที่จะยกเลิกการจองนี้?';
            break;
        default:
            confirmMessage = `คุณแน่ใจหรือไม่ที่จะเปลี่ยนสถานะเป็น ${status}?`;
    }
    
    if (confirm(confirmMessage)) {
        form.status = status;
        form.patch(route('admin.bookings.update-status', props.booking.id));
    }
};

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
import { computed } from 'vue';

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
                                    @click="updateStatus('confirmed')"
                                    class="btn bg-emerald-600 hover:bg-emerald-700 text-white border-none shadow-md hover:shadow-lg transition-all"
                                    :disabled="form.processing"
                                >
                                    ยืนยันการจอง
                                </button>
                                <button
                                    v-if="booking.status !== 'completed' && booking.status !== 'pending' && booking.status !== 'cancelled'"
                                    @click="updateStatus('completed')"
                                    class="btn bg-blue-600 hover:bg-blue-700 text-white border-none shadow-md hover:shadow-lg transition-all"
                                    :disabled="form.processing"
                                >
                                    เสร็จสิ้นการรักษา
                                </button>
                                <button
                                    v-if="booking.status !== 'cancelled' && booking.status !== 'completed'"
                                    @click="updateStatus('cancelled')"
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
                    <div class="bg-gradient-to-r from-indigo-50 to-white px-8 py-6 border-b border-indigo-100 flex justify-between items-center">
                        <h3 class="font-bold text-indigo-900 text-xl flex items-center gap-3">
                            <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            </div>
                            บันทึกเวชระเบียน
                             <span class="text-xs font-normal text-indigo-400 border border-indigo-200 px-2 py-0.5 rounded-full">Medical Record</span>
                        </h3>
                        <Link :href="route('admin.treatment.create', booking.id)" class="group flex items-center gap-2 text-sm font-bold text-indigo-600 hover:text-indigo-700 bg-white px-4 py-2 rounded-xl border border-indigo-100 hover:border-indigo-300 transition-all shadow-sm hover:shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 group-hover:scale-110 transition-transform">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            แก้ไขบันทึก
                        </Link>
                    </div>
                    
                    <div class="p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                            <!-- Left Details: Patient & Vital Signs -->
                            <div class="lg:col-span-4 space-y-6 border-r border-slate-100 pr-0 lg:pr-8">


                                <section>
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4 text-slate-400">
                                          <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                        </svg>
                                        สัญญาณชีพ (Vital Signs)
                                    </h4>
                                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-5">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="text-center">
                                                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">ความดัน (BP)</div>
                                                <div class="text-xl font-bold text-indigo-600">{{ booking.treatment_record.blood_pressure || '-' }}</div>
                                                <div class="text-[10px] text-slate-400">mmHg</div>
                                            </div>
                                            <div class="text-center relative">
                                                <div class="absolute left-0 top-2 bottom-2 w-px bg-slate-100"></div>
                                                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">อุณหภูมิ (Temp)</div>
                                                <div class="text-xl font-bold text-rose-500">{{ booking.treatment_record.temperature || '-' }}</div>
                                                <div class="text-[10px] text-slate-400">°C</div>
                                            </div>
                                            <div class="text-center border-t border-slate-100 pt-4">
                                                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">ชีพจร (Pulse)</div>
                                                <div class="text-xl font-bold text-blue-500">{{ booking.treatment_record.pulse_rate || '-' }}</div>
                                                <div class="text-[10px] text-slate-400">bpm</div>
                                            </div>
                                            <div class="text-center border-t border-slate-100 pt-4 relative">
                                                <div class="absolute left-0 top-4 bottom-0 w-px bg-slate-100"></div>
                                                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">หายใจ (Resp)</div>
                                                <div class="text-xl font-bold text-emerald-500">{{ booking.treatment_record.respiratory_rate || '-' }}</div>
                                                <div class="text-[10px] text-slate-400">bpm</div>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                                            <div class="bg-slate-50 rounded-xl p-3 flex justify-between items-center px-4">
                                                <span class="text-xs font-bold text-slate-500">BW</span>
                                                <span class="font-bold text-slate-800">{{ booking.treatment_record.weight || '-' }} <span class="text-[10px] text-slate-400 font-normal">kg</span></span>
                                            </div>
                                            <div class="bg-slate-50 rounded-xl p-3 flex justify-between items-center px-4">
                                                <span class="text-xs font-bold text-slate-500">HT</span>
                                                <span class="font-bold text-slate-800">{{ booking.treatment_record.height || '-' }} <span class="text-[10px] text-slate-400 font-normal">cm</span></span>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <!-- Clinical Assessment (Moved to Left) -->
                                <section>
                                     <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4 text-slate-400">
                                          <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
                                        </svg>
                                        การตรวจประเมิน (Clinical)
                                     </h4>
                                     <div class="space-y-4">
                                        <div class="bg-amber-50 p-4 rounded-xl border border-amber-100 relative overflow-hidden group hover:shadow-sm transition-shadow">
                                            <div class="absolute top-0 right-0 w-12 h-12 bg-amber-100 rounded-bl-full -mr-6 -mt-6 opacity-50 group-hover:scale-110 transition-transform"></div>
                                            <dt class="text-[10px] font-bold text-amber-500 mb-1 uppercase tracking-wide">อาการสำคัญ (CC)</dt>
                                            <dd class="text-sm font-medium text-slate-800 leading-snug">{{ booking.treatment_record.chief_complaint || '-' }}</dd>
                                        </div>
                                         <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 relative overflow-hidden group hover:shadow-sm transition-shadow">
                                            <div class="absolute top-0 right-0 w-12 h-12 bg-indigo-100 rounded-bl-full -mr-6 -mt-6 opacity-50 group-hover:scale-110 transition-transform"></div>
                                            <dt class="text-[10px] font-bold text-indigo-500 mb-1 uppercase tracking-wide">การวินิจฉัย (Dx)</dt>
                                            <dd class="text-sm font-medium text-indigo-900 leading-snug">{{ booking.treatment_record.diagnosis || '-' }}</dd>
                                        </div>
                                     </div>
                                </section>
                            </div>

                            <!-- Right Details: Assessment & Treatment -->
                            <div class="lg:col-span-8 space-y-8">
                                <!-- Clinical Assessment -->


                                <!-- Physical Exam & Pain Areas -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <section>
                                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">การตรวจร่างกาย (PE)</h4>
                                        <div class="bg-white p-6 rounded-2xl border border-slate-200 text-sm text-slate-700 leading-relaxed font-medium h-full min-h-[250px]">
                                            {{ booking.treatment_record.physical_exam || 'ไม่มีข้อมูลการตรวจร่างกาย' }}
                                        </div>
                                    </section>
                                    <section>
                                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">ตำแหน่งที่ปวด</h4>
                                         <div class="bg-white p-4 rounded-2xl border border-slate-200 flex flex-col justify-start items-center h-full min-h-[250px]">
                                            <div v-if="booking.treatment_record.pain_areas && booking.treatment_record.pain_areas.length > 0" class="w-full">
                                                <div class="flex justify-center">
                                                    <BodyPartSelector :model-value="booking.treatment_record.pain_areas" :readonly="true" />
                                                </div>
                                                
                                                <div v-if="isDetailedPainArea(booking.treatment_record.pain_areas)" class="mt-6 border-t border-slate-100 pt-4 w-full">
                                                    <h5 class="text-xs font-bold text-slate-500 mb-3 uppercase tracking-wide">รายละเอียดอาการ</h5>
                                                    <ul class="space-y-3">
                                                        <li v-for="(item, idx) in booking.treatment_record.pain_areas" :key="idx" class="text-sm bg-slate-50 p-3 rounded-lg border border-slate-100">
                                                            <div class="font-bold text-indigo-900 flex items-center justify-between">
                                                                {{ item.area }}
                                                                <div class="flex gap-2">
                                                                    <span v-if="item.pain_level" class="text-xs px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 border border-rose-200">Before: {{ item.pain_level }}/10</span>
                                                                    <span v-if="item.pain_level_after" class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">After: {{ item.pain_level_after }}/10</span>
                                                                </div>
                                                            </div>
                                                            <div v-if="item.symptom" class="text-slate-600 mt-1 pl-2 border-l-2 border-indigo-200">{{ item.symptom }}</div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div v-else class="text-slate-400 text-xs text-center italic my-auto">ไม่ได้ระบุตำแหน่ง</div>
                                        </div>
                                    </section>
                                </div>

                                <!-- Treatment & Plan -->
                                <section>
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">แผนการรักษา (Treatment Plan)</h4>
                                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                                        <div class="p-6 md:p-8">
                                             <div class="prose prose-sm prose-slate max-w-none mb-6">
                                                <p class="whitespace-pre-wrap leading-relaxed">{{ booking.treatment_record.treatment_details || 'ไม่มีข้อมูลรายละเอียดการรักษา' }}</p>
                                             </div>
                                             
                                             <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6 border-t border-slate-100">
                                                 <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                                                     <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">น้ำหนักมือ</div>
                                                     <div class="text-lg font-bold text-slate-800 capitalize">{{ booking.treatment_record.massage_weight || '-' }}</div>
                                                 </div>
                                                  <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-center">
                                                     <div class="text-[10px] font-bold text-rose-400 uppercase mb-1">Pain (Before)</div>
                                                     <div class="text-2xl font-bold text-rose-600">{{ booking.treatment_record.pain_level_before ?? '-' }}</div>
                                                 </div>
                                                  <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-center">
                                                     <div class="text-[10px] font-bold text-emerald-400 uppercase mb-1">Pain (After)</div>
                                                     <div class="text-2xl font-bold text-emerald-600">{{ booking.treatment_record.pain_level_after ?? '-' }}</div>
                                                 </div>
                                             </div>
                                        </div>
                                    </div>
                                </section>

                                <section v-if="booking.treatment_record.notes">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">หมายเหตุแพทย์</h4>
                                    <div class="bg-yellow-50 p-6 rounded-2xl border border-yellow-100 text-yellow-900 text-sm relative"> 
                                        {{ booking.treatment_record.notes }}
                                    </div>
                                </section>

                                <div class="mt-4 pt-4 border-t border-slate-100 flex justify-between items-center bg-indigo-50 p-4 rounded-xl">
                                    <span class="text-sm font-bold text-indigo-900 uppercase">ยอดรวมค่ารักษา (Total Bill)</span>
                                    <span class="text-2xl font-bold text-indigo-700">{{ booking.price ? Number(booking.price).toLocaleString() : '0' }} ฿</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
