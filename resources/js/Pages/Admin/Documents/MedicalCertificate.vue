<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    visit: Object,
    clinic: Object
});

const form = ref({
    date: new Date().toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' }),
    doctor_name: props.visit.doctor?.name || 'นายสังวรณ์ เชื้อเต๊ะ', // Default doctor if missing
    license_no: props.visit.doctor?.license_number || '3597', // Default license
    patient_name: props.visit.patient.name,
    exam_date: new Date(props.visit.visit_date).toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' }),
    diagnosis: [
        props.visit.treatment_record?.chief_complaint ? `อาการสำคัญ: ${props.visit.treatment_record.chief_complaint}` : '',
        props.visit.treatment_record?.diagnosis ? `การวินิจฉัย: ${props.visit.treatment_record.diagnosis}` : ''
    ].filter(Boolean).join('\n'),
    comment: 'ขอรับรองว่าผู้ป่วยได้มารับการตรวจรักษาจริง',
    doctor_title: 'แพทย์แผนไทยผู้ตรวจ'
});

const print = () => {
    if (typeof window !== 'undefined') {
        window.print();
    }
};
</script>

<template>
    <Head title="ใบรับรองแพทย์ (Medical Certificate)" />

    <div class="min-h-screen bg-slate-100 p-8 print:p-0 print:bg-white font-sarabun text-slate-800 leading-relaxed">
        
        <!-- Toolbar -->
        <div class="max-w-[210mm] mx-auto mb-6 flex justify-between items-center print:hidden">
            <h1 class="font-bold text-xl">พิมพ์ใบรับรองแพทย์</h1>
            <button @click="print" class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 font-bold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                </svg>
                Print
            </button>
        </div>

        <!-- A4 Paper -->
        <div class="bg-white shadow-xl print:shadow-none max-w-[210mm] min-h-[297mm] mx-auto p-[25mm] relative print:p-0">
            
            <!-- Logo -->
            <div class="flex justify-center mb-6">
                 <div class="w-24 h-24 rounded-full border-2 border-slate-200 flex items-center justify-center bg-slate-50 print:border-none">
                     <img src="/images/logo_bw.png" alt="Logo" class="w-20 h-20 object-contain filter grayscale contrast-125 opacity-80" onerror="this.style.display='none'">
                </div>
            </div>

            <!-- Header -->
            <div class="text-center mb-12 space-y-1">
                <h2 class="font-bold text-lg">{{ clinic.name_th }}</h2>
                <h3 class="text-sm font-medium text-slate-600">{{ clinic.name_en }}</h3>
                <p class="text-xs text-slate-500">{{ clinic.address_line }}</p>
            </div>

            <div class="text-center mb-12">
                <h1 class="text-2xl font-bold mb-2">ใบรับรองแพทย์</h1>
                <h2 class="text-sm font-medium text-slate-500 uppercase tracking-widest">Medical Certificate</h2>
            </div>
            
            <div class="flex justify-end mb-12">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-sm">วันที่:</span>
                    <input v-model="form.date" class="text-right border-b border-dotted border-slate-300 focus:outline-none focus:border-indigo-500 bg-transparent print:border-none w-40 text-sm">
                </div>
            </div>

            <!-- Content -->
            <div class="space-y-8 text-base leading-loose px-8">
                <!-- Doctor Info -->
                <div class="flex flex-wrap items-baseline gap-2">
                    <span>ข้าพเจ้า</span>
                    <input v-model="form.doctor_name" class="flex-1 min-w-[200px] border-b border-dotted border-slate-300 focus:outline-none focus:border-indigo-500 bg-transparent print:border-none px-2 font-bold text-center">
                </div>

                <div class="flex flex-wrap items-baseline gap-2">
                     <span>เป็นแพทย์ได้ขึ้นทะเบียน ได้รับอนุญาตให้เป็นผู้ประกอบวิชาชีพการแพทย์แผนไทย สาขา นวดไทย</span>
                </div>
                
                 <div class="flex flex-wrap items-baseline gap-2">
                     <span>เลขที่ พท.น.</span>
                    <input v-model="form.license_no" class="w-32 border-b border-dotted border-slate-300 focus:outline-none focus:border-indigo-500 bg-transparent print:border-none px-2 font-bold text-center">
                </div>

                <div class="flex flex-wrap items-baseline gap-2 mt-4">
                    <span>ได้ทำการตรวจร่างกายของ</span>
                    <input v-model="form.patient_name" class="flex-1 min-w-[250px] border-b border-dotted border-slate-300 focus:outline-none focus:border-indigo-500 bg-transparent print:border-none px-2 font-bold text-center">
                </div>

                <div class="flex flex-wrap items-baseline gap-2">
                    <span>เมื่อวันที่</span>
                    <input v-model="form.exam_date" class="flex-1 min-w-[200px] border-b border-dotted border-slate-300 focus:outline-none focus:border-indigo-500 bg-transparent print:border-none px-2 font-bold text-center">
                </div>

                <div class="w-full mt-6">
                    <div class="font-bold mb-2">พบว่ามีอาการ / โรค :</div>
                    <textarea v-model="form.diagnosis" rows="4" class="w-full border border-slate-200 rounded-lg p-3 text-sm focus:ring-indigo-500 focus:border-indigo-500 print:border-none print:resize-none print:p-0 print:bg-transparent leading-relaxed"></textarea>
                </div>

                <div class="flex flex-wrap items-baseline gap-2 mt-8 justify-center">
                    <input v-model="form.comment" class="w-full text-center border-b border-dotted border-slate-300 focus:outline-none focus:border-indigo-500 bg-transparent print:border-none px-2 font-medium">
                </div>
            </div>

            <!-- Signatures -->
             <div class="flex justify-between mt-24 px-12">
                <div class="text-center w-64 pt-8">
                     <div class="border-b border-dotted border-slate-400 mb-2 h-8"></div>
                     <p class="text-sm text-slate-500">ผู้รับการตรวจ</p>
                </div>

                <div class="text-center w-64">
                    <!-- Signature Line -->
                    <div class="border-b border-dotted border-slate-400 mb-2 h-16"></div> 
                    <p class="font-bold text-sm mb-1">({{ form.doctor_name }})</p>
                    <input v-model="form.doctor_title" class="text-center text-xs text-slate-500 w-full border-none bg-transparent focus:ring-0 p-0">
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
