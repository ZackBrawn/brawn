<template>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">Edit Kategori: {{ form.name }}</h2>
                <Link :href="route('admin.categories.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Kembali ke Daftar
                </Link>
            </div>

            <!-- Formulir -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <form @submit.prevent="submit">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="space-y-6">
                            <!-- Nama Kategori -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                                <div class="mt-1">
                                    <input type="text" id="name" v-model="form.name" :class="{'border-red-500': form.errors.name}" class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md">
                                    <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                                </div>
                            </div>

                            <!-- Gambar Kategori -->
                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700">Gambar Kategori</label>
                                <div class="mt-1 flex items-center">
                                    <span class="h-16 w-16 rounded-md overflow-hidden bg-gray-100">
                                        <img v-if="displayImage" :src="displayImage" :alt="category.name" class="h-full w-full object-cover">
                                        <svg v-else class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </span>
                                    <div class="ml-4">
                                        <label for="image" class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Ganti Gambar
                                        </label>
                                        <input id="image" name="image" type="file" class="sr-only" accept="image/*" @change="onFileChange">
                                    </div>
                                </div>
                                <p v-if="form.errors.image" class="mt-2 text-sm text-red-600">{{ form.errors.image }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 space-x-3">
                        <Link :href="route('admin.categories.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Batal
                        </Link>
                        <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span v-if="form.processing">Memperbarui...</span>
                            <span v-else>Perbarui Kategori</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// Definisikan layout dan props
defineOptions({ layout: AdminLayout });
const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
});

// State untuk form, diinisialisasi dengan data dari prop
const form = useForm({
    _method: 'put',
    name: props.category.name,
    image: null,
    description: props.category.description, // Tambahkan kolom deskripsi
});

// State untuk preview gambar
const imagePreview = ref(null);

// Computed property untuk menampilkan gambar saat ini atau preview baru
const displayImage = computed(() => {
    if (imagePreview.value) {
        return imagePreview.value;
    }
    if (props.category.image) {
        return `/storage/${props.category.image}`;
    }
    return null;
});

// Tangani perubahan file
const onFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => (imagePreview.value = e.target.result);
        reader.readAsDataURL(file);
    } else {
        form.image = null;
        imagePreview.value = null;
    }
};

// Kirim form update
const submit = () => {
    form.post(route('admin.categories.update', props.category.id), {
        onSuccess: () => {
            // Reset input file setelah sukses
            imagePreview.value = null;
        },
    });
};
</script>
