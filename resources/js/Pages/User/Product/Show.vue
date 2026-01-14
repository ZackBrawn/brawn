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
    console.log('User wishlist:', wishlist); // Debug log
    console.log('Current product ID:', props.product.id); // Debug log
    return wishlist;
});

// Cek apakah produk ada di wishlist dengan debugging
const isInWishlist = computed(() => {
    // Pastikan user sudah login sebelum melakukan pengecekan
    if (!page.props.auth?.user) {
        console.log('User not logged in');
        return false;
    }

    // Debug struktur data wishlist
    if (userWishlist.value.length > 0) {
        console.log('Full wishlist item structure:', userWishlist.value[0]);
        console.log('Available keys:', Object.keys(userWishlist.value[0]));
    }

    // Cek apakah produk ini ada di dalam array wishlist yang dikirim dari server
    const found = userWishlist.value.some(item => {
        // Coba berbagai kemungkinan nama property
        const itemProductId = item.product_id || item.id || item.productId || item;
        console.log('Comparing:', itemProductId, 'with', props.product.id);
        console.log('Item structure:', item);

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

    console.log('Is in wishlist:', found);
    return found;
});

// Watch untuk debugging perubahan wishlist
watch(() => page.props.wishlist, (newWishlist) => {
    console.log('Wishlist changed:', newWishlist);
}, { deep: true });

const toggleWishlist = async () => {
    // Check if user is logged in
    if (!page.props.auth?.user) {
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

const updateCartQuantity = (newQty) => {
    if (cartLoading.value) return;

    // Check stock limit for manual input
    if (newQty > props.product.stock) {
        toast.error(`Stok hanya tersedia ${props.product.stock}`);
        newQty = props.product.stock;
        // Return checks if no change needed actually depends on current value which we might not have clean ref to if user typed it
        // But inertia reload will fix UI
    }

    if (newQty <= 0) {
        if (cartItem.value) {
            cartLoading.value = true;
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
        }
        return;
    }

    cartLoading.value = true;

    // If item exists, update. If not, add (backend handles add vs update usually, but here we used specific endpoints in ProductCard)
    // Actually ProductCard uses cart.add for init, then cart.update.
    // Let's mirror ProductCard logic more closely for consistency.

    if (cartItem.value) {
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
    } else {
        router.post(route('cart.add', props.product.id), {
            quantity: newQty
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
    }
};

const addToCart = () => {
    updateCartQuantity(1);
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

    <Head :title="product.name" />

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li>
                        <div class="flex items-center">
                            <Link :href="route('home')" class="text-sm font-medium text-gray-700 hover:text-indigo-600">
                                Beranda</Link>
                        </div>
                    </li>
                    <li v-if="product.category">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                            <Link :href="route('categories.show', product.category.slug)"
                                class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2">{{
                                    product.category.name }}</Link>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ product.name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="w-full md:w-1/3 lg:w-1/2">
                            <div class="bg-gray-100 rounded-lg p-4 flex items-center justify-center">
                                <img :src="product.image_url || 'https://via.placeholder.com/300x300?text=No+Image'"
                                    :alt="product.name" class="max-h-64 w-auto object-contain" />
                            </div>
                        </div>

                        <div class="w-full md:w-2/3 lg:w-3/4">
                            <div class="flex justify-between items-start">
                                <h1 class="text-2xl font-bold text-gray-900">{{ product.name }}</h1>
                                <div class="relative">
                                    <button type="button"
                                        @click="!page.props.auth?.user ? toast.error('Silakan login terlebih dahulu untuk menambahkan ke wishlist') : toggleWishlist()"
                                        :disabled="wishlistLoading"
                                        class="p-2 rounded-full transition-all duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                        :title="!page.props.auth?.user ? 'Login untuk menambahkan ke wishlist' : (isInWishlist ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist')">
                                        <!-- Loading spinner -->
                                        <svg v-if="wishlistLoading" class="h-6 w-6 animate-spin text-gray-400"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>

                                        <!-- Heart icon - dengan debugging visual -->
                                        <svg v-else class="h-6 w-6 transition-colors duration-200"
                                            :class="isInWishlist ? 'text-red-500' : 'text-gray-400 hover:text-red-400'"
                                            viewBox="0 0 24 24" :fill="isInWishlist ? 'currentColor' : 'none'"
                                            :stroke="isInWishlist ? 'none' : 'currentColor'"
                                            :stroke-width="isInWishlist ? '0' : '2'">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>

                                    <!-- Tooltip for non-logged in users -->
                                    <div v-if="!page.props.auth?.user"
                                        class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap opacity-0 pointer-events-none transition-opacity duration-200 hover:opacity-100">
                                        Login diperlukan
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ formatPrice(product.price) }}
                                </p>

                                <div class="mt-4 space-y-2">
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500 w-20">Kategori</span>
                                        <Link v-if="product.category"
                                            :href="route('categories.show', product.category.slug)"
                                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500">{{
                                                product.category.name }}</Link>
                                    </div>

                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500 w-20">Supplier</span>
                                        <span v-if="product.supplier" class="text-sm text-yellow-700">{{
                                            product.supplier.name }}</span>
                                        <span v-else class="text-sm text-gray-500">-</span>
                                    </div>

                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500 w-20">Stok</span>
                                        <span class="text-sm font-medium"
                                            :class="product.stock > 0 ? 'text-green-600' : 'text-red-600'">
                                            {{ product.stock > 0 ? 'Tersedia' : 'Stok Habis' }}
                                            <span v-if="product.stock > 0" class="text-gray-500">({{ product.stock }}
                                                pcs)</span>
                                        </span>
                                    </div>

                                    <div class="pt-4">
                                        <div v-if="cartItem"
                                            class="inline-flex items-center justify-between border border-indigo-200 rounded-md bg-indigo-50 shadow-sm">
                                            <button @click="updateCartQuantity(cartItem.quantity - 1)"
                                                :disabled="cartLoading"
                                                class="px-3 py-2 text-indigo-700 hover:bg-indigo-100 transition-colors disabled:opacity-50 border-r border-indigo-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <div class="relative w-16">
                                                <input type="number" :value="cartItem.quantity"
                                                    @change="(e) => updateCartQuantity(parseInt(e.target.value))"
                                                    class="w-full text-center text-base text-indigo-900 font-bold bg-transparent border-none focus:ring-0 p-0 appearance-none [-moz-appearance:_textfield] [&::-webkit-inner-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none"
                                                    :max="product.stock" min="1" />
                                                <div v-if="cartLoading"
                                                    class="absolute inset-0 flex items-center justify-center bg-indigo-50 bg-opacity-80">
                                                    <svg class="animate-spin h-4 w-4 text-indigo-600"
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
                                            <button @click="updateCartQuantity(cartItem.quantity + 1)"
                                                :disabled="cartLoading || cartItem.quantity >= product.stock"
                                                class="px-3 py-2 text-indigo-700 hover:bg-indigo-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed border-l border-indigo-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>

                                        <button v-else type="button" @click="addToCart"
                                            class="flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                            :disabled="product.stock <= 0 || cartLoading">
                                            <svg v-if="cartLoading" class="animate-spin h-5 w-5 mr-2 text-white"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            Tambah ke Keranjang
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Deskripsi Produk</h3>
                        <div class="prose max-w-none">
                            <p v-if="product.description" class="text-gray-600">{{ product.description }}</p>
                            <p v-else class="text-gray-400 italic">Tidak ada deskripsi produk.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>