<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ProductCard from '@/Components/ProductCard.vue';

const props = defineProps({
    user: Object,
    products: Array,
});
</script>

<template>

    <Head title="Welcome" />
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="text-center">
                <h1 class="text-2xl font-bold mb-4">Our Application</h1>

                <div class="mt-4">
                    <Link :href="route('login')"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:from-indigo-700 hover:to-blue-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Log in
                    </Link>

                    <Link :href="route('register')"
                        class="ml-4 inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:from-indigo-700 hover:to-blue-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Register
                    </Link>
                </div>
            </div>
        </div>
    </div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div v-if="user" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Selamat datang, {{ user.name }}!</h1>
                <p class="text-gray-600">Ini adalah dashboard user Anda. Di sini Anda bisa melihat produk terbaru,
                    pesanan terakhir, dan informasi lainnya.</p>
            </div>

            <div class="mt-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Produk Terbaru</h2>
                <div v-if="products.length > 0"
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <ProductCard v-for="product in products" :key="product.id" :product="product" />
                </div>
                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    Tidak ada produk yang tersedia saat ini.
                </div>
            </div>
        </div>
    </div>
</template>
