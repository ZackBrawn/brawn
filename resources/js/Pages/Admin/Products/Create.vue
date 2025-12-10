<template>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">Tambah Produk Baru</h2>
                <Link :href="route('admin.products.index')"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                Kembali ke Daftar Produk
                </Link>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <form @submit.prevent="submit">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <!-- Nama Produk -->
                            <div class="sm:col-span-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <input type="text" id="name" v-model="form.name"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        :class="{ 'border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.name }">
                                </div>
                                <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <!-- Kategori -->
                            <div class="sm:col-span-4">
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <select id="category_id" v-model="form.category_id"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                        :class="{ 'border-red-300 text-red-900': form.errors.category_id }">
                                        <option value="">Pilih Kategori</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>
                                <p v-if="form.errors.category_id" class="mt-2 text-sm text-red-600">{{ form.errors.category_id }}</p>
                            </div>

                            <!-- Supplier -->
                            <div class="sm:col-span-4">
                                <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <select id="supplier_id" v-model="form.supplier_id"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                        :class="{ 'border-red-300 text-red-900': form.errors.supplier_id }">
                                        <option value="">Pilih Supplier</option>
                                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                            {{ supplier.name }}
                                        </option>
                                    </select>
                                </div>
                                <p v-if="form.errors.supplier_id" class="mt-2 text-sm text-red-600">{{ form.errors.supplier_id }}</p>
                            </div>

                            <!-- Harga dan Stok -->
                            <div class="sm:col-span-2">
                                <label for="price" class="block text-sm font-medium text-gray-700">Harga (Rp) <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="text" id="price" 
                                        :value="formatPrice(form.price)"
                                        @input="handlePriceInput($event)"
                                        class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md font-mono"
                                        :class="{ 'border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.price }"
                                        @keydown.enter.prevent="$event.target.blur()">
                                </div>
                                <p v-if="form.errors.price" class="mt-2 text-sm text-red-600">{{ form.errors.price }}</p>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="stock" class="block text-sm font-medium text-gray-700">Stok <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <input type="number" id="stock" v-model.number="form.stock" min="0"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        :class="{ 'border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.stock }">
                                </div>
                                <p v-if="form.errors.stock" class="mt-2 text-sm text-red-600">{{ form.errors.stock }}</p>
                            </div>

                            <!-- Deskripsi -->
                            <div class="sm:col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <div class="mt-1">
                                    <textarea id="description" v-model="form.description" rows="3"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                        :class="{ 'border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.description }"></textarea>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Deskripsi singkat tentang produk Anda.</p>
                                <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
                            </div>

                            <!-- Gambar Produk -->
                            <div class="sm:col-span-6">
                                <label class="block text-sm font-medium text-gray-700">
                                    Gambar Produk <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 flex items-center">
                                    <span class="inline-block h-24 w-24 rounded-md overflow-hidden bg-gray-100">
                                        <img v-if="imagePreview" :src="imagePreview" alt="Preview" class="h-full w-full object-cover">
                                        <svg v-else class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </span>
                                    <div class="ml-4">
                                        <label for="image_url" class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Pilih Gambar
                                        </label>
                                        <input id="image_url" type="file" class="sr-only" accept="image/*" @change="onFileChange">
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Format: JPG, PNG, GIF (Maks. 2MB)</p>
                                <p v-if="form.errors.image_url" class="mt-2 text-sm text-red-600">{{ form.errors.image_url }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <Link :href="route('admin.products.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Batal
                        </Link>
                        <button type="submit"
                            class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Simpan Produk</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });
const props = defineProps({
    categories: { type: Array, default: () => [] },
    suppliers: { type: Array, default: () => [] },
});

const imagePreview = ref(null);
const form = useForm({
    name: '',
    category_id: '',
    supplier_id: '',
    price: 0,
    stock: 0,
    description: '',
    image_url: null,
});

const onFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image_url = file;
        const reader = new FileReader();
        reader.onload = (e) => (imagePreview.value = e.target.result);
        reader.readAsDataURL(file);
    } else {
        form.image_url = null;
        imagePreview.value = null;
    }
};

const formatPrice = (value) => {
    if (!value && value !== 0) return '';
    // Convert to string, remove all non-digit characters, then format with dots
    const num = String(value).replace(/\D/g, '');
    return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

const handlePriceInput = (event) => {
    // Get the input value and remove all non-digit characters
    const value = event.target.value.replace(/\D/g, '');
    
    // Update the form price as a number
    form.price = value ? parseInt(value, 10) : 0;
    
    // Format the display value with dots
    event.target.value = formatPrice(value);
};

const submit = () => {
    // Ensure price is a number before submitting
    if (typeof form.price === 'string') {
        form.price = parseInt(form.price.replace(/\./g, ''), 10) || 0;
    }
    
    form.post(route('admin.products.store'), {
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
        },
    });
};
</script>
