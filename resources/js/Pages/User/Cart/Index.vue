<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { computed } from 'vue';
import { useToast } from 'vue-toastification';

defineOptions({
    layout: UserLayout
});

const props = defineProps({
    cart: {
        type: Array,
        default: () => []
    }
});

const toast = useToast();

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const total = computed(() => {
    return props.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
});

const updateQuantity = (item, quantity) => {
    if (quantity < 1) return;

    router.post(route('cart.update', item.id), {
        quantity: quantity
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // toast.success('Keranjang diperbarui');
        }
    });
};

const removeItem = (item) => {
    if (confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) {
        router.delete(route('cart.remove', item.id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Produk dihapus dari keranjang');
            }
        });
    }
};

const getImageUrl = (imagePath) => {
    if (!imagePath) return '/images/placeholder.png'; // Fallback
    if (imagePath.startsWith('http')) return imagePath;
    return `/storage/${imagePath}`;
};
</script>

<template>

    <Head title="Keranjang Belanja" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1
                class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent mb-8">
                Keranjang Belanja</h1>

            <div v-if="cart.length > 0" class="flex flex-col lg:flex-row gap-8">
                <!-- Cart Items -->
                <div class="lg:w-2/3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <ul role="list" class="divide-y divide-gray-200">
                            <li v-for="item in cart" :key="item.id" class="p-6 flex sm:flex-row flex-col items-center">
                                <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                                    <img :src="getImageUrl(item.image)" :alt="item.name"
                                        class="h-full w-full object-cover object-center">
                                </div>

                                <div class="ml-4 flex flex-1 flex-col">
                                    <div>
                                        <div class="flex justify-between text-base font-medium text-gray-900">
                                            <h3>
                                                <Link :href="route('products.show', item.id)">{{ item.name }}</Link>
                                            </h3>
                                            <p class="ml-4">{{ formatPrice(item.price * item.quantity) }}</p>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">{{ formatPrice(item.price) }} / item</p>
                                    </div>
                                    <div class="flex flex-1 items-end justify-between text-sm">
                                        <div class="flex items-center border border-gray-300 rounded-md">
                                            <button @click="updateQuantity(item, parseInt(item.quantity) - 1)"
                                                class="px-3 py-1 text-gray-600 hover:bg-gray-100 disabled:opacity-50"
                                                :disabled="item.quantity <= 1">-</button>
                                            <span class="px-3 py-1 text-gray-900 font-medium">{{ item.quantity }}</span>
                                            <button @click="updateQuantity(item, parseInt(item.quantity) + 1)"
                                                class="px-3 py-1 text-gray-600 hover:bg-gray-100">+</button>
                                        </div>

                                        <div class="flex">
                                            <button type="button" @click="removeItem(item)"
                                                class="font-medium text-indigo-600 hover:text-indigo-500">Hapus</button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:w-1/3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Pesanan</h2>

                        <div class="flow-root">
                            <dl class="-my-4 text-sm divide-y divide-gray-200">
                                <div class="py-4 flex items-center justify-between">
                                    <dt class="text-gray-600">Subtotal</dt>
                                    <dd class="font-medium text-gray-900">{{ formatPrice(total) }}</dd>
                                </div>
                                <div class="py-4 flex items-center justify-between">
                                    <dt class="text-gray-600">Pengiriman</dt>
                                    <dd class="font-medium text-gray-900">Gratis</dd>
                                </div>
                                <div class="py-4 flex items-center justify-between border-t border-gray-200">
                                    <dt class="text-base font-medium text-gray-900">Total Biaya</dt>
                                    <dd class="text-base font-bold text-indigo-600">{{ formatPrice(total) }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="mt-6">
                            <Link :href="route('checkout.index')"
                                class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Checkout
                            </Link>
                        </div>
                        <div class="mt-6 flex justify-center text-center text-sm text-gray-500">
                            <p>
                                atau
                                <Link :href="route('products.index')"
                                    class="font-medium text-indigo-600 hover:text-indigo-500">
                                    Lanjut Belanja
                                    <span aria-hidden="true"> &rarr;</span>
                                </Link>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty Cart -->
            <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-6 py-12 text-center">
                <svg class="mx-auto h-24 w-24 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.182 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3
                    class="mt-2 text-lg font-medium bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent">
                    Keranjang Anda kosong</h3>
                <p class="mt-1 text-sm bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent">
                    Sepertinya Anda belum menambahkan produk apapun ke keranjang.</p>
                <div class="mt-6">
                    <Link :href="route('products.index')"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gradient-to-r from-indigo-600 to-blue-400 hover:from-indigo-500 hover:to-blue-300 border-0 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Mulai Belanja
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
