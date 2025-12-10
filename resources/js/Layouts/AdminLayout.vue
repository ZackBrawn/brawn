<script setup>
import { Link, router } from '@inertiajs/vue3';
// Memperbaiki impor Ziggy untuk memastikan fungsi route tersedia
import { route } from 'ziggy-js';
import { useToast } from 'vue-toastification';
import 'vue-toastification/dist/index.css';

// Initialize toast
const toast = useToast(); 

defineProps({
    title: String
});

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('admin.dashboard')">
                                <span class="text-xl font-bold">Zackpanel</span>
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <Link :href="route('admin.dashboard')" :class="{'border-indigo-400 text-gray-900': route().current('admin.dashboard'), 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !route().current('admin.dashboard')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                Dashboard
                            </Link>
                            <Link :href="route('admin.payment-methods.index')" :class="{'border-indigo-400 text-gray-900': route().current('admin.payment-methods.*'), 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !route().current('admin.payment-methods.*')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                Pembayaran
                            </Link>
                            <Link :href="route('admin.categories.index')" :class="{'border-indigo-400 text-gray-900': route().current('admin.categories.*'), 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !route().current('admin.categories.*')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                Kategori
                            </Link>
                            <Link :href="route('admin.products.index')" :class="{'border-indigo-400 text-gray-900': route().current('admin.products.*'), 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !route().current('admin.products.*')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                Produk
                            </Link>
                            <Link :href="route('admin.suppliers.index')" :class="{'border-indigo-400 text-gray-900': route().current('admin.suppliers.*'), 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !route().current('admin.suppliers.*')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                Supplier
                            </Link>
                            <Link :href="route('admin.orders.index')" :class="{'border-indigo-400 text-gray-900': route().current('admin.orders.*'), 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !route().current('admin.orders.*')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                Order
                            </Link>
                            <Link :href="route('admin.users.index')" :class="{'border-indigo-400 text-gray-900': route().current('admin.users.*'), 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !route().current('admin.users.*')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                User
                            </Link>
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative">
                            <button @click="logout" class="text-sm text-gray-700 hover:text-gray-900">
                                Log Out
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ title }}
                </h2>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
    
    <!-- Toast Container -->
    <div id="toast-container"></div>
</template>
