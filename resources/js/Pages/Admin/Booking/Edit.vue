<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import Calendar from '@/Components/Calendar.vue';

const props = defineProps({
    booking: Object,
    doctors: Array,
});

const form = useForm({
    doctor_id: props.booking.doctor_id,
    appointment_date: props.booking.appointment_date,
    start_time: props.booking.start_time ? props.booking.start_time.substring(0, 5) : '',
    duration_minutes: props.booking.duration_minutes,
    symptoms: props.booking.symptoms,
    price: props.booking.price,
});

const monthlyAvailability = ref({});
const availableSlots = ref([]);
const loadingEx = ref(false);
const errorMessage = ref('');
const availableDoctors = ref([]);

const fetchMonthlyAvailability = async (month, year) => {
    try {
        const res = await axios.get(route('api.availability'), { params: { month, year } });
        monthlyAvailability.value = res.data;
    } catch (e) {
        console.error("Error fetching monthly availability:", e);
    }
};

const fetchSlots = async () => {
    if (!form.appointment_date || !form.duration_minutes) return;

    loadingEx.value = true;
    availableSlots.value = [];
    errorMessage.value = '';

    try {
        const res = await axios.get(route('api.available-slots'), {
            params: { 
                date: form.appointment_date, 
                duration: form.duration_minutes 
            }
        });
        availableSlots.value = res.data;
        
        // Check if current current time is in available slots logic?
        // Actually, if we are editing, we might keep the current time even if "unavailable" (e.g. override)
        // But let's show availability.
    } catch (e) {
        if (e.response && e.response.status === 422) {
             errorMessage.value = e.response.data.message;
        }
    } finally {
        loadingEx.value = false;
    }
};

// Initial fetch if date is set
onMounted(() => {
    if (form.appointment_date) {
        // Trigger availability for the current month of the booking
        const d = new Date(form.appointment_date);
        fetchMonthlyAvailability(d.getMonth() + 1, d.getFullYear());
        fetchSlots();
    }
});

const onDateSelected = async (date) => {
    form.appointment_date = date;
    form.start_time = ''; // Reset time when date changes
    await fetchSlots();
};

const submit = () => {
    form.patch(route('admin.bookings.update', props.booking.id));
};

const formatTime = (time) => {
    const [hour, minute] = time.split(':');
    return `${hour}:${minute}`;
};

const selectTime = (slot) => {
    form.start_time = slot.time;
    // auto select doctor if current doctor is available in this slot
    // or keep current if valid
};

</script>

<template>
    <Head title="Edit Booking" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Booking #{{ booking.id }}</h2>
                <Link :href="route('admin.bookings.show', booking.id)" class="text-sm text-gray-600 hover:text-gray-900">&larr; Back</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Left Column -->
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Patient</label>
                                        <div class="mt-1 p-2 bg-gray-100 rounded border border-gray-200 text-gray-700">
                                            {{ booking.user ? booking.user.name : (booking.customer_name || 'Guest') }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Symptoms</label>
                                        <textarea v-model="form.symptoms" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                        <div v-if="form.errors.symptoms" class="text-red-500 text-xs mt-1">{{ form.errors.symptoms }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Price (Total Bill)</label>
                                        <input type="number" v-model="form.price" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        <div v-if="form.errors.price" class="text-red-500 text-xs mt-1">{{ form.errors.price }}</div>
                                    </div>

                                    <div>
                                         <label class="block text-sm font-medium text-gray-700">Doctor</label>
                                         <select v-model="form.doctor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                             <option v-for="doc in doctors" :key="doc.id" :value="doc.id">
                                                 {{ doc.name }} ({{ doc.specialty }})
                                             </option>
                                         </select>
                                         <div v-if="form.errors.doctor_id" class="text-red-500 text-xs mt-1">{{ form.errors.doctor_id }}</div>
                                    </div>
                                </div>

                                <!-- Right Column: Schedule -->
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Reschedule Date</label>
                                        <div class="border rounded-lg p-4 bg-gray-50">
                                            <Calendar 
                                                :availability="monthlyAvailability"
                                                @monthChanged="({ month, year }) => fetchMonthlyAvailability(month, year)"
                                                @dateSelected="onDateSelected"
                                                :selectedDate="form.appointment_date"
                                            />
                                        </div>
                                        <div v-if="form.errors.appointment_date" class="text-red-500 text-xs mt-1">{{ form.errors.appointment_date }}</div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Duration (Minutes)</label>
                                            <select v-model="form.duration_minutes" @change="fetchSlots" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option :value="30">30 Minutes</option>
                                                <option :value="60">60 Minutes</option>
                                                <option :value="90">90 Minutes</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Start Time</label>
                                            <input type="time" v-model="form.start_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                            <div v-if="form.errors.start_time" class="text-red-500 text-xs mt-1">{{ form.errors.start_time }}</div>
                                        </div>
                                    </div>

                                    <!-- Available Slots Helper -->
                                    <div class="bg-indigo-50 p-4 rounded-lg">
                                        <h4 class="text-xs font-bold text-indigo-700 uppercase mb-2">Available Slots for {{ form.appointment_date }}</h4>
                                        <div v-if="loadingEx" class="flex justify-center p-2"><span class="loading loading-spinner text-indigo-500"></span></div>
                                        <div v-else-if="availableSlots.length === 0" class="text-xs text-gray-500 italic">No available slots found (or date not selected).</div>
                                        <div v-else class="flex flex-wrap gap-2 max-h-40 overflow-y-auto">
                                            <button 
                                                v-for="slot in availableSlots" 
                                                :key="slot.time" 
                                                type="button"
                                                @click="form.start_time = slot.time"
                                                class="px-2 py-1 text-xs bg-white border border-indigo-200 rounded hover:bg-indigo-600 hover:text-white transition-colors"
                                            >
                                                {{ slot.time.substring(0, 5) }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end border-t border-gray-200 pt-6">
                                <Link :href="route('admin.bookings.show', booking.id)" class="mr-3 px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 font-medium">Cancel</Link>
                                <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-bold shadow-md">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
