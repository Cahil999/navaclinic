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
import { computed } from 'vue';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

const props = defineProps({
    bookings: {
        type: Array,
        default: () => [],
    },
    isDoctor: {
        type: Boolean,
        default: false,
    },
    todayStats: {
        type: Object,
        default: () => ({ total: 0, pending: 0, completed: 0, revenue: 0 }),
    },
    nextBooking: {
        type: Object,
        default: null,
    },
    workloadChart: {
        type: Object,
        default: () => ({ labels: [], data: [] }),
    }
});

const workloadChartConfig = computed(() => ({
  labels: props.workloadChart.labels,
  datasets: [
    {
      label: 'Patients',
      backgroundColor: '#818cf8',
      hoverBackgroundColor: '#6366f1',
      borderRadius: 8,
      borderSkipped: false,
      data: props.workloadChart.data
    }
  ]
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
        backgroundColor: 'rgba(255, 255, 255, 0.9)',
        titleColor: '#1e293b',
        bodyColor: '#475569',
        borderColor: '#e2e8f0',
        borderWidth: 1,
        padding: 12,
        displayColors: false,
        titleFont: { size: 13, weight: 'bold' },
        bodyFont: { size: 13 },
        callbacks: {
            label: (context) => `${context.formattedValue} Patients`
        }
    }
  },
  scales: {
    y: {
        beginAtZero: true,
        grid: { display: true, color: '#f1f5f9', drawBorder: false },
        ticks: { stepSize: 1, color: '#94a3b8', font: { size: 11 } },
        border: { display: false }
    },
    x: {
        grid: { display: false, drawBorder: false },
        ticks: { color: '#64748b', font: { size: 11, weight: 500 } },
        border: { display: false }
    }
  }
}

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric', weekday: 'long' };
    return new Date(dateString).toLocaleDateString('th-TH', options);
};

const formatTimePosix = (timeString) => {
    if (!timeString) return '';
    return timeString.substring(0, 5);
};

const getStatusColor = (status) => {
    switch (status) {
        case 'confirmed': return 'bg-teal-100 text-teal-700 border-teal-200';
        case 'pending': return 'bg-amber-100 text-amber-700 border-amber-200';
        case 'cancelled': return 'bg-rose-100 text-rose-700 border-rose-200';
        case 'completed': return 'bg-slate-100 text-slate-600 border-slate-200';
        default: return 'bg-gray-100 text-gray-600 border-gray-200';
    }
};

const getStatusDot = (status) => {
    switch (status) {
        case 'confirmed': return 'bg-teal-500';
        case 'pending': return 'bg-amber-500';
        case 'cancelled': return 'bg-rose-500';
        case 'completed': return 'bg-slate-500';
        default: return 'bg-gray-400';
    }
};

const getStatusText = (status) => {
    switch (status) {
        case 'confirmed': return 'ยืนยันแล้ว';
        case 'pending': return 'รอยืนยัน';
        case 'cancelled': return 'ยกเลิก';
        case 'completed': return 'เสร็จสิ้น';
        default: return status;
    }
};

const navigateToBooking = (id) => {
    router.get(route('booking.show', id));
};
</script>

<template>
    <Head title="ประวัติการจองคิว" />

    <AuthenticatedLayout>
        <!-- Background Decorations -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
            <div class="absolute top-[-10%] right-[-5%] w-96 h-96 bg-blue-400/20 rounded-full blur-3xl animate-blob"></div>
            <div class="absolute bottom-[-10%] left-[-5%] w-96 h-96 bg-teal-400/20 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
            <div class="absolute top-[20%] left-[10%] w-72 h-72 bg-purple-300/20 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
        </div>

        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-fade-in-down">
                <div>
                    <h2 class="text-3xl font-bold leading-tight text-gray-800 flex items-center gap-3">
                        <div class="p-2 bg-gradient-to-br from-blue-500 to-teal-400 rounded-xl shadow-lg shadow-blue-500/20 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </div>
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-gray-800 to-gray-600">{{ isDoctor ? 'แดชบอร์ดแพทย์' : 'ประวัติการจองคิว' }}</span>
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 ml-1">
                        {{ isDoctor ? 'สรุปภาพรวมและตารางนัดหมายของคุณ' : 'จัดการและดูสถานะการนัดหมายของคุณทั้งหมดที่นี่' }}
                    </p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <!-- Doctor Overview Section -->
                <div v-if="isDoctor" class="mb-10 space-y-8 animate-fade-in-up">
                    
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Total Today -->
                        <div class="bg-white/60 backdrop-blur-xl p-6 rounded-[2rem] shadow-sm border border-white/50 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
                             <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition-transform"></div>
                             <div class="relative">
                                <div class="text-sm font-semibold text-blue-600 uppercase tracking-wider mb-1">นัดหมายวันนี้</div>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-4xl font-bold text-slate-800">{{ todayStats.total }}</span>
                                    <span class="text-sm text-slate-500">เคส</span>
                                </div>
                             </div>
                             <div class="mt-4 flex items-center gap-2 text-sm text-slate-600 bg-white/50 p-2 rounded-xl w-fit">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-blue-500">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                <span>วันที่ {{ new Date().toLocaleDateString('th-TH', { day: 'numeric', month: 'short' }) }}</span>
                             </div>
                        </div>

                        <!-- Pending -->
                         <div class="bg-white/60 backdrop-blur-xl p-6 rounded-[2rem] shadow-sm border border-white/50 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
                             <div class="absolute top-0 right-0 w-32 h-32 bg-amber-100 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition-transform"></div>
                             <div class="relative">
                                <div class="text-sm font-semibold text-amber-600 uppercase tracking-wider mb-1">รอตรวจ / คงเหลือ</div>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-4xl font-bold text-slate-800">{{ todayStats.pending }}</span>
                                    <span class="text-sm text-slate-500">ยูนิต</span>
                                </div>
                             </div>
                             <div class="mt-4 w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-amber-400 h-full rounded-full" :style="`width: ${todayStats.total > 0 ? (todayStats.pending / todayStats.total * 100) : 0}%`"></div>
                             </div>
                        </div>

                        <!-- Completed -->
                         <div class="bg-white/60 backdrop-blur-xl p-6 rounded-[2rem] shadow-sm border border-white/50 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
                             <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-100 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition-transform"></div>
                             <div class="relative">
                                <div class="text-sm font-semibold text-emerald-600 uppercase tracking-wider mb-1">ตรวจแล้ว</div>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-4xl font-bold text-slate-800">{{ todayStats.completed }}</span>
                                    <span class="text-sm text-slate-500">ยูนิต</span>
                                </div>
                             </div>
                             <div class="mt-4 flex items-center gap-2 text-sm text-emerald-700 bg-emerald-50 p-2 rounded-xl w-fit">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span>Completed</span>
                             </div>
                        </div>

                        <!-- Revenue -->
                         <div class="bg-white/60 backdrop-blur-xl p-6 rounded-[2rem] shadow-sm border border-white/50 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
                             <div class="absolute top-0 right-0 w-32 h-32 bg-rose-100 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition-transform"></div>
                             <div class="relative">
                                <div class="text-sm font-semibold text-rose-600 uppercase tracking-wider mb-1">รายรับวันนี้</div>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-3xl font-bold text-slate-800">฿{{ todayStats.revenue ? todayStats.revenue.toLocaleString() : '0' }}</span>
                                </div>
                             </div>
                             <div class="mt-4 flex items-center gap-2 text-sm text-rose-700 bg-rose-50 p-2 rounded-xl w-fit">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                </svg>
                                <span>Estimated</span>
                             </div>
                        </div>
                    </div>

                    <!-- Next Booking Hero Card -->
                    <div v-if="nextBooking" class="bg-gradient-to-br from-indigo-600 to-violet-600 rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-2xl shadow-indigo-500/30">
                        <!-- Decorative Circles -->
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-2xl"></div>
                        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-16 -mb-16 blur-2xl"></div>

                        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
                            <div class="flex-1">
                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-indigo-100 text-sm font-medium mb-4 border border-white/10">
                                    <span class="relative flex h-2 w-2">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                                    </span>
                                    คิวถัดไป (Next Up)
                                </div>
                                <h3 class="text-3xl font-bold mb-2">{{ nextBooking.user ? nextBooking.user.name : (nextBooking.customer_name || 'Walk-in Guest') }}</h3>
                                <p class="text-indigo-100 text-lg mb-6 line-clamp-2 max-w-xl">{{ nextBooking.symptoms || 'ไม่มีรายละเอียดอาการ' }}</p>
                                
                                <div class="flex flex-wrap gap-4">
                                     <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 px-5 border border-white/10 flex items-center gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-indigo-200">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        <div>
                                            <div class="text-xs text-indigo-200">เวลานัดหมาย</div>
                                            <div class="text-lg font-bold">{{ formatTimePosix(nextBooking.start_time) }} น.</div>
                                        </div>
                                     </div>
                                     <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 px-5 border border-white/10 flex items-center gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-indigo-200">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                        </svg>
                                        <div>
                                            <div class="text-xs text-indigo-200">ข้อมูลติดต่อ</div>
                                            <div class="text-sm font-semibold">{{ nextBooking.user ? (nextBooking.user.phone_number || '-') : (nextBooking.customer_phone || '-') }}</div>
                                        </div>
                                     </div>
                                </div>
                            </div>

                            <div class="w-full md:w-auto">
                                <button @click="navigateToBooking(nextBooking.id)" class="w-full md:w-auto group relative flex items-center justify-center gap-2 bg-white text-indigo-600 font-bold py-4 px-8 rounded-2xl shadow-xl hover:bg-indigo-50 transition-all duration-300 transform hover:scale-105 active:scale-95">
                                    <span>เริ่มการรักษา</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 group-hover:translate-x-1 transition-transform">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Workload Chart Section -->
                    <div class="bg-white/60 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-sm border border-white/50 relative overflow-hidden">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">สถิติคนไข้ย้อนหลัง 7 วัน</h3>
                                <p class="text-slate-500 text-sm">Weekly Patient Overview</p>
                            </div>
                            <div class="bg-indigo-50 text-indigo-600 p-2 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                </svg>
                            </div>
                        </div>
                        <div class="h-64 w-full">
                            <Bar :data="workloadChartConfig" :options="chartOptions" />
                        </div>
                    </div>
                </div>

                <!-- Empty State for Patient -->
                <div v-if="bookings.length === 0 && !isDoctor" class="animate-fade-in-up flex flex-col items-center justify-center py-20 bg-white/80 backdrop-blur-xl rounded-[2rem] shadow-sm border border-white/50 text-center relative overflow-hidden group">
                     <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-teal-50/50 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                    <div class="bg-gradient-to-br from-blue-100 to-teal-50 p-6 rounded-full mb-6 relative animate-float">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-16 text-blue-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2 relative">ยังไม่มีประวัติการจองคิว</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-8 relative">
                        เริ่มต้นดูแลสุขภาพของคุณด้วยการนัดหมายแพทย์ผู้เชี่ยวชาญกับเราวันนี้ สะดวก รวดเร็ว และใส่ใจ
                    </p>
                    <Link :href="route('booking.create')" class="relative btn btn-primary btn-lg text-white rounded-full px-8 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-1 transition-all">
                        เริ่มจองคิวแรกของคุณ
                    </Link>
                </div>

                <!-- Empty State for Doctor (Only if no history AT ALL) -->
                <div v-if="bookings.length === 0 && isDoctor && !todayStats.total" class="animate-fade-in-up flex flex-col items-center justify-center py-20 bg-white/80 backdrop-blur-xl rounded-[2rem] shadow-sm border border-white/50 text-center relative overflow-hidden group">
                     <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-purple-50/50 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                    <div class="bg-gradient-to-br from-blue-100 to-purple-50 p-6 rounded-full mb-6 relative animate-float">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-16 text-blue-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.25 2.25 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2 relative">ยังไม่มีคิวที่ได้รับมอบหมาย</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-8 relative">
                        เมื่อมีการจองคิวใหม่เข้ามาและได้รับการมอบหมายให้คุณ ระบบจะแสดงข้อมูลที่นี่
                    </p>
                </div>

                <!-- Booking List Header -->
                 <div v-if="bookings.length > 0" class="flex items-center gap-3 mb-6 animate-fade-in-up">
                    <div :class="['h-8 w-1 rounded-full', isDoctor ? 'bg-indigo-500' : 'bg-teal-500']"></div>
                    <h3 class="text-xl font-bold text-gray-800">{{ isDoctor ? 'ตารางนัดหมายทั้งหมด' : 'ประวัติการจองของฉัน' }}</h3>
                 </div>

                <!-- Booking List -->
                <div v-if="bookings.length > 0" class="grid gap-6">
                    <!-- Desktop Table View -->
                    <div class="hidden md:block bg-white/80 backdrop-blur-xl rounded-[2rem] shadow-sm border border-white/20 overflow-hidden animate-fade-in-up">
                        <table class="table w-full">
                            <thead class="bg-slate-50/50 border-b border-slate-100">
                                <tr>
                                    <th class="py-5 px-6 text-sm font-semibold text-slate-600">{{ isDoctor ? 'ลูกค้า' : 'แพทย์ผู้รักษา' }}</th>
                                    <th class="py-5 px-6 text-sm font-semibold text-slate-600">วันและเวลานัดหมาย</th>
                                    <th class="py-5 px-6 text-sm font-semibold text-slate-600">อาการ/รายละเอียด</th>
                                    <th class="py-5 px-6 text-sm font-semibold text-slate-600 text-center">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(booking, index) in bookings" :key="booking.id" 
                                    class="hover:bg-white transition-all duration-300 group border-b border-slate-50 last:border-none hover:shadow-md hover:scale-[1.01] relative z-0 hover:z-10 cursor-pointer"
                                    :style="{ animationDelay: `${index * 100}ms` }"
                                    @click="navigateToBooking(booking.id)">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="avatar placeholder">
                                                <div class="bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-600 rounded-2xl w-12 h-12 ring-4 ring-white shadow-sm group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                                    <span class="text-lg font-bold">{{ isDoctor ? (booking.user ? booking.user.name.charAt(0) : (booking.customer_name ? booking.customer_name.charAt(0) : '?')) : booking.doctor.name.charAt(0) }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800 text-base group-hover:text-blue-600 transition-colors">{{ isDoctor ? (booking.user ? booking.user.name : booking.customer_name) : booking.doctor.name }}</div>
                                                <div class="text-xs text-slate-500">{{ isDoctor ? 'ลูกค้า' : (booking.doctor.expertise || 'แพทย์แผนไทย') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex items-center gap-2 font-medium text-slate-700">
                                                <div class="p-1 px-2 rounded-md bg-blue-50 text-blue-600 text-xs font-bold border border-blue-100">
                                                    {{ formatDate(booking.appointment_date).split(' ')[0] }}
                                                </div>
                                                <span>{{ formatDate(booking.appointment_date).substring(formatDate(booking.appointment_date).indexOf(' ') + 1) }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-sm text-slate-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-slate-400">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                                {{ formatTimePosix(booking.start_time) }} น. • <span class="text-slate-400">{{ booking.duration_minutes }} นาที</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="max-w-xs">
                                            <p class="text-slate-700 text-sm line-clamp-2 leading-relaxed" :title="booking.symptoms">
                                                {{ booking.symptoms }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border shadow-sm" :class="getStatusColor(booking.status)">
                                            <span class="w-2 h-2 rounded-full" :class="getStatusDot(booking.status)"></span>
                                            {{ getStatusText(booking.status) }}
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="md:hidden space-y-4 animate-fade-in-up">
                        <div v-for="(booking, index) in bookings" :key="booking.id" 
                             class="bg-white/80 backdrop-blur-xl p-5 rounded-3xl shadow-sm border border-slate-100 active:scale-[0.98] transition-all duration-300 hover:shadow-lg hover:-translate-y-1 relative overflow-hidden cursor-pointer"
                             :style="{ animationDelay: `${index * 100}ms` }"
                             @click="navigateToBooking(booking.id)">
                            
                            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-500/5 to-transparent rounded-bl-full -mr-4 -mt-4"></div>

                            <div class="flex justify-between items-start mb-4 relative">
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-600 rounded-2xl w-12 h-12 ring-2 ring-white shadow-sm">
                                            <span class="text-lg font-bold">{{ isDoctor ? (booking.user ? booking.user.name.charAt(0) : (booking.customer_name ? booking.customer_name.charAt(0) : '?')) : booking.doctor.name.charAt(0) }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-base">{{ isDoctor ? (booking.user ? booking.user.name : booking.customer_name) : booking.doctor.name }}</div>
                                        <div class="text-xs text-slate-500">{{ isDoctor ? 'ลูกค้า' : (booking.doctor.expertise || 'แพทย์แผนไทย') }}</div>
                                    </div>
                                </div>
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border shadow-sm" :class="getStatusColor(booking.status)">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDot(booking.status)"></span>
                                    {{ getStatusText(booking.status) }}
                                </div>
                            </div>

                            <div class="space-y-3 mb-4 relative">
                                <div class="flex items-center gap-3 text-slate-700 bg-slate-50/80 p-3 rounded-2xl border border-slate-100">
                                    <div class="p-2 bg-white rounded-xl shadow-sm text-blue-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-slate-500">วันนัดหมาย</div>
                                        <div class="bg-blue-500/10 inline-block px-2 py-0.5 rounded text-blue-700 font-bold text-sm">
                                            {{ formatDate(booking.appointment_date) }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex gap-3">
                                    <div class="flex-1 flex items-center gap-3 text-slate-700 bg-slate-50/80 p-3 rounded-2xl border border-slate-100">
                                        <div class="p-2 bg-white rounded-xl shadow-sm text-amber-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-xs text-slate-500">เวลา</div>
                                            <div class="font-semibold">{{ formatTimePosix(booking.start_time) }} น.</div>
                                        </div>
                                    </div>
                                    <div class="flex-1 flex items-center gap-3 text-slate-700 bg-slate-50/80 p-3 rounded-2xl border border-slate-100">
                                        <div class="p-2 bg-white rounded-xl shadow-sm text-green-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-xs text-slate-500">ระยะเวลา</div>
                                            <div class="font-semibold">{{ booking.duration_minutes }} นาที</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3 text-slate-700 bg-slate-50/80 p-3 rounded-2xl border border-slate-100">
                                    <div class="p-2 bg-white rounded-xl shadow-sm text-purple-500 mt-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-xs text-slate-500 mb-0.5">อาการเบื้องต้น</div>
                                        <span class="text-sm line-clamp-3 leading-relaxed">{{ booking.symptoms }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}

@keyframes fade-in-down {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in-up {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.animate-fade-in-down {
    animation: fade-in-down 0.8s ease-out forwards;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}
</style>
