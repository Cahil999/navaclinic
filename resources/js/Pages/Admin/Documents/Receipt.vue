<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
    visit: Object,
    clinic: Object
});

const form = ref({
    date: new Date(props.visit.created_at).toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' }),
    customer_name: props.visit.patient.name,
    customer_id: props.visit.patient.id_card_number,
    cn: props.visit.patient.patient_id,
    items: [
        { name: 'ค่านวดรักษา', amount: Number(props.visit.treatment_fee || props.visit.price) }
    ],
    total: Number(props.visit.price),
    cashier: 'เจ้าหน้าที่การเงิน/Cashier'
});

const updateItem = (index, field, value) => {
    form.value.items[index][field] = value;
    // Auto update total if amount changes? Let's keep it manual or simple sum
    form.value.total = form.value.items.reduce((sum, item) => sum + Number(item.amount), 0);
};

const addItem = () => {
    form.value.items.push({ name: '', amount: 0 });
};

const removeItem = (index) => {
    form.value.items.splice(index, 1);
    form.value.total = form.value.items.reduce((sum, item) => sum + Number(item.amount), 0);
};

const print = () => {
    if (typeof window !== 'undefined') {
        window.print();
    }
};
</script>

<template>
    <Head title="ใบเสร็จรับเงิน (Receipt)" />

    <div class="min-h-screen bg-slate-100 p-8 print:p-0 print:bg-white font-sans text-slate-800">
        
        <!-- Toolbar -->
        <div class="max-w-[210mm] mx-auto mb-6 flex justify-between items-center print:hidden">
            <h1 class="font-bold text-xl">พิมพ์ใบเสร็จรับเงิน</h1>
            <div class="flex gap-2">
                 <button @click="addItem" class="px-4 py-2 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 text-sm font-bold">
                    + เพิ่มรายการ
                </button>
                <button @click="print" class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 font-bold flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                    </svg>
                    Print
                </button>
            </div>
        </div>

        <!-- A4 Paper -->
        <div class="bg-white shadow-xl print:shadow-none max-w-[210mm] min-h-[297mm] mx-auto p-[15mm] print:p-0 relative font-sarabun">
            
            <!-- Header -->
            <div class="flex justify-between items-start mb-8 border-b border-gray-100 pb-6">
                <!-- Logo & Clinic Info -->
                <div class="flex gap-4">
                    <div class="w-24 h-24 flex-shrink-0">
                         <img src="/images/logo_bw.png" alt="Logo" class="w-full h-full object-contain filter grayscale contrast-125 opacity-80" onerror="this.style.display='none'">
                    </div>
                    <div class="text-sm space-y-1">
                        <h2 class="font-bold text-lg">{{ clinic.name_th }}</h2>
                        <h3 class="font-bold text-gray-600 mb-2">{{ clinic.name_en }}</h3>
                        <p>{{ clinic.address_1 }}</p>
                        <p>{{ clinic.address_2 }}</p>
                        <p>{{ clinic.phone }}</p>
                    </div>
                </div>

                <!-- License Box -->
                <div class="border border-gray-300 p-2 text-[10px] text-right rounded w-[220px]">
                    <p class="whitespace-pre-line leading-relaxed">{{ clinic.license }}</p>
                </div>
            </div>

            <!-- Title -->
            <div class="text-center mb-8">
                <h1 class="text-xl font-bold">ใบเสร็จรับเงิน</h1>
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-widest mt-1">RECEIPT</h2>
            </div>

            <!-- Customer Info -->
            <div class="flex justify-between items-start mb-6 text-sm">
                <div class="space-y-2 flex-1">
                    <div class="flex">
                        <span class="w-24 font-bold">ชื่อ-สกุล :</span>
                        <input v-model="form.customer_name" class="flex-1 border-b border-dotted border-gray-300 focus:outline-none focus:border-indigo-500 bg-transparent print:border-none px-1">
                    </div>
                    <div class="flex">
                        <span class="w-24 font-bold">CN :</span>
                        <input v-model="form.cn" class="flex-1 border-b border-dotted border-gray-300 focus:outline-none focus:border-indigo-500 bg-transparent print:border-none px-1">
                    </div>
                    <div class="flex">
                        <span class="w-24 font-bold">เลขบัตรประชาชน :</span>
                        <input v-model="form.customer_id" class="flex-1 border-b border-dotted border-gray-300 focus:outline-none focus:border-indigo-500 bg-transparent print:border-none px-1">
                    </div>
                </div>
                <div class="space-y-2 w-[250px] text-right flex flex-col items-end">
                    <div class="flex items-center justify-end w-full">
                        <span class="font-bold mr-2">วันที่ :</span>
                        <input v-model="form.date" class="text-right border-b border-dotted border-gray-300 focus:outline-none focus:border-indigo-500 bg-transparent print:border-none px-1 w-40">
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="border border-black mb-4 min-h-[400px] flex flex-col">
                <!-- Header -->
                <div class="flex border-b border-black text-sm font-bold bg-gray-50 print:bg-transparent">
                    <div class="w-16 border-r border-black p-2 text-center">ลำดับ<br><span class="text-[10px] font-normal uppercase">NO.</span></div>
                    <div class="flex-1 border-r border-black p-2 text-center">รายการ<br><span class="text-[10px] font-normal uppercase">ITEMS</span></div>
                    <div class="w-32 p-2 text-center">จำนวนเงิน<br><span class="text-[10px] font-normal uppercase">AMOUNT</span></div>
                </div>

                <!-- Rows -->
                <div class="flex-1 relative">
                    <div v-for="(item, index) in form.items" :key="index" class="flex text-sm group">
                        <div class="w-16 p-2 text-center pt-3 relative">
                            {{ index + 1 }}
                            <button @click="removeItem(index)" class="print:hidden absolute left-1 top-3 text-red-300 hover:text-red-500 text-xs">x</button>
                        </div>
                        <div class="flex-1 p-2 pt-3 border-l border-r border-black/10 print:border-none relative">
                            <input v-model="item.name" class="w-full bg-transparent border-none p-0 focus:ring-0 text-sm placeholder-gray-300" placeholder="รายการ">
                        </div>
                        <div class="w-32 p-2 pt-3 text-right relative">
                            <input type="number" v-model="item.amount" step="0.01" class="w-full bg-transparent border-none p-0 focus:ring-0 text-sm text-right" @input="updateItem(index, 'amount', $event.target.value)">
                        </div>
                    </div>
                    
                    <!-- Vertical Lines for print structure (visual only) -->
                    <div class="absolute inset-0 pointer-events-none flex">
                        <div class="w-16 border-r border-black h-full"></div>
                        <div class="flex-1 border-r border-black h-full"></div>
                        <div class="w-32 h-full"></div>
                    </div>
                </div>

                <!-- Total -->
                <div class="border-t border-black flex text-sm">
                    <div class="flex-1 border-r border-black p-2 text-right font-bold flex items-center justify-end">รวมทั้งสิ้น TOTAL</div>
                    <div class="w-32 p-2 text-right font-bold bg-gray-50 print:bg-transparent">
                        {{ Number(form.total).toLocaleString('th-TH', { minimumFractionDigits: 2 }) }}
                    </div>
                </div>
            </div>

            <!-- Footer Signatures -->
            <div class="flex justify-end mt-16 px-8">
                <div class="text-center w-64">
                    <div class="border-b border-dotted border-black mb-2 h-8"></div>
                    <p class="font-bold text-sm mb-1">สังวรณ์ เชื้อเต๊ะ</p>
                    <input v-model="form.cashier" class="text-center text-xs text-gray-500 w-full border-none bg-transparent focus:ring-0 p-0">
                </div>
            </div>

        </div>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;700&display=swap');

.font-sarabun {
    font-family: 'Sarabun', sans-serif;
}
</style>
