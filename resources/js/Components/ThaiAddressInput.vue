<script setup>
import { ref, computed, watch } from 'vue';
import { 
    searchAddressByDistrict, 
    searchAddressByAmphoe, 
    searchAddressByProvince, 
    searchAddressByZipcode 
} from 'thai-address-database';

const props = defineProps({
    subDistrict: { type: String, default: '' },
    district: { type: String, default: '' },
    province: { type: String, default: '' },
    postalCode: { type: String, default: '' },
});

const emit = defineEmits([
    'update:subDistrict', 
    'update:district', 
    'update:province', 
    'update:postalCode',
    'selected'
]);

// Internal state to handle input, but sync with props
const form = ref({
    subDistrict: props.subDistrict,
    district: props.district,
    province: props.province,
    postalCode: props.postalCode,
});

watch(() => props.subDistrict, (val) => form.value.subDistrict = val);
watch(() => props.district, (val) => form.value.district = val);
watch(() => props.province, (val) => form.value.province = val);
watch(() => props.postalCode, (val) => form.value.postalCode = val);

const suggestions = ref([]);
const showSuggestions = ref(false);
const activeInput = ref(null); // 'subDistrict', 'district', 'province', 'postalCode'

const handleInput = (type, value) => {
    // Update parent immediately
    // Mapping internal 'type' to prop names
    // type: 'district' (subdistrict in lib), 'amphoe' (district), 'province', 'zipcode'
    
    // BUT the lib uses: district=SubDistrict, amphoe=District. 
    // Let's stick to standard naming in my form:
    // subDistrict, district, province, postalCode.

    form.value[type] = value;
    activeInput.value = type;

    // Map my form keys to lib keys for update
    const propMap = {
        subDistrict: 'update:subDistrict',
        district: 'update:district',
        province: 'update:province',
        postalCode: 'update:postalCode'
    };
    emit(propMap[type], value);

    if (!value) {
        suggestions.value = [];
        showSuggestions.value = false;
        return;
    }

    let results = [];
    try {
        if (type === 'subDistrict') {
            results = searchAddressByDistrict(value);
        } else if (type === 'district') {
            results = searchAddressByAmphoe(value);
        } else if (type === 'province') {
            results = searchAddressByProvince(value);
        } else if (type === 'postalCode') {
            results = searchAddressByZipcode(value);
        }
    } catch (e) {
        console.error("Address search error", e);
    }

    // Process results (lib returns max 20 by default)
    suggestions.value = results.slice(0, 10); // Limit to 10
    showSuggestions.value = results.length > 0;
};

const selectAddress = (addr) => {
    // addr has: district, amphoe, province, zipcode
    form.value.subDistrict = addr.district;
    form.value.district = addr.amphoe;
    form.value.province = addr.province;
    form.value.postalCode = addr.zipcode;

    emit('update:subDistrict', addr.district);
    emit('update:district', addr.amphoe);
    emit('update:province', addr.province);
    emit('update:postalCode', addr.zipcode);
    
    // Emit detailed object
    emit('selected', {
        subDistrict: addr.district,
        district: addr.amphoe,
        province: addr.province,
        postalCode: addr.zipcode
    });

    showSuggestions.value = false;
    activeInput.value = null;
};

// Close suggestions when clicking outside handling is tricky in simple setup, 
// using a blur with delay or overlay. Overlay is safer.
const closeSuggestions = () => {
    // Small delay to allow click event to register
    setTimeout(() => {
        showSuggestions.value = false;
        activeInput.value = null;
    }, 200);
};

</script>

<template>
    <div class="relative space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <!-- Tambon (Subdistrict) -->
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">ตำบล / แขวง</label>
                <input 
                    type="text" 
                    v-model="form.subDistrict"
                    @input="handleInput('subDistrict', $event.target.value)"
                    @focus="activeInput = 'subDistrict'"
                    @blur="closeSuggestions"
                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    placeholder="ระบุตำบล"
                />
            </div>

            <!-- Amphoe (District) -->
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">อำเภอ / เขต</label>
                <input 
                    type="text" 
                    v-model="form.district"
                    @input="handleInput('district', $event.target.value)"
                    @focus="activeInput = 'district'"
                    @blur="closeSuggestions"
                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    placeholder="ระบุอำเภอ"
                />
            </div>

            <!-- Province -->
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">จังหวัด</label>
                <input 
                    type="text" 
                    v-model="form.province"
                    @input="handleInput('province', $event.target.value)"
                    @focus="activeInput = 'province'"
                    @blur="closeSuggestions"
                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    placeholder="ระบุจังหวัด"
                />
            </div>

            <!-- Zipcode -->
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">รหัสไปรษณีย์</label>
                <input 
                    type="text" 
                    v-model="form.postalCode"
                    @input="handleInput('postalCode', $event.target.value)"
                    @focus="activeInput = 'postalCode'"
                    @blur="closeSuggestions"
                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    placeholder="ระบุรหัสไปรษณีย์"
                />
            </div>
        </div>

        <!-- Suggestion Dropdown -->
        <div 
            v-if="showSuggestions && suggestions.length > 0" 
            class="absolute left-0 right-0 z-50 mt-1 bg-white rounded-lg shadow-xl border border-gray-100 max-h-60 overflow-y-auto"
            style="top: calc(100% - 1.5rem);"
        >
            <div class="py-1">
                <div 
                    v-for="(item, index) in suggestions" 
                    :key="index"
                    @mousedown.prevent="selectAddress(item)" 
                    class="px-4 py-2 hover:bg-blue-50 cursor-pointer text-sm text-gray-700 flex flex-wrap gap-x-2"
                >
                    <span class="font-medium text-blue-600">{{ item.district }}</span>
                    <span>&raquo;</span>
                    <span>{{ item.amphoe }}</span>
                    <span>&raquo;</span>
                    <span>{{ item.province }}</span>
                    <span>&raquo;</span>
                    <span class="font-bold text-gray-900">{{ item.zipcode }}</span>
                </div>
            </div>
            <!-- Powered by hint -->
            <div class="px-4 py-1 bg-gray-50 text-[10px] text-gray-400 text-right">
                Auto-suggestion by Thai Address Database
            </div>
        </div>
    </div>
</template>
