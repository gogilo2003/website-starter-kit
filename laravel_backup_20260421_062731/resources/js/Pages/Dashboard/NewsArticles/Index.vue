<script setup lang="ts">
import { iNewsArticle, iNewsArticles, iNotification } from '@/interfaces';
import { router, useForm } from '@inertiajs/vue3';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import Icon from '../../../Components/Icons/Icon.vue';
import InputError from '../../../Components/InputError.vue';
import InputLabel from '../../../Components/InputLabel.vue';
import Modal from '../../../Components/Modal.vue';
import PrimaryButton from '../../../Components/PrimaryButton.vue';
import SecondaryButton from '../../../Components/SecondaryButton.vue';
import TextInput from '../../../Components/TextInput.vue';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout.vue';

const props = defineProps<{
    news_articles: iNewsArticles;
    notification: iNotification;
}>();

const form = useForm<{
    id: number | null;
    title: string;
    picture: File | null;
    content: string;
}>({
    id: null,
    title: '',
    picture: null,
    content: '',
});

const showNewsArticleDialog = ref(false);
const edit = ref(false);
const dialogTitle = ref('New News Article');

const close = () => cancel();
const cancel = () => {
    showNewsArticleDialog.value = false;
    form.id = null;
    form.title = '';
    form.picture = null;
    form.content = '';
    form.errors.id = '';
    form.errors.title = '';
    form.errors.picture = '';
    form.errors.content = '';
    form.reset();
};

const addNewsArticle = () => {
    showNewsArticleDialog.value = true;
    edit.value = false;
    dialogTitle.value = 'New News Article';
};

const editNewsArticle = (news_article: iNewsArticle) => {
    edit.value = true;
    showNewsArticleDialog.value = true;
    dialogTitle.value = 'Edit News Article';
    form.id = news_article.id;
    form.title = news_article.title;
    form.content = news_article.content;
    form.errors.id = '';
    form.errors.title = '';
    form.errors.picture = '';
    form.errors.content = '';
};

const submit = () => {
    if (edit.value) {
        form.transform((data) => {
            return { ...data, _method: 'patch' };
        }).post(
            route('dashboard-news_articles-update', { news_article: form.id }),
            {
                onSuccess: () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        icon: 'success',
                        text:
                            props?.notification?.success ??
                            'News Article Updated successfully',
                    });
                    close();
                },
                onError: () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        timer: 2000,
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
    } else {
        form.transform((data) => {
            return { ...data, _method: null };
        }).post(route('dashboard-news_articles-store'), {
            onSuccess: () => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: 'success',
                    text:
                        props?.notification?.success ??
                        'News Article Created Successfully',
                });
                close();
            },
            onError: () => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
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

const deleteNewsArticle = (news_article: iNewsArticle) => {
    console.log(news_article);
    if (confirm('Are you sure you want to delete this news_article?')) {
        router.delete(
            route('dashboard-news_articles-destroy', news_article.id),
            {
                onSuccess: () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        icon: 'success',
                        text:
                            props?.notification?.success ??
                            'News article deleted successfully',
                    });
                    close();
                },
                onError: () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        timer: 2000,
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

const publishNewsArticle = (news_article: iNewsArticle) => {
    if (
        confirm(
            `Are you sure you want to ${news_article.published ? 'un-publish' : 'publish'} this news_article?`,
        )
    ) {
        router.patch(
            route('dashboard-news_articles-publish', news_article.id),
            {},
            {
                onSuccess: () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        icon: 'success',
                        text:
                            props?.notification?.success ??
                            'News Article published status changed successfully',
                    });
                    close();
                },
                onError: () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        timer: 2000,
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

const promoteNewsArticle = (news_article: iNewsArticle) => {
    if (
        confirm(
            `Are you sure you want to ${news_article.front ? 'Demote' : 'Promote'} this news_article?`,
        )
    ) {
        router.patch(
            route('dashboard-news_articles-promote', news_article.id),
            {},
            {
                onSuccess: () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        icon: 'success',
                        text:
                            props?.notification?.success ??
                            'News Article promoted status changed',
                    });
                    close();
                },
                onError: () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        timer: 2000,
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

const handleChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.picture = target.files[0];
    }
};
</script>

<template>
    <Modal :show="showNewsArticleDialog" max-width="5xl">
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
                <div class="mb-6">
                    <InputLabel value="Title" />
                    <TextInput v-model="form.title" class="w-full" />
                    <InputError :message="form.errors.title" />
                </div>
                <div class="mb-6">
                    <InputLabel value="Select Picture" />
                    <input
                        class="block w-full cursor-pointer rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-900 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:placeholder-gray-400"
                        type="file"
                        @change="handleChange"
                    />
                    <InputError :message="form.errors.picture" />
                </div>
                <div class="h-48">
                    <!-- <InputLabel value="Body" /> -->
                    <InputError :message="form.errors.content" />
                    <QuillEditor
                        theme="snow"
                        v-model:content="form.content"
                        content-type="html"
                    />
                </div>
            </div>
            <div class="m-3 flex justify-between rounded-lg border p-3">
                <PrimaryButton>Save</PrimaryButton>
                <SecondaryButton @click="cancel">Cancel</SecondaryButton>
            </div>
        </form>
    </Modal>
    <AuthenticatedLayout title="NewsArticles">
        <div class="py-12">
            <div class="sm:px-6 lg:px-8">
                <div
                    class="flex w-full flex-col items-center justify-center md:flex-row md:justify-between"
                >
                    <SecondaryButton @click="addNewsArticle"
                        >New News Article</SecondaryButton
                    >
                </div>
                <div
                    class="overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg"
                >
                    <div class="flex flex-col gap-6">
                        <div
                            class="flex flex-col items-center justify-between gap-8 rounded-lg border border-primary-700 px-6 py-3 shadow-sm md:flex-row"
                            v-for="news_article in news_articles.data"
                        >
                            <div class="flex-1">
                                <h4
                                    v-text="news_article?.title"
                                    class="text-lg font-semibold text-primary-600"
                                ></h4>
                                <div
                                    v-html="news_article?.content"
                                    class="line-clamp-2 text-sm text-gray-600"
                                ></div>
                            </div>
                            <div class="flex w-full flex-none gap-2 md:w-96">
                                <SecondaryButton
                                    @click="editNewsArticle(news_article)"
                                    >Edit</SecondaryButton
                                >
                                <SecondaryButton
                                    @click="deleteNewsArticle(news_article)"
                                    >Delete</SecondaryButton
                                >
                                <SecondaryButton
                                    @click="publishNewsArticle(news_article)"
                                >
                                    {{
                                        news_article?.published
                                            ? 'Un Publish'
                                            : 'Publish'
                                    }}
                                </SecondaryButton>
                                <SecondaryButton
                                    @click="promoteNewsArticle(news_article)"
                                >
                                    {{
                                        news_article?.front
                                            ? 'Demote'
                                            : 'Promote'
                                    }}
                                </SecondaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
