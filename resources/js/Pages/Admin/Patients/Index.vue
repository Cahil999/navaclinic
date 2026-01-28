<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3'; // Added useForm
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue'; // Added Modal
import InputLabel from '@/Components/InputLabel.vue'; // Added InputLabel
import SecondaryButton from '@/Components/SecondaryButton.vue'; // Added SecondaryButton
import PrimaryButton from '@/Components/PrimaryButton.vue'; // Added PrimaryButton
import InputError from '@/Components/InputError.vue'; // Added InputError
import TextInput from '@/Components/TextInput.vue'; // Added TextInput

const props = defineProps({
    patients: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
    availablePackages: { // Added availablePackages prop
        type: Array,
        default: () => [],
    }
});

const search = ref(props.filters.search);

watch(search, debounce((value) => {
    router.get(
        route('admin.patients.index'),
        { search: value },
        { preserveState: true, replace: true }
    );
}, 300));

// --- Sell Package Logic ---
const showSellModal = ref(false);
const selectedPatient = ref(null);
const sellForm = useForm({
    user_id: '',
    service_package_id: '',
});

const openSellModal = (patient) => {
    selectedPatient.value = patient;
    sellForm.user_id = patient.id;
    sellForm.service_package_id = '';
    showSellModal.value = true;
};

const closeSellModal = () => {
    showSellModal.value = false;
    sellForm.reset();
    selectedPatient.value = null;
};

const submitSellPackage = () => {
    sellForm.post(route('admin.patient-packages.store'), {
        onSuccess: () => {
            closeSellModal();
            // Optional: Show success notification or toast
        },
    });
};

// --- Register Patient Logic ---
const showRegisterModal = ref(false);
const showConfirmRegisterModal = ref(false); // New confirmation step

const registerForm = useForm({
    name: '',
    phone_number: '',
    id_card_number: '',
    date_of_birth: '',
    gender: '',
    race: '',
    nationality: '',
    religion: '',
    occupation: '',
    address: '',
    emergency_contact_name: '',
    emergency_contact_phone: '',
    underlying_disease: '',
    surgery_history: '',
    drug_allergy: '',
    accident_history: '',
});

const openRegisterModal = () => {
    registerForm.reset();
    showRegisterModal.value = true;
};

const closeRegisterModal = () => {
    showRegisterModal.value = false;
    registerForm.reset();
};

const askToConfirmRegister = () => {
    // Validate basic fields before asking to confirm (optional, but good UX)
    registerForm.clearErrors();
    
    // Check if at least name is present (or rely on HTML5 validation / backend)
    // We'll proceed to confirmation modal where the actual "Submit" happens
    // but usually validation happens on submit. We can just show the confirmation.
    showConfirmRegisterModal.value = true;
};

const closeConfirmRegisterModal = () => {
    showConfirmRegisterModal.value = false;
};

const submitRegister = () => {
    registerForm.post(route('admin.patients.store'), {
        onSuccess: () => {
            closeConfirmRegisterModal();
            closeRegisterModal();
        },
        onError: () => {
            closeConfirmRegisterModal(); 
            // Keep the main form open so they can fix errors
        }
    });
};
</script>

<template>
    <Head title="Patients" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-black leading-tight">
                รายชื่อคนไข้ทั้งหมด
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div>
                    <div class="mb-6 flex flex-col md:flex-row justify-between gap-4 items-center">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="ค้นหาด้วยรหัส, ชื่อ, อีเมล หรือเบอร์โทร..."
                            class="w-full md:w-1/3 px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent shadow-sm bg-white text-slate-700"
                        />
                        <PrimaryButton @click="openRegisterModal" class="bg-blue-600 hover:bg-blue-700">
                            <span class="mr-2">+</span> ลงทะเบียนคนไข้ใหม่
                        </PrimaryButton>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-100">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-slate-600">
                                <thead class="text-xs text-slate-700 uppercase bg-blue-50 border-b border-blue-100">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 font-bold text-blue-900">ชื่อ</th>
                                        <th scope="col" class="px-6 py-4 font-bold text-blue-900">เบอร์โทรศัพท์</th>
                                        <th scope="col" class="px-6 py-4 font-bold text-blue-900">วันที่สมัคร</th>
                                        <th scope="col" class="px-6 py-4 font-bold text-blue-900">การดำเนินการ</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100">
                                    <tr v-for="patient in patients.data" :key="patient.id" class="hover:bg-blue-50/50 transition-colors">
                                        <td class="px-6 py-4 font-medium text-slate-900">
                                            {{ patient.name }}<br>
                                            <span 
                                                class="text-xs text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded-full mt-1 inline-block"
                                            >
                                                {{ patient.patient_id || 'ID: ' + patient.id }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-slate-800">{{ patient.phone_number || '-' }}</span>
                                        </td>
                                        <td class="px-6 py-4">{{ new Date(patient.created_at).toLocaleDateString() }}</td>
                                        <td class="px-6 py-4 flex gap-2">
                                            <Link 
                                                v-if="patient.type === 'user'"
                                                :href="route('admin.patients.show', patient.id)" 
                                                class="text-blue-600 hover:text-blue-800 font-bold transition-colors"
                                            >
                                                ดูรายละเอียด
                                            </Link>
                                            <Link 
                                                v-else
                                                :href="route('admin.patients.guest.show', patient.booking_id)" 
                                                class="text-blue-600 hover:text-blue-800 font-bold transition-colors"
                                            >
                                                ดูรายละเอียด
                                            </Link>

                                            <!-- Sell Package Button (Only for Registered Users) -->
                                            <button 
                                                v-if="patient.type === 'user'"
                                                @click="openSellModal(patient)"
                                                class="text-green-600 hover:text-green-800 font-bold transition-colors text-sm"
                                            >
                                                ขายแพ็คเกจ
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="patients.data.length === 0">
                                        <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                            ไม่พบข้อมูลคนไข้
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-end">
                        <Pagination :links="patients.links" />
                    </div>

                </div>
            </div>
        </div>

        <!-- Register Patient Modal -->
        <Modal :show="showRegisterModal" @close="closeRegisterModal">
            <div class="p-6 max-h-[90vh] overflow-y-auto">
                <h2 class="text-xl font-bold text-gray-900 mb-6">
                    ลงทะเบียนคนไข้ใหม่
                </h2>
                
                <form @submit.prevent="askToConfirmRegister" class="space-y-4">
                    <!-- Section: Personal Information -->
                    <div>
                        <h3 class="text-lg font-medium text-blue-900 border-b pb-2 mb-4">ข้อมูลส่วนตัว</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="name" value="ชื่อ-นามสกุล *" />
                                <TextInput id="name" v-model="registerForm.name" class="mt-1 block w-full" required />
                                <InputError :message="registerForm.errors.name" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="phone_number" value="เบอร์โทรศัพท์" />
                                <TextInput id="phone_number" v-model="registerForm.phone_number" class="mt-1 block w-full" />
                                <InputError :message="registerForm.errors.phone_number" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="id_card_number" value="เลขบัตรประชาชน / พาสปอร์ต" />
                                <TextInput id="id_card_number" v-model="registerForm.id_card_number" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <InputLabel for="date_of_birth" value="วันเกิด" />
                                <TextInput id="date_of_birth" type="date" v-model="registerForm.date_of_birth" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <InputLabel for="gender" value="เพศ" />
                                <select 
                                    id="gender" 
                                    v-model="registerForm.gender"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="">ระบุเพศ</option>
                                    <option value="Male">ชาย</option>
                                    <option value="Female">หญิง</option>
                                    <option value="Other">อื่นๆ</option>
                                </select>
                            </div>
                           <div>
                                <InputLabel for="occupation" value="อาชีพ" />
                                <TextInput id="occupation" v-model="registerForm.occupation" class="mt-1 block w-full" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <InputLabel for="race" value="เชื้อชาติ" />
                                <TextInput id="race" v-model="registerForm.race" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <InputLabel for="nationality" value="สัญชาติ" />
                                <TextInput id="nationality" v-model="registerForm.nationality" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <InputLabel for="religion" value="ศาสนา" />
                                <TextInput id="religion" v-model="registerForm.religion" class="mt-1 block w-full" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <InputLabel for="address" value="ที่อยู่" />
                            <textarea 
                                id="address" 
                                v-model="registerForm.address"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="2"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Section: Emergency Contact -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-blue-900 border-b pb-2 mb-4">ผู้ติดต่อฉุกเฉิน</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="emergency_contact_name" value="ชื่อผู้ติดต่อ" />
                                <TextInput id="emergency_contact_name" v-model="registerForm.emergency_contact_name" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <InputLabel for="emergency_contact_phone" value="เบอร์โทรศัพท์ผู้ติดต่อ" />
                                <TextInput id="emergency_contact_phone" v-model="registerForm.emergency_contact_phone" class="mt-1 block w-full" />
                            </div>
                        </div>
                    </div>

                    <!-- Section: Medical History -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-blue-900 border-b pb-2 mb-4">ประวัติการรักษา</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="drug_allergy" value="ประวัติแพ้ยา" />
                                <textarea 
                                    id="drug_allergy" 
                                    v-model="registerForm.drug_allergy"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="2"
                                ></textarea>
                            </div>
                             <div>
                                <InputLabel for="underlying_disease" value="โรคประจำตัว" />
                                <textarea 
                                    id="underlying_disease" 
                                    v-model="registerForm.underlying_disease"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="2"
                                ></textarea>
                            </div>
                             <div>
                                <InputLabel for="surgery_history" value="ประวัติการผ่าตัด" />
                                <textarea 
                                    id="surgery_history" 
                                    v-model="registerForm.surgery_history"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="2"
                                ></textarea>
                            </div>
                             <div>
                                <InputLabel for="accident_history" value="ประวัติอุบัติเหตุ" />
                                <textarea 
                                    id="accident_history" 
                                    v-model="registerForm.accident_history"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="2"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <SecondaryButton @click="closeRegisterModal"> ยกเลิก </SecondaryButton>
                        <PrimaryButton 
                            class="bg-blue-600 hover:bg-blue-700" 
                            :class="{ 'opacity-25': registerForm.processing }"
                            :disabled="registerForm.processing"
                        >
                            ถัดไป: ยืนยันข้อมูล
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Confirmation Modal -->
        <Modal :show="showConfirmRegisterModal" @close="closeConfirmRegisterModal" maxWidth="sm">
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    ยืนยันการลงทะเบียน?
                </h2>
                <div class="text-slate-600 mb-6">
                    <p class="mb-2">คุณแน่ใจหรือไม่ที่จะบันทึกข้อมูลคนไข้นี้?</p>
                    <ul class="text-sm list-disc list-inside space-y-1 bg-slate-50 p-3 rounded-md">
                        <li>Name: <strong>{{ registerForm.name }}</strong></li>
                        <li>Phone: {{ registerForm.phone_number || '-' }}</li>
                        <li>ID: {{ registerForm.id_card_number || '-' }}</li>
                    </ul>
                </div>

                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="closeConfirmRegisterModal" class="border-red-200 text-red-600 hover:bg-red-50"> 
                        ไม่, แก้ไขข้อมูล
                    </SecondaryButton>
                    <PrimaryButton 
                        class="bg-green-600 hover:bg-green-700 focus:ring-green-500"
                        @click="submitRegister"
                        :class="{ 'opacity-25': registerForm.processing }"
                        :disabled="registerForm.processing"
                    >
                         ใช่, บันทึกข้อมูล
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Sell Package Modal -->
        <Modal :show="showSellModal" @close="closeSellModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    ขายแพ็คเกจให้กับ {{ selectedPatient?.name }}
                </h2>

                <div class="mt-4">
                    <InputLabel for="package_select" value="เลือกแพ็คเกจ" />
                    <select
                        id="package_select"
                        v-model="sellForm.service_package_id"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    >
                        <option value="" disabled>กรุณาเลือกแพ็คเกจ...</option>
                        <option v-for="pkg in availablePackages" :key="pkg.id" :value="pkg.id">
                            {{ pkg.name }} - {{ Number(pkg.price).toLocaleString() }} THB ({{ pkg.total_sessions }} ครั้ง)
                        </option>
                    </select>
                    <InputError :message="sellForm.errors.service_package_id" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeSellModal"> ยกเลิก </SecondaryButton>
                    <PrimaryButton 
                        class="ml-3 bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-900 ring-green-500"
                        :class="{ 'opacity-25': sellForm.processing }"
                        :disabled="sellForm.processing || !sellForm.service_package_id"
                        @click="submitSellPackage"
                    >
                        ยืนยันการขาย
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>
