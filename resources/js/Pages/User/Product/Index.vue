<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref, watch } from 'vue';

defineOptions({
    layout: UserLayout,
});

const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const search = ref(props.filters.search || '');
const categoryId = ref(props.filters.category_id || '');

const handleFilter = () => {
    router.get(route('products.index'), {
        search: search.value,
        category_id: categoryId.value,
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true,
    });
};

let timeout = null;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        handleFilter();
    }, 500);
});

watch(categoryId, () => {
    handleFilter();
});

const resetFilter = () => {
    search.value = '';
    categoryId.value = '';
    handleFilter();
};

</script>

<template>
    <Head title="Katalog Produk" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
                <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent">
                    Katalog Produk
                </h1>

                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <select 
                        v-model="categoryId" 
                        @change="handleFilter"
                        class="w-full sm:w-auto rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">Semua Kategori</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>

                    <div class="relative w-full sm:w-auto">
                        <input 
                            type="text" 
                            v-model="search" 
                            @keydown.enter="handleFilter"
                            placeholder="Cari produk..." 
                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 pr-10"
                        >
                        <button @click="handleFilter" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-indigo-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div v-if="products.data.length > 0">
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6">
                    <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
                </div>

                <!-- Pagination -->
                <div class="mt-10 flex justify-center">
                    <Pagination :links="products.links" />
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20 bg-white rounded-xl shadow-sm border border-gray-100">
                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900">Produk tidak ditemukan</h3>
                <p class="text-gray-500 mt-1">Coba sesuaikan kata kunci pencarian atau kategori Anda.</p>
                <button @click="resetFilter" class="mt-6 text-indigo-600 font-semibold hover:text-indigo-500 transition-colors">
                    Hapus Semua Filter
                </button>
            </div>
        </div>
    </div>
</template>
