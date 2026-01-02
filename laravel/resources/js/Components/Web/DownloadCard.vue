<template>
    <div
        class="flex flex-col items-center justify-center rounded-xl bg-white p-2 text-center shadow md:flex-row md:items-stretch md:justify-normal md:gap-2 md:p-2 md:text-left">
        <div class="h-16 w-16 flex-none">
            <Icon :type="type" class="h-full w-full object-contain" />
        </div>
        <div class="flex flex-1 flex-col">
            <div class="flex-1 text-xl font-medium" v-text="title"></div>
            <div class="flex flex-none flex-col items-center md:items-start">
                <p class="line-clamp-3 text-sm text-gray-800" v-text="description"></p>
                <div class="mt-2 flex items-center gap-1 text-sm">
                    <span class="font-medium">File Size:</span><span v-text="size"></span>
                </div>
            </div>
            <div class="flex flex-none items-center justify-center py-3 md:items-start md:justify-normal">
                <a class="flex items-center gap-2 transition-[color] duration-300 hover:text-primary-500" :href="url"
                    target="_blank">
                    <Icon type="download" class="h-6 w-6" />Download
                </a>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { iDownload } from '@/interfaces';
import Icon from '../Icons/Icon.vue';

const props = defineProps<{
    download: iDownload;
}>();

const { title, description, type, size, url } = props.download;
</script>
