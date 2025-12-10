<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    notifications: Object,
});

const form = useForm({});

const markAsRead = (id) => {
    form.post(route('notifications.mark-as-read', id), {
        preserveScroll: true,
    });
};

const markAllAsRead = () => {
    form.post(route('notifications.mark-all-as-read'), {
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="Notifikasi" />

    <UserLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h1
                    class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent mb-8">
                    Notifikasi</h1>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div v-if="notifications.data.length > 0" class="flex justify-between items-center mb-6">
                        <button @click="markAllAsRead"
                            class="text-sm text-indigo-600 hover:text-indigo-900 font-medium focus:outline-none">
                            Tandai semua sudah dibaca
                        </button>
                    </div>

                    <div v-if="notifications.data.length > 0" class="space-y-4">
                        <div v-for="notification in notifications.data" :key="notification.id"
                            class="flex items-start p-4 rounded-lg border"
                            :class="{ 'bg-indigo-50 border-indigo-100': !notification.read_at, 'bg-white border-gray-200': notification.read_at }">

                            <div class="flex-shrink-0">
                                <svg v-if="notification.type.includes('Success')" class="h-6 w-6 text-green-500"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg v-else-if="notification.type.includes('Error')" class="h-6 w-6 text-red-500"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg v-else class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>

                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900">
                                        {{ notification.data.title || 'Notifikasi' }}
                                    </h4>
                                    <span class="text-xs text-gray-500">
                                        {{ new Date(notification.created_at).toLocaleString('id-ID') }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ notification.data.message || 'Tidak ada konten.' }}
                                </p>
                                <div v-if="!notification.read_at" class="mt-2 text-right">
                                    <button @click="markAsRead(notification.id)"
                                        class="text-xs text-indigo-600 hover:text-indigo-900 font-medium">
                                        Tandai dibaca
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 flex justify-center">
                            <template v-for="(link, key) in notifications.links" :key="key">
                                <div v-if="link.url === null"
                                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded"
                                    v-html="link.label" />
                                <Link v-else
                                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
                                    :class="{ 'bg-indigo-600 text-white': link.active }" :href="link.url"
                                    v-html="link.label" />
                            </template>
                        </div>

                    </div>

                    <div v-else class="text-center py-6">
                        <svg class="mx-auto h-24 w-24 text-indigo-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <h3
                            class="mt-2 text-lg font-medium bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent">
                            Tidak ada notifikasi</h3>
                        <p
                            class="mt-1 text-sm bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent">
                            Anda belum
                            memiliki notifikasi apapun saat ini.</p>
                        <div class="mt-6">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
