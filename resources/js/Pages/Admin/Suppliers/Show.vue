<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Detail Supplier</h1>
                    </div>
                    <div class="flex space-x-3">
                        <Link :href="route('admin.suppliers.edit', supplier.id)"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Edit
                        </Link>
                        <button type="button"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            @click="showDeleteModal = true">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-6 py-6">
            <div class="bg-white shadow overflow-hidden rounded-lg">
                <!-- Supplier Information -->
                <div class="px-6 py-5">
                    <h2 class="text-lg font-medium text-gray-900 mb-6 pb-4 border-b border-gray-200">Informasi Supplier</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        <div class="space-y-1">
                            <dt class="text-sm font-medium text-gray-500">Nama Supplier</dt>
                            <dd class="text-base text-gray-900 font-medium">{{ supplier.name }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-sm font-medium text-gray-500">Telepon</dt>
                            <dd class="text-base">
                                <a :href="'https://wa.me/62' + supplier.phone" class="text-indigo-600 hover:text-indigo-800 hover:underline"
                                    target="_blank" rel="noopener noreferrer">
                                    {{ supplier.phone || '-' }}
                                </a>
                            </dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-sm font-medium text-gray-500">Jumlah Produk</dt>
                            <dd class="text-base">
                                <span
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    {{ supplier.products_count || 0 }} Produk
                                </span>
                            </dd>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-200 space-y-4">
                        <div class="space-y-1">
                            <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                            <dd class="text-base text-gray-900">{{ supplier.address || '-' }}</dd>
                        </div>
                        <div v-if="supplier.contact_person" class="space-y-1">
                            <dt class="text-sm font-medium text-gray-500">Nama Kontak Person</dt>
                            <dd class="text-base text-gray-900">{{ supplier.contact_person }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="mt-6">
                <div class="bg-white shadow overflow-hidden rounded-lg">
                    <div class="px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-medium text-gray-900">Daftar Produk</h2>
                            <span class="text-sm text-gray-500">Total: {{ supplier.products_count || 0 }} produk</span>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4">
                        <div v-if="supplier.products && supplier.products.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="product in supplier.products" :key="product.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex-shrink-0 h-16 w-16">
                                                <img v-if="product.image_url" :src="`/storage/${product.image_url}`" :alt="product.name" class="h-full w-full object-cover rounded">
                                                <div v-else class="h-full w-full bg-gray-200 flex items-center justify-center text-gray-400 rounded">
                                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ product.category?.name || 'Tidak Berkategori' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ formatCurrency(product.price) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                {{ product.stock || 0 }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada produk</h3>
                            <p class="mt-1 text-sm text-gray-500">Belum ada produk yang terdaftar untuk supplier ini.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 flex justify-end">
                <Link :href="route('admin.suppliers.index')"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar Supplier
                </Link>
            </div>

            <!-- Delete Confirmation Modal -->
            <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full mx-4"
                    @click.stop>
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Hapus Supplier</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Apakah Anda yakin ingin menghapus supplier <strong>{{ supplier.name }}</strong>?
                                    </p>
                                    <div v-if="supplier.products_count > 0" class="mt-4 p-4 bg-yellow-50 text-yellow-700 rounded-md">
                                        <p class="font-medium">Peringatan!</p>
                                        <p class="text-sm mt-1">
                                            Terdapat {{ supplier.products_count }} produk yang terkait dengan supplier ini.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                            :disabled="deleteProcessing" @click="deleteSupplier">
                            <span v-if="deleteProcessing">Menghapus...</span>
                            <span v-else>Hapus</span>
                        </button>
                        <button type="button" @click="showDeleteModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useToast } from 'vue-toastification';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    supplier: { type: Object, required: true },
});

const toast = useToast();
const showDeleteModal = ref(false);
const deleteProcessing = ref(false);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};

const deleteSupplier = () => {
    deleteProcessing.value = true;
    
    router.delete(route('admin.suppliers.destroy', props.supplier.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            toast.success('Supplier berhasil dihapus!');
        },
        onError: (errors) => {
            deleteProcessing.value = false;
            showDeleteModal.value = false;
            const errorMessage = errors.error || Object.values(errors).flat().join(' ');
            toast.error(errorMessage);
        },
    });
};
</script>
