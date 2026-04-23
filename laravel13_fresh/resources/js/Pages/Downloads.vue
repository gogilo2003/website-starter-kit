<script setup lang="ts">
import WebLayout from '@/Layouts/WebLayout.vue';
import { iDownload, iDownloadCategory } from '../interfaces';
import Container from '@/Components/Container.vue';
import DownloadCard from '@/Components/Web/DownloadCard.vue';
import WebHeader from '@/Layouts/WebHeader.vue';
import Heading2 from '@/Components/Web/Heading2.vue';

defineProps<{
    category: iDownloadCategory,
    downloads: iDownload[]
}>()

</script>

<template>
    <WebLayout :title="category?.name ?? 'Downloads'">
        <template #header>
            <WebHeader>
                <Heading2>Downloads</Heading2>
                <h3 class="capitalize" v-text="category?.name"></h3>
                <p class="text-base capitalize" v-text="category?.description"></p>
            </WebHeader>
        </template>
        <div class="bg-white pt-2 pb-12">
            <Container>
                <div class="flex md:items-stretch md:justify-center flex-col md:flex-row gap-4 md:gap-8 mt-8 flex-wrap">
                    <DownloadCard class="w-full md:w-[calc(50%_-_2rem)]" :download="download"
                        v-for="download in downloads" />
                </div>
            </Container>
        </div>
    </WebLayout>
</template>
