<script setup lang="ts">
import Heading2 from '@/Components/Web/Heading2.vue';
import NewsCard from '@/Components/Web/NewsCard.vue';
import Container from '../Components/Container.vue';
import { iNewsArticle } from '../interfaces';
import WebHeader from '../Layouts/WebHeader.vue';
import WebLayout from '../Layouts/WebLayout.vue';

defineProps<{
    news_articles: iNewsArticle[];
    news_article_intro: string;
}>();
</script>

<template>
    <WebLayout title="News">
        <template #header>
            <WebHeader>
                <Heading2>News</Heading2>
            </WebHeader>
        </template>
        <Container class="py-24">
            <div
                v-if="news_article_intro"
                class="mb-16 text-center text-lg"
                v-text="news_article_intro"
            ></div>
            <div class="grid grid-cols-1 gap-12 md:grid-cols-3">
                <NewsCard
                    :news_article="news_article"
                    v-for="news_article in news_articles"
                />
            </div>
        </Container>
    </WebLayout>
</template>
