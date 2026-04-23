<script setup lang="ts">
import { iElement, iElements, iNotification } from '@/interfaces';
import { router, useForm } from '@inertiajs/vue3';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { debounce } from 'lodash';
import Swal from 'sweetalert2';
import { ref, watch } from 'vue';
import Icon from '../../../Components/Icons/Icon.vue';
import InputError from '../../../Components/InputError.vue';
import InputLabel from '../../../Components/InputLabel.vue';
import Modal from '../../../Components/Modal.vue';
import Paginator from '../../../Components/Paginator.vue';
import PrimaryButton from '../../../Components/PrimaryButton.vue';
import SecondaryButton from '../../../Components/SecondaryButton.vue';
import TextareaInput from '../../../Components/TextareaInput.vue';
import TextInput from '../../../Components/TextInput.vue';
import useCopyText from '../../../Composables/useCopyText';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout.vue';

const { copiedText, copyText } = useCopyText();

const props = defineProps<{
    elements: iElements;
    notification: iNotification;
    searchVal: string;
}>();

const form = useForm<{
    id: number | null;
    title: string;
    picture: File | null;
    content: string;
    type: string;
    icon: string;
}>({
    id: null,
    title: '',
    picture: null,
    content: '',
    type: 'text',
    icon: '',
});

const showElementDialog = ref(false);
const edit = ref(false);
const dialogTitle = ref('New Element');

const close = () => cancel();
const cancel = () => {
    showElementDialog.value = false;
    form.id = null;
    form.title = '';
    form.picture = null;
    form.content = '';
    form.icon = '';
    form.type = 'text';
    form.errors.id = '';
    form.errors.title = '';
    form.errors.picture = '';
    form.errors.content = '';
    form.reset();
};

const addElement = () => {
    showElementDialog.value = true;
    edit.value = false;
    dialogTitle.value = 'New Element';
};

const editElement = (element: iElement) => {
    edit.value = true;
    showElementDialog.value = true;
    dialogTitle.value = 'Edit Element';
    form.id = element.id ?? null;
    form.title = element.title;
    form.content = element.content ?? '';
    form.icon = element.icon ?? '';
    form.type = element.type ?? 'text';
    form.errors.id = '';
    form.errors.title = '';
    form.errors.picture = '';
    form.errors.content = '';
};

const submit = () => {
    if (edit.value) {
        form.transform((data) => {
            return { ...data, _method: 'patch' };
        }).post(route('dashboard-elements-update', { element: form.id }), {
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
                        'Element updated successfully',
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
        }).post(route('dashboard-elements-store'), {
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
                        'Element created successfully',
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

const deleteElement = (element: iElement) => {
    console.log(element);
    if (confirm('Are you sure you want to delete this element?')) {
        router.delete(route('dashboard-elements-destroy', element.id), {
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
                        'Element deleted successfully',
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

const publishElement = (element: iElement) => {
    if (
        confirm(
            `Are you sure you want to ${element.published ? 'un-publish' : 'publish'} this element?`,
        )
    ) {
        router.patch(
            route('dashboard-elements-publish', element.id),
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
                            'Element publication status changed successfully',
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

const search = ref(props.searchVal);

const debouncedSearch = debounce((value: string) => {
    let data = {};

    if (value) {
        data = { search: value };
    }

    router.get(route('dashboard-elements'), data, {
        only: ['elements', 'searchVal'],
        preserveScroll: true,
        preserveState: true,
    });
}, 500);

watch(search, (newValue) => {
    if (newValue !== undefined) {
        debouncedSearch(newValue);
    }
});
// watch(
//     () => search.value,
//     debounce((value: string) => {
//         let data = {};

//         if (value) {
//             data = { search: value };
//         }

//         router.get(route('dashboard-elements'), data, {
//             only: ['elements', 'searchVal'],
//             preserveScroll: true,
//             preserveState: true,
//         });
//     }, 500),
// );

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.picture = target.files[0];
    }
};
</script>

<template>
    <Modal :show="showElementDialog" max-width="3xl">
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
                <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
                    <div class="mb-6">
                        <InputLabel value="Content Type" />
                        <select
                            v-model="form.type"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option value="text">Text</option>
                            <option value="multiline">Multi Line</option>
                            <option value="richtext">Rich Text</option>
                        </select>
                        <InputError :message="form.errors.type" />
                    </div>

                    <div class="mb-6">
                        <InputLabel value="Icon" />
                        <TextInput v-model="form.icon" class="w-full" />
                        <InputError :message="form.errors.icon" />
                    </div>

                    <div class="mb-6">
                        <InputLabel value="Select Picture" />
                        <input
                            class="block w-full cursor-pointer rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-900 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:placeholder-gray-400"
                            type="file"
                            @change="handleFileChange"
                        />
                        <InputError :message="form.errors.picture" />
                    </div>
                </div>
                <div class="mb-6" :class="{ 'h-56': form.type == 'richtext' }">
                    <InputLabel value="Content" />
                    <TextInput
                        v-if="form.type == 'text'"
                        rows="5"
                        v-model="form.content"
                        class="w-full"
                    />
                    <TextareaInput
                        v-if="form.type == 'multiline'"
                        rows="5"
                        v-model="form.content"
                        class="w-full"
                    />
                    <InputError :message="form.errors.content" />
                    <QuillEditor
                        v-if="form.type == 'richtext'"
                        v-model:content="form.content"
                        content-type="html"
                        class="w-full"
                    />
                </div>
            </div>
            <div
                class="m-3 flex justify-between rounded-lg border p-3"
                :class="{ 'mt-16': form.type == 'richtext' }"
            >
                <PrimaryButton>Save</PrimaryButton>
                <SecondaryButton @click="cancel">Cancel</SecondaryButton>
            </div>
        </form>
    </Modal>
    <AuthenticatedLayout title="Elements">
        <div class="py-12">
            <div class="sm:px-6 lg:px-8">
                <div class="flex items-center justify-between pb-6">
                    <TextInput v-model="search" placeholder="Search Value" />
                    <SecondaryButton @click="addElement"
                        >New Element</SecondaryButton
                    >
                </div>
                <div
                    class="overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg"
                >
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div
                            class="flex flex-col gap-8 rounded-lg border border-primary-700 px-6 py-3 shadow-sm"
                            v-for="element in elements.data"
                        >
                            <div class="flex gap-2">
                                <div
                                    class="h-16 w-16 flex-none"
                                    v-if="element?.photo || element?.icon"
                                >
                                    <img
                                        class="h-16 w-16 object-cover"
                                        v-if="element?.photo"
                                        :src="element?.photo"
                                        alt=""
                                    />
                                    <Icon
                                        class="h-16 w-16"
                                        v-if="element?.icon"
                                        :type="element?.icon"
                                        alt=""
                                    />
                                </div>
                                <div class="flex flex-1 flex-col gap-2">
                                    <h4
                                        v-text="element?.title"
                                        class="text-lg font-semibold text-primary-600"
                                    ></h4>
                                    <div class="relative">
                                        <span class="relative">
                                            <code
                                                class="text-xs"
                                                v-text="element?.name"
                                            ></code>
                                            <Icon
                                                class="absolute -right-6 bottom-[50%] h-5 w-5 translate-y-[50%] cursor-pointer"
                                                type="copy"
                                                @click="copyText(element?.name)"
                                            />
                                        </span>
                                    </div>
                                    <div
                                        v-html="element?.content"
                                        class="line-clamp-2 text-sm text-gray-600"
                                    ></div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <SecondaryButton @click="editElement(element)"
                                    >Edit</SecondaryButton
                                >
                                <SecondaryButton @click="deleteElement(element)"
                                    >Delete</SecondaryButton
                                >
                                <SecondaryButton
                                    @click="publishElement(element)"
                                >
                                    {{
                                        element?.published
                                            ? 'Un Publish'
                                            : 'Publish'
                                    }}
                                </SecondaryButton>
                            </div>
                        </div>
                    </div>
                    <div class="my-6">
                        <Paginator :items="elements" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
