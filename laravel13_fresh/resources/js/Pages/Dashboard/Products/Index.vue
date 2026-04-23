<script setup lang="ts">
import ProductAttributes from '@/Components/ProductAttributes.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { Quill, QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import Table from 'quill-table-module';
import TableUI from 'quill-table-ui';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import Icon from '../../../Components/Icons/Icon.vue';
import InputError from '../../../Components/InputError.vue';
import InputLabel from '../../../Components/InputLabel.vue';
import Modal from '../../../Components/Modal.vue';
import PrimaryButton from '../../../Components/PrimaryButton.vue';
import SecondaryButton from '../../../Components/SecondaryButton.vue';
import TextareaInput from '../../../Components/TextareaInput.vue';
import TextInput from '../../../Components/TextInput.vue';
import {
    iAttribute,
    iNotification,
    iProduct,
    iProductCategory,
    iProducts,
} from '../../../interfaces';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout.vue';

const props = defineProps<{
    products: iProducts;
    notification?: iNotification;
    category: iProductCategory;
}>();

const form = useForm<{
    id: number | null;
    title: string;
    picture: File | null;
    summary: string;
    description: string;
    features: iAttribute[];
}>({
    id: null,
    title: '',
    picture: null,
    summary: '',
    description: '',
    features: [],
});

const showProductDialog = ref(false);
const edit = ref(false);
const dialogTitle = ref('New Product');

const confirmAction = async (options: {
    title?: string;
    text: string;
    confirmButtonText?: string;
    icon?: 'warning' | 'question';
}) => {
    const result = await Swal.fire({
        title: options.title ?? 'Are you sure?',
        text: options.text,
        icon: options.icon ?? 'warning',
        showCancelButton: true,
        confirmButtonText: options.confirmButtonText ?? 'Yes, continue',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        focusCancel: true,
    });

    return result.isConfirmed;
};

const toast = (icon: 'success' | 'error', text: string) => {
    Swal.fire({
        toast: true,
        position: 'top-end',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        icon,
        text,
    });
};

const close = () => cancel();
const cancel = () => {
    showProductDialog.value = false;
    form.id = null;
    form.title = '';
    form.picture = null;
    form.summary = '';
    form.description = '';
    form.features = [];
    form.errors.id = '';
    form.errors.title = '';
    form.errors.picture = '';
    form.errors.summary = '';
    form.errors.description = '';
    form.errors.features = '';
    form.reset();
};

const newProduct = () => {
    showProductDialog.value = true;
    edit.value = false;
    dialogTitle.value = 'New Product';
};

const editProduct = (product: iProduct) => {
    edit.value = true;
    showProductDialog.value = true;
    dialogTitle.value = 'Edit Product';
    form.id = product.id;
    form.title = product.title;
    form.summary = product.summary;
    form.description = product.description;
    form.features = product.features;
    form.errors.id = '';
    form.errors.title = '';
    form.errors.picture = '';
    form.errors.summary = '';
    form.errors.description = '';
    form.errors.features = '';
};

const submit = () => {
    console.log(edit.value);

    if (edit.value) {
        form.transform((data) => {
            return {
                ...data,
                features: data.features,
                category: props.category.id,
                _method: 'patch',
            };
        }).post(route('dashboard-products-update', { product: form.id }), {
            only: ['products', 'notification'],
            onSuccess: () => {
                Swal.fire({
                    toast: true,
                    position: 'top',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: 'success',
                    text:
                        props?.notification?.success ??
                        'Product updated successfully',
                });
                close();
            },
            onError: () => {
                Swal.fire({
                    toast: true,
                    position: 'top',
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
            return {
                ...data,
                features: data.features,
                category: props.category.id,
                _method: null,
            };
        }).post(route('dashboard-products-store'), {
            only: ['products', 'notification'],
            onSuccess: () => {
                Swal.fire({
                    toast: true,
                    position: 'top',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: 'success',
                    text:
                        props?.notification?.success ??
                        'Product created successfully',
                });
                close();
            },
            onError: () => {
                Swal.fire({
                    toast: true,
                    position: 'top',
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

const deleteProduct = async (product: iProduct) => {
    const confirmed = await confirmAction({
        title: 'Delete Product',
        text: 'Are you sure you want to delete this product?',
        confirmButtonText: 'Yes, delete',
    });

    if (!confirmed) return;

    router.delete(route('dashboard-products-destroy', product.id), {
        only: ['products', 'notification'],
        onSuccess: () => {
            toast(
                'success',
                props?.notification?.success ?? 'Product deleted successfully',
            );
            close();
        },
        onError: () => {
            toast(
                'error',
                props?.notification?.danger ??
                    'Something went wrong! please check your fields and try again',
            );
        },
    });
};

const publishProduct = async (product: iProduct) => {
    const action = product.published ? 'Un-publish' : 'Publish';

    const confirmed = await confirmAction({
        title: `${action} Product`,
        text: `Are you sure you want to ${action.toLowerCase()} this product?`,
        confirmButtonText: `Yes, ${action.toLowerCase()}`,
        icon: 'question',
    });

    if (!confirmed) return;

    router.patch(
        route('dashboard-products-publish', product.id),
        {},
        {
            only: ['products', 'notification'],
            onSuccess: () => {
                toast(
                    'success',
                    props?.notification?.success ??
                        'Product status updated successfully',
                );
                close();
            },
            onError: () => {
                toast(
                    'error',
                    props?.notification?.danger ??
                        'Something went wrong! please check your fields and try again',
                );
            },
        },
    );
};

const promoteProduct = async (product: iProduct) => {
    const action = product.front ? 'Demote' : 'Promote';

    const confirmed = await confirmAction({
        title: `${action} Product`,
        text: `Are you sure you want to ${action.toLowerCase()} this product?`,
        confirmButtonText: `Yes, ${action.toLowerCase()}`,
    });

    if (!confirmed) return;

    router.patch(
        route('dashboard-products-promote', product.id),
        {},
        {
            only: ['products', 'notification'],
            onSuccess: () => {
                toast(
                    'success',
                    props?.notification?.success ??
                        'Product promotion status updated successfully',
                );
                close();
            },
            onError: () => {
                toast(
                    'error',
                    props?.notification?.danger ??
                        'Something went wrong! please check your fields and try again',
                );
            },
        },
    );
};

Quill.register('modules/table', Table);
// Quill.register('modules/table', TableUI)

const modules = {
    table: true,
    tableUI: true,
    name: 'tableUI',
    module: TableUI,
    options: {},
};

const handlePictureChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.picture = target.files[0];
    } else {
        form.picture = null;
    }
};

const activeTab = ref<string>('details');
const activateTab = (name: string) => {
    activeTab.value = name;
};
</script>

<template>
    <Modal :show="showProductDialog" max-width="5xl">
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
            <div class="mb-16 mt-3 px-3 py-3">
                <div class="inline-flex rounded-lg bg-gray-100 p-1">
                    <button
                        @click.prevent="activateTab('details')"
                        :class="[
                            'rounded-md px-4 py-2 text-sm font-medium transition-all duration-200',
                            activeTab === 'details'
                                ? 'bg-white text-primary-600 shadow-sm'
                                : 'text-gray-600 hover:text-gray-900',
                        ]"
                    >
                        Details
                    </button>
                    <button
                        @click.prevent="activateTab('features')"
                        :class="[
                            'rounded-md px-4 py-2 text-sm font-medium transition-all duration-200',
                            activeTab === 'features'
                                ? 'bg-white text-primary-600 shadow-sm'
                                : 'text-gray-600 hover:text-gray-900',
                        ]"
                    >
                        Attributes
                    </button>
                </div>
                <div class="mt-8">
                    <div v-if="activeTab == 'details'" class="space-y-6">
                        <div class="mb-6">
                            <InputLabel value="Title" />
                            <TextInput v-model="form.title" class="w-full" />
                            <InputError :message="form.errors.title" />
                        </div>
                        <div class="mb-6">
                            <InputLabel value="Summary" />
                            <TextareaInput
                                v-model="form.summary"
                                class="w-full"
                            />
                            <InputError :message="form.errors.summary" />
                        </div>
                        <div class="mb-6">
                            <InputLabel value="Select Picture" />
                            <input
                                class="block w-full cursor-pointer rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-900 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:placeholder-gray-400"
                                type="file"
                                @input="handlePictureChange($event)"
                            />
                            <InputError :message="form.errors.picture" />
                        </div>
                        <div class="h-48">
                            <!-- <InputLabel value="Body" /> -->
                            <InputError :message="form.errors.description" />
                            <QuillEditor
                                :modules="modules"
                                theme="snow"
                                v-model:content="form.description"
                                content-type="html"
                            />
                        </div>
                    </div>
                    <div v-else-if="activeTab == 'features'" class="space-y-6">
                        <ProductAttributes v-model="form.features" />
                    </div>
                </div>
            </div>
            <div class="m-3 flex justify-between rounded-lg border p-3">
                <PrimaryButton>Save</PrimaryButton>
                <SecondaryButton @click="cancel">Cancel</SecondaryButton>
            </div>
        </form>
    </Modal>
    <AuthenticatedLayout title="Products">
        <div v-if="category" class="pt-12">
            <div class="sm:px-6 lg:px-8">
                <div
                    class="flex flex-col overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg md:flex-row md:items-center"
                >
                    <div class="flex-1">
                        <div
                            class="text-xl font-semibold"
                            v-text="category.name"
                        ></div>
                        <div v-text="category.description"></div>
                        <div
                            v-text="
                                `${category.published ? 'Published' : 'Un-Published'}`
                            "
                            :class="{
                                'text-lime-600': category.published,
                                'text-red-600': !category.published,
                            }"
                        ></div>
                    </div>
                    <div class="flex flex-none gap-2">
                        <Link
                            class="inline-flex items-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-25"
                            :href="route('dashboard-products-categories')"
                        >
                            <Icon
                                class="h-4 w-4 object-contain"
                                type="arrow-back"
                            />
                            Categories</Link
                        >
                        <SecondaryButton @click="newProduct"
                            >New Product</SecondaryButton
                        >
                    </div>
                </div>
            </div>
        </div>
        <div class="py-12">
            <div class="sm:px-6 lg:px-8">
                <div v-if="!category" class="mb-6">
                    <SecondaryButton @click="newProduct"
                        >New Product</SecondaryButton
                    >
                </div>
                <div
                    class="overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg"
                >
                    <div class="flex flex-col gap-6">
                        <div
                            class="flex flex-col items-center justify-between gap-8 rounded-lg border border-primary-700 px-6 py-3 shadow-sm md:flex-row"
                            v-for="product in products.data"
                        >
                            <div class="flex-1">
                                <h4
                                    v-text="product?.title"
                                    class="text-lg font-semibold text-primary-600"
                                ></h4>
                                <div
                                    v-text="product?.summary"
                                    class="line-clamp-2 text-sm text-gray-600"
                                ></div>
                            </div>
                            <div class="flex flex-none gap-2">
                                <SecondaryButton @click="editProduct(product)"
                                    >Edit</SecondaryButton
                                >
                                <SecondaryButton @click="deleteProduct(product)"
                                    >Delete</SecondaryButton
                                >
                                <SecondaryButton
                                    @click="publishProduct(product)"
                                    class="whitespace-nowrap"
                                >
                                    {{
                                        product?.published
                                            ? 'Un Publish'
                                            : 'Publish'
                                    }}
                                </SecondaryButton>
                                <SecondaryButton
                                    @click="promoteProduct(product)"
                                >
                                    {{ product?.front ? 'Demote' : 'Promote' }}
                                </SecondaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
