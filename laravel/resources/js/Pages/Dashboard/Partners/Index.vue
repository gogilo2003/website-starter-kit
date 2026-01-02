<script setup lang="ts">
import { iNotification, iPartner } from '@/interfaces';
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
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout.vue';

const props = defineProps<{
    partners: iPartner[];
    notification: iNotification;
}>();

const form = useForm<{
    id: number | null;
    title: string;
    logo: File | null;
    website: string;
    description: string;
}>({
    id: null,
    title: '',
    logo: null,
    website: '',
    description: '',
});

const showPartnerDialog = ref(false);
const edit = ref(false);
const dialogTitle = ref('New Partner');

const close = () => cancel();
const cancel = () => {
    showPartnerDialog.value = false;
    form.id = null;
    form.title = '';
    form.logo = null;
    form.website = '';
    form.description = '';
    form.errors.id = '';
    form.errors.title = '';
    form.errors.logo = '';
    form.errors.website = '';
    form.errors.description = '';
    form.reset();
};

const addPartner = () => {
    showPartnerDialog.value = true;
    edit.value = false;
    dialogTitle.value = 'New Partner';
};

const editPartner = (partner: iPartner) => {
    edit.value = true;
    showPartnerDialog.value = true;
    dialogTitle.value = 'Edit Partner';
    form.id = partner.id;
    form.title = partner.title;
    form.website = partner.website;
    form.description = partner.description;
    form.errors.id = '';
    form.errors.title = '';
    form.errors.logo = '';
    form.errors.website = '';
    form.errors.description = '';
};

const submit = () => {
    if (edit.value) {
        form.transform((data) => {
            return { ...data, _method: 'patch' };
        }).post(route('dashboard-partners-update', { partner: form.id }), {
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
                        'Partner updated successfully',
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
        }).post(route('dashboard-partners-store'), {
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
                        'Partner created successfully',
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

const deletePartner = (partner: iPartner) => {
    console.log(partner);
    if (confirm('Are you sure you want to delete this partner?')) {
        router.delete(route('dashboard-partners-destroy', partner.id), {
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
                        'Partner deleted successfully',
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

const publishPartner = (partner: iPartner) => {
    if (
        confirm(
            `Are you sure you want to ${partner.published ? 'un-publish' : 'publish'} this partner?`,
        )
    ) {
        router.patch(
            route('dashboard-partners-publish', partner.id),
            {},
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
                            'Partner status updated successfully',
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

const promotePartner = (partner: iPartner) => {
    if (
        confirm(
            `Are you sure you want to ${partner.front ? 'Demote' : 'Promote'} this partner?`,
        )
    ) {
        router.patch(
            route('dashboard-partners-promote', { partner: partner.id }),
            {},
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
                            'Partner promotion status updated successfully',
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
const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.logo = target.files[0];
    } else {
        form.logo = null;
    }
};
</script>

<template>
    <Modal :show="showPartnerDialog" max-width="3xl">
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
                    <InputLabel value="Website" />
                    <TextInput v-model="form.website" class="w-full" />
                    <InputError :message="form.errors.website" />
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
    <AuthenticatedLayout title="Partners">
        <div class="py-12">
            <div class="sm:px-6 lg:px-8">
                <div class="mb-6">
                    <SecondaryButton @click="addPartner"
                        >New Partner</SecondaryButton
                    >
                </div>
                <div
                    class="overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg"
                >
                    <div class="flex flex-col gap-6">
                        <div
                            class="flex flex-col items-center justify-between gap-8 rounded-lg border border-primary-700 px-6 py-3 shadow-sm md:flex-row"
                            v-for="partner in partners"
                        >
                            <div class="flex-1">
                                <h4
                                    v-text="partner?.title"
                                    class="text-lg font-semibold text-primary-600"
                                ></h4>
                                <div
                                    v-text="partner?.website"
                                    class="line-clamp-2 text-sm text-gray-600"
                                ></div>
                            </div>
                            <div class="flex w-full flex-none gap-2 md:w-96">
                                <SecondaryButton @click="editPartner(partner)"
                                    >Edit</SecondaryButton
                                >
                                <SecondaryButton @click="deletePartner(partner)"
                                    >Delete</SecondaryButton
                                >
                                <SecondaryButton
                                    @click="publishPartner(partner)"
                                >
                                    {{
                                        partner?.published
                                            ? 'Un Publish'
                                            : 'Publish'
                                    }}
                                </SecondaryButton>
                                <SecondaryButton
                                    @click="promotePartner(partner)"
                                >
                                    {{ partner?.front ? 'Demote' : 'Promote' }}
                                </SecondaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
