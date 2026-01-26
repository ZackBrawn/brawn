<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineOptions({
    layout: UserLayout,
});

const props = defineProps({
    order: {
        type: Object,
        required: true,
    }
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getImageUrl = (imagePath) => {
    if (!imagePath) return '/images/placeholder.png';
    if (imagePath.startsWith('http')) return imagePath;
    if (!imagePath.startsWith('storage/')) return `/storage/${imagePath}`;
    return `/${imagePath}`;
};
const proofForm = useForm({
    payment_proof: null,
});

const handleFileUpload = (event) => {
    proofForm.payment_proof = event.target.files[0];
};

const submitProof = () => {
    proofForm.post(route('orders.upload-proof', props.order.id), {
        preserveScroll: true,
        onSuccess: () => {
            proofForm.reset();
        },
    });
};
</script>

<template>
    <Head :title="`Order #${order.id}`" />

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Order Status & ID -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border-t-4" 
                :class="{
                    'border-yellow-500': order.status === 'Menunggu Pembayaran',
                    'border-blue-500': order.status === 'Dibayar' || order.status === 'Diproses',
                    'border-indigo-500': order.status === 'Dikirim',
                    'border-green-500': order.status === 'Selesai',
                    'border-red-500': order.status === 'Dibatalkan'
                }">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-2xl font-bold text-gray-900">Order #{{ order.id }}</h1>
                        <span class="px-3 py-1 rounded-full text-sm font-medium"
                            :class="{
                                'bg-yellow-100 text-yellow-800': order.status === 'Menunggu Pembayaran',
                                'bg-blue-100 text-blue-800': order.status === 'Dibayar' || order.status === 'Diproses',
                                'bg-indigo-100 text-indigo-800': order.status === 'Dikirim',
                                'bg-green-100 text-green-800': order.status === 'Selesai',
                                'bg-red-100 text-red-800': order.status === 'Dibatalkan'
                            }">
                            {{ order.status }}
                        </span>
                    </div>
                    <p class="text-gray-600 text-sm">Dibuat pada: {{ formatDate(order.created_at) }}</p>
                </div>
            </div>

            <!-- Payment Instructions (Only if Pending) -->
            <div v-if="order.status === 'Menunggu Pembayaran' && order.payment_method" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Instruksi Pembayaran
                    </h2>
                    <div class="bg-indigo-50 rounded-lg p-5 border border-indigo-100">
                        <p class="text-gray-700 mb-2">Silakan transfer sebesar <span class="font-bold text-indigo-700">{{ formatPrice(order.grand_total) }}</span> ke rekening berikut:</p>
                        
                        <div class="mt-4 space-y-2">
                            <div class="flex justify-between border-b border-indigo-200 pb-2">
                                <span class="text-gray-600">Bank</span>
                                <span class="font-semibold text-gray-900">{{ order.payment_method.name }}</span>
                            </div>
                            <div class="flex justify-between border-b border-indigo-200 pb-2">
                                <span class="text-gray-600">Nomor Rekening</span>
                                <span class="font-mono font-bold text-lg text-gray-900 tracking-wider">{{ order.payment_method.account_number }}</span>
                            </div>
                            <div class="flex justify-between pt-1">
                                <span class="text-gray-600">Atas Nama</span>
                                <span class="font-medium text-gray-900">{{ order.payment_method.account_name }}</span>
                            </div>
                        </div>

                        <div class="mt-6 text-sm text-gray-600">
                            <p>Setelah melakukan transfer, pesanan Anda akan otomatis diproses setelah kami memverifikasi pembayaran Anda.Jika mengalami kendala silahkan hubungi admin kami di <a href="https://wa.me/628123456789" class="text-blue-500 hover:text-blue-900 font-semibold text-lg">WhatsApp</a>
                            </p>
                            <p class="text-red-500 font-semibold">Pesana di anggap batal jika tidak melakukan pembayaran dalam waktu 1x24 jam</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Bukti Pembayaran -->
            <div v-if="order.status === 'Menunggu Pembayaran' || order.bukti_pembayaran" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Bukti Pembayaran
                    </h2>

                    <!-- Show uploaded proof if exists -->
                    <div v-if="order.bukti_pembayaran" class="mb-6">
                        <p class="text-sm text-gray-600 mb-2">Bukti pembayaran yang Anda unggah:</p>
                        <div class="border rounded-lg p-2 inline-block">
                            <img :src="getImageUrl(order.bukti_pembayaran)" alt="Bukti Pembayaran" class="max-w-full h-auto max-h-64 rounded">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Status: <span class="font-medium text-indigo-600" v-if="order.status === 'Menunggu Pembayaran'">Sedang diverifikasi admin</span><span v-else>{{ order.status }}</span></p>
                    </div>

                    <!-- Upload Form -->
                    <div v-if="order.status === 'Menunggu Pembayaran'" class="bg-gray-50 rounded-lg p-5 border border-dashed border-gray-300">
                        <form @submit.prevent="submitProof">
                            <div class="mb-4">
                                <label for="payment_proof" class="block text-sm font-medium text-gray-700 mb-1">Unggah Bukti Transfer</label>
                                <input 
                                    type="file" 
                                    id="payment_proof" 
                                    @change="handleFileUpload"
                                    accept="image/*"
                                    class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-indigo-50 file:text-indigo-700
                                        hover:file:bg-indigo-100"
                                />
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maks: 2MB.</p>
                                <div v-if="proofForm.errors.payment_proof" class="text-red-600 text-sm mt-1">
                                    {{ proofForm.errors.payment_proof }}
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <PrimaryButton :disabled="proofForm.processing || !proofForm.payment_proof">
                                    {{ proofForm.processing ? 'Mengunggah...' : 'Kirim Bukti Pembayaran' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Shipping Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Alamat Pengiriman
                    </h2>
                    <div v-if="order.address" class="text-gray-700">
                        <p class="font-medium text-lg">{{ order.address.recipient_name }}</p>
                        <p class="text-sm font-medium text-gray-500 mb-1">{{ order.address.label }}</p>
                        <p>{{ order.address.full_address }}</p>
                        <p>{{ order.address.city }}, {{ order.address.province }} {{ order.address.postal_code }}</p>
                    </div>
                    <div v-else class="text-gray-500 italic">
                        Informasi alamat tidak tersedia.
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Rincian Produk</h2>
                    <div class="flow-root">
                        <ul role="list" class="-my-6 divide-y divide-gray-200">
                            <li v-for="item in order.order_items" :key="item.id" class="py-6 flex">
                                <div class="flex-shrink-0 h-24 w-24 border border-gray-200 rounded-md overflow-hidden bg-gray-100">
                                    <img 
                                        :src="getImageUrl(item.product && item.product.image_url)" 
                                        :alt="item.product?.name" 
                                        class="w-full h-full object-center object-cover"
                                    >
                                </div>

                                <div class="ml-4 flex-1 flex flex-col">
                                    <div>
                                        <div class="flex justify-between text-base font-medium text-gray-900">
                                            <h3>
                                                <a href="#">{{ item.product?.name || 'Produk dihapus' }}</a>
                                            </h3>
                                            <p class="ml-4">{{ formatPrice(item.price * item.quantity) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex-1 flex items-end justify-between text-sm">
                                        <p class="text-gray-500">Qty {{ item.quantity }} x {{ formatPrice(item.price) }}</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-200 p-6 bg-gray-50">
                    <div class="flex justify-between text-base font-medium text-gray-900">
                        <p>Total Pembayaran</p>
                        <p class="text-xl text-indigo-600">{{ formatPrice(order.grand_total) }}</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-center">
                <Link :href="route('dashboard')" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Kembali ke Beranda
                </Link>
            </div>
        </div>
    </div>
</template>
