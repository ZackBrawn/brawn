<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    cart: {
        type: Array,
        default: () => []
    },
    auth: {
        type: Object,
        default: () => ({ user: {} })
    },
    paymentMethods: {
        type: Array,
        default: () => []
    },
    defaultAddress: {
        type: Object,
        default: () => null
    }
});

const confirmingCheckout = ref(false);

const form = useForm({
    name: props.auth?.user?.name || '',
    email: props.auth?.user?.email || '',
    address: props.defaultAddress?.full_address || '', 
    city: props.defaultAddress?.city || '',
    postal_code: props.defaultAddress?.postal_code || '',
    phone: props.auth?.user?.phone_number || '', 
    payment_method_id: null,
    note: ''
});

onMounted(() => {
    if (props.paymentMethods.length > 0) {
        form.payment_method_id = props.paymentMethods[0].id;
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

const subtotal = computed(() => {
    return props.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
});

const total = computed(() => subtotal.value);

const confirmCheckout = () => {
    confirmingCheckout.value = true;
};

const closeCheckoutModal = () => {
    confirmingCheckout.value = false;
};

const submit = () => {
    form.post(route('checkout.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeCheckoutModal();
        },
        onError: () => {
            closeCheckoutModal();
        }
    });
};

const getImageUrl = (imagePath) => {
    if (!imagePath) return '/images/placeholder.png';
    if (imagePath.startsWith('http')) return imagePath;
    return `/storage/${imagePath}`;
};
</script>

<template>
    <Head title="Checkout" />

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <Link :href="route('cart.index')" class="text-sm text-gray-600 hover:text-indigo-600 flex items-center gap-1 group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Keranjang
                </Link>
            </div>
            
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent mb-8">
                Checkout
            </h1>

            <form @submit.prevent="confirmCheckout">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Left Column: Shipping & Payment -->
                    <div class="lg:w-2/3 space-y-6">
                        
                        <!-- Shipping Information -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 transition-shadow hover:shadow-md">
                            <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                                <span class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm font-bold">1</span>
                                Informasi Pengiriman
                            </h2>
                            
                            <div v-if="defaultAddress" class="space-y-4">
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ form.name }}</p>
                                            <p class="text-gray-600 mt-1">{{ defaultAddress.full_address }}</p>
                                            <p class="text-gray-600">{{ defaultAddress.city }}, {{ defaultAddress.province }} {{ defaultAddress.postal_code }}</p>
                                            <p class="text-gray-600 mt-1">{{ form.phone }}</p>
                                        </div>
                                        <Link :href="route('profile.edit')" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                            Ubah Alamat
                                        </Link>
                                    </div>
                                </div>

                                <div>
                                    <InputLabel for="note" value="Catatan Tambahan (Opsional)" />
                                    <textarea 
                                        id="note" 
                                        v-model="form.note"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        rows="3"
                                        placeholder="Catatan untuk kurir"
                                    ></textarea>
                                </div>
                            </div>

                            <div v-else class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                <p class="text-gray-600 mb-4">Anda belum memiliki alamat pengiriman utama.</p>
                                <Link :href="route('profile.edit')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Tambah Alamat di Profil
                                </Link>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 transition-shadow hover:shadow-md">
                            <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                                <span class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm font-bold">2</span>
                                Metode Pembayaran
                            </h2>

                            <div v-if="paymentMethods.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label v-for="method in paymentMethods" :key="method.id" class="cursor-pointer relative">
                                    <input type="radio" v-model="form.payment_method_id" :value="method.id" class="peer sr-only">
                                    <div class="p-4 rounded-lg border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition-all text-center h-full flex flex-col items-center justify-center text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span class="font-medium text-gray-900">{{ method.name }}</span>
                                        <span v-if="method.account_number" class="text-xs text-gray-500 mt-1">{{ method.account_number }}</span>
                                    </div>
                                </label>
                            </div>
                            <div v-else class="text-center p-4 bg-yellow-50 rounded-md">
                                <p class="text-yellow-700 text-sm">Belum ada metode pembayaran yang tersedia.</p>
                            </div>
                            <InputError :message="form.errors.payment_method_id" class="mt-2" />
                        </div>
                    </div>

                    <!-- Right Column: Order Summary -->
                    <div class="lg:w-1/3">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 sticky top-6 border-t-4 border-indigo-500">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h2>

                            <!-- Item List (Collapsed) -->
                            <div class="flow-root mb-6 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                                <ul role="list" class="divide-y divide-gray-200">
                                    <li v-for="item in cart" :key="item.id" class="py-3 flex">
                                        <div class="flex-shrink-0 h-16 w-16 border border-gray-200 rounded-md overflow-hidden">
                                            <img :src="getImageUrl(item.image)" :alt="item.name" class="w-full h-full object-center object-cover">
                                        </div>

                                        <div class="ml-4 flex-1 flex flex-col">
                                            <div>
                                                <div class="flex justify-between text-base font-medium text-gray-900">
                                                    <h3 class="line-clamp-1">
                                                        <Link :href="route('products.show', item.id)">{{ item.name }}</Link>
                                                    </h3>
                                                    <p class="ml-4">{{ formatPrice(item.price * item.quantity) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex-1 flex items-end justify-between text-sm">
                                                <p class="text-gray-500">Qty {{ item.quantity }}</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <div v-if="cart.length === 0" class="text-center py-4 text-gray-500 italic">
                                    Keranjang kosong
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-4 space-y-4">
                                <div class="flex items-center justify-between text-sm">
                                    <dt class="text-gray-600">Subtotal</dt>
                                    <dd class="font-medium text-gray-900">{{ formatPrice(subtotal) }}</dd>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <dt class="text-gray-600">Pengiriman</dt>
                                    <dd class="font-medium text-green-600">Gratis</dd>
                                </div>
                                <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                                    <dt class="text-lg font-bold text-gray-900">Total</dt>
                                    <dd class="text-xl font-bold text-indigo-600">{{ formatPrice(total) }}</dd>
                                </div>
                            </div>

                            <div class="mt-8">
                                <PrimaryButton 
                                    class="w-full justify-center py-4 text-lg shadow-lg hover:shadow-xl transition-all" 
                                    :class="{ 'opacity-25 cursor-not-allowed': form.processing || cart.length === 0 || paymentMethods.length === 0 || !defaultAddress }" 
                                    :disabled="form.processing || cart.length === 0 || paymentMethods.length === 0 || !defaultAddress"
                                >
                                    Bayar Sekarang
                                </PrimaryButton>
                                <p v-if="cart.length === 0" class="text-red-500 text-xs text-center mt-2">
                                    Keranjang Anda kosong.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirm Checkout Modal -->
    <Modal :show="confirmingCheckout" @close="closeCheckoutModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Konfirmasi Pesanan
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Apakah Anda yakin ingin melanjutkan pesanan ini? Pastikan semua data sudah benar.
            </p>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeCheckoutModal">
                    Batal
                </SecondaryButton>

                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="submit"
                >
                    Ya, Pesan Sekarang
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>