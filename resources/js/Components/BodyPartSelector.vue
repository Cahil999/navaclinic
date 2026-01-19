<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    readonly: {
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits(['update:modelValue']);

const selectedParts = ref([...props.modelValue]);

watch(() => props.modelValue, (newVal) => {
    selectedParts.value = [...newVal];
});

const togglePart = (part) => {
    if (props.readonly) return;
    
    if (selectedParts.value.includes(part)) {
        selectedParts.value = selectedParts.value.filter(p => p !== part);
    } else {
        selectedParts.value.push(part);
    }
    emit('update:modelValue', selectedParts.value);
};

const isSelected = (part) => selectedParts.value.includes(part);

</script>

<template>
    <div class="flex flex-col items-center">
        <p class="text-sm text-slate-500 mb-4">Click on body parts to select painful areas</p>
        <div class="flex flex-wrap justify-center gap-8">
            <!-- Front View -->
            <div class="relative">
                <div class="text-center text-xs font-bold text-slate-400 mb-2">FRONT (ด้านหน้า)</div>
                <svg width="200" height="400" viewBox="0 0 200 400" xmlns="http://www.w3.org/2000/svg" class="select-none">
                    <!-- Head -->
                    <circle cx="100" cy="35" r="25" 
                        @click="togglePart('Head (Front)')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Head (Front)') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                    <!-- Neck -->
                    <rect x="90" y="60" width="20" height="15" 
                        @click="togglePart('Neck (Front)')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Neck (Front)') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                    <!-- Shoulders -->
                    <path d="M60 75 Q100 75 140 75 Q150 90 145 110 L55 110 Q50 90 60 75" 
                        @click="togglePart('Shoulders (Front)')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Shoulders (Front)') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                    <!-- Chest -->
                    <rect x="70" y="110" width="60" height="50" rx="5"
                        @click="togglePart('Chest')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Chest') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                    <!-- Abdomen -->
                    <rect x="70" y="165" width="60" height="60" rx="5"
                        @click="togglePart('Abdomen')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Abdomen') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                    <!-- Left Arm (Viewer's Left) -->
                    <rect x="35" y="80" width="20" height="100" rx="10" transform="rotate(10 45 80)"
                         @click="togglePart('Right Arm')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Right Arm') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                    <!-- Right Arm (Viewer's Right) -->
                     <rect x="145" y="80" width="20" height="100" rx="10" transform="rotate(-10 155 80)"
                         @click="togglePart('Left Arm')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Left Arm') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                    <!-- Hips -->
                    <path d="M70 230 L130 230 L140 250 L60 250 Z"
                        @click="togglePart('Hips (Front)')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Hips (Front)') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                     <!-- Left Leg -->
                    <rect x="70" y="255" width="25" height="120" rx="5"
                         @click="togglePart('Right Leg (Front)')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Right Leg (Front)') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                    <!-- Right Leg -->
                    <rect x="105" y="255" width="25" height="120" rx="5"
                         @click="togglePart('Left Leg (Front)')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Left Leg (Front)') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                </svg>
            </div>

            <!-- Back View -->
            <div class="relative">
                <div class="text-center text-xs font-bold text-slate-400 mb-2">BACK (ด้านหลัง)</div>
                <svg width="200" height="400" viewBox="0 0 200 400" xmlns="http://www.w3.org/2000/svg" class="select-none">
                     <!-- Head -->
                    <circle cx="100" cy="35" r="25" 
                        @click="togglePart('Head (Back)')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Head (Back)') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                    <!-- Neck -->
                    <rect x="90" y="60" width="20" height="15" 
                        @click="togglePart('Neck (Back)')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Neck (Back)') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                    <!-- Shoulders -->
                    <path d="M60 75 Q100 75 140 75 Q150 90 145 110 L55 110 Q50 90 60 75" 
                        @click="togglePart('Shoulders (Back)')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Shoulders (Back)') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                    <!-- Upper Back -->
                    <rect x="70" y="110" width="60" height="50" rx="5"
                        @click="togglePart('Upper Back')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Upper Back') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                    <!-- Lower Back -->
                    <rect x="70" y="165" width="60" height="60" rx="5"
                        @click="togglePart('Lower Back')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Lower Back') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                     <!-- Left Arm (Viewer's Left -> Patient's Left) -->
                    <rect x="35" y="80" width="20" height="100" rx="10" transform="rotate(10 45 80)"
                         @click="togglePart('Left Arm (Back)')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Left Arm (Back)') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                    <!-- Right Arm (Viewer's Right -> Patient's Right) -->
                     <rect x="145" y="80" width="20" height="100" rx="10" transform="rotate(-10 155 80)"
                         @click="togglePart('Right Arm (Back)')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Right Arm (Back)') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                     <!-- Hips/Glutes -->
                    <path d="M70 230 L130 230 L140 250 L60 250 Z"
                        @click="togglePart('Hips (Back)')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Hips (Back)') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                     <!-- Left Leg -->
                    <rect x="70" y="255" width="25" height="120" rx="5"
                         @click="togglePart('Left Leg (Back)')"
                        :class="['transition-colors', readonly ? '' : 'cursor-pointer', isSelected('Left Leg (Back)') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                    <!-- Right Leg -->
                    <rect x="105" y="255" width="25" height="120" rx="5"
                         @click="togglePart('Right Leg (Back)')"
                        :class="['cursor-pointer transition-colors', isSelected('Right Leg (Back)') ? 'fill-rose-500' : 'fill-slate-200 hover:fill-rose-200']"
                    />
                </svg>
            </div>
        </div>
        
        <div class="mt-4 flex flex-wrap gap-2 justify-center max-w-lg">
            <span v-for="part in selectedParts" :key="part" class="px-2 py-1 bg-rose-100 text-rose-700 rounded-lg text-xs font-bold border border-rose-200">
                {{ part }}
                <button @click.stop="togglePart(part)" class="ml-1 text-rose-400 hover:text-rose-900">&times;</button>
            </span>
        </div>
    </div>
</template>
