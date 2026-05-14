<script setup>
import { Head, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref } from 'vue';

defineOptions({
    layout: UserLayout,
});

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
});

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

const imageErrors = ref({});

const handleImageError = (id) => {
    imageErrors.value[id] = true;
};
</script>

<template>

    <Head title="Kategori Produk" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1
                class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent mb-8">
                Daftar Kategori</h1>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    <Link v-for="category in categories" :key="category.id"
                        :href="route('categories.show', category.slug)"
                        class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col h-full">
                        <div class="p-4 flex-1 flex flex-col items-center shadow-2xl border border-gray-100">
                            <div
                                class="w-28 h-28 bg-gray-200 rounded-full overflow-hidden mb-3 flex-shrink-0 flex items-center justify-center border border-gray-100">
                                <img v-if="category.image && !imageErrors[category.id]" 
                                    :src="getImageUrl(category.image)" 
                                    :alt="category.name"
                                    class="w-full h-full object-cover" 
                                    @error="handleImageError(category.id)" />
                                <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-50 to-blue-100">
                                    <svg class="h-10 w-10 text-indigo-300" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <h3 class="text-lg font-medium text-gray-900 text-center">{{ category.name }}</h3>
                                <p class="mt-1 text-sm text-gray-500 text-center">{{ category.products_count }} produk
                                </p>
                            </div>
                        </div>
                    </Link>
                </div>

                <div v-if="categories.length === 0" class="text-center py-12">
                    <p class="bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent">Belum ada
                        kategori yang tersedia.</p>
                </div>
            </div>
        </div>
    </div>
</template>
