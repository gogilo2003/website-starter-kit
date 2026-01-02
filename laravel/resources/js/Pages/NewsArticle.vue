<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';
import Container from '../Components/Container.vue';
import Heading4 from '../Components/Web/Heading4.vue';
import WebHeader from '../Layouts/WebHeader.vue';
import WebLayout from '../Layouts/WebLayout.vue';

interface iNewsArticle {
    id: Number;
    title: string;
    author: string;
    url: string;
    picture: string;
    date: string;
    content: string;
}
defineProps<{
    news_article: iNewsArticle;
    news_articles: iNewsArticle[];
}>();
</script>

<template>
    <WebLayout :title="news_article?.title ?? ''">
        <template #header>
            <WebHeader>{{ news_article?.title ?? '' }}</WebHeader>
        </template>
        <Container class="py-12">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                <div class="md:col-span-3">
                    <div class="prose min-w-full">
                        <div class="h-96">
                            <img
                                class="m-0 h-full w-full rounded-lg border border-primary-50 bg-bottom object-cover p-0"
                                :src="news_article.picture"
                                :alt="news_article.title"
                            />
                        </div>
                        <div class="my-3 flex gap-4">
                            <div class="flex gap-2">
                                <span class="font-medium">Posted On:</span>
                                <span
                                    v-text="news_article.date"
                                    class="italic"
                                ></span>
                            </div>
                            <!-- <div class="flex gap-2">
                                <span class="font-medium">By:</span>
                                <span v-text="news_article.author" class="italic"></span>
                            </div> -->
                        </div>
                        <div
                            v-html="news_article.content"
                            class="prose max-w-full"
                        ></div>
                    </div>
                </div>
                <div class="rounded-lg bg-gray-50 px-6">
                    <Heading4>Other News Articles</Heading4>
                    <div class="mt-6 flex flex-col gap-3">
                        <Link
                            :href="url"
                            v-for="{ url, picture, title } in news_articles"
                            class="flex items-center gap-2"
                        >
                            <div class="h-14 w-14 flex-none">
                                <img
                                    class="h-full w-full object-cover"
                                    :src="picture"
                                    :alt="title"
                                />
                            </div>
                            <div class="flex-1" v-text="title"></div>
                        </Link>
                    </div>
                </div>
            </div>
        </Container>
    </WebLayout>
</template>
