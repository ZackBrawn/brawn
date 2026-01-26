<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import Pagination from '@/Components/Pagination.vue';

// Definisikan layout untuk halaman ini
defineOptions({ layout: UserLayout });

// Definisikan props yang diterima dari controller
const props = defineProps({
    auth: {
        type: Object,
        default: () => ({
            user: null
        })
    },
    products: {
        type: Object,
        default: () => ({})
    },
});

const user = computed(() => props.auth?.user);
</script>

<template>

    <Head title="Dashboard User" />
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div v-if="user" class="bg-white bg-opacity-50 overflow-hidden shadow-lg sm:rounded-lg p-6">
                <h1
                    class="text-3xl text-center font-bold bg-gradient-to-r from-indigo-500 to-blue-300 bg-clip-text text-transparent">
                    Selamat datang,<span
                        class="bg-gradient-to-r from-blue-300 to-indigo-500 bg-clip-text text-transparent">{{ user?.name
                        }}</span>!
                </h1>
                <div class="text-center text-sm text-gray-500 mt-3">
                    {{ user.email }}
                </div>
            </div>

            <div class="mt-8">
                <h2
                    class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent mb-4">
                    Produk Terbaru</h2>
                <div v-if="products.data && products.data.length > 0">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
                    </div>
                    <div class="mt-6 flex justify-center">
                        <Pagination :links="products.links" />
                    </div>
                </div>
                <div v-else
                    class="bg-white bg-opacity-50 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent">
                    Produk yang anda cari belum tersedia.
                </div>
            </div>
        </div>
    </div>
</template>