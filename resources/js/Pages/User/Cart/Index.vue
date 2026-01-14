<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { computed, ref } from 'vue';
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

const processingItem = ref(null);

const updateQuantity = (item, quantity) => {
    if (processingItem.value) return; // Prevent double submit

    // Validasi stok
    if (quantity > item.stock) {
        toast.error(`Stok hanya tersedia ${item.stock}`);
        quantity = item.stock;
        if (quantity === parseInt(item.quantity)) return; // No change needed
    }

    if (quantity < 1) return;

    processingItem.value = item.id;

    router.post(route('cart.update', item.id), {
        quantity: quantity
    }, {
        preserveScroll: true,
        onSuccess: () => {
            processingItem.value = null;
            // toast.success('Keranjang diperbarui');
        },
        onError: () => {
            processingItem.value = null;
            toast.error('Gagal memperbarui keranjang');
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
                                        <div
                                            class="flex items-center border border-indigo-200 rounded-md bg-indigo-50 w-full sm:w-auto">
                                            <button @click="updateQuantity(item, parseInt(item.quantity) - 1)"
                                                class="px-3 py-1 text-indigo-700 hover:bg-indigo-100 transition-colors disabled:opacity-50"
                                                :disabled="item.quantity <= 1 || processingItem === item.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <div class="relative w-12 sm:w-16">
                                                <input type="number" :value="item.quantity"
                                                    @change="(e) => updateQuantity(item, parseInt(e.target.value))"
                                                    class="w-full text-center text-sm text-indigo-900 font-bold bg-transparent border-none focus:ring-0 p-0 appearance-none [-moz-appearance:_textfield] [&::-webkit-inner-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none"
                                                    :max="item.stock" min="1" />
                                                <div v-if="processingItem === item.id"
                                                    class="absolute inset-0 flex items-center justify-center bg-indigo-50 bg-opacity-80">
                                                    <svg class="animate-spin h-3 w-3 text-indigo-600"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <button @click="updateQuantity(item, parseInt(item.quantity) + 1)"
                                                class="px-3 py-1 text-indigo-700 hover:bg-indigo-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                :disabled="processingItem === item.id || item.quantity >= item.stock">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
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
                                <Link :href="route('home')" class="font-medium text-indigo-600 hover:text-indigo-500">
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
                    <Link :href="route('home')"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gradient-to-r from-indigo-600 to-blue-400 hover:from-indigo-500 hover:to-blue-300 border-0 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Mulai Belanja
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
