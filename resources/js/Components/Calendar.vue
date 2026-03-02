<script setup>
import { ref, computed, onMounted, watch } from 'vue';

const props = defineProps({
    availability: Object, // { '2024-05-20': { status: 'available' | 'full' } }
    initialDate: Date
});

const emit = defineEmits(['monthChanged', 'dateSelected']);

const currentDate = ref(props.initialDate || new Date());
const selectedDate = ref(null);

const currentMonth = computed(() => currentDate.value.getMonth());
const currentYear = computed(() => currentDate.value.getFullYear());

const monthNames = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
  "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
];

const daysInMonth = computed(() => {
    return new Date(currentYear.value, currentMonth.value + 1, 0).getDate();
});

const firstDayOfMonth = computed(() => {
    return new Date(currentYear.value, currentMonth.value, 1).getDay();
});

const days = computed(() => {
    let daysArray = [];
    // Padding for empty start days
    for (let i = 0; i < firstDayOfMonth.value; i++) {
        daysArray.push(null);
    }
    // Days
    for (let i = 1; i <= daysInMonth.value; i++) {
        daysArray.push(new Date(currentYear.value, currentMonth.value, i));
    }
    return daysArray;
});

const formatDate = (date) => {
    if (!date) return null;
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const isPastDate = (date) => {
    if (!date) return false;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return date < today;
};

const isToday = (date) => {
    if (!date) return false;
    const today = new Date();
    return date.getDate() === today.getDate() && 
           date.getMonth() === today.getMonth() && 
           date.getFullYear() === today.getFullYear();
};

const getStatus = (date) => {
    if (!date) return null;
    if (isPastDate(date)) return 'past';
    const dateStr = formatDate(date);
    if (!props.availability) return 'available';
    return props.availability[dateStr]?.status || 'available';
};

const findNextAvailableDate = (clickedDate) => {
    let checkDate = new Date(clickedDate);
    checkDate.setDate(checkDate.getDate() + 1);
    
    // Check up to 30 days ahead
    for (let i = 0; i < 30; i++) {
        const nextStatus = getStatus(checkDate);
        if (nextStatus !== 'past' && nextStatus !== 'closed' && nextStatus !== 'full') {
            return checkDate;
        }
        checkDate.setDate(checkDate.getDate() + 1);
    }
    return null;
};

const selectDate = (date) => {
    if (!date) return;
    const status = getStatus(date);
    
    if (status === 'past') {
        return; // Disabled fully
    }
    
    if (status === 'full' || status === 'closed') {
        import('sweetalert2').then((module) => {
            const Swal = module.default;
            const nextAvail = findNextAvailableDate(date);
            let text = 'ไม่มีหมอว่างในวันที่คุณเลือก หรือคลินิกปิดทำการ';
            
            if (nextAvail) {
                 const nextStr = nextAvail.toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' });
                 text += `\n\nวันที่ว่างถัดไปคือ: ${nextStr}`;
            }
            
            Swal.fire({
                icon: 'warning',
                title: 'ไม่สามารถจองได้',
                text: text,
                confirmButtonText: 'ตกลง',
                confirmButtonColor: '#4F46E5',
            });
        });
        return;
    }
    
    selectedDate.value = date;
    emit('dateSelected', formatDate(date));
};

const prevMonth = () => {
    currentDate.value = new Date(currentYear.value, currentMonth.value - 1, 1);
    emit('monthChanged', { month: currentMonth.value + 1, year: currentYear.value });
};

const nextMonth = () => {
    currentDate.value = new Date(currentYear.value, currentMonth.value + 1, 1);
    emit('monthChanged', { month: currentMonth.value + 1, year: currentYear.value });
};

onMounted(() => {
     emit('monthChanged', { month: currentMonth.value + 1, year: currentYear.value });
});

</script>

<template>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-center mb-4">
            <button @click="prevMonth" type="button" class="p-2 hover:bg-gray-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
            <h2 class="text-lg font-semibold">{{ monthNames[currentMonth] }} {{ currentYear + 543 }}</h2>
            <button @click="nextMonth" type="button" class="p-2 hover:bg-gray-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-7 gap-1 text-center mb-2">
            <div class="text-xs font-bold text-gray-500">อา</div>
            <div class="text-xs font-bold text-gray-500">จ</div>
            <div class="text-xs font-bold text-gray-500">อ</div>
            <div class="text-xs font-bold text-gray-500">พ</div>
            <div class="text-xs font-bold text-gray-500">พฤ</div>
            <div class="text-xs font-bold text-gray-500">ศ</div>
            <div class="text-xs font-bold text-gray-500">ส</div>
        </div>
        <div class="grid grid-cols-7 gap-1">
            <div v-for="(day, index) in days" :key="index" class="aspect-square">
                <div v-if="day" 
                    @click="selectDate(day)"
                    :class="[
                        'w-full h-full flex flex-col items-center justify-center rounded-lg cursor-pointer transition-colors relative',
                        formatDate(day) === formatDate(selectedDate) ? 'bg-indigo-600 text-white' : 'hover:bg-gray-100',
                        getStatus(day) === 'full' ? 'opacity-50 cursor-not-allowed bg-gray-100' : '',
                        getStatus(day) === 'closed' ? 'bg-red-50 text-red-500 cursor-not-allowed' : '',
                        getStatus(day) === 'past' ? 'opacity-30 cursor-not-allowed bg-gray-50' : '',
                        isToday(day) && formatDate(day) !== formatDate(selectedDate) ? 'border-2 border-indigo-500 font-bold' : ''
                    ]"
                >
                    <span class="text-sm z-10">{{ day.getDate() }}</span>
                    <span v-if="getStatus(day) === 'full'" class="text-[10px] text-red-500 font-bold z-10">เต็ม</span>
                    <span v-if="getStatus(day) === 'closed'" class="text-[10px] text-red-500 font-bold z-10">ปิด</span>
                    <span v-if="isToday(day) && formatDate(day) !== formatDate(selectedDate)" class="absolute top-1 right-1 w-2 h-2 bg-indigo-500 rounded-full"></span>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="mt-4 border-t pt-3 flex flex-wrap items-center justify-center gap-4 text-xs text-gray-500">
            <div class="flex items-center">
                <span class="w-3 h-3 bg-red-50 rounded-sm mr-1 border border-red-200"></span>
                <span>ร้านปิด (Closed)</span>
            </div>
            <div class="flex items-center">
                <span class="w-3 h-3 bg-gray-100 rounded-sm mr-1 border border-gray-200"></span>
                <span>คิวเต็ม (Full)</span>
            </div>
            <div class="flex items-center">
                <span class="w-3 h-3 bg-gray-50 rounded-sm mr-1 opacity-30"></span>
                <span>วันที่ผ่านมา (Past)</span>
            </div>
            <div class="flex items-center">
                <span class="w-2 h-2 bg-indigo-500 rounded-full mr-1"></span>
                <span>วันนี้ (Today)</span>
            </div>
        </div>
    </div>
</template>
