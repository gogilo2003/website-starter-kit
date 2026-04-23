<script lang="ts" setup>
import { iProductCategory } from '@/interfaces';
import { Link } from '@inertiajs/vue3';
import Icon from '../Icons/Icon.vue';

defineProps<{ category: iProductCategory }>();
</script>
<template>
    <div
        class="relative flex flex-col items-center gap-4 rounded-md rounded-tr-[5rem] border border-gray-200 bg-white p-3 transition-all duration-300 hover:drop-shadow-xl"
    >
        <div class="flex flex-none items-center gap-2">
            <div class="flex-none">
                <Icon
                    :type="category.icon"
                    class="h-12 w-12 text-secondary-600"
                />
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold" v-text="category.name"></h3>
                <p
                    class="line-clamp-4 text-xs text-gray-600"
                    v-text="category.description"
                ></p>
            </div>
        </div>
        <div class="relative w-full flex-1 overflow-hidden rounded-md border">
            <img
                :src="category.picture_url"
                :alt="category.name"
                class="h-full w-full object-cover"
            />
            <Link
                :href="route('products', { slug: category.slug })"
                class="absolute bottom-4 right-3 flex flex-nowrap items-center gap-1 rounded-full bg-secondary-600 px-3 py-3 text-xs font-semibold capitalize text-gray-50 transition-all duration-300 hover:bg-gray-800"
            >
                <Icon class="h-5 w-5" type="arrow-right" />
            </Link>
        </div>
    </div>
</template>
