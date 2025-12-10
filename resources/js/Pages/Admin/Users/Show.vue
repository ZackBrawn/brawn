<template>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">Detail Pengguna: {{ user.name }}</h2>
                <div class="flex space-x-3">
                    <Link :href="route('admin.users.edit', user.id)" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Edit
                    </Link>
                    <Link :href="route('admin.users.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Kembali
                    </Link>
                </div>
            </div>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                        <div class="lg:col-span-1">
                            <div class="px-4 sm:px-0">
                                <div class="text-center">
                                    <div class="mx-auto h-32 w-32 rounded-full bg-gray-200 flex items-center justify-center text-4xl font-bold text-gray-600 mb-4">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ user.name }}</h3>
                                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                                    <div class="mt-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'">
                                            {{ user.role === 'admin' ? 'Admin' : 'User' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-8 border-t border-gray-200 pt-6">
                                    <dl class="space-y-4">
                                        <div v-if="user.phone" class="sm:grid sm:grid-cols-3 sm:gap-4">
                                            <dt class="text-sm font-medium text-gray-500">Telepon</dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ user.phone }}</dd>
                                        </div>
                                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                                            <dt class="text-sm font-medium text-gray-500">Bergabung</dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                                {{ formatDate(user.created_at) }}
                                                <span class="text-gray-500 text-xs">({{ formatTimeAgo(user.created_at) }})</span>
                                            </dd>
                                        </div>
                                        <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                                            <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ formatDateTime(user.updated_at) }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-2">
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div class="px-4 py-5 sm:px-6">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">Aktivitas Terbaru</h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Riwayat aktivitas pengguna.</p>
                                </div>
                                <div class="border-t border-gray-200">
                                    <dl>
                                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">Belum ada aktivitas</dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                                Tidak ada aktivitas yang tercatat untuk pengguna ini.
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });
const props = defineProps({
    user: { type: Object, required: true }
});

const formatDate = (dateString) => {
    const options = { day: 'numeric', month: 'short', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};
const formatDateTime = (dateString) => {
    const options = { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};
const formatTimeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    let interval = Math.floor(seconds / 31536000);
    if (interval >= 1) return `${interval} tahun yang lalu`;
    interval = Math.floor(seconds / 2592000);
    if (interval >= 1) return `${interval} bulan yang lalu`;
    interval = Math.floor(seconds / 86400);
    if (interval >= 1) return `${interval} hari yang lalu`;
    interval = Math.floor(seconds / 3600);
    if (interval >= 1) return `${interval} jam yang lalu`;
    interval = Math.floor(seconds / 60);
    if (interval >= 1) return `${interval} menit yang lalu`;
    return 'baru saja';
};
</script>
