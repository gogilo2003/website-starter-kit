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
    iNotification,
    iProductCategories,
    iProductCategory,
} from '@/interfaces';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { ref } from 'vue';

/* -------------------------------------------------------------------------- */
/* Props */
/* -------------------------------------------------------------------------- */
const props = defineProps<{
    categories: iProductCategories;
    notification?: iNotification;
}>();

/* -------------------------------------------------------------------------- */
/* Form */
/* -------------------------------------------------------------------------- */
const form = useForm<{
    id: number | null;
    name: string;
    description: string;
    icon: string;
    picture: File | null;
    slug?: string;
}>({
    id: null,
    name: '',
    description: '',
    icon: '',
    picture: null,
    slug: '',
});

/* -------------------------------------------------------------------------- */
/* Image handling */
/* -------------------------------------------------------------------------- */
const fileInput = ref<HTMLInputElement | null>(null);
const previewUrl = ref<string | null>(null);
const selectedPicture = ref(false);
const pictureUrl = ref('');

const selectPicture = () => {
    fileInput.value?.click();
};

const onFileSelect = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (!input.files || !input.files.length) return;

    const file = input.files[0];

    form.picture = file;
    selectedPicture.value = true;

    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
    }

    previewUrl.value = URL.createObjectURL(file);
};

const clearPicture = () => {
    form.picture = null;
    selectedPicture.value = false;

    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
        previewUrl.value = pictureUrl.value ?? null;
    }

    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

/* -------------------------------------------------------------------------- */
/* Dialog state */
/* -------------------------------------------------------------------------- */
const showCategoryDialog = ref(false);
const edit = ref(false);
const dialogTitle = ref('New Category');

const close = () => cancel();

const cancel = () => {
    showCategoryDialog.value = false;

    form.reset();
    form.clearErrors();

    clearPicture();

    edit.value = false;
};

/* -------------------------------------------------------------------------- */
/* CRUD */
/* -------------------------------------------------------------------------- */
const addCategory = () => {
    showCategoryDialog.value = true;
    edit.value = false;
    dialogTitle.value = 'New Category';
    pictureUrl.value = '';
    previewUrl.value = '';
};

const editCategory = (category: iProductCategory) => {
    edit.value = true;
    showCategoryDialog.value = true;
    dialogTitle.value = 'Edit Category';

    form.id = category.id;
    form.name = category.name;
    form.description = category.description;
    form.icon = category.icon;
    form.picture = null;

    pictureUrl.value = category.picture_url ?? '';
    previewUrl.value = category.picture_url ?? null;
    selectedPicture.value = false;

    form.clearErrors();
};

const submit = () => {
    const method = edit.value ? 'patch' : 'post';
    const url = edit.value
        ? route('dashboard-products-categories-update', { category: form.id })
        : route('dashboard-products-categories-store');

    form.transform((data) => ({ ...data, _method: method })).post(url, {
        preserveScroll: true,
        preserveState: true,
        only: ['categories', 'notification', 'errors'],
        onSuccess: () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false,
                icon: 'success',
                text:
                    props.notification?.success ??
                    `Category ${edit.value ? 'updated' : 'created'} successfully`,
            });
            close();
        },
        onError: () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false,
                icon: 'error',
                text:
                    props.notification?.danger ??
                    'Please fix the errors and try again',
            });
        },
    });
};

const deleteCategory = (category: iProductCategory) => {
    if (!confirm('Are you sure you want to delete this category?')) return;

    router.delete(
        route('dashboard-products-categories-destroy', {
            category: category.id,
        }),
        {
            preserveScroll: true,
            preserveState: true,
            only: ['categories', 'notification', 'errors'],
        },
    );
};

/* -------------------------------------------------------------------------- */
/* Publish / Promote */
/* -------------------------------------------------------------------------- */
const publishCategory = (id: number) => {
    router.patch(
        route('dashboard-products-categories-publish', { category: id }),
    );
};

const promoteCategory = (id: number) => {
    router.patch(
        route('dashboard-products-categories-promote', { category: id }),
    );
};

/* -------------------------------------------------------------------------- */
/* Utilities */
/* -------------------------------------------------------------------------- */
const copyToClipboard = (value: string) => {
    navigator.clipboard.writeText(value).then(() => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false,
            icon: 'success',
            text: 'Copied to clipboard',
        });
    });
};
</script>

<template>
    <!-- Modal -->
    <Modal :show="showCategoryDialog" max-width="3xl">
        <template #title>
            <div class="flex items-center justify-between bg-gray-100 p-3">
                <h3 class="text-lg font-semibold">{{ dialogTitle }}</h3>
                <Icon
                    type="close"
                    class="h-6 w-6 cursor-pointer"
                    @click="close"
                />
            </div>
        </template>
        <form @submit.prevent="submit">
            <div class="flex gap-4 p-4">
                <!-- Image -->
                <div class="flex w-56 flex-col gap-4">
                    <div class="h-48 w-full">
                        <img
                            v-if="previewUrl"
                            :src="previewUrl"
                            class="h-full w-full rounded object-cover"
                        />
                        <div
                            v-else
                            class="flex h-full items-center justify-center rounded border border-dashed text-sm text-gray-400"
                        >
                            No image selected
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center justify-center">
                        <input
                            ref="fileInput"
                            type="file"
                            class="hidden"
                            accept="image/*"
                            @change="onFileSelect"
                        />

                        <SecondaryButton
                            v-if="!selectedPicture"
                            type="button"
                            @click="selectPicture"
                        >
                            Browse
                        </SecondaryButton>

                        <SecondaryButton
                            v-else
                            type="button"
                            @click="clearPicture"
                        >
                            Clear
                        </SecondaryButton>

                        <InputError :message="form.errors.picture" />
                    </div>
                </div>

                <!-- Fields -->
                <div class="flex-1">
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
                        <TextareaInput
                            v-model="form.description"
                            class="w-full"
                        />
                        <InputError :message="form.errors.description" />
                    </div>
                </div>
            </div>

            <div class="flex justify-between border-t p-4">
                <PrimaryButton type="submit">Save</PrimaryButton>
                <SecondaryButton type="button" @click="cancel">
                    Cancel
                </SecondaryButton>
            </div>
        </form>
    </Modal>

    <!-- Page -->
    <AuthenticatedLayout title="Product Categories">
        <div class="flex items-center justify-between px-8 pt-8">
            <h2 class="text-xl font-semibold">Product Categories</h2>
            <SecondaryButton @click="addCategory">New Category</SecondaryButton>
        </div>

        <div class="px-8 py-12">
            <div class="rounded bg-white p-6 shadow">
                <div class="space-y-4">
                    <div
                        v-for="category in categories.data"
                        :key="category.id"
                        class="flex items-center gap-4 rounded border p-4"
                    >
                        <img
                            :src="category.picture_url"
                            class="h-16 w-16 rounded object-cover"
                        />

                        <div class="flex-1">
                            <h4 class="font-semibold text-primary-600">
                                {{ category.name }}
                            </h4>
                            <div
                                class="flex items-center gap-1 text-sm text-gray-600"
                            >
                                {{ category.slug }}
                                <button @click="copyToClipboard(category.slug)">
                                    <Icon type="copy" class="h-4 w-4" />
                                </button>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <SecondaryButton
                                @click="publishCategory(category.id)"
                            >
                                {{
                                    category.published ? 'Unpublish' : 'Publish'
                                }}
                            </SecondaryButton>

                            <SecondaryButton
                                @click="promoteCategory(category.id)"
                            >
                                {{ category.promoted ? 'Demote' : 'Promote' }}
                            </SecondaryButton>

                            <SecondaryButton @click="editCategory(category)">
                                Edit
                            </SecondaryButton>

                            <SecondaryButton @click="deleteCategory(category)">
                                Delete
                            </SecondaryButton>

                            <Link
                                :href="route('dashboard-products', category.id)"
                                class="inline-flex items-center rounded-full border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-25"
                            >
                                Manage Products
                            </Link>
                        </div>
                    </div>
                </div>

                <Paginator :items="categories" class="mt-6" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
