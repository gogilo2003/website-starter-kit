<script setup lang="ts">
import Paginator from '@/Components/Paginator.vue';
import { iNotification, iSlide, iSlides } from '@/interfaces';
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
    slides: iSlides;
    notification?: iNotification;
}>();

const form = useForm<{
    id: number | null;
    title: string;
    picture: File | null;
    caption: string;
}>({
    id: null,
    title: '',
    picture: null,
    caption: '',
});

const showSlideDialog = ref(false);
const edit = ref(false);
const dialogTitle = ref('New Slide');

const close = () => cancel();
const cancel = () => {
    showSlideDialog.value = false;
    form.id = null;
    form.title = '';
    form.picture = null;
    form.caption = '';
    form.errors.id = '';
    form.errors.title = '';
    form.errors.picture = '';
    form.errors.caption = '';
    form.reset();
};

const addSlide = () => {
    showSlideDialog.value = true;
    edit.value = false;
    dialogTitle.value = 'New Slide';
};

const editSlide = (slide: iSlide) => {
    edit.value = true;
    showSlideDialog.value = true;
    dialogTitle.value = 'Edit Slide';
    form.id = slide.id;
    form.title = slide.title;
    form.caption = slide.caption;
    form.errors.id = '';
    form.errors.title = '';
    form.errors.picture = '';
    form.errors.caption = '';
};

const submit = () => {
    if (edit.value) {
        form.transform((data) => {
            return { ...data, _method: 'patch' };
        }).post(route('dashboard-slides-update', { slide: form.id }), {
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
                        'Slide updated successfully',
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
        }).post(route('dashboard-slides-store'), {
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
                        'Slide created successfully',
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

const deleteSlide = (slide: iSlide) => {
    console.log(slide);
    if (confirm('Are you sure you want to delete this slide?')) {
        router.delete(route('dashboard-slides-destroy', slide.id), {
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
                        'Slide deleted successfully',
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

const publishSlide = (slide: iSlide) => {
    if (
        confirm(
            `Are you sure you want to ${slide?.published ? 'un-publish' : 'publish'} this slide?`,
        )
    ) {
        router.patch(
            route('dashboard-slides-publish', slide.id),
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
                            'Slide publication status updated successfully',
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

const handleFileInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.picture = target.files[0];
    } else {
        form.picture = null;
    }
};
</script>

<template>
    <Modal :show="showSlideDialog" max-width="3xl">
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
            <div class="mt-6 px-3 py-3">
                <div class="mb-6">
                    <InputLabel value="Title" />
                    <TextInput v-model="form.title" class="w-full" />
                    <InputError :message="form.errors.title" />
                </div>
                <div class="mb-6">
                    <InputLabel value="Caption" />
                    <TextareaInput
                        rows="5"
                        v-model="form.caption"
                        class="w-full"
                    />
                    <InputError :message="form.errors.caption" />
                </div>
                <div class="mb-6">
                    <InputLabel value="Select Picture/Video" />
                    <input
                        class="block w-full cursor-pointer rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-900 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:placeholder-gray-400"
                        type="file"
                        @input="handleFileInput($event)"
                    />
                    <InputError :message="form.errors.picture" />
                </div>
            </div>
            <div class="m-3 flex justify-between rounded-lg border p-3">
                <PrimaryButton>Save</PrimaryButton>
                <SecondaryButton @click="cancel">Cancel</SecondaryButton>
            </div>
        </form>
    </Modal>
    <AuthenticatedLayout title="Slides">
        <template #header>
            <div
                class="flex w-full flex-col items-center justify-center md:flex-row md:justify-between"
            >
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Slides
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="sm:px-6 lg:px-8">
                <div class="mb-6 flex flex-col justify-between md:flex-row">
                    <div class=""></div>
                    <SecondaryButton @click="addSlide"
                        >New Slide</SecondaryButton
                    >
                </div>
                <div
                    class="overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg"
                >
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div
                            class="flex flex-col items-center gap-8 rounded-lg border border-primary-700 px-6 py-3 shadow-sm"
                            v-for="slide in slides.data"
                        >
                            <div>
                                <div class="h-64 w-full">
                                    <component
                                        :is="
                                            slide.media_type == 'picture' ||
                                            slide.media_type == 'image'
                                                ? 'img'
                                                : 'video'
                                        "
                                        :src="slide.picture"
                                        alt=""
                                        class="h-full w-full"
                                        :class="
                                            slide.media_type == 'picture' ||
                                            slide.media_type == 'image'
                                                ? 'object-cover'
                                                : 'object-contain'
                                        "
                                    ></component>
                                </div>
                                <h4
                                    v-text="slide?.title"
                                    class="text-lg font-semibold text-primary-600"
                                ></h4>
                                <div
                                    v-text="slide?.caption"
                                    class="line-clamp-2 text-sm text-gray-600"
                                ></div>
                            </div>
                            <div class="flex gap-2">
                                <SecondaryButton @click="editSlide(slide)"
                                    >Edit</SecondaryButton
                                >
                                <SecondaryButton @click="deleteSlide(slide)"
                                    >Delete</SecondaryButton
                                >
                                <SecondaryButton @click="publishSlide(slide)">
                                    {{
                                        slide?.published
                                            ? 'Un Publish'
                                            : 'Publish'
                                    }}
                                </SecondaryButton>
                            </div>
                        </div>
                    </div>
                    <Paginator :items="slides" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
