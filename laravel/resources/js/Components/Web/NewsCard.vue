<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { iNewsArticle } from '../../interfaces';
import Icon from '../Icons/Icon.vue';
defineProps<{
    news_article: iNewsArticle | null;
}>();
</script>
<template>
    <div class="relative">
        <img
            class="relative z-0 h-64 w-full rounded-xl object-cover"
            :src="news_article?.picture ?? ''"
            alt=""
        />
        <div
            class="relative -top-10 left-5 z-10 flex flex-col gap-4 rounded-2xl bg-gray-50 px-8 py-5"
        >
            <div class="flex justify-between">
                <div v-text="news_article?.date"></div>
                <div v-text="news_article?.author"></div>
            </div>
            <div class="flex flex-col gap-3">
                <div
                    v-text="news_article?.title"
                    class="flex-none text-xl font-semibold"
                ></div>
                <div
                    v-html="news_article?.content"
                    class="line-clamp-2 flex-none"
                ></div>
                <Link
                    :href="news_article?.url"
                    class="flex flex-none flex-nowrap items-center gap-1"
                >
                    <Icon type="arrow-forward" class="h-4 w-4 flex-none" />
                    <span class="flex-1">Read More</span>
                </Link>
            </div>
        </div>
    </div>
</template>
