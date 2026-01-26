<!-- Ganti bagian script setup untuk wishlist logic -->
<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref, computed, watch } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

defineOptions({
    layout: UserLayout,
});

const emit = defineEmits(['wishlist-updated']);

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const wishlistLoading = ref(false);
const showWishlistError = ref('');

// Mengambil data wishlist dari props halaman Inertia
const userWishlist = computed(() => {
    const wishlist = page.props.wishlist || [];
    return wishlist;
});

// Cek apakah produk ada di wishlist dengan debugging
const isInWishlist = computed(() => {
    // Pastikan user sudah login sebelum melakukan pengecekan
    if (!page.props.auth?.user) {
        console.log('User not logged in');
        return false;
    }
    // Cek apakah produk ini ada di dalam array wishlist yang dikirim dari server
    const found = userWishlist.value.some(item => {
        // Coba berbagai kemungkinan nama property
        const itemProductId = item.product_id || item.id || item.productId || item;

        // Jika item adalah langsung ID (number)
        if (typeof item === 'number') {
            return item === parseInt(props.product.id);
        }

        // Jika item adalah object dengan product_id
        if (item.product_id) {
            return parseInt(item.product_id) === parseInt(props.product.id);
        }

        // Jika item adalah object dengan id
        if (item.id) {
            return parseInt(item.id) === parseInt(props.product.id);
        }

        return false;
    });
    return found;
});

const toggleWishlist = async () => {
    // Check if user is logged in
    if (!page.props.auth?.user) {
        console.log('User not logged in');
        toast.error('Silakan login terlebih dahulu untuk menambahkan ke wishlist');
        return;
    }

    if (wishlistLoading.value) return;

    wishlistLoading.value = true;
    showWishlistError.value = '';

    try {
        const url = isInWishlist.value
            ? route('wishlist.remove', props.product.id)
            : route('wishlist.add', props.product.id);

        const method = isInWishlist.value ? 'delete' : 'post';

        const response = await axios({
            method,
            url,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            data: {}
        });

        if (response.data.success) {
            // Show success toast notification with product name
            const productName = props.product.name;
            if (isInWishlist.value) {
                toast.success(`"${productName}" berhasil dihapus dari wishlist`, {
                    timeout: 3000,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    icon: '❤️',
                });
            } else {
                toast.success(`"${productName}" berhasil ditambahkan ke wishlist`, {
                    timeout: 3000,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    icon: '❤️',
                });
            }

            // Update page props dengan wishlist terbaru
            if (response.data.wishlist) {
                page.props.wishlist = response.data.wishlist;
                emit('wishlist-updated', response.data.wishlist);
            }
        } else {
            const errorMessage = response.data.message || 'Gagal memperbarui wishlist';
            showWishlistError.value = errorMessage;
            toast.error(errorMessage);
            console.error('Wishlist operation failed:', response.data.message);
        }
    } catch (error) {
        let errorMessage = 'Terjadi kesalahan saat memperbarui wishlist';

        if (error.response) {
            if (error.response.status === 401) {
                errorMessage = 'Silakan login terlebih dahulu';
            } else if (error.response.status === 403) {
                errorMessage = 'Anda tidak memiliki akses untuk melakukan aksi ini';
            } else {
                errorMessage = error.response.data?.message || errorMessage;
            }
            console.error('Error response:', {
                status: error.response.status,
                data: error.response.data,
                headers: error.response.headers
            });
        } else if (error.request) {
            errorMessage = 'Tidak ada respon dari server';
            console.error('No response received:', error.request);
        } else {
            errorMessage = error.message || errorMessage;
            console.error('Request setup error:', error.message);
        }

        showWishlistError.value = errorMessage;
        toast.error(errorMessage);
    } finally {
        wishlistLoading.value = false;

        if (showWishlistError.value) {
            setTimeout(() => {
                showWishlistError.value = '';
            }, 5000);
        }
    }
};

const cartLoading = ref(false);

const cartItem = computed(() => {
    return page.props.cart?.find(item => item.id === props.product.id);
});

const addToCart = () => {
    if (cartLoading.value) return;
    cartLoading.value = true;

    router.post(route('cart.add', props.product.id), {
        quantity: 1
    }, {
        preserveScroll: true,
        onSuccess: () => {
            cartLoading.value = false;
            toast.success('Produk ditambahkan ke keranjang');
        },
        onError: () => {
            cartLoading.value = false;
            toast.error('Gagal menambahkan ke keranjang');
        }
    });
};

const updateCartQuantity = (newQty) => {
    if (cartLoading.value) return;
    cartLoading.value = true;

    if (newQty <= 0) {
        // Remove item
        router.delete(route('cart.remove', props.product.id), {
            preserveScroll: true,
            onSuccess: () => {
                cartLoading.value = false;
                toast.success('Produk dihapus dari keranjang');
            },
            onError: () => {
                cartLoading.value = false;
            }
        });
    } else {
        // Validate stock
        if (newQty > props.product.stock) {
            toast.error(`Stok hanya tersedia ${props.product.stock}`);
            newQty = props.product.stock;
            // Jika input manual melebihi stok, kita paksa update ke max stock tapi tidak reload jika sudah sama
            if (newQty === cartItem.value.quantity) {
                cartLoading.value = false;
                // Force update render key input hack if needed, but router interaction might be enough or simple return
                return;
            }
        }

        // Update quantity
        router.post(route('cart.update', props.product.id), {
            quantity: newQty
        }, {
            preserveScroll: true,
            onSuccess: () => {
                cartLoading.value = false;
            },
            onError: () => {
                cartLoading.value = false;
                toast.error('Gagal mengupdate keranjang');
            }
        });
    }
};

const getImageUrl = (imagePath) => {
    if (!imagePath) return '/images/placeholder.png';
    if (imagePath.startsWith('http')) return imagePath;
    if (!imagePath.startsWith('storage/')) return `/storage/${imagePath}`;
    return `/${imagePath}`;
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};
</script>

<template>

    <div
        class="group bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 h-80 w-52 flex flex-col relative">
        <!-- Error Message -->
        <div v-if="showWishlistError"
            class="absolute top-0 left-0 right-0 bg-red-100 text-red-700 text-xs p-2 text-center z-20">
            {{ showWishlistError }}
        </div>
        <!-- Wishlist Button - Only shows on hover -->
        <button type="button"
            @click="!page.props.auth?.user ? toast.error('Silakan login terlebih dahulu untuk menambahkan ke wishlist') : toggleWishlist()"
            :disabled="wishlistLoading"
            class="absolute top-2 right-2 p-2 bg-white rounded-full shadow-md hover:bg-gray-100 transition-all duration-200 z-10 opacity-0 group-hover:opacity-100"
            :title="!page.props.auth?.user ? 'Login untuk menambahkan ke wishlist' : (isInWishlist ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist')">
            <!-- Loading spinner -->
            <svg v-if="wishlistLoading" class="h-6 w-6 animate-spin text-gray-400" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>

            <!-- Heart icon - BAGIAN KUNCI untuk warna -->
            <svg v-else class="h-6 w-6 transition-colors duration-200"
                :class="isInWishlist ? 'text-red-500' : 'text-gray-400 hover:text-red-400'" viewBox="0 0 24 24"
                :fill="isInWishlist ? 'currentColor' : 'none'" :stroke="isInWishlist ? 'none' : 'currentColor'"
                :stroke-width="isInWishlist ? '0' : '2'">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </button>




        <Link :href="route('products.show', product.slug)">
            <div class="px-4 pt-0 pb-2">
                <img :src="getImageUrl(product.image_url)" :alt="product.name"
                    class="w-full h-32 object-contain transition-transform duration-300 hover:scale-105 border-x-2 border-b-2 rounded-b-lg border-gray-200"
                    loading="lazy" @error="handleImageError" />
            </div>
        </Link>
        <div class="px-4 pt-4 pb-6 flex-grow flex flex-col">
            <Link :href="route('products.show', product.slug)">
                <h3 class="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition-colors line-clamp-1" :title="product.name">
                    {{ product.name }}
                </h3>
            </Link>
            <div class="flex justify-between items-start mt-1">
                <p class="text-sm text-blue-500 line-clamp-1 pr-2" :title="product.category.name">{{ product.category.name }}</p>
                <p class="text-xs whitespace-nowrap">
                    <span :class="product.stock > 10 ? 'text-green-600' : 'text-red-500 font-bold'">Stok: {{
                        product.stock }}</span>
                </p>
            </div>
            <p v-if="product.supplier" class="text-sm text-gray-500 mt-1">
                {{ product.supplier.name }}
            </p>
            <div class="mt-auto pt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xl font-bold text-gray-900">{{ formatPrice(product.price) }}</span>
                </div>
                <div v-if="cartItem"
                    class="flex items-center justify-between border border-indigo-200 rounded-md bg-indigo-50 w-full">
                    <button @click="updateCartQuantity(cartItem.quantity - 1)" :disabled="cartLoading"
                        class="px-3 py-2 text-indigo-700 hover:bg-indigo-100 transition-colors disabled:opacity-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>
                    <div class="relative flex-1">
                        <input type="number" :value="cartItem.quantity"
                            @change="(e) => updateCartQuantity(parseInt(e.target.value))"
                            class="w-full text-center text-sm text-indigo-900 font-bold bg-transparent border-none focus:ring-0 p-0 appearance-none [-moz-appearance:_textfield] [&::-webkit-inner-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none"
                            :max="product.stock" min="1" />
                        <div v-if="cartLoading"
                            class="absolute inset-0 flex items-center justify-center bg-indigo-50 bg-opacity-80">
                            <svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <button @click="updateCartQuantity(cartItem.quantity + 1)"
                        :disabled="cartLoading || cartItem.quantity >= product.stock"
                        class="px-3 py-2 text-indigo-700 hover:bg-indigo-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>

                <button v-else @click="addToCart" :disabled="cartLoading"
                    class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed shadow-sm hover:shadow active:scale-95 transform duration-100">
                    <svg v-if="cartLoading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>{{ cartLoading ? 'Processing...' : 'Beli' }}</span>
                </button>
            </div>
        </div>

    </div>
</template>