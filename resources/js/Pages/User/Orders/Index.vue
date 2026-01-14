<script setup>
import { Head, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

defineOptions({
    layout: UserLayout,
});

const props = defineProps({
    orders: {
        type: Array,
        default: () => [],
    },
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>

<template>

    <Head title="Pesanan Saya" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1
                class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent mb-8">
                Pesanan Saya</h1>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div v-if="orders.length > 0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID Pesanan
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Detail</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="order in orders" :key="order.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                        #{{ order.id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(order.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-green-100 text-green-800': order.status === 'completed',
                                                'bg-yellow-100 text-yellow-800': order.status === 'pending',
                                                'bg-red-100 text-red-800': order.status === 'cancelled',
                                                'bg-blue-100 text-blue-800': order.status === 'processing'
                                            }">
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatPrice(order.total_price) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('orders.show', order.id)"
                                            class="text-indigo-600 hover:text-indigo-900">Detail</Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div v-else class="text-center py-6">
                    <svg class="mx-auto h-24 w-24 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <h3
                        class="mt-2 text-lg font-medium bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent">
                        Belum Ada Pesanan</h3>
                    <p class="mt-1 text-sm bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent">
                        Anda belum melakukan pemesanan apapun.</p>
                    <div class="mt-6">
                        <Link :href="route('home')"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gradient-to-r from-indigo-600 to-blue-400 hover:from-indigo-500 hover:to-blue-300 border-0 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Mulai Belanja
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
