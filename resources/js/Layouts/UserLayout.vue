<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';

const showingNavigationDropdown = ref(false);
const page = usePage();

// Debug log to check page props
onMounted(() => {
    console.log('UserLayout mounted with page props:', page.props);
});

// Watch for changes in page props
watch(() => page.props, (newProps) => {
    console.log('Page props updated:', newProps);
}, { deep: true });

const user = computed(() => {
    console.log('Current auth user:', page.props?.auth?.user);
    return page.props?.auth?.user || null;
});
</script>

<template>
    <div>
        <!-- Toast Container -->
        <div id="toast-container"></div>

        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('home')">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                                </Link>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                                v-if="$page.props.auth && $page.props.auth.user">
                                <Link :href="route('categories.index')"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                        </path>
                                    </svg>
                                </Link>
                            </div>
                        </div>

                        <!-- Search Bar -->
                        <div class="hidden sm:flex items-center flex-1 max-w-xl mx-4">
                            <div class="relative w-full">
                                <input type="text"
                                    class="w-full px-4 py-2 border-2 border-blue-400 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-center gradient-placeholder"
                                    placeholder="Cari produk...">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6 gap-12">
                            <template v-if="$page.props.auth && $page.props.auth.user">
                                <Link :href="route('wishlist.index')" class="text-gray-500 hover:text-gray-700">
                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                        </path>
                                    </svg>
                                </Link>
                                <Link :href="route('cart.index')" class="text-gray-500 hover:text-gray-700 relative">
                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.182 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </Link>

                                <!-- Notification Dropdown -->
                                <div class="relative">
                                    <Dropdown align="right" width="96">
                                        <template #trigger>
                                            <button type="button"
                                                class="relative p-1 rounded-full text-gray-500 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                <span class="sr-only">View notifications</span>
                                                <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                </svg>
                                                <!-- Red dot for unread notifications -->
                                                <span v-if="$page.props.unread_notifications_count > 0"
                                                    class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white bg-red-500"></span>
                                            </button>
                                        </template>

                                        <template #content>
                                            <div class="px-4 py-2 border-b border-gray-100 font-semibold text-gray-700">
                                                Notifikasi
                                            </div>
                                            <div
                                                v-if="$page.props.notifications && $page.props.notifications.length > 0">
                                                <div v-for="notification in $page.props.notifications"
                                                    :key="notification.id"
                                                    class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 text-sm">
                                                    <p class="text-gray-800 font-medium">{{ notification.data.title ||
                                                        'Notifikasi Baru' }}</p>
                                                    <p class="text-gray-500 text-xs mt-1">{{ notification.data.message
                                                        || 'Anda memiliki notifikasi baru.' }}</p>
                                                </div>
                                            </div>
                                            <div v-else
                                                class="px-4 py-4 text-center text-sm text-gray-500 border-b border-gray-100">
                                                Tidak ada notifikasi.
                                            </div>
                                            <Link :href="route('notifications.index')"
                                                class="block w-full px-4 py-2 text-center text-sm text-indigo-600 hover:text-indigo-800 hover:bg-gray-100 font-medium transition duration-150 ease-in-out">
                                                Lihat Semua Notifikasi
                                            </Link>
                                        </template>
                                    </Dropdown>
                                </div>

                                <div class="relative">
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button type="button"
                                                    class="inline-flex items-center border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                    <svg class="h-6 w-6 text-indigo-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <DropdownLink :href="route('profile.index')" class="text-indigo-600"> Profil
                                            </DropdownLink>
                                            <DropdownLink :href="route('orders.index')" class="text-indigo-600"> Pesanan
                                                Saya </DropdownLink>
                                            <DropdownLink :href="route('logout')" method="post" as="button"
                                                class="text-indigo-600">
                                                Log Out
                                            </DropdownLink>
                                        </template>
                                    </Dropdown>
                                </div>
                            </template>
                            <template v-else>
                                <Link :href="route('register')"
                                    class="ms-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md font-bold text-medium uppercase tracking-widest bg-gradient-to-r from-indigo-600 to-blue-400 bg-clip-text text-transparent hover:opacity-75 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 disabled:opacity-25">
                                    Register
                                </Link>
                                <Link :href="route('login')"
                                    class="ms-4 inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 !bg-gradient-to-r !from-indigo-600 !to-blue-400 !border-0 hover:!from-indigo-500 hover:!to-blue-300 transition-all duration-300">
                                    Log in
                                </Link>
                            </template>
                        </div>

                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{
                                        'hidden': showingNavigationDropdown,
                                        'inline-flex': !showingNavigationDropdown,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{
                                        'hidden': !showingNavigationDropdown,
                                        'inline-flex': showingNavigationDropdown,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                    class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1" v-if="$page.props.auth && $page.props.auth.user">
                        <ResponsiveNavLink :href="route('home')" :active="route().current('home')">
                            Beranda
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('products.index')" :active="route().current('products.index')">
                            Produk
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('wishlist.index')" :active="route().current('wishlist.index')">
                            Wishlist
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('cart.index')" :active="route().current('cart.index')">
                            Keranjang
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('orders.index')" :active="route().current('orders.index')">
                            Pesanan Saya
                        </ResponsiveNavLink>
                    </div>

                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div v-if="$page.props.auth && $page.props.auth.user" class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <template v-if="$page.props.auth && $page.props.auth.user">
                                <ResponsiveNavLink :href="route('profile.edit')"> Profil </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </ResponsiveNavLink>
                            </template>
                            <template v-else>
                                <ResponsiveNavLink :href="route('login')"> Login </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('register')"> Register </ResponsiveNavLink>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>

            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.gradient-placeholder::placeholder {
    background: linear-gradient(to right, #4f46e5, #60a5fa);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    font-weight: 900;
}
</style>