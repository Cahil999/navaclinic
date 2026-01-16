<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    patients: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Patients" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                All Patients
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Name</th>
                                        <th scope="col" class="px-6 py-3">Email</th>
                                        <th scope="col" class="px-6 py-3">Joined</th>
                                        <th scope="col" class="px-6 py-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="patient in patients.data" :key="patient.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ patient.name }}</td>
                                        <td class="px-6 py-4">{{ patient.email }}</td>
                                        <td class="px-6 py-4">{{ new Date(patient.created_at).toLocaleDateString() }}</td>
                                        <td class="px-6 py-4">
                                            <Link :href="route('admin.patients.show', patient.id)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                                View Details
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="patients.data.length === 0">
                                        <td colspan="4" class="px-6 py-4 text-center">No patients found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination could be added here -->
                        <div v-if="patients.links && patients.links.length > 3" class="mt-4">
                            <!-- TODO: Pagination Component -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
