<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';

defineOptions({
    layout: UserLayout
});

const page = usePage();
const props = defineProps({
    wishlist: {
        type: Array,
        default: () => []
    },
    products: {
        type: Object,
        default: () => ({
            data: []
        })
    }
});

// Create a reactive wishlist array that we can modify
const wishlist = ref([...props.wishlist]);

// Compute wishlist product IDs for easy checking
const wishlistProductIds = computed(() => {
    return wishlist.value.map(item => item.product_id);
});

// Handle wishlist updates from child components
const handleWishlistUpdate = (updatedWishlist) => {
    wishlist.value = updatedWishlist;

    // If the wishlist becomes empty, we need to update the products list
    if (updatedWishlist.length === 0) {
        props.products.data = [];
    }
};
</script>

<template>

    <Head title="Wishlist Saya" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1
                class="text-3xl font-bold mb-8 bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent">
                Wishlist Saya</h1>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div v-if="products.data.length > 0"
                    class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6">
                    <div v-for="product in products.data" :key="product.id" class="w-full">
                        <ProductCard :product="product" :is-in-wishlist="wishlistProductIds.includes(product.id)"
                            :wishlist="wishlist" @wishlist-updated="handleWishlistUpdate"
                            :key="`product-${product.id}-${wishlistProductIds.includes(product.id) ? 'in-wishlist' : 'not-in-wishlist'}`" />
                    </div>
                </div>

                <div v-else class="text-center py-6">
                    <svg class="mx-auto h-24 w-24 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <h3
                        class="mt-2 text-lg font-medium bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent">
                        Wishlist Kosong</h3>
                    <p class="mt-1 text-sm bg-gradient-to-r from-blue-600 to-indigo-400 bg-clip-text text-transparent">
                        Tambahkan produk ke wishlist Anda untuk melihatnya di sini.</p>
                    <div class="mt-6">
                            <Link :href="route('home')"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gradient-to-r from-indigo-600 to-blue-400 hover:from-indigo-500 hover:to-blue-300 border-0 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Jelajahi Produk
                            </Link>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="products.data.length > 0" class="mt-8 flex justify-center">
                    <div class="flex items-center space-x-1">
                        <template v-for="(link, key) in products.links" :key="key">
                            <Link v-if="link.url !== null" :href="link.url" v-html="link.label"
                                class="px-4 py-2 border rounded-md text-sm font-medium" :class="{
                                    'bg-indigo-600 text-white border-indigo-600': link.active,
                                    'border-gray-300 text-gray-500 hover:bg-gray-50': !link.active
                                }" />
                            <span v-else v-html="link.label" class="px-4 py-2 text-gray-500 text-sm" />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
