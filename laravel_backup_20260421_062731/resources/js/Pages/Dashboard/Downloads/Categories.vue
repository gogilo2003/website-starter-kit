<script setup lang="ts">
import Icon from '@/Components/Icons/Icon.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import Paginator from '@/Components/Paginator.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import TextInput from '@/Components/TextInput.vue';
import {
    iDownloadCategories,
    iDownloadCategory,
    iNotification,
} from '@/interfaces';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { ref } from 'vue';

const props = defineProps<{
    categories: iDownloadCategories;
    notification?: iNotification;
}>();

const form = useForm<{
    id: number | null;
    name: string;
    description: string;
    icon: string;
}>({
    id: null,
    name: '',
    description: '',
    icon: '',
});

const showCategoryDialog = ref(false);
const edit = ref(false);
const dialogTitle = ref('New Category');

const close = () => cancel();
const cancel = () => {
    showCategoryDialog.value = false;
    form.id = null;
    form.name = '';
    form.description = '';
    form.icon = '';
    form.errors.id = '';
    form.errors.name = '';
    form.errors.description = '';
    form.errors.icon = '';
    form.reset();
};

const addCategory = () => {
    showCategoryDialog.value = true;
    edit.value = false;
    dialogTitle.value = 'New Page Section';
};

const editCategory = (category: iDownloadCategory) => {
    edit.value = true;
    showCategoryDialog.value = true;
    dialogTitle.value = 'Edit Page Section';
    form.id = category.id;
    form.name = category.name;
    form.description = category.description ?? '';
    form.icon = category.icon ?? '';
    form.errors.id = '';
    form.errors.name = '';
    form.errors.description = '';
    form.errors.icon = '';
};

const submit = () => {
    const method = edit.value ? 'patch' : 'post';
    const url = edit.value
        ? route('dashboard-downloads-categories-update', {
              download_category: form.id,
          })
        : route('dashboard-downloads-categories-store');

    form.transform((data) => {
        return { ...data, _method: method };
    }).post(url, {
        onSuccess: () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                timer: 3000,
                timerProgressBar: true,
                icon: 'success',
                text:
                    props?.notification?.success ??
                    'Category saved successfully',
            });
            close();
        },
        onError: () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                timer: 3000,
                timerProgressBar: true,
                icon: 'error',
                text:
                    props?.notification?.danger ||
                    'Something went wrong! please check your fields and try again',
            });
        },
    });
};

const deleteCategory = (category: iDownloadCategory) => {
    console.log(category);
    if (confirm('Are you sure you want to delete this category?')) {
        router.delete(
            route('dashboard-downloads-categories-destroy', category.id),
            {
                onSuccess: () => {
                    Swal.fire({
                        icon: 'success',
                        text:
                            props?.notification?.success ??
                            'Category deleted successfully',
                    });
                    close();
                },
                onError: () => {
                    Swal.fire({
                        icon: 'error',
                        text:
                            props?.notification?.danger ||
                            'Something went wrong! please check your fields and try again',
                    });
                },
            },
        );
    }
};

const showDownloadsManager = ref(false);
const selectedCategory = ref<iDownloadCategory | null>(null);
const manageDownloads = (Category: iDownloadCategory) => {
    selectedCategory.value = Category;
    showDownloadsManager.value = true;
};

const closeDownloadsManager = () => {
    selectedCategory.value = null;
    showDownloadsManager.value = false;
};

const copyToClipboard = (value: string) => {
    try {
        navigator.clipboard
            .writeText(value)
            .then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    timerProgressBar: true,
                    icon: 'success',
                    text: 'Copied to clipboard',
                });
            })
            .catch((error) => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: 'error',
                    text: 'Failed to copy to clipboard',
                });
            });
    } catch (error) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            timer: 2000,
            timerProgressBar: true,
            icon: 'error',
            text: 'Failed to copy to clipboard',
        });
    }
};
</script>

<template>
    <Modal :show="showCategoryDialog" max-width="3xl">
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
                    <InputLabel value="Icon" />
                    <TextInput v-model="form.icon" class="w-full" />
                    <InputError :message="form.errors.icon" />
                </div>
                <div class="mb-6">
                    <InputLabel value="Description" />
                    <TextareaInput v-model="form.description" class="w-full" />
                    <InputError :message="form.errors.description" />
                </div>
            </div>
            <div class="mx-3 mb-3 flex justify-between rounded-lg border p-3">
                <PrimaryButton>Save</PrimaryButton>
                <SecondaryButton @click="cancel">Cancel</SecondaryButton>
            </div>
        </form>
    </Modal>
    <AuthenticatedLayout title="Download Categories">
        <div class="px-8 pt-8">
            <div
                class="flex w-full flex-col items-center justify-center md:flex-row md:justify-between"
            >
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Download Categories
                </h2>
                <SecondaryButton @click="addCategory"
                    >New Category</SecondaryButton
                >
            </div>
        </div>

        <div class="py-12">
            <div class="sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg"
                >
                    <div class="flex flex-col gap-6">
                        <div
                            class="flex flex-col items-center justify-between gap-8 rounded-lg border border-primary-700 px-6 py-3 shadow-sm md:flex-row"
                            v-for="category in categories.data"
                        >
                            <div class="flex-1">
                                <h4
                                    v-text="category?.name"
                                    class="text-lg font-semibold text-primary-600"
                                ></h4>
                                <div class="flex items-center gap-1">
                                    <div
                                        v-text="category?.slug"
                                        class="line-clamp-2 text-sm text-gray-600"
                                    ></div>
                                    <button
                                        @click="copyToClipboard(category?.slug)"
                                    >
                                        <Icon type="copy" class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                            <div class="flex w-full flex-none gap-2 md:w-96">
                                <SecondaryButton @click="editCategory(category)"
                                    >Edit</SecondaryButton
                                >
                                <SecondaryButton
                                    @click="deleteCategory(category)"
                                    >Delete</SecondaryButton
                                >
                                <Link
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25"
                                    :href="
                                        route(
                                            'dashboard-downloads',
                                            category.id,
                                        )
                                    "
                                    >Manage Downloads</Link
                                >
                            </div>
                        </div>
                    </div>
                    <Paginator :items="categories" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
