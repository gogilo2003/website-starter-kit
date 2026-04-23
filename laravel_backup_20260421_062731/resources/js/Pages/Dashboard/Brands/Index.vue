<script setup lang="ts">
import Paginator from '@/Components/Paginator.vue';
import { iBrand, iBrands, iNotification } from '@/interfaces';
import { router, useForm } from '@inertiajs/vue3';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { debounce } from 'lodash';
import Swal from 'sweetalert2';
import { ref, watch } from 'vue';
import Icon from '../../../Components/Icons/Icon.vue';
import InputError from '../../../Components/InputError.vue';
import InputLabel from '../../../Components/InputLabel.vue';
import Modal from '../../../Components/Modal.vue';
import PrimaryButton from '../../../Components/PrimaryButton.vue';
import SecondaryButton from '../../../Components/SecondaryButton.vue';
import TextInput from '../../../Components/TextInput.vue';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout.vue';

const props = defineProps<{
    brands: iBrands;
    notification?: iNotification;
    search?: string;
}>();

const form = useForm<{
    id: number | null;
    name: string;
    logo: File | null;
}>({
    id: null,
    name: '',
    logo: null,
});

const showBrandDialog = ref(false);
const edit = ref(false);
const dialogTitle = ref('New Brand');
const searchVal = ref('');

const close = () => cancel();
const cancel = () => {
    showBrandDialog.value = false;
    form.id = null;
    form.name = '';
    form.logo = null;
    form.errors.id = '';
    form.errors.name = '';
    form.errors.logo = '';
    form.reset();
};

const addBrand = () => {
    showBrandDialog.value = true;
    edit.value = false;
    dialogTitle.value = 'New Brand';
};

const editBrand = (brand: iBrand) => {
    edit.value = true;
    showBrandDialog.value = true;
    dialogTitle.value = 'Edit Brand';
    form.id = brand.id;
    form.name = brand.name;
    form.errors.id = '';
    form.errors.name = '';
    form.errors.logo = '';
};

const submit = () => {
    if (edit.value) {
        form.transform((data) => {
            return { ...data, _method: 'patch' };
        }).post(route('dashboard-brands-update', { brand: form.id }), {
            onSuccess: () => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: 'success',
                    text:
                        props?.notification?.success ??
                        'Brand updated successfully',
                });
                close();
            },
            onError: () => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: 'error',
                    text:
                        props?.notification?.danger ||
                        'Something went wrong! please check your fields and try again',
                });
            },
        });
    } else {
        form.transform((data) => {
            return { ...data, _method: null };
        }).post(route('dashboard-brands-store'), {
            onSuccess: () => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: 'success',
                    text:
                        props?.notification?.success ??
                        'Brand created successfully',
                });
                close();
            },
            onError: () => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: 'error',
                    text:
                        props?.notification?.danger ||
                        'Something went wrong! please check your fields and try again',
                });
            },
        });
    }
};

const deleteBrand = (brand: iBrand) => {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to delete this brand. This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('dashboard-brands-destroy', brand.id), {
                onSuccess: () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        icon: 'success',
                        text:
                            props?.notification?.success ??
                            'Brand deleted successfully',
                    });
                    close();
                },
                onError: () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        icon: 'error',
                        text:
                            props?.notification?.danger ||
                            'Something went wrong! please check your fields and try again',
                    });
                },
            });
        }
    });
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.logo = target.files[0];
    } else {
        form.logo = null;
    }
};

const performSearch = (searchTerm: string) => {
    router.get(
        route('dashboard-brands'),
        {
            search: searchTerm,
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true,
            only: ['brands'],
        },
    );
};

// Initialize search value from URL params if exists
searchVal.value =
    new URLSearchParams(window.location.search).get('search') || '';

// Debounced search watcher
watch(
    () => searchVal.value,
    debounce((value) => {
        console.log('Searching for:', value);
        performSearch(value);
    }, 500),
);

// Function to clear search
const clearSearch = () => {
    searchVal.value = '';
    performSearch('');
};
</script>

<template>
    <Modal :show="showBrandDialog" max-width="3xl">
        <template #title>
            <div class="flex items-center justify-between bg-gray-100 p-3">
                <h3 class="text-lg font-semibold" v-text="dialogTitle"></h3>
                <Icon
                    type="close"
                    @click="close"
                    class="h-6 w-6 cursor-pointer rounded-full border border-secondary-300 text-secondary-300 transition-colors duration-300 hover:border-secondary-500 hover:text-secondary-500"
                />
            </div>
        </template>
        <form @submit.prevent="submit">
            <div class="mt-3 px-3 py-3">
                <div class="mb-6">
                    <InputLabel value="Name" />
                    <TextInput v-model="form.name" class="w-full" />
                    <InputError :message="form.errors.name" />
                </div>
                <div class="mb-6">
                    <InputLabel value="Select Logo" />
                    <input
                        class="block w-full cursor-pointer rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-900 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:placeholder-gray-400"
                        type="file"
                        @input="handleFileChange($event)"
                    />
                    <InputError :message="form.errors.logo" />
                </div>
            </div>
            <div class="mx-3 mb-3 flex justify-between rounded-lg border p-3">
                <PrimaryButton>Save</PrimaryButton>
                <SecondaryButton @click="cancel">Cancel</SecondaryButton>
            </div>
        </form>
    </Modal>
    <AuthenticatedLayout title="Brands">
        <div class="py-12">
            <div class="sm:px-6 lg:px-8">
                <div
                    class="mb-6 flex flex-col gap-4 sm:items-start sm:justify-between md:flex-row"
                >
                    <!-- Search Input -->
                    <div class="relative max-w-md flex-1">
                        <div class="relative max-w-md">
                            <input
                                v-model="searchVal"
                                type="text"
                                placeholder="Search brands by name..."
                                class="relative z-0 w-full rounded-full border border-gray-300 bg-gray-50 py-2 pl-10 pr-4 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500"
                            />
                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                            >
                                <svg
                                    class="h-5 w-5 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                </svg>
                            </div>
                            <button
                                v-if="searchVal"
                                @click="clearSearch"
                                class="absolute inset-y-0 right-0 flex items-center pr-3"
                            >
                                <svg
                                    class="h-5 w-5 text-gray-400 hover:text-gray-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            {{
                                searchVal
                                    ? `Search results for: "${searchVal}"`
                                    : 'Type to search brands...'
                            }}
                        </p>
                    </div>

                    <!-- Add Brand Button -->
                    <SecondaryButton
                        @click="addBrand"
                        class="whitespace-nowrap"
                    >
                        <svg
                            class="mr-2 h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                        New Brand
                    </SecondaryButton>
                </div>

                <div
                    class="overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg"
                >
                    <!-- Results Count -->
                    <div v-if="brands.total" class="mb-4 text-sm text-gray-600">
                        Found {{ brands.total }} brand{{
                            brands.total === 1 ? '' : 's'
                        }}
                        <span v-if="searchVal"> for "{{ searchVal }}"</span>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-if="brands.data.length === 0"
                        class="py-12 text-center"
                    >
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                            />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">
                            {{
                                searchVal ? 'No brands found' : 'No brands yet'
                            }}
                        </h3>
                        <p class="mt-1 text-gray-500">
                            {{
                                searchVal
                                    ? 'Try a different search term'
                                    : 'Get started by creating a new brand'
                            }}
                        </p>
                        <div v-if="!searchVal" class="mt-6">
                            <SecondaryButton @click="addBrand">
                                <svg
                                    class="mr-2 h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v16m8-8H4"
                                    />
                                </svg>
                                Add Your First Brand
                            </SecondaryButton>
                        </div>
                    </div>

                    <!-- Brands List -->
                    <div class="flex flex-col gap-6">
                        <div
                            class="flex flex-col items-center justify-between gap-8 rounded-lg border border-gray-200 bg-white px-6 py-4 shadow-sm transition-shadow duration-200 hover:shadow-md md:flex-row"
                            v-for="brand in brands.data"
                            :key="brand.id"
                        >
                            <div class="flex flex-1 items-center space-x-4">
                                <!-- Display logo using logo_url field -->
                                <div class="flex-shrink-0">
                                    <img
                                        :src="
                                            brand.logo_url ||
                                            '/images/placeholder.png'
                                        "
                                        :alt="brand.name"
                                        class="h-16 w-16 rounded-lg border border-gray-200 object-cover p-1"
                                    />
                                </div>
                                <div>
                                    <h4
                                        v-text="brand?.name"
                                        class="text-lg font-semibold text-gray-900"
                                    ></h4>
                                    <p class="text-sm text-gray-500">
                                        Created:
                                        {{
                                            new Date(
                                                brand.created_at,
                                            ).toLocaleDateString()
                                        }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-none gap-2 md:w-auto">
                                <SecondaryButton @click="editBrand(brand)">
                                    <svg
                                        class="mr-2 h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                        />
                                    </svg>
                                    Edit
                                </SecondaryButton>
                                <SecondaryButton @click="deleteBrand(brand)">
                                    <svg
                                        class="mr-2 h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        />
                                    </svg>
                                    Delete
                                </SecondaryButton>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination if available -->
                    <div class="mt-8">
                        <Paginator :items="brands" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
