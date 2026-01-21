<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import BodyPartSelector from '@/Components/BodyPartSelector.vue';
import { computed } from 'vue';

const props = defineProps({
    visit: Object,
});

const paymentForm = useForm({
    amount: '',
    payment_method: 'cash',
    notes: '',
    payment_date: new Date().toISOString().slice(0, 16),
});

const totalPaid = computed(() => {
    if (!props.visit.payments) return 0;
    return props.visit.payments.reduce((sum, payment) => sum + Number(payment.amount), 0);
});

const remainingAmount = computed(() => {
    const price = props.visit.price ? Number(props.visit.price) : 0;
    return Math.max(0, price - totalPaid.value);
});

const submitPayment = () => {
    if (!paymentForm.amount) return;
    
    if (Number(paymentForm.amount) > remainingAmount.value) {
        alert(`ยอดชำระเกินกว่าที่ต้องชำระ (สูงสุด ${remainingAmount.value.toLocaleString()} ฿)`);
        return;
    }
    paymentForm.post(route('admin.visits.payments.store', props.visit.id), {
        onSuccess: () => {
            paymentForm.reset('amount', 'notes');
            paymentForm.payment_method = 'cash';
        }
    });
};

const deletePayment = (id) => {
    if (confirm('Are you sure you want to delete this payment?')) {
        useForm({}).delete(route('admin.payments.destroy', id), {
             preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Visit Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('admin.patients.show', visit.patient.id)" class="text-slate-400 hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    Visit Details
                </h2>
                <span class="px-3 py-1 text-xs rounded-full font-bold uppercase tracking-wide"
                    :class="{'bg-emerald-100 text-emerald-800': visit.status === 'completed', 'bg-blue-100 text-blue-800': visit.status === 'ongoing', 'bg-slate-100 text-slate-800': visit.status === 'pending'}">
                    {{ visit.status }}
                </span>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Main Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                         <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Patient</p>
                            <h3 class="font-bold text-slate-900 text-lg">{{ visit.patient.name }}</h3>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Visit Date</p>
                            <p class="font-bold text-slate-900">{{ new Date(visit.visit_date).toLocaleDateString() }} {{ new Date(visit.visit_date).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</p>
                        </div>
                    </div>
                    
                    <div class="p-8 space-y-8">
                        <!-- Clinical Info -->
                        <div>
                            <h4 class="font-bold text-slate-800 border-b border-slate-100 pb-2 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Clinical Information
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase mb-1">Doctor</p>
                                    <p class="text-slate-900 font-medium">{{ visit.doctor?.name || 'Unassigned' }}</p>
                                </div>
                                 <div class="md:col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-100">
                                    <p class="text-xs font-bold text-slate-400 uppercase mb-2">Symptoms / Chief Complaint</p>
                                    <p class="text-slate-900 leading-relaxed">{{ visit.symptoms || '-' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-xs font-bold text-slate-400 uppercase mb-1">Notes</p>
                                    <p class="text-slate-700">{{ visit.notes || '-' }}</p>
                                </div>
                            </div>
                        </div>

                         <!-- Related Booking -->
                         <div v-if="visit.booking" class="pt-6 border-t border-slate-100">
                            <h4 class="font-bold text-slate-800 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Related Booking
                            </h4>
                            <div class="bg-amber-50 rounded-xl p-4 border border-amber-100 flex items-center justify-between">
                                <div>
                                    <p class="text-amber-900 font-bold">Booking #{{ visit.booking.id }}</p>
                                    <p class="text-amber-700 text-sm">{{ visit.booking.appointment_date }} @ {{ visit.booking.start_time }}</p>
                                </div>
                                <Link :href="route('admin.bookings.show', visit.booking.id)" class="text-amber-700 hover:text-amber-900 font-bold text-xs uppercase underline">
                                    View Booking
                                </Link>
                            </div>
                         </div>
                         <div v-else class="pt-6 border-t border-slate-100">
                             <p class="text-emerald-600 font-medium flex items-center text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Walk-in Visit (No Booking Required)
                             </p>
                         </div>

                         <!-- Management Actions -->
                         <div class="pt-6 border-t border-slate-100">
                            <h4 class="font-bold text-slate-800 mb-4">Management</h4>
                            <div class="flex flex-wrap gap-4">
                                <Link
                                    v-if="!visit.treatment_record"
                                    :href="route('admin.visits.treatment.create', visit.id)"
                                    class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-none shadow-md hover:shadow-lg transition-all"
                                >
                                    เพิ่มรายละเอียดการรักษา
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Treatment Record Section -->
                <div v-if="visit.treatment_record" class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-indigo-100">
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
                        <Link :href="route('admin.visits.treatment.create', visit.id)" class="group flex items-center gap-2 text-sm font-bold text-indigo-600 hover:text-indigo-700 bg-white px-4 py-2 rounded-xl border border-indigo-100 hover:border-indigo-300 transition-all shadow-sm hover:shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 group-hover:scale-110 transition-transform">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            แก้ไขบันทึก
                        </Link>
                    </div>
                    
                    <div class="p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                            <!-- Left Details: Vital Signs -->
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
                                                <div class="text-xl font-bold text-indigo-600">{{ visit.treatment_record.blood_pressure || '-' }}</div>
                                                <div class="text-[10px] text-slate-400">mmHg</div>
                                            </div>
                                            <div class="text-center relative">
                                                <div class="absolute left-0 top-2 bottom-2 w-px bg-slate-100"></div>
                                                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">อุณหภูมิ (Temp)</div>
                                                <div class="text-xl font-bold text-rose-500">{{ visit.treatment_record.temperature || '-' }}</div>
                                                <div class="text-[10px] text-slate-400">°C</div>
                                            </div>
                                            <div class="text-center border-t border-slate-100 pt-4">
                                                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">ชีพจร (Pulse)</div>
                                                <div class="text-xl font-bold text-blue-500">{{ visit.treatment_record.pulse_rate || '-' }}</div>
                                                <div class="text-[10px] text-slate-400">bpm</div>
                                            </div>
                                            <div class="text-center border-t border-slate-100 pt-4 relative">
                                                <div class="absolute left-0 top-4 bottom-0 w-px bg-slate-100"></div>
                                                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">หายใจ (Resp)</div>
                                                <div class="text-xl font-bold text-emerald-500">{{ visit.treatment_record.respiratory_rate || '-' }}</div>
                                                <div class="text-[10px] text-slate-400">bpm</div>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                                            <div class="bg-slate-50 rounded-xl p-3 flex justify-between items-center px-4">
                                                <span class="text-xs font-bold text-slate-500">BW</span>
                                                <span class="font-bold text-slate-800">{{ visit.treatment_record.weight || '-' }} <span class="text-[10px] text-slate-400 font-normal">kg</span></span>
                                            </div>
                                            <div class="bg-slate-50 rounded-xl p-3 flex justify-between items-center px-4">
                                                <span class="text-xs font-bold text-slate-500">HT</span>
                                                <span class="font-bold text-slate-800">{{ visit.treatment_record.height || '-' }} <span class="text-[10px] text-slate-400 font-normal">cm</span></span>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <!-- Clinical Assessment -->
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
                                            <dd class="text-sm font-medium text-slate-800 leading-snug">{{ visit.treatment_record.chief_complaint || '-' }}</dd>
                                        </div>
                                         <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 relative overflow-hidden group hover:shadow-sm transition-shadow">
                                            <div class="absolute top-0 right-0 w-12 h-12 bg-indigo-100 rounded-bl-full -mr-6 -mt-6 opacity-50 group-hover:scale-110 transition-transform"></div>
                                            <dt class="text-[10px] font-bold text-indigo-500 mb-1 uppercase tracking-wide">การวินิจฉัย (Dx)</dt>
                                            <dd class="text-sm font-medium text-indigo-900 leading-snug">{{ visit.treatment_record.diagnosis || '-' }}</dd>
                                        </div>
                                     </div>
                                </section>
                            </div>

                            <!-- Right Details: Assessment & Treatment -->
                            <div class="lg:col-span-8 space-y-8">
                                <!-- Physical Exam & Pain Areas -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <section>
                                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">การตรวจร่างกาย (PE)</h4>
                                        <div class="bg-white p-6 rounded-2xl border border-slate-200 text-sm text-slate-700 leading-relaxed font-medium h-full min-h-[250px]">
                                            {{ visit.treatment_record.physical_exam || 'ไม่มีข้อมูลการตรวจร่างกาย' }}
                                        </div>
                                    </section>
                                    <section>
                                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">ตำแหน่งที่ปวด</h4>
                                         <div class="bg-white p-4 rounded-2xl border border-slate-200 flex justify-center items-center h-full min-h-[250px]">
                                            <div v-if="visit.treatment_record.pain_areas && visit.treatment_record.pain_areas.length > 0">
                                                <BodyPartSelector :model-value="visit.treatment_record.pain_areas" :readonly="true" />
                                            </div>
                                            <div v-else class="text-slate-400 text-xs text-center italic">ไม่ได้ระบุตำแหน่ง</div>
                                        </div>
                                    </section>
                                </div>

                                <!-- Treatment & Plan -->
                                <section>
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">แผนการรักษา (Treatment Plan)</h4>
                                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                                        <div class="p-6 md:p-8">
                                             <div class="prose prose-sm prose-slate max-w-none mb-6">
                                                <p class="whitespace-pre-wrap leading-relaxed">{{ visit.treatment_record.treatment_details || 'ไม่มีข้อมูลรายละเอียดการรักษา' }}</p>
                                             </div>
                                             
                                             <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6 border-t border-slate-100">
                                                 <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                                                     <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">น้ำหนักมือ</div>
                                                     <div class="text-lg font-bold text-slate-800 capitalize">{{ visit.treatment_record.massage_weight || '-' }}</div>
                                                 </div>
                                                  <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-center">
                                                     <div class="text-[10px] font-bold text-rose-400 uppercase mb-1">Pain (Before)</div>
                                                     <div class="text-2xl font-bold text-rose-600">{{ visit.treatment_record.pain_level_before ?? '-' }}</div>
                                                 </div>
                                                  <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-center">
                                                     <div class="text-[10px] font-bold text-emerald-400 uppercase mb-1">Pain (After)</div>
                                                     <div class="text-2xl font-bold text-emerald-600">{{ visit.treatment_record.pain_level_after ?? '-' }}</div>
                                                 </div>
                                             </div>
                                        </div>
                                    </div>
                                </section>

                                <section v-if="visit.treatment_record.notes">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">หมายเหตุแพทย์</h4>
                                    <div class="bg-yellow-50 p-6 rounded-2xl border border-yellow-100 text-yellow-900 text-sm relative"> 
                                        {{ visit.treatment_record.notes }}
                                    </div>
                                </section>

                                <div class="mt-4 pt-4 border-t border-slate-100 flex justify-between items-center bg-indigo-50 p-4 rounded-xl">
                                    <span class="text-sm font-bold text-indigo-900 uppercase">ยอดรวมค่ารักษา (Total Bill)</span>
                                    <span class="text-2xl font-bold text-indigo-700">{{ visit.price ? Number(visit.price).toLocaleString() : '0' }} ฿</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial & Payments Section -->
                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-emerald-100">
                    <div class="bg-gradient-to-r from-emerald-50 to-white px-8 py-6 border-b border-emerald-100 flex justify-between items-center">
                        <h3 class="font-bold text-emerald-900 text-xl flex items-center gap-3">
                            <div class="p-2 bg-emerald-100 rounded-lg text-emerald-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            การเงิน & การชำระเงิน
                            <span class="text-xs font-normal text-emerald-400 border border-emerald-200 px-2 py-0.5 rounded-full">Financial</span>
                        </h3>
                        <div class="flex items-center gap-4">
                            <div class="text-right hidden sm:block">
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">ยอดค่ารักษา</div>
                                <div class="text-xl font-bold text-slate-700 leading-none">
                                    {{ visit.price ? Number(visit.price).toLocaleString() : '0' }} ฿
                                </div>
                            </div>
                            
                            <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>

                            <div class="text-right mr-2 hidden sm:block">
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">ยอดที่ต้องชำระ</div>
                                <div v-if="remainingAmount > 0" class="text-xl font-bold text-rose-600 leading-none">
                                    {{ remainingAmount.toLocaleString() }} ฿
                                </div>
                                <div v-else class="text-base font-bold text-emerald-600 flex items-center justify-end gap-1 leading-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                      <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    ชำระครบแล้ว
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Payment History -->
                            <div class="lg:col-span-2 space-y-6">
                                <h4 class="text-sm font-bold text-slate-400 uppercase tracking-widest">ประวัติการชำระเงิน</h4>
                                
                                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                                    <table class="w-full text-sm text-left text-slate-600">
                                        <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                                            <tr>
                                                <th class="px-4 py-3">วันที่</th>
                                                <th class="px-4 py-3">รายการ/วิธีชำระ</th>
                                                <th class="px-4 py-3 text-right">ยอดเงิน</th>
                                                <th class="px-4 py-3 text-center">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            <tr v-if="visit.payments && visit.payments.length > 0" v-for="payment in visit.payments" :key="payment.id" class="hover:bg-slate-50 transition-colors">
                                                <td class="px-4 py-3">
                                                    {{ new Date(payment.payment_date).toLocaleDateString('th-TH') }} 
                                                    <span class="text-xs text-slate-400 block">{{ new Date(payment.payment_date).toLocaleTimeString('th-TH', {hour: '2-digit', minute:'2-digit'}) }}</span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="font-medium text-slate-800 capitalize">{{ payment.payment_method }}</span>
                                                    <div v-if="payment.notes" class="text-xs text-slate-500">{{ payment.notes }}</div>
                                                </td>
                                                <td class="px-4 py-3 text-right font-bold text-emerald-600">+{{ Number(payment.amount).toLocaleString() }} ฿</td>
                                                <td class="px-4 py-3 text-center">
                                                    <button @click="deletePayment(payment.id)" class="text-slate-400 hover:text-rose-500 transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                                          <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.1499.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149-.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 0 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr v-else>
                                                <td colspan="4" class="px-4 py-8 text-center text-slate-400 italic">ยังไม่มีประวัติการชำระเงิน</td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="bg-slate-50 border-t border-slate-200">
                                            <tr>
                                                <td colspan="2" class="px-4 py-4 text-right font-bold text-slate-600">รวมที่ชำระแล้ว (Total Paid):</td>
                                                <td class="px-4 py-4 text-right font-bold text-emerald-700 text-lg">{{ totalPaid.toLocaleString() }} ฿</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                 <td colspan="2" class="px-4 py-2 text-right font-bold text-slate-500 text-xs">คงเหลือที่ต้องชำระ:</td>
                                                 <td class="px-4 py-2 text-right font-bold text-slate-500 text-xs">{{ remainingAmount.toLocaleString() }} ฿</td>
                                                 <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Add Payment Form -->
                            <div class="space-y-6">
                                <h4 class="text-sm font-bold text-slate-400 uppercase tracking-widest">เพิ่มรายการชำระ</h4>
                                <div class="bg-indigo-50 p-6 rounded-2xl border border-indigo-100">

                                    <div class="mb-4 flex justify-between items-center text-sm">
                                       <span class="font-bold text-slate-500">คงเหลือที่ต้องชำระ</span>
                                       <span :class="remainingAmount > 0 ? 'text-rose-600' : 'text-emerald-600'" class="font-bold">
                                         {{ remainingAmount.toLocaleString() }} ฿
                                       </span>
                                    </div>

                                    <form @submit.prevent="submitPayment" class="space-y-4 mt-6">
                                        <div>
                                            <label class="block text-xs font-bold text-indigo-700 mb-1">จำนวนเงินที่ชำระ / Amount</label>
                                            <input type="number" v-model="paymentForm.amount" 
                                                class="w-full rounded-lg border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500" 
                                                placeholder="0.00" 
                                                step="0.01"
                                                min="0"
                                                :max="remainingAmount"
                                                required>
                                            <div v-if="paymentForm.amount > remainingAmount" class="text-rose-500 text-xs mt-1">
                                                ยอดชำระเกินกว่าที่ต้องชำระ (สูงสุด {{ remainingAmount.toLocaleString() }} ฿)
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-indigo-700 mb-1">วิธีชำระ / Method</label>
                                            <select v-model="paymentForm.payment_method" class="w-full rounded-lg border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="cash">เงินสด (Cash)</option>
                                                <option value="transfer">เงินโอน (Transfer)</option>
                                                <option value="credit_card">บัตรเครดิต (Credit Card)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-indigo-700 mb-1">หมายเหตุ / Note</label>
                                            <input type="text" v-model="paymentForm.notes" class="w-full rounded-lg border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Optional">
                                        </div>
                                        <button type="submit" :disabled="paymentForm.processing" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold shadow-md hover:shadow-lg transition-all flex justify-center items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            บันทึกรับเงิน
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
