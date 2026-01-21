<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import BodyPartSelector from '@/Components/BodyPartSelector.vue';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import { ref, computed } from 'vue';
import { 
    UserIcon, 
    PhoneIcon, 
    IdentificationIcon, 
    CalendarIcon, 
    BriefcaseIcon, 
    HomeIcon, 
    HeartIcon, 
    ExclamationTriangleIcon,
    ClockIcon,
    ClipboardDocumentListIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    patient: {
        type: Object,
        required: true,
    },
    bookings: {
        type: Array,
        required: true,
    },
    stats: {
        type: Object,
        default: () => ({}),
    },
    medicalSummary: {
        type: Object,
        default: () => null,
    },
});

const getStatusClass = (status) => {
    switch (status) {
        case 'confirmed':
            return 'bg-emerald-100 text-emerald-800 border border-emerald-200';
        case 'pending':
            return 'bg-amber-100 text-amber-800 border border-amber-200';
        case 'cancelled':
            return 'bg-red-100 text-red-800 border border-red-200';
         case 'completed':
            return 'bg-blue-100 text-blue-800 border border-blue-200';
        default:
            return 'bg-slate-100 text-slate-800 border border-slate-200';
    }
};

const showEditModal = ref(false);

const form = useForm({
    name: props.patient.name,
    phone_number: props.patient.phone_number,
    id_card_number: props.patient.id_card_number,
    date_of_birth: props.patient.date_of_birth,
    gender: props.patient.gender,
    race: props.patient.race,
    nationality: props.patient.nationality,
    religion: props.patient.religion,
    occupation: props.patient.occupation,
    address: props.patient.address,
    emergency_contact_name: props.patient.emergency_contact_name,
    emergency_contact_phone: props.patient.emergency_contact_phone,
    
    // Medical History
    underlying_disease: props.patient.underlying_disease,
    surgery_history: props.patient.surgery_history,
    drug_allergy: props.patient.drug_allergy,
    accident_history: props.patient.accident_history,
});

const submit = () => {
    form.put(route('admin.patients.update', props.patient.id), {
        onSuccess: () => {
            showEditModal.value = false;
        }
    });
};

const patientAge = computed(() => {
    if (!props.patient.date_of_birth) return '-';
    return new Date().getFullYear() - new Date(props.patient.date_of_birth).getFullYear();
});
</script>

<template>
    <Head :title="patient.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <Link :href="route('admin.patients.index')" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </Link>
                        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                            {{ patient.name }}
                        </h2>
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-full border border-indigo-100">
                            {{ patient.patient_id }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-slate-500 ml-9">
                        {{ patient.gender || '-' }} • {{ patientAge }} Years Old
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="showEditModal = true" class="inline-flex items-center justify-center px-4 py-2 bg-white text-slate-700 border border-slate-200 rounded-lg text-sm font-medium hover:bg-slate-50 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Profile
                    </button>
                    <Link :href="route('admin.bookings.create', { user_id: patient.id })" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 shadow-md shadow-indigo-200 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Appointment
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    <!-- Left Sidebar: Patient Info -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Essential Info Card -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                                <h3 class="font-bold text-slate-800 flex items-center">
                                    <UserIcon class="w-5 h-5 mr-2 text-indigo-500" />
                                    About Patient
                                </h3>
                            </div>
                            <div class="p-6 space-y-5">
                                <div class="flex items-start">
                                    <PhoneIcon class="w-5 h-5 text-slate-400 mt-0.5 mr-3 flex-shrink-0" />
                                    <div>
                                        <p class="text-xs text-slate-500 font-medium uppercase">Phone Number</p>
                                        <p class="text-slate-900 font-semibold">{{ patient.phone_number || '-' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <IdentificationIcon class="w-5 h-5 text-slate-400 mt-0.5 mr-3 flex-shrink-0" />
                                    <div>
                                        <p class="text-xs text-slate-500 font-medium uppercase">ID Card Number</p>
                                        <p class="text-slate-900 font-medium">{{ patient.id_card_number || '-' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <CalendarIcon class="w-5 h-5 text-slate-400 mt-0.5 mr-3 flex-shrink-0" />
                                    <div>
                                        <p class="text-xs text-slate-500 font-medium uppercase">Date of Birth</p>
                                        <p class="text-slate-900 font-medium">
                                            {{ patient.date_of_birth ? new Date(patient.date_of_birth).toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric'}) : '-' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <BriefcaseIcon class="w-5 h-5 text-slate-400 mt-0.5 mr-3 flex-shrink-0" />
                                    <div>
                                        <p class="text-xs text-slate-500 font-medium uppercase">Occupation</p>
                                        <p class="text-slate-900 font-medium">{{ patient.occupation || '-' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <HomeIcon class="w-5 h-5 text-slate-400 mt-0.5 mr-3 flex-shrink-0" />
                                    <div>
                                        <p class="text-xs text-slate-500 font-medium uppercase">Address</p>
                                        <p class="text-slate-900 font-medium leading-relaxed text-sm">{{ patient.address || '-' }}</p>
                                    </div>
                                </div>
                                <div class="pt-4 border-t border-slate-100 flex items-start">
                                    <HeartIcon class="w-5 h-5 text-rose-400 mt-0.5 mr-3 flex-shrink-0" />
                                    <div>
                                        <p class="text-xs text-slate-500 font-medium uppercase">Emergency Contact</p>
                                        <p class="text-slate-900 font-medium">{{ patient.emergency_contact_name || '-' }}</p>
                                        <a v-if="patient.emergency_contact_phone" :href="'tel:' + patient.emergency_contact_phone" class="text-indigo-600 text-sm hover:underline block mt-1">
                                            {{ patient.emergency_contact_phone }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Alerts (Compact) -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                             <div class="bg-gradient-to-r from-red-50 to-white px-6 py-4 border-b border-red-50 flex items-center justify-between">
                                <h3 class="font-bold text-red-900 flex items-center text-sm uppercase tracking-wider">
                                    <ExclamationTriangleIcon class="w-5 h-5 mr-2 text-red-500" />
                                    Important Medical Info
                                </h3>
                            </div>
                            <div class="divide-y divide-slate-50">
                                <div class="p-5 hover:bg-slate-50 transition-colors">
                                    <p class="text-xs font-bold text-red-500 uppercase mb-1">Drug Allergies (แพ้ยา)</p>
                                    <p class="text-slate-900 font-bold break-words">{{ patient.drug_allergy || 'No known allergies' }}</p>
                                </div>
                                <div class="p-5 hover:bg-slate-50 transition-colors">
                                    <p class="text-xs font-bold text-indigo-500 uppercase mb-1">Underlying Diseases (โรคประจำตัว)</p>
                                    <p class="text-slate-900 font-medium break-words">{{ patient.underlying_disease || 'None' }}</p>
                                </div>
                                <div class="p-5 hover:bg-slate-50 transition-colors">
                                    <p class="text-xs font-bold text-slate-400 uppercase mb-1">Surgery History</p>
                                    <p class="text-slate-700 text-sm">{{ patient.surgery_history || '-' }}</p>
                                </div>
                                 <div class="p-5 hover:bg-slate-50 transition-colors">
                                    <p class="text-xs font-bold text-slate-400 uppercase mb-1">Accident History</p>
                                    <p class="text-slate-700 text-sm">{{ patient.accident_history || '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Main Content -->
                    <div class="lg:col-span-8 space-y-6">
                        
                        <!-- Quick Stats Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center">
                                <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600 mr-4">
                                     <ClipboardDocumentListIcon class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Visits</p>
                                    <p class="text-2xl font-bold text-slate-900">{{ stats?.total_visits || 0 }}</p>
                                </div>
                            </div>
                            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center">
                                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600 mr-4">
                                     <CalendarIcon class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Next Appt.</p>
                                    <div v-if="stats?.next_appointment">
                                        <p class="text-lg font-bold text-slate-900">{{ stats.next_appointment.appointment_date }}</p>
                                        <p class="text-xs text-slate-500">{{ stats.next_appointment.start_time }}</p>
                                    </div>
                                    <p v-else class="text-lg font-bold text-slate-300">None</p>
                                </div>
                            </div>
                             <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center">
                                <div class="p-3 bg-rose-50 rounded-xl text-rose-600 mr-4">
                                     <ClockIcon class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Last Visit</p>
                                    <p class="text-lg font-bold text-slate-900">{{ stats?.last_visit || 'Never' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Overview -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                             <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                                <h3 class="font-bold text-slate-800 text-lg">Latest Medical Overview</h3>
                                <span v-if="medicalSummary" class="text-xs font-medium bg-slate-100 text-slate-500 px-2 py-1 rounded">
                                    Updated: {{ new Date(medicalSummary.last_updated).toLocaleDateString() }}
                                </span>
                            </div>
                            
                            <div v-if="medicalSummary" class="p-6">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                                    <div class="p-4 bg-slate-50 rounded-xl text-center">
                                        <p class="text-xs text-slate-400 uppercase font-bold mb-1">Weight</p>
                                        <p class="text-2xl font-bold text-slate-800">{{ medicalSummary.weight || '-' }} <span class="text-sm font-normal text-slate-500">kg</span></p>
                                    </div>
                                    <div class="p-4 bg-slate-50 rounded-xl text-center">
                                        <p class="text-xs text-slate-400 uppercase font-bold mb-1">Height</p>
                                        <p class="text-2xl font-bold text-slate-800">{{ medicalSummary.height || '-' }} <span class="text-sm font-normal text-slate-500">cm</span></p>
                                    </div>
                                    <div class="p-4 bg-slate-50 rounded-xl text-center">
                                        <p class="text-xs text-slate-400 uppercase font-bold mb-1">BMI</p>
                                        <p class="text-2xl font-bold text-slate-800">
                                             {{ (medicalSummary.weight && medicalSummary.height) ? (medicalSummary.weight / ((medicalSummary.height / 100) * (medicalSummary.height / 100))).toFixed(1) : '-' }}
                                        </p>
                                    </div>
                                    <div class="p-4 bg-slate-50 rounded-xl text-center">
                                        <p class="text-xs text-slate-400 uppercase font-bold mb-1">BP</p>
                                        <p class="text-2xl font-bold text-slate-800">{{ medicalSummary.blood_pressure || '-' }}</p>
                                    </div>
                                </div>

                                <div v-if="medicalSummary.pain_areas && medicalSummary.pain_areas.length > 0">
                                    <h4 class="text-sm font-bold text-slate-700 mb-4 flex items-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span>
                                        Treated Areas (ส่วนที่รักษาล่าสุด)
                                    </h4>
                                    <div class="bg-slate-50 rounded-xl p-6 border border-slate-100 flex justify-center">
                                         <BodyPartSelector 
                                            :modelValue="medicalSummary.pain_areas" 
                                            :readonly="true" 
                                        />
                                    </div>
                                </div>
                            </div>
                            <div v-else class="p-12 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-300 mb-4">
                                    <ClipboardDocumentListIcon class="w-8 h-8" />
                                </div>
                                <p class="text-slate-500 font-medium">No medical records available yet.</p>
                                <p class="text-slate-400 text-sm">Vital signs and treatment areas will appear here after the first visit.</p>
                            </div>
                        </div>

                         <!-- Booking History -->
                         <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                             <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                                <h3 class="font-bold text-slate-800 text-lg">Booking History</h3>
                            </div>
                             <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-slate-600">
                                    <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 font-semibold">Date / Time</th>
                                            <th scope="col" class="px-6 py-3 font-semibold">Doctor</th>
                                            <th scope="col" class="px-6 py-3 font-semibold">Status</th>
                                            <th scope="col" class="px-6 py-3 font-semibold text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        <tr v-for="booking in bookings" :key="booking.id" class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-slate-900">{{ booking.appointment_date }}</div>
                                                <div class="text-xs text-slate-500">{{ booking.start_time }}</div>
                                            </td>
                                            <td class="px-6 py-4 font-medium text-slate-700">
                                                {{ booking.doctor?.name }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span :class="getStatusClass(booking.status)" class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide">
                                                    {{ booking.status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <Link :href="route('admin.bookings.show', booking.id)" class="text-indigo-600 hover:text-indigo-800 font-bold text-xs uppercase tracking-wide border border-indigo-100 px-3 py-1.5 rounded hover:bg-indigo-50 transition-all">
                                                    View Details
                                                </Link>
                                            </td>
                                        </tr>
                                        <tr v-if="bookings.length === 0">
                                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                                No bookings found for this patient.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                         </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Edit Modal (Reusing existing form logic but cleaning up styles slightly) -->
        <Modal :show="showEditModal" @close="showEditModal = false">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-slate-900">
                        Edit Patient Information
                    </h2>
                     <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- General Information -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-4 border-b border-indigo-100 pb-2">General Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Name (ชื่อ-นามสกุล)</label>
                                <input type="text" v-model="form.name" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Phone (เบอร์โทร)</label>
                                <input type="text" v-model="form.phone_number" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <InputError :message="form.errors.phone_number" class="mt-2" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">ID Card Number</label>
                                <input type="text" v-model="form.id_card_number" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Date of Birth</label>
                                <input type="date" v-model="form.date_of_birth" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Gender</label>
                                <select v-model="form.gender" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="male">Male (ชาย)</option>
                                    <option value="female">Female (หญิง)</option>
                                    <option value="other">Other (อื่นๆ)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Occupation</label>
                                <input type="text" v-model="form.occupation" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Race</label>
                                <input type="text" v-model="form.race" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nationality</label>
                                <input type="text" v-model="form.nationality" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Religion</label>
                                <input type="text" v-model="form.religion" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                                <textarea v-model="form.address" rows="2" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Emergency Contact</label>
                                <input type="text" v-model="form.emergency_contact_name" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Emergency Phone</label>
                                <input type="text" v-model="form.emergency_contact_phone" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Medical History -->
                    <div class="bg-red-50 p-4 rounded-xl border border-red-100">
                        <h3 class="text-xs font-bold text-red-600 uppercase tracking-wider mb-4 border-b border-red-200 pb-2">Medical Warning Info</h3>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Underlying Disease (โรคประจำตัว)</label>
                                <input type="text" v-model="form.underlying_disease" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g. Hypertension, Diabetes">
                            </div>
                             <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Drug Allergies (แพ้ยา/อาหาร)</label>
                                <input type="text" v-model="form.drug_allergy" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g. Penicillin, Seafood">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Surgery History</label>
                                <input type="text" v-model="form.surgery_history" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Accident History</label>
                                <input type="text" v-model="form.accident_history" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="showEditModal = false" class="px-5 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 disabled:opacity-50 transition-colors shadow-lg shadow-indigo-200">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
