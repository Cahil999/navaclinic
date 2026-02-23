<template>
    <div v-if="patient && (patient.drug_allergy || patient.underlying_disease || patient.surgery_history || patient.accident_history)" class="flex items-center gap-1 inline-flex">
        <!-- แพ้ยา (Drug Allergy) -->
        <BeakerIcon 
            v-if="patient.drug_allergy" 
            class="w-4 h-4 text-red-500 animate-pulse" 
            title="มีประวัติแพ้ยา" 
        />
        <!-- โรคประจำตัว (Underlying Disease) -->
        <HeartIcon 
            v-if="patient.underlying_disease" 
            class="w-4 h-4 text-indigo-500 animate-pulse" 
            title="มีโรคประจำตัว" 
        />
        <!-- ประวัติผ่าตัด (Surgery History) -->
        <ScissorsIcon 
            v-if="patient.surgery_history" 
            class="w-4 h-4 text-blue-500 animate-pulse" 
            title="มีประวัติผ่าตัด" 
        />
        <!-- ประวัติอุบัติเหตุ (Accident History) -->
        <BoltIcon 
            v-if="patient.accident_history" 
            class="w-4 h-4 text-amber-500 animate-pulse" 
            title="มีประวัติอุบัติเหตุ" 
        />
    </div>
</template>

<script setup>
import { BeakerIcon, HeartIcon, ScissorsIcon, BoltIcon } from '@heroicons/vue/24/solid';

defineProps({
    patient: {
        type: Object,
        required: false,
        default: null
    }
});
</script>
