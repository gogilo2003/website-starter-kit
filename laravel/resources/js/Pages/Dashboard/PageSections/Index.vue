<script setup lang="ts">
import Paginator from '@/Components/Paginator.vue';
import { router, useForm } from '@inertiajs/vue3';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
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
    iElement,
    iNotification,
    iPageSection,
    iPageSections,
} from '../../../interfaces';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout.vue';
import ElementsManager from './ElementsManager.vue';

const props = defineProps<{
    page_sections: iPageSections;
    elements: iElement[];
    notification: iNotification;
}>();

const form = useForm<{
    id: number | null;
    title: string;
    description: string;
}>({
    id: null,
    title: '',
    description: '',
});

const showPageSectionDialog = ref(false);
const edit = ref(false);
const dialogTitle = ref('New PageSection');

const close = () => cancel();
const cancel = () => {
    showPageSectionDialog.value = false;
    form.id = null;
    form.title = '';
    form.description = '';
    form.errors.id = '';
    form.errors.title = '';
    form.errors.description = '';
    form.reset();
};

const addPageSection = () => {
    showPageSectionDialog.value = true;
    edit.value = false;
    dialogTitle.value = 'New Page Section';
};

const editPageSection = (page_section: iPageSection) => {
    edit.value = true;
    showPageSectionDialog.value = true;
    dialogTitle.value = 'Edit Page Section';
    form.id = page_section.id;
    form.title = page_section.title;
    form.description = page_section.description;
    form.errors.id = '';
    form.errors.title = '';
    form.errors.description = '';
};

const submit = () => {
    const method = edit.value ? 'patch' : 'post';
    const url = edit.value
        ? route('dashboard-page-sections-update', { page_section: form.id })
        : route('dashboard-page-sections-store');

    form.transform((data) => {
        return { ...data, _method: method };
    }).post(url, {
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
                    'Page Section saved successful!',
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
};

const deletePageSection = (page_section: iPageSection) => {
    if (confirm('Are you sure you want to delete this page_section?')) {
        router.delete(
            route('dashboard-page_sections-destroy', page_section.id),
            {
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
                            'Page Section deleted successfully',
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
            },
        );
    }
};

const showElementsManager = ref(false);
const selectedPageSection = ref<iPageSection | null>(null);
const manageElements = (pageSection: iPageSection) => {
    selectedPageSection.value = pageSection;
    showElementsManager.value = true;
};

const closeElementsManager = () => {
    selectedPageSection.value = null;
    showElementsManager.value = false;
};

const copyToClipboard = (value: string) => {
    try {
        navigator.clipboard
            .writeText(value)
            .then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: 'success',
                    text: 'Copied to clipboard',
                });
            })
            .catch((error) => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
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
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            icon: 'error',
            text: 'Failed to copy to clipboard',
        });
    }
};
</script>

<template>
    <ElementsManager
        :show="showElementsManager"
        :page-section="selectedPageSection"
        @close="closeElementsManager"
    />
    <Modal :show="showPageSectionDialog" max-width="3xl">
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
                    <InputLabel value="Title" />
                    <TextInput v-model="form.title" class="w-full" />
                    <InputError :message="form.errors.title" />
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
    <AuthenticatedLayout title="PageSections">
        <div class="px-8 pt-8">
            <div
                class="flex w-full flex-col items-center justify-center md:flex-row md:justify-between"
            >
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    PageSections
                </h2>
                <SecondaryButton @click="addPageSection"
                    >New PageSection</SecondaryButton
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
                            v-for="page_section in page_sections.data"
                        >
                            <div class="flex-1">
                                <h4
                                    v-text="page_section?.title"
                                    class="text-lg font-semibold text-primary-600"
                                ></h4>
                                <div class="flex items-center gap-1">
                                    <div
                                        v-text="page_section?.name"
                                        class="line-clamp-2 text-sm text-gray-600"
                                    ></div>
                                    <button
                                        @click="
                                            copyToClipboard(page_section?.name)
                                        "
                                    >
                                        <Icon type="copy" class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                            <div class="flex w-full flex-none gap-2 md:w-96">
                                <SecondaryButton
                                    @click="editPageSection(page_section)"
                                    >Edit</SecondaryButton
                                >
                                <SecondaryButton
                                    @click="deletePageSection(page_section)"
                                    >Delete</SecondaryButton
                                >
                                <SecondaryButton
                                    @click="manageElements(page_section)"
                                    >Manage Elements</SecondaryButton
                                >
                            </div>
                        </div>
                    </div>
                    <Paginator :items="page_sections" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
