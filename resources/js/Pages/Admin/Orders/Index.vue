<script setup>
import { router, Link, Head, usePage } from '@inertiajs/vue3'; // Tambahkan 'Head' di sini
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref, watch, onMounted } from 'vue';
import { useToast } from 'vue-toastification';

defineOptions({
    layout: AdminLayout,
});

const props = defineProps({
    orders: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    statusCounts: {
        type: Object,
        default: () => ({}),
    },
    statusOptions: {
        type: Object,
        default: () => ({}),
    },
});

const toast = useToast();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || 'all');

const getStatusBadgeClass = (status) => {
    const classes = {
        'Menunggu Pembayaran': 'bg-yellow-100 text-yellow-800',
        'Dibayar': 'bg-blue-100 text-blue-800',
        'Diproses': 'bg-blue-100 text-blue-800',
        'Dikirim': 'bg-indigo-100 text-indigo-800',
        'Selesai': 'bg-green-100 text-green-800',
        'Dibatalkan': 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const formatCurrency = (amount) => {
    if (amount === null || amount === undefined || isNaN(amount)) {
        return 'Rp 0';
    }
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(Number(amount));
};

const updateFilters = () => {
    router.get(route('admin.orders.index'),
        { ...route().params, search: search.value || undefined, status: status.value === 'all' ? undefined : status.value, page: 1 },
        { preserveState: true, preserveScroll: true, replace: true }
    );
};

const updateOrderStatus = async (orderId, newStatus) => {
    if (!confirm(`Apakah Anda yakin ingin mengubah status pesanan menjadi "${newStatus}"?`)) {
        return;
    }

    try {
        await router.put(route('admin.orders.update-status', orderId), { status: newStatus }, {
            preserveState: true,
            onSuccess: () => {
                toast.success(`Status pesanan berhasil diubah menjadi "${newStatus}"`);
            },
            onError: (errors) => {
                const errorMessage = errors.message || Object.values(errors).flat().join(' ');
                toast.error(`Gagal mengubah status: ${errorMessage}`);
            }
        });
    } catch (error) {
        console.error('Update status error:', error);
        toast.error('Terjadi kesalahan. Silakan coba lagi.');
    }
};

watch(search, (value) => {
    updateFilters();
}, { debounce: 300 });

watch(status, (value) => {
    updateFilters();
});

onMounted(() => {
    const { flash } = usePage().props;
    if (flash?.success) {
        toast.success(flash.success);
    }
    if (flash?.error) {
        toast.error(flash.error);
    }
});
</script>

<template>
    <Head title="Daftar Pesanan" />
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">Daftar Pesanan</h1>
            </div>
            
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8 overflow-x-auto">
                    <button v-for="(label, value) in statusOptions" :key="value" @click="status = value" :class="[
                        status === value
                            ? 'border-indigo-500 text-indigo-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                    ]">
                        {{ label }}
                        <span v-if="statusCounts[value]" :class="[
                            status === value ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-900',
                            'ml-2 py-0.5 px-2 rounded-full text-xs font-medium'
                        ]">
                            {{ statusCounts[value] }}
                        </span>
                    </button>
                </nav>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex-1">
                        <label for="search" class="sr-only">Cari</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" v-model="search"
                                class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                                placeholder="Cari pesanan..." />
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No. Pesanan
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pelanggan
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium text-gray-900">
                                    #{{ order.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="order.user" class="space-y-1">
                                        <div class="text-sm font-medium text-gray-900">{{ order.user.name || 'Tamu' }}</div>
                                        <div class="text-sm text-gray-500">{{ order.user.telepon || '-' }}</div>
                                    </div>
                                    <div v-else class="text-sm text-gray-500">Data pelanggan tidak tersedia</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ formatCurrency(order.grand_total) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getStatusBadgeClass(order.status)]">
                                        {{ order.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ new Date(order.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}</div>
                                    <div class="text-xs text-gray-400">
                                        {{ new Date(order.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <button v-if="order.status === 'Menunggu Pembayaran'"
                                            @click="updateOrderStatus(order.id, 'Dibayar')"
                                            class="inline-flex justify-center items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <svg class="h-3.5 w-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Bayar
                                        </button>
                                        <button v-if="order.status === 'Dibayar'"
                                            @click="updateOrderStatus(order.id, 'Diproses')"
                                            class="inline-flex justify-center items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="h-3.5 w-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Proses
                                        </button>
                                        <button v-if="order.status === 'Diproses'"
                                            @click="updateOrderStatus(order.id, 'Dikirim')"
                                            class="inline-flex justify-center items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="h-3.5 w-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                            </svg>
                                            Kirim
                                        </button>
                                        <Link :href="route('admin.orders.show', order.id)"
                                            class="inline-flex justify-center items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        <svg class="h-3.5 w-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Detail
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="orders.data.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pesanan</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Tidak ada pesanan yang cocok dengan pencarian Anda.
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div v-if="orders.last_page > 1" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <Pagination :links="orders.links" />
                </div>
            </div>
        </div>
    </div>
</template>
