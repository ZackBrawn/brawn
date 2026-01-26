<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    addresses: {
        type: Array,
        default: () => [],
    },
});

const confirmingAddressDeletion = ref(false);
const addressToDelete = ref(null);
const showAddressModal = ref(false);
const editingAddress = ref(null);

const form = useForm({
    label: '',
    recipient_name: '',
    full_address: '',
    city: '',
    province: '',
    postal_code: '',
    is_primary: false,
});

const openCreateModal = () => {
    editingAddress.value = null;
    form.reset();
    form.clearErrors();
    showAddressModal.value = true;
};

const openEditModal = (address) => {
    editingAddress.value = address;
    form.label = address.label;
    form.recipient_name = address.recipient_name;
    form.full_address = address.full_address;
    form.city = address.city;
    form.province = address.province;
    form.postal_code = address.postal_code;
    form.is_primary = Boolean(address.is_primary);
    form.clearErrors();
    showAddressModal.value = true;
};

const closeModal = () => {
    showAddressModal.value = false;
    form.reset();
};

const submitAddress = () => {
    if (editingAddress.value) {
        form.put(route('profile.addresses.update', editingAddress.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('profile.addresses.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const confirmAddressDeletion = (id) => {
    addressToDelete.value = id;
    confirmingAddressDeletion.value = true;
};

const deleteAddress = () => {
    form.delete(route('profile.addresses.destroy', addressToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingAddressDeletion.value = false;
            addressToDelete.value = null;
        },
    });
};

const setDefault = (id) => {
    form.patch(route('profile.addresses.setdefault', id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <section>
        <header class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-lg font-medium text-gray-900">Daftar Alamat</h2>
                <p class="mt-1 text-sm text-gray-600">Kelola alamat pengiriman Anda.</p>
            </div>
            <PrimaryButton @click="openCreateModal">Tambah Alamat Baru</PrimaryButton>
        </header>

        <div v-if="addresses.length === 0" class="text-gray-500 text-center py-4 bg-gray-50 rounded-lg">
            Belum ada alamat yang tersimpan.
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="address in addresses" :key="address.id"
                class="bg-white border rounded-lg p-4 shadow-sm relative transition-all hover:shadow-md"
                :class="{ 'border-indigo-500 ring-2 ring-indigo-200': address.is_primary, 'border-gray-200': !address.is_primary }">
                
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-semibold text-gray-800">{{ address.label }}</h3>
                    <span v-if="address.is_primary" class="bg-indigo-100 text-indigo-800 text-xs px-2 py-0.5 rounded-full font-medium">Utama</span>
                </div>

                <p class="text-gray-600 text-sm mb-1"><span class="font-medium text-gray-900">{{ address.recipient_name }}</span> | {{ address.full_address }}</p>
                <p class="text-gray-600 text-sm mb-3">
                    {{ address.city }}, {{ address.province }} {{ address.postal_code }}
                </p>

                <div class="flex items-center gap-2 mt-4 pt-3 border-t border-gray-100">
                    <button v-if="!address.is_primary" @click="setDefault(address.id)" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        Jadikan Utama
                    </button>
                    <div class="flex-grow"></div>
                    <button @click="openEditModal(address)" class="text-sm text-gray-500 hover:text-gray-700">
                        Edit
                    </button>
                    <button @click="confirmAddressDeletion(address.id)" class="text-sm text-red-500 hover:text-red-700 ml-3">
                        Hapus
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Form Alamat -->
        <Modal :show="showAddressModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ editingAddress ? 'Edit Alamat' : 'Tambah Alamat Baru' }}
                </h2>

                <div class="space-y-4">
                    <div v-if="form.errors.error" class="bg-red-50 text-red-600 p-3 rounded-md text-sm">
                        {{ form.errors.error }}
                    </div>

                    <div>
                        <InputLabel for="label" value="Label Alamat (cth: Rumah, Kantor)" />
                        <TextInput id="label" v-model="form.label" type="text" class="mt-1 block w-full" placeholder="Rumah" required />
                        <InputError :message="form.errors.label" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="recipient_name" value="Nama Penerima" />
                        <TextInput id="recipient_name" v-model="form.recipient_name" type="text" class="mt-1 block w-full" placeholder="zack" required />
                        <InputError :message="form.errors.recipient_name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="full_address" value="Alamat Lengkap" />
                        <TextInput id="full_address" v-model="form.full_address" type="text" class="mt-1 block w-full" placeholder="Nama Jalan, No. Rumah, RT/RW" required />
                        <InputError :message="form.errors.full_address" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="city" value="Kota/Kabupaten" />
                            <TextInput id="city" v-model="form.city" type="text" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.city" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="province" value="Provinsi" />
                            <TextInput id="province" v-model="form.province" type="text" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.province" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="postal_code" value="Kode Pos" />
                        <TextInput id="postal_code" v-model="form.postal_code" type="text" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.postal_code" class="mt-2" />
                    </div>

                    <div class="flex items-center mt-2">
                        <input id="is_primary" type="checkbox" v-model="form.is_primary" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="is_primary" class="ml-2 text-sm text-gray-600">Jadikan sebagai alamat utama</label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal">Batal</SecondaryButton>
                    <PrimaryButton @click="submitAddress" :disabled="form.processing">
                        {{ editingAddress ? 'Simpan Perubahan' : 'Simpan Alamat' }}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Modal Konfirmasi Hapus -->
        <Modal :show="confirmingAddressDeletion" @close="confirmingAddressDeletion = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Hapus Alamat?</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Apakah Anda yakin ingin menghapus alamat ini? Data yang sudah dihapus tidak dapat dikembalikan.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="confirmingAddressDeletion = false">Batal</SecondaryButton>
                    <PrimaryButton class="bg-red-600 hover:bg-red-700" @click="deleteAddress" :disabled="form.processing">
                        Hapus Alamat
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
