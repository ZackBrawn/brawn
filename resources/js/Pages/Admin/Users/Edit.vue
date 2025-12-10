<template>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">Edit: {{ user.name }}</h2>
                <Link :href="route('admin.users.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Kembali ke Daftar Pengguna
                </Link>
            </div>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Informasi Akun</h3>
                                <p class="mt-1 text-sm text-gray-500">Perbarui informasi akun pengguna.</p>
                            </div>
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                    <input type="text" id="name" v-model="form.name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" :class="{ 'border-red-500': form.errors.name }">
                                    <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                                    <input type="email" id="email" v-model="form.email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" :class="{ 'border-red-500': form.errors.email }">
                                    <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{ form.errors.email }}</p>
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                                    <input type="password" id="password" v-model="form.password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" :class="{ 'border-red-500': form.errors.password }">
                                    <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">{{ form.errors.password }}</p>
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                                    <input type="password" id="password_confirmation" v-model="form.password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" :class="{ 'border-red-500': form.errors.password_confirmation }">
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                                    <select id="role" v-model="form.role" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm" :class="{ 'border-red-500': form.errors.role }">
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    <p v-if="form.errors.role" class="mt-2 text-sm text-red-600">{{ form.errors.role }}</p>
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                    <input type="text" id="phone" v-model="form.phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" :class="{ 'border-red-500': form.errors.phone }">
                                    <p v-if="form.errors.phone" class="mt-2 text-sm text-red-600">{{ form.errors.phone }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-8 flex justify-end space-x-3">
                            <Link :href="route('admin.users.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Batal
                            </Link>
                            <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                                <span v-if="form.processing">Menyimpan...</span>
                                <span v-else>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { onMounted } from 'vue';
import { useToast } from 'vue-toastification';

defineOptions({ layout: AdminLayout });
const props = defineProps({
    user: { type: Object, required: true }
});

const form = useForm({
    _method: 'put',
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    role: props.user.role,
    phone: props.user.telepon || '' // Menggunakan 'telepon' dari prop, tapi diubah menjadi 'phone' di form
});

const toast = useToast();
const page = usePage();

const submit = () => {
    form.post(route('admin.users.update', props.user.id), {
        onSuccess: () => {
            toast.success('Pengguna berhasil diperbarui!');
        },
        onError: (errors) => {
            const errorMessage = Object.values(errors).flat().join(' ');
            toast.error(errorMessage);
        },
    });
};

onMounted(() => {
    if (page.props.flash.success) toast.success(page.props.flash.success);
    if (page.props.flash.error) toast.error(page.props.flash.error);
});
</script>
