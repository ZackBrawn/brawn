<template>
  <!-- Delete Confirmation Modal -->
  <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
      </div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
      <div
        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div
              class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Hapus Pengguna</h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Apakah Anda yakin ingin menghapus pengguna <strong>{{ userToDelete?.name }}</strong>?
                  Tindakan ini tidak dapat dibatalkan.
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button @click="deleteUser" type="button"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
            Hapus
          </button>
          <button @click="cancelDelete" type="button"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Batal
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">Daftar Pengguna</h2>
        <Link :href="route('admin.users.create')"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Pengguna
        </Link>
      </div>

      <!-- Search and Filter -->
      <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
        <form @submit.prevent="filterUsers" class="space-y-4 sm:space-y-0 sm:flex sm:space-x-4">
          <div class="flex-1">
            <input type="text" v-model="filters.search" placeholder="Cari nama, email..."
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
          </div>
          <div class="relative">
            <select v-model="filters.role"
              class="appearance-none w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10">
              <option value="">Semua Role</option>
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <button type="submit"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Filter
          </button>
          <button type="button" @click="resetFilters" v-if="hasFilters"
            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Reset
          </button>
        </form>
      </div>

      <!-- Users Table -->
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Nama
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Email
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Role
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Nomor Telepon
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Tanggal Daftar
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                      <span class="text-gray-600">{{ user.name?.charAt(0)?.toUpperCase() || 'U' }}</span>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ user.name || 'Unknown User' }}</div>
                      <div class="text-xs text-yellow-500">ID: {{ user.id }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-500">
                  {{ user.email || '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'">
                    {{ user.role === 'admin' ? 'Admin' : 'User' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <a v-if="user.phone" :href="'https://wa.me/62' + user.phone" class="text-blue-600 hover:underline"
                    target="_blank" rel="noopener noreferrer">
                    {{ user.phone }}
                  </a>
                  <span v-else class="text-gray-400">-</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(user.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end space-x-3">
                    <Link :href="route('admin.users.show', user.id)"
                      class="text-blue-600 hover:text-blue-800" title="Detail">
                    Detail </Link>
                    <Link :href="route('admin.users.edit', user.id)"
                      class="text-yellow-600 hover:text-yellow-900" title="Edit">
                    Edit
                    </Link>
                    <button v-if="currentUser?.id !== user.id" @click="confirmDelete(user)"
                      class="text-red-600 hover:text-red-800">
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!users?.data || users.data.length === 0">
                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                  Tidak ada data pengguna yang ditemukan.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <Pagination v-if="users?.links" :links="users.links" class="mt-4" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { useToast } from 'vue-toastification';

defineOptions({ layout: AdminLayout });
const page = usePage();
const props = defineProps({
  users: { type: Object, default: () => ({ data: [], links: [] }) },
  filters: {
    type: Object,
    default: () => ({ search: '', role: '' })
  }
});

const toast = useToast();
const filters = ref({
  search: props.filters?.search || '',
  role: props.filters?.role || ''
});
const showDeleteModal = ref(false);
const userToDelete = ref(null);

const currentUser = computed(() => page.props.auth?.user);
const hasFilters = computed(() => filters.value.search || filters.value.role);
const formatDate = (dateString) => {
  if (!dateString) return '-';
  try {
    const options = { day: 'numeric', month: 'short', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
  } catch (error) { return '-'; }
};

const filterUsers = () => {
  router.get(route('admin.users.index'), filters.value, {
    preserveState: true,
    replace: true
  });
};

const resetFilters = () => {
  filters.value = { search: '', role: '' };
  filterUsers();
};

const confirmDelete = (user) => {
  if (currentUser.value?.id === user.id) {
    toast.error('Tidak dapat menghapus akun Anda sendiri!');
    return;
  }
  userToDelete.value = user;
  showDeleteModal.value = true;
};
const cancelDelete = () => {
  showDeleteModal.value = false;
  userToDelete.value = null;
};

const deleteUser = () => {
  if (!userToDelete.value) {
    toast.error('Pengguna tidak valid.');
    return;
  }

  router.post(route('admin.users.destroy', userToDelete.value.id), {
    _method: 'DELETE'
  }, {
    preserveScroll: true,
    onSuccess: () => {
      cancelDelete();
      toast.success('Pengguna berhasil dihapus!');
    },
    onError: (errors) => {
      cancelDelete();
      const errorMessage = errors.error || Object.values(errors).flat().join(' ');
      toast.error(`Gagal menghapus: ${errorMessage}`);
    },
  });
};

onMounted(() => {
  const { flash } = page.props;
  if (flash?.success) toast.success(flash.success);
  if (flash?.error) toast.error(flash.error);
});
</script>
