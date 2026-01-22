<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import BodyPartSelector from '@/Components/BodyPartSelector.vue';

const props = defineProps({
    booking: {
        type: Object,
        default: null,
    },
    visit: {
        type: Object,
        default: null,
    },
    previousRecord: {
        type: Object,
        default: null,
    },
    isVisit: {
        type: Boolean,
        default: false,
    }
});

import { computed } from 'vue';

// ... (props definition remains the same)

// Get the entity (booking or visit)
const entity = props.isVisit ? props.visit : props.booking;
const entityPrice = props.isVisit ? props.visit?.price : props.booking?.price;

// Helper to normalize pain_areas
const getInitialPainAreas = () => {
    const areas = props.previousRecord?.pain_areas || [];
    if (!Array.isArray(areas)) return [];
    
    // Check if it's an array of strings (legacy) or objects
    if (areas.length > 0 && typeof areas[0] === 'string') {
        return areas.map(area => ({
            area: area,
            symptom: '',
            pain_level: '',
            pain_level_after: '',
            characteristic: ''
        }));
    }
    return areas;
};

const form = useForm({
    // Vital Signs
    weight: props.previousRecord?.weight || '',
    height: props.previousRecord?.height || '',
    temperature: props.previousRecord?.temperature || '',
    pulse_rate: props.previousRecord?.pulse_rate || '',
    respiratory_rate: props.previousRecord?.respiratory_rate || '',
    blood_pressure: props.previousRecord?.blood_pressure || '',
    
    // Examination
    chief_complaint: props.previousRecord?.chief_complaint || '',
    physical_exam: props.previousRecord?.physical_exam || '',
    
    // Treatment
    pain_level_before: props.previousRecord?.pain_level_before || '',
    pain_level_after: props.previousRecord?.pain_level_after || '',
    pain_areas: getInitialPainAreas(),
    massage_weight: props.previousRecord?.massage_weight || '',
    diagnosis: props.previousRecord?.diagnosis || '',
    treatment_details: props.previousRecord?.treatment_details || '',
    notes: props.previousRecord?.notes || '',
    price: entityPrice || '',
});

// Computed property for BodyPartSelector (needs simple array of strings)
const selectedParts = computed(() => {
    return form.pain_areas.map(item => item.area);
});

// Handle updates from BodyPartSelector
const updateParts = (newParts) => {
    // 1. Remove areas that are no longer selected
    form.pain_areas = form.pain_areas.filter(item => newParts.includes(item.area));
    
    // 2. Add new areas that are selected but not in form data
    newParts.forEach(part => {
        if (!form.pain_areas.find(item => item.area === part)) {
            form.pain_areas.push({
                area: part,
                symptom: '',
                pain_level: '',
                pain_level_after: '',
                characteristic: ''
            });
        }
    });
};

const submit = () => {
    Swal.fire({
        title: 'ต้องการจะบันทึกข้อมูลใช่ไหม',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            const routeName = props.isVisit ? 'admin.visits.treatment.store' : 'admin.treatment.store';
            const routeParam = entity.id;
            
            form.post(route(routeName, routeParam), {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Data Saved Successfully',
                        text: 'Treatment details have been recorded.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4f46e5', // indigo-600
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            });
        }
    });
};
</script>

<template>
    <Head title="Add Treatment Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Add Treatment Details
                </h2>
                <Link :href="isVisit ? route('admin.visits.show', entity.id) : route('admin.bookings.show', entity.id)" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                    &larr; Back to {{ isVisit ? 'Visit' : 'Booking' }}
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-blue-100">
                    <!-- Header -->
                    <div class="bg-indigo-50 px-8 py-6 border-b border-indigo-100 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-indigo-900 text-lg">Medical Record (บันทึกเวชระเบียน)</h3>
                            <p class="text-sm text-indigo-600 mt-1">Patient: <span class="font-semibold">{{ entity.patient ? entity.patient.name : (entity.user ? entity.user.name : entity.customer_name) }}</span></p>
                        </div>
                        <div class="text-right text-xs text-slate-500">
                            {{ isVisit ? 'Visit' : 'Booking' }} ID: #{{ entity.id }}<br>
                            Date: {{ isVisit ? new Date(entity.visit_date).toLocaleDateString() : entity.appointment_date }}
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="p-8 space-y-8">
                        
                        <!-- Section 1: Vital Signs -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-slate-800 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-indigo-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                                Vital Signs (สัญญาณชีพ)
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Weight (kg)</label>
                                    <input type="number" step="0.1" v-model="form.weight" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <InputError class="mt-2" :message="form.errors.weight" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Height (cm)</label>
                                    <input type="number" step="0.1" v-model="form.height" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <InputError class="mt-2" :message="form.errors.height" />
                                </div>
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-xs font-medium text-slate-600 mb-1">BP (mmHg)</label>
                                    <input type="text" v-model="form.blood_pressure" placeholder="120/80" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <InputError class="mt-2" :message="form.errors.blood_pressure" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Temp (°C)</label>
                                    <input type="number" step="0.01" v-model="form.temperature" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <InputError class="mt-2" :message="form.errors.temperature" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Pulse (bpm)</label>
                                    <input type="number" v-model="form.pulse_rate" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <InputError class="mt-2" :message="form.errors.pulse_rate" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Resp (bpm)</label>
                                    <input type="number" v-model="form.respiratory_rate" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <InputError class="mt-2" :message="form.errors.respiratory_rate" />
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Examination -->
                        <div class="space-y-6 pt-6 border-t border-slate-100">
                             <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-900 mb-1">Chief Complaint (CC) - อาการสำคัญ</label>
                                    <textarea v-model="form.chief_complaint" rows="2" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Patient's primary symptom..."></textarea>
                                    <InputError class="mt-2" :message="form.errors.chief_complaint" />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-900 mb-1">Physical Examination (PE) - ผลการตรวจร่างกาย</label>
                                    <textarea v-model="form.physical_exam" rows="3" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Physical exam findings..."></textarea>
                                    <InputError class="mt-2" :message="form.errors.physical_exam" />
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Diagnosis & Treatment -->
                        <div class="space-y-6 pt-6 border-t border-slate-100">
                             <div>
                                <label class="block text-sm font-bold text-slate-900 mb-1">Diagnosis (การวินิจฉัยโรค)</label>
                                <textarea v-model="form.diagnosis" rows="2" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-indigo-50/50" required placeholder="Medical diagnosis..."></textarea>
                                <InputError class="mt-2" :message="form.errors.diagnosis" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-slate-900 mb-1">Treatment Procedures (รายละเอียดการรักษา)</label>
                                    <textarea v-model="form.treatment_details" rows="4" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required placeholder="Treatment steps performed..."></textarea>
                                    <InputError class="mt-2" :message="form.errors.treatment_details" />
                                </div>
                                <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 h-fit">
                                    <label class="block text-sm font-bold text-indigo-900 mb-3">Massage Weight (น้ำหนักมือ)</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center p-2 bg-white rounded-lg border border-indigo-100 cursor-pointer hover:border-indigo-300 transition-colors">
                                            <input type="radio" value="light" v-model="form.massage_weight" class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                            <span class="ml-2 text-sm text-slate-700">Light (เบา)</span>
                                        </label>
                                        <label class="flex items-center p-2 bg-white rounded-lg border border-indigo-100 cursor-pointer hover:border-indigo-300 transition-colors">
                                            <input type="radio" value="medium" v-model="form.massage_weight" class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                            <span class="ml-2 text-sm text-slate-700">Medium (ปานกลาง)</span>
                                        </label>
                                        <label class="flex items-center p-2 bg-white rounded-lg border border-indigo-100 cursor-pointer hover:border-indigo-300 transition-colors">
                                            <input type="radio" value="heavy" v-model="form.massage_weight" class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                            <span class="ml-2 text-sm text-slate-700">Heavy (หนัก)</span>
                                        </label>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.massage_weight" />
                                </div>
                                <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 h-fit">
                                    <label class="block text-sm font-bold text-indigo-900 mb-3">Pain Level (ระดับความเจ็บปวด 0-10)</label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Before (ก่อน)</label>
                                            <input type="number" min="0" max="10" v-model="form.pain_level_before" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm placeholder:text-gray-300" placeholder="0-10">
                                            <InputError class="mt-2" :message="form.errors.pain_level_before" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">After (หลัง)</label>
                                            <input type="number" min="0" max="10" v-model="form.pain_level_after" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm placeholder:text-gray-300" placeholder="0-10">
                                            <InputError class="mt-2" :message="form.errors.pain_level_after" />
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="pt-6 border-t border-slate-100">
                                <label class="block text-sm font-bold text-slate-900 mb-4">Pain Areas (บริเวณที่ปวด)</label>
                                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                                    <BodyPartSelector 
                                        :model-value="selectedParts"
                                        @update:model-value="updateParts" 
                                    />
                                </div>
                                
                                <!-- Detailed Pain Symptoms List -->
                                <div class="mt-6 space-y-4">
                                    <h5 class="text-sm font-bold text-indigo-900 border-b border-indigo-100 pb-2">Symptom Details (รายละเอียดอาการ)</h5>
                                    
                                    <div v-if="form.pain_areas.length === 0" class="text-center py-8 bg-slate-50 rounded-xl border border-dashed border-slate-300 text-slate-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 mx-auto mb-2 text-slate-400">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672ZM12 2.25V4.5m5.834.166-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243-1.59-1.59" />
                                        </svg>
                                        <p class="text-sm">Click on the body map above to add symptom details.</p>
                                        <p class="text-xs text-slate-400 mt-1">(คลิกที่รูปหุ่นด้านบนเพื่อระบุรายละเอียดอาการ)</p>
                                    </div>
                                    
                                    <div v-else class="space-y-4">
                                        <div v-for="(item, index) in form.pain_areas" :key="index" class="bg-slate-50 p-4 rounded-xl border border-slate-100 animate-fadeIn">
                                        <div class="flex justify-between items-center mb-3">
                                            <span class="font-bold text-slate-800 flex items-center gap-2">
                                                <span class="w-6 h-6 bg-indigo-100 rounded-full flex items-center justify-center text-xs text-indigo-600 font-bold">{{ index + 1 }}</span>
                                                {{ item.area }}
                                            </span>
                                            <button type="button" @click="updateParts(selectedParts.filter(p => p !== item.area))" class="text-xs text-rose-500 hover:text-rose-700 hover:underline">
                                                Remove
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-medium text-slate-600 mb-1">Symptom (อาการ)</label>
                                                <input type="text" v-model="item.symptom" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Ex. Achy, Sharp pain...">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-600 mb-1">Pain (Before)</label>
                                                <input type="number" min="0" max="10" v-model="item.pain_level" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="0-10">
                                            </div>
                                             <div>
                                                <label class="block text-xs font-medium text-slate-600 mb-1">Pain (After)</label>
                                                <input type="number" min="0" max="10" v-model="item.pain_level_after" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm bg-emerald-50 border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500" placeholder="0-10">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                        <!-- Section 5: Other -->
                        <div class="space-y-6 pt-6 border-t border-slate-100">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Price (Total Bill - ยอดรวมค่ารักษา)</label>
                                <div class="relative">
                                    <input type="number" step="0.01" v-model="form.price" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-4 pr-12 font-bold text-slate-700" placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-slate-500 sm:text-sm">THB</span>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="form.errors.price" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Doctor's Notes (บันทึกเพิ่มเติม)</label>
                                <textarea v-model="form.notes" rows="2" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                <InputError class="mt-2" :message="form.errors.notes" />
                            </div>
                        </div>

                        <div class="pt-8 border-t border-slate-100 flex justify-end gap-3 sticky bottom-0 bg-white p-4 -mx-4 -mb-4 shadow-lg sm:static sm:shadow-none sm:p-0 sm:m-0">
                            <Link :href="isVisit ? route('admin.visits.show', entity.id) : route('admin.bookings.show', entity.id)" class="px-6 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-xl text-sm font-medium hover:bg-slate-50 transition-colors">
                                Cancel
                            </Link>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="px-8 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all disabled:opacity-50"
                            >
                                Save Treatment Details
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
