<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';

// Definisikan layout untuk halaman ini
defineOptions({
    layout: AdminLayout,
});

// Define props received from the controller
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_products: 0,
            total_stock: 0,
            sold_stock: 0,
            total_orders: 0,
            pending_orders: 0,
            completed_orders: 0,
            total_customers: 0,
            low_stock_products: 0,
            today_sales: 0,
            week_sales: 0,
            month_sales: 0,
            year_sales: 0,
            today_sales_formatted: 'Rp 0',
            week_sales_formatted: 'Rp 0',
            month_sales_formatted: 'Rp 0',
            year_sales_formatted: 'Rp 0',
        }),
    },
    recent_orders: {
        type: Array,
        default: () => [],
    },
    monthly_sales: {
        type: Array,
        default: () => [],
    },
});

// Access data safely using computed properties
const stats = computed(() => props.stats || {});
const recentOrders = computed(() => props.recent_orders || []);
const monthlySales = computed(() => props.monthly_sales || []);

// Helper untuk memformat mata uang
const formatCurrency = (amount) => {
    if (amount === null || amount === undefined || isNaN(amount)) {
        return 'Rp 0';
    }
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(Number(amount));
};

// Helper untuk mendapatkan nama bulan saat ini
const currentMonth = new Date().toLocaleString('id-ID', { month: 'long' });
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
        <!-- ... -->
    </div>
            <!-- Tampilkan pesan error dari backend jika ada -->
    <div v-if="$page.props.flash?.error" class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
        <p class="font-bold">Error!</p>
        <p>{{ $page.props.flash.error }}</p>
    </div>

            <!-- Kartu Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Pesanan -->
                <Link :href="route('admin.orders.index')" class="block">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200 cursor-pointer h-full">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Total Pesanan</h3>
                                    <p class="text-2xl font-bold">{{ stats.total_orders || 0 }}</p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <span class="text-green-600 font-medium">+{{ stats.today_orders || 0 }} hari ini</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </Link>

                <!-- Pesanan Menunggu Pembayaran -->
                <Link :href="route('admin.orders.index', { status: 'Menunggu Pembayaran' })" class="block">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200 cursor-pointer h-full">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Menunggu Pembayaran</h3>
                                    <p class="text-2xl font-bold">{{ stats.pending_orders || 0 }}</p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ stats.completed_orders || 0 }} pesanan selesai
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </Link>

                <!-- Total Produk -->
                <Link :href="route('admin.products.index')" class="block">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200 cursor-pointer h-full">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Total Produk</h3>
                                    <p class="text-2xl font-bold">{{ stats.total_products || 0 }}</p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <span class="font-medium">{{ stats.total_stock || 0 }}</span> stok tersedia
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <span class="font-medium">{{ stats.sold_stock || 0 }}</span> terjual
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </Link>

                <!-- Total Pelanggan -->
                <Link :href="route('admin.users.index')" class="block">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200 cursor-pointer h-full">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Total Pelanggan</h3>
                                    <p class="text-2xl font-bold">{{ stats.total_customers || 0 }}</p>
                                    <p class="text-sm text-gray-500 mt-1">Pengguna terdaftar</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Pesanan Terbaru -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-medium text-gray-900">Pesanan Terbaru</h2>
                                <Link :href="route('admin.orders.index')" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    Lihat Semua
                                </Link>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                #{{ order.id }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ order.user?.name || 'Tamu' }}</div>
                                                <div class="text-sm text-gray-500">{{ order.created_at }}</div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="px-2 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ formatCurrency(order.total_raw || 0) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span :class="[
                                                    'px-2 py-1 inline-flex text-sm leading-5 font-semibold rounded-full',
                                                    order.status === 'Selesai' ? 'bg-green-100 text-green-800' :
                                                    order.status === 'Dibatalkan' ? 'bg-red-100 text-red-800' :
                                                    'bg-yellow-100 text-yellow-800'
                                                ]">
                                                    {{ order.status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                <Link 
                                                    :href="route('admin.orders.show', order.id)" 
                                                    class="inline-flex justify-center items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                >
                                                    <svg class="h-3.5 w-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Detail
                                                </Link>
                                            </td>
                                        </tr>
                                        <tr v-if="recentOrders.length === 0">
                                            <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">
                                                Belum ada pesanan
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Penjualan -->
                <div>
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Ringkasan {{ currentMonth }}</h2>
                            <div class="space-y-4">
                                <div class="p-4 bg-blue-50 rounded-lg">
                                    <div class="text-sm font-medium text-gray-500">Total Penjualan</div>
                                    <div class="mt-1 text-2xl font-semibold text-gray-900">
                                        {{ formatCurrency(stats.month_sales || 0) }}
                                    </div>
                                    <div class="mt-1 text-sm text-gray-500">
                                        {{ stats.completed_orders || 0 }} pesanan selesai
                                    </div>
                                </div>
                                
                                <div class="p-4 bg-green-50 rounded-lg">
                                    <div class="text-sm font-medium text-gray-500">Pesanan Hari Ini</div>
                                    <div class="mt-1 text-2xl font-semibold text-gray-900">
                                        {{ stats.today_orders || 0 }}
                                    </div>
                                    <div class="mt-1 text-sm text-gray-500">
                                        {{ formatCurrency(stats.today_sales || 0) }} total
                                    </div>
                                </div>
                                
                                <div class="p-4 bg-yellow-50 rounded-lg">
                                    <div class="text-sm font-medium text-gray-500">Menunggu Pembayaran</div>
                                    <div class="mt-1 text-2xl font-semibold text-gray-900">
                                        {{ stats.pending_orders || 0 }}
                                    </div>
                                    <div class="mt-1 text-sm text-gray-500">
                                        Perlu konfirmasi pembayaran
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>