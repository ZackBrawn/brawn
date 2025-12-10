<template>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">Edit Metode Pembayaran: {{ paymentMethod.name }}</h2>
                <Link :href="route('admin.payment-methods.index')"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Kembali ke Daftar
                </Link>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <!-- Nama Bank -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Bank <span class="text-red-500">*</span></label>
                                <input type="text" id="name" v-model="form.name" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-300': form.errors.name }" />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <!-- Nomor Rekening -->
                            <div>
                                <label for="account_number" class="block text-sm font-medium text-gray-700">Nomor Rekening <span class="text-red-500">*</span></label>
                                <input type="text" id="account_number" v-model="form.account_number" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-300': form.errors.account_number }" />
                                <p v-if="form.errors.account_number" class="mt-1 text-sm text-red-600">{{ form.errors.account_number }}</p>
                            </div>

                            <!-- Atas Nama -->
                            <div>
                                <label for="account_name" class="block text-sm font-medium text-gray-700">Atas Nama <span class="text-red-500">*</span></label>
                                <input type="text" id="account_name" v-model="form.account_name" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-300': form.errors.account_name }" />
                                <p v-if="form.errors.account_name" class="mt-1 text-sm text-red-600">{{ form.errors.account_name }}</p>
                            </div>

                            <!-- is_active -->
                            <div class="flex items-center">
                                <input id="is_active" name="is_active" type="checkbox" v-model="form.is_active" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700">
                                    Aktifkan metode pembayaran ini
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end space-x-3">
                                <Link :href="route('admin.payment-methods.index')"
                                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Batal
                                </Link>
                                <button type="submit"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    :disabled="form.processing">
                                    <span v-if="form.processing">Menyimpan...</span>
                                    <span v-else>Simpan Perubahan</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({
    layout: AdminLayout,
});

const props = defineProps({
    paymentMethod: {
        type: Object,
        required: true
    }
});

const form = useForm({
    _method: 'put',
    name: props.paymentMethod.name,
    account_number: props.paymentMethod.account_number,
    account_name: props.paymentMethod.account_name,
    is_active: props.paymentMethod.is_active,
});

const submit = () => {
    form.post(route('admin.payment-methods.update', props.paymentMethod.id));
};
</script>
