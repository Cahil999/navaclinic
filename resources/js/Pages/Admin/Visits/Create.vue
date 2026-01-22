<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch, onMounted, computed } from 'vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    patient: Object,
    bookings: Array,
    doctors: Array,
});

const mode = ref('booking'); // 'booking' or 'walk_in'
const selectedBooking = ref(null);

const form = useForm({
    patient_id: props.patient.id,
    type: 'booking',
    booking_id: null,
    doctor_id: '',
    visit_date: '',
    symptoms: '',
    notes: '',
});

const visitTime = ref('');

// Calculate the correct route for the patient (Guest vs Registered)
const patientRoute = computed(() => {
    const id = String(props.patient.id);
    if (id.startsWith('guest_')) {
        const bookingId = id.replace('guest_', '');
        return route('admin.patients.guest.show', bookingId);
    }
    return route('admin.patients.show', props.patient.id);
});

// Initialize form type
onMounted(() => {
    // Set default time for walk-in to now
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    visitTime.value = `${hours}:${minutes}`;
});

watch(mode, (newMode) => {
    form.type = newMode;
    if (newMode === 'booking') {
        form.doctor_id = '';
        form.visit_date = '';
        form.symptoms = '';
    } else {
        form.booking_id = null;
        selectedBooking.value = null;
        // Reset time to now
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        visitTime.value = `${hours}:${minutes}`;
    }
});

watch(selectedBooking, (booking) => {
    if (booking) {
        form.booking_id = booking.id;
        form.symptoms = booking.symptoms || ''; // Pre-fill symptoms from booking
    }
});

const submit = () => {
    if (!confirm('Are you sure you want to start this visit?')) return;
    
    if (mode.value === 'walk_in') {
        // limit to today
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        form.visit_date = `${year}-${month}-${day}T${visitTime.value}`;
    }

    form.post(route('admin.visits.store'));
};
</script>

<template>
    <Head title="New Visit" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="patientRoute" class="text-slate-400 hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    New Visit: {{ patient.name }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    
                    <!-- Toggle Switch -->
                    <div class="border-b border-slate-100 p-2 flex bg-slate-50">
                        <button 
                            @click="mode = 'booking'"
                            :class="{'bg-white shadow-sm text-indigo-600 font-bold': mode === 'booking', 'text-slate-500 hover:text-slate-700': mode !== 'booking'}"
                            class="flex-1 py-3 px-4 rounded-xl text-sm transition-all focus:outline-none flex items-center justify-center gap-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Existing Booking (มีการจอง)
                        </button>
                        <button 
                            @click="mode = 'walk_in'"
                            :class="{'bg-white shadow-sm text-emerald-600 font-bold': mode === 'walk_in', 'text-slate-500 hover:text-slate-700': mode !== 'walk_in'}"
                            class="flex-1 py-3 px-4 rounded-xl text-sm transition-all focus:outline-none flex items-center justify-center gap-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Walk-in (ว็อคอิน)
                        </button>
                    </div>

                    <div class="p-8">
                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <!-- MODE: EXISTING BOOKING -->
                            <div v-if="mode === 'booking'" class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Select Booking (เลือกรายการจอง)</label>
                                    <div v-if="bookings.length > 0" class="space-y-3">
                                        <div 
                                            v-for="booking in bookings" 
                                            :key="booking.id"
                                            @click="selectedBooking = booking"
                                            :class="{'ring-2 ring-indigo-500 bg-indigo-50': selectedBooking?.id === booking.id, 'hover:bg-slate-50 border-slate-200': selectedBooking?.id !== booking.id}"
                                            class="cursor-pointer border rounded-xl p-4 transition-all"
                                        >
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <p class="font-bold text-slate-800">{{ booking.appointment_date }} • {{ booking.start_time }}</p>
                                                    <p class="text-sm text-slate-500">Dr. {{ booking.doctor?.name || 'Any Doctor' }}</p>
                                                </div>
                                                <div class="text-right">
                                                     <span class="px-2 py-1 bg-amber-100 text-amber-800 text-xs rounded-full font-bold uppercase">{{ booking.status }}</span>
                                                </div>
                                            </div>
                                             <p v-if="booking.symptoms" class="mt-2 text-sm text-slate-600 bg-white/50 p-2 rounded border border-slate-100 italic">
                                                "{{ booking.symptoms }}"
                                            </p>
                                        </div>
                                    </div>
                                    <div v-else class="text-center py-8 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                                        <p class="text-slate-500">No pending bookings found for this patient.</p>
                                    </div>
                                    <InputError :message="form.errors.booking_id" class="mt-2" />
                                </div>
                            </div>

                            <!-- MODE: WALK-IN -->
                            <div v-if="mode === 'walk_in'" class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Select Doctor (เลือกหมอ)</label>
                                    <select v-model="form.doctor_id" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        <option value="" disabled>Select a doctor...</option>
                                        <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                                            {{ doctor.name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.doctor_id" class="mt-2" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Time (เวลาที่เข้าตรวจ)</label>
                                    <input type="time" v-model="visitTime" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Symptoms / Chief Complaint (อาการเบื้องต้น)</label>
                                    <textarea v-model="form.symptoms" rows="3" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Describe symptoms..."></textarea>
                                    <InputError :message="form.errors.symptoms" class="mt-2" />
                                </div>
                            </div>

                            <!-- COMMON: NOTES -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Additional Notes (บันทึกเพิ่มเติม)</label>
                                <textarea v-model="form.notes" rows="2" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500"></textarea>
                            </div>

                            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-4">
                                <Link :href="patientRoute" class="text-slate-500 hover:text-slate-700 font-medium text-sm">
                                    Cancel
                                </Link>
                                <button 
                                    type="submit" 
                                    :disabled="form.processing || (mode === 'booking' && !form.booking_id)" 
                                    :class="{'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-200': mode === 'booking', 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200': mode === 'walk_in', 'opacity-50 cursor-not-allowed': form.processing || (mode === 'booking' && !form.booking_id)}"
                                    class="px-6 py-2.5 text-white rounded-lg font-bold shadow-lg transition-all"
                                >
                                    {{ mode === 'booking' ? 'Confirm Visit' : 'Confirm Walk-in' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
