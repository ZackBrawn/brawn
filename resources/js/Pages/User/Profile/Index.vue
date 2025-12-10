<script setup>
import { Head } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { Link } from '@inertiajs/vue3';

defineOptions({
    layout: UserLayout,
});

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profil Saya" />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6">Profil Saya</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Profile Information -->
                        <div class="md:col-span-2 space-y-6">
                            <!-- Profile Info Card -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-6">
                                        <h3 class="text-lg font-medium text-gray-900">Informasi Profil</h3>
                                        <Link 
                                            :href="route('profile.edit')" 
                                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                                        >
                                            Edit
                                        </Link>
                                    </div>
                                    <div class="space-y-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Nama</p>
                                            <p class="mt-1 text-sm text-gray-900">{{ $page.props.auth.user.name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Email</p>
                                            <p class="mt-1 text-sm text-gray-900">{{ $page.props.auth.user.email }}</p>
                                            <p v-if="!$page.props.auth.user.email_verified_at" class="mt-1 text-sm text-yellow-600">
                                                Email Anda belum terverifikasi.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Actions -->
                        <div class="space-y-6">
                            <!-- Account Status -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status Akun</h3>
                                    <div class="flex items-center">
                                        <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center mr-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Akun Aktif</p>
                                            <p class="text-sm text-gray-500">Terdaftar sejak {{ new Date($page.props.auth.user.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Account -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Hapus Akun</h3>
                                    <p class="text-sm text-gray-600 mb-4">
                                        Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen.
                                    </p>
                                    <Link 
                                        :href="route('profile.destroy')" 
                                        method="delete" 
                                        as="button"
                                        class="text-sm font-medium text-red-600 hover:text-red-500"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus akun Anda? Tindakan ini tidak dapat dibatalkan.')"
                                    >
                                        Hapus Akun
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
