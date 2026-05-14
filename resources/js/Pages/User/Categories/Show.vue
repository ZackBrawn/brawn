<script setup>
import { Head, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';

defineOptions({
    layout: UserLayout,
});

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
});

// Format price to IDR currency
const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(price);
};

const getImageUrl = (path) => {
    // If the path is already a full URL, return it as is
    if (!path) return '';
    if (path.startsWith('http') || path.startsWith('https') || path.startsWith('//')) {
        return path;
    }
    // If it's a storage path (starts with 'category-images/'), convert to storage URL
    if (path.startsWith('category-images/')) {
        return `/storage/${path}`;
    }
    // Otherwise, assume it's a relative path from the public directory
    return `/${path.replace(/^\//, '')}`;
};
</script>

<template>

    <Head :title="`Kategori ${category.name}`" />

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Category Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex items-center">
                            <div
                                class="w-20 h-20 bg-gray-50 rounded-full overflow-hidden mb-3 flex-shrink-0 flex items-center justify-center border border-gray-100">
                                <img v-if="category.image" :src="getImageUrl(category.image)" :alt="category.name"
                                    class="w-3/4 h-3/4 object-contain" />
                                <div v-else class="w-full h-full flex items-center justify-center bg-gray-50">
                                    <svg class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h1 class="text-2xl font-semibold text-gray-900">{{ category.name }}</h1>
                                <p class="text-gray-600 mt-1">{{ category.products?.length || 0 }} produk tersedia</p>
                            </div>
                        </div>
                        <div>
                            <Link 
                                :href="route('categories.index')" 
                                class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Kembali ke Kategori
                            </Link>
                        </div>
                    </div>
                    <p v-if="category.description" class="mt-4 text-gray-600">
                        {{ category.description }}
                    </p>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="!category.products" class="text-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
                <p class="mt-4 text-sm text-gray-600">Memuat produk...</p>
            </div>

            <!-- Products Grid -->
            <div v-else-if="category.products && category.products.length > 0"
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4">
                <div v-for="product in category.products" :key="product.id" class="w-full">
                    <ProductCard :product="product" class="w-full h-full" />
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12 bg-white rounded-lg shadow-sm">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada produk</h3>
                <p class="mt-1 text-sm bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent">Belum ada produk yang tersedia dalam kategori ini.</p>
                <div class="mt-6">
                    <Link :href="route('categories.index')"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Kembali ke Daftar Kategori
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
