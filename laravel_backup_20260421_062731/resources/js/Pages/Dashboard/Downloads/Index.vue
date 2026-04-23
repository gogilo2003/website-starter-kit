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
    iDownload,
    iDownloadCategory,
    iDownloads,
    iNotification,
} from '@/interfaces';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, ref } from 'vue';

const props = defineProps<{
    downloads: iDownloads;
    category?: iDownloadCategory;
    notification?: iNotification;
}>();

const show = ref(false);

const form = useForm<{
    id: number | null;
    title: string;
    description: string;
    file: File | null;
    is_active: boolean;
    is_featured: boolean;
    category: number | null;
}>({
    id: null,
    title: '',
    description: '',
    file: null,
    is_active: true,
    is_featured: true,
    category: null,
});

const title = computed(() => (form.id ? 'Edit Download' : 'New Download'));
const newDownload = () => {
    form.id = null;
    form.title = '';
    form.description = '';
    form.file = null;
    form.is_active = true;
    form.is_featured = true;
    form.category = props.category?.id ?? null;

    show.value = true;
};

const close = () => {
    form.id = null;
    form.title = '';
    form.description = '';
    form.file = null;
    form.is_active = true;
    form.is_featured = true;
    form.category = props.category?.id || null;

    show.value = false;
};

const editDownload = (download: iDownload) => {
    form.id = download.id;
    form.title = download.title;
    form.description = download.description;
    form.file = null;
    form.is_active = download.is_active ? true : false;
    form.is_featured = download.is_featured ? true : false;
    form.category = props.category?.id ?? null;

    show.value = true;
};

const submit = () => {
    if (form.id) {
        form.transform((data) => ({ ...data, _method: 'patch' })).post(
            route('dashboard-downloads-update', form.id),
            {
                preserveScroll: true,
                preserveState: true,
                only: ['downloads', 'notification'],
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
                            'Download updated successfully',
                    });
                    close();
                },
            },
        );
    } else {
        form.post(route('dashboard-downloads-store'), {
            preserveScroll: true,
            preserveState: true,
            only: ['downloads', 'notification'],
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
                        'Download created successfully',
                });
                close();
            },
        });
    }
};

const deleteDownload = (id: number) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('dashboard-downloads-destroy', id), {
                preserveScroll: true,
                preserveState: true,
                only: ['downloads', 'notification'],
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
                            'Download deleted successfully',
                    });
                },
            });
        }
    });
};

const activate = (id: number) => {
    router.patch(
        route('dashboard-downloads-activate', id),
        {},
        {
            preserveScroll: true,
            preserveState: true,
            only: ['downloads', 'notification'],
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
                        'Download status updated successfully',
                });
            },
        },
    );
};

const feature = (id: number) => {
    router.patch(
        route('dashboard-downloads-feature', id),
        {},
        {
            preserveScroll: true,
            preserveState: true,
            only: ['downloads', 'notification'],
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
                        'Download feature status updated successfully',
                });
            },
        },
    );
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.file = target.files![0];
};
</script>

<template>
    <Modal :show="show">
        <div class="mb-3 border-b px-4 py-3">
            <div class="flex-1 text-lg font-medium" v-text="title"></div>
            <button @click="close"></button>
        </div>
        <form @submit.prevent="submit">
            <div class="px-4 py-3">
                <div class="mb-4">
                    <InputLabel value="Title" />
                    <TextInput v-model="form.title" class="w-full" />
                    <InputError :message="form.errors.title" />
                </div>
                <div class="mb-4">
                    <InputLabel value="Description" />
                    <TextareaInput v-model="form.description" class="w-full" />
                    <InputError :message="form.errors.description" />
                </div>
                <div class="mb-4">
                    <InputLabel value="File" />
                    <input
                        type="file"
                        @change="handleFileChange"
                        class="w-full"
                    />
                    <InputError :message="form.errors.file" />
                </div>
                <div
                    class="flex flex-col gap-2 md:flex-row md:items-center md:gap-4"
                >
                    <div class="mb-4">
                        <InputLabel>
                            <div class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    v-model="form.is_active"
                                />
                                <span>Active</span>
                            </div>
                        </InputLabel>
                        <InputError :message="form.errors.is_active" />
                    </div>
                    <div class="mb-4">
                        <InputLabel>
                            <div class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    v-model="form.is_featured"
                                />
                                <span>Featured</span>
                            </div>
                        </InputLabel>
                        <InputError :message="form.errors.is_featured" />
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <PrimaryButton type="submit">Save</PrimaryButton>
                    <SecondaryButton @click="close">Cancel</SecondaryButton>
                </div>
            </div>
        </form>
    </Modal>
    <AuthenticatedLayout title="Downloads">
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
                                `${category.is_active ? 'Active' : 'In-Active'}`
                            "
                            :class="{
                                'text-lime-600': category.is_active,
                                'text-red-600': !category.is_active,
                            }"
                        ></div>
                    </div>
                    <div class="flex flex-none gap-2">
                        <Link
                            class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-25"
                            :href="route('dashboard-downloads-categories')"
                        >
                            <Icon
                                class="h-4 w-4 object-contain"
                                type="arrow-back"
                            />
                            Categories</Link
                        >
                        <SecondaryButton @click="newDownload"
                            >New Download</SecondaryButton
                        >
                    </div>
                </div>
            </div>
        </div>
        <div class="relative py-12">
            <div class="relative flex flex-col gap-4 sm:px-6 lg:px-8">
                <div
                    class="relative flex items-center bg-white p-6 shadow-xl sm:rounded-lg"
                    v-for="download in downloads.data"
                >
                    <div class="flex-1">
                        <div
                            class="text-lg font-medium"
                            v-text="download.title"
                        ></div>
                        <div
                            class="text-sm font-light text-gray-700"
                            v-text="download.description"
                        ></div>
                        <div class="flex gap-4">
                            <div
                                class="text-sm font-light text-gray-700"
                                v-text="download.name"
                            ></div>
                            <div
                                class="text-sm font-light text-gray-700"
                                v-text="download.size"
                            ></div>
                            <div
                                class="text-sm font-light text-gray-700"
                                v-text="download.category"
                            ></div>
                        </div>
                    </div>
                    <div class="relative flex flex-none gap-1">
                        <SecondaryButton
                            class="whitespace-nowrap"
                            @click="activate(download.id)"
                            >{{
                                download.is_active ? 'Deactivate' : 'Activate'
                            }}
                        </SecondaryButton>
                        <SecondaryButton
                            class="whitespace-nowrap"
                            @click="feature(download.id)"
                            >{{
                                download.is_featured ? 'Un-feature' : 'Feature'
                            }}
                        </SecondaryButton>
                        <SecondaryButton
                            class="whitespace-nowrap"
                            @click="deleteDownload(download.id)"
                        >
                            Delete
                        </SecondaryButton>
                        <SecondaryButton
                            class="whitespace-nowrap"
                            @click="editDownload(download)"
                            >Edit
                        </SecondaryButton>
                    </div>
                </div>
                <Paginator :items="downloads" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
