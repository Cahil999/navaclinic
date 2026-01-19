<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'
import { Bar } from 'vue-chartjs'
import { computed, ref, watch } from 'vue';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

const props = defineProps({
    bookings: {
        type: Array,
        required: true,
    },
    stats: {
        type: Object,
        required: true,
    },
    chartData: {
        type: Object,
        required: false,
        default: () => ({ labels: [], data: [], title: '' })
    },
    filters: {
        type: Object,
        required: false,
        default: () => ({ year: new Date().getFullYear(), month: null })
    }
});

const selectedYear = ref(props.filters.year);
const selectedMonth = ref(props.filters.month);

const years = computed(() => {
    const currentYear = new Date().getFullYear();
    const years = [];
    // Add next 2 years
    for (let i = 2; i >= 1; i--) {
        years.push(currentYear + i);
    }
    // Add current year and past 5 years
    for (let i = 0; i < 5; i++) {
        years.push(currentYear - i);
    }
    return years;
});

const months = [
    { value: null, label: 'All Months (Yearly View)' },
    { value: 1, label: 'January' },
    { value: 2, label: 'February' },
    { value: 3, label: 'March' },
    { value: 4, label: 'April' },
    { value: 5, label: 'May' },
    { value: 6, label: 'June' },
    { value: 7, label: 'July' },
    { value: 8, label: 'August' },
    { value: 9, label: 'September' },
    { value: 10, label: 'October' },
    { value: 11, label: 'November' },
    { value: 12, label: 'December' },
];

const updateChart = () => {
    router.get(route('admin.dashboard'), {
        year: selectedYear.value,
        month: selectedMonth.value
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['chartData', 'filters']
    });
};

const chartDataConfig = computed(() => ({
  labels: props.chartData.labels,
  datasets: [
    {
      label: 'Bookings',
      backgroundColor: '#3b82f6',
      data: props.chartData.data
    }
  ]
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
      legend: {
          display: false
      }
  },
  scales: {
      y: {
          beginAtZero: true,
          ticks: {
              stepSize: 1
          }
      }
  }
}

const getStatusClass = (status) => {
    switch (status) {
        case 'confirmed':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'pending':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'cancelled':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    }
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Admin Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-slate-100 hover:shadow-lg transition-all duration-300 group">
                        <div class="text-slate-500 text-sm font-medium uppercase tracking-wider group-hover:text-blue-600 transition-colors">Total Patients</div>
                        <div class="text-3xl font-bold text-slate-900 mt-2">{{ stats.total_patients }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-slate-100 hover:shadow-lg transition-all duration-300 group">
                        <div class="text-slate-500 text-sm font-medium uppercase tracking-wider group-hover:text-blue-600 transition-colors">Total Doctors</div>
                        <div class="text-3xl font-bold text-slate-900 mt-2">{{ stats.total_doctors }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-slate-100 hover:shadow-lg transition-all duration-300 group">
                        <div class="text-slate-500 text-sm font-medium uppercase tracking-wider group-hover:text-blue-600 transition-colors">Today's Appointments</div>
                        <div class="text-3xl font-bold text-slate-900 mt-2">{{ stats.today_bookings }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-slate-100 hover:shadow-lg transition-all duration-300 group">
                        <div class="text-slate-500 text-sm font-medium uppercase tracking-wider group-hover:text-blue-600 transition-colors">Pending Requests</div>
                        <div class="text-3xl font-bold text-slate-900 mt-2">{{ stats.pending_bookings }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <!-- Chart Section -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-100">
                        <div class="p-6">
                             <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-slate-800">{{ chartData.title || 'Bookings Overview' }}</h3>
                                <div class="flex gap-2">
                                    <select v-model="selectedYear" @change="updateChart" class="text-sm border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                                    </select>
                                    <select v-model="selectedMonth" @change="updateChart" class="text-sm border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                                    </select>
                                </div>
                             </div>
                             <div class="h-64">
                                <Bar :data="chartDataConfig" :options="chartOptions" />
                             </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-100">
                    <div class="p-6 text-slate-900">
                        <h3 class="text-lg font-bold mb-4 text-slate-800">Latest Bookings</h3>
                        
                        <div class="overflow-x-auto rounded-lg border border-slate-200">
                            <table class="w-full text-sm text-left rtl:text-right text-slate-600">
                                <thead class="text-xs text-slate-700 uppercase bg-blue-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 font-bold text-blue-900">Patient</th>
                                        <th scope="col" class="px-6 py-3 font-bold text-blue-900">Doctor</th>
                                        <th scope="col" class="px-6 py-3 font-bold text-blue-900">Date & Time</th>
                                        <th scope="col" class="px-6 py-3 font-bold text-blue-900">Symptoms</th>
                                        <th scope="col" class="px-6 py-3 font-bold text-blue-900">Status</th>
                                        <th scope="col" class="px-6 py-3 font-bold text-blue-900">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100">
                                    <tr v-for="booking in bookings" :key="booking.id" class="hover:bg-blue-50/50 transition-colors">
                                        <td class="px-6 py-4 font-medium text-slate-900 whitespace-nowrap">
                                            <div v-if="booking.user">
                                                {{ booking.user.name }}
                                                <div class="text-xs text-slate-500">{{ booking.user.email }}</div>
                                            </div>
                                            <div v-else>
                                                {{ booking.customer_name || 'Guest' }}
                                                <div class="text-xs text-slate-500">{{ booking.customer_phone || '-' }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">{{ booking.doctor?.name || 'Unknown Doctor' }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-slate-900">{{ booking.appointment_date }}</div>
                                            <div class="text-xs text-slate-500">{{ booking.start_time }} ({{ booking.duration_minutes }}m)</div>
                                        </td>
                                        <td class="px-6 py-4 truncate max-w-xs">{{ booking.symptoms }}</td>
                                        <td class="px-6 py-4">
                                            <span :class="getStatusClass(booking.status)" class="px-2 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                                                {{ booking.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <Link :href="route('admin.bookings.show', booking.id)" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                View
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="bookings.length === 0">
                                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                            No bookings found.
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
