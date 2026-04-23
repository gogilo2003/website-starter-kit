<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Icon from './Icons/Icon.vue';

const props = defineProps<{
    name: string;
    items: Array<{
        name: string;
        icon: string;
        picture: string;
        slug: string;
        description: string;
        picture_url?: string;
    }>;
}>();

const classes = computed(() => {
    return route().current(props.name)
        ? 'text-primary-600'
        : 'bg-transparent text-gray-800 hover:text-primary-600 focus:text-primary-600';
});
</script>

<template>
    <div v-if="items?.length" class="group">
        <span :class="classes"
            class="inline-flex cursor-pointer items-center gap-2 px-3 py-6 text-lg font-medium capitalize transition duration-150 ease-in-out focus:outline-none">
            <span>
                <slot />
            </span>
            <Icon class="h-6 w-4" type="chevron-down" />
        </span>
        <div
            class="absolute left-0 top-[calc(100%_+_0px)] grid w-full grid-rows-[0fr] transition-[grid-template-rows] duration-500 group-hover:grid-rows-[1fr]">
            <div class="flex w-full flex-col overflow-y-hidden rounded-b-xl bg-gray-100 shadow-xl">
                <div class="grid grid-cols-3 gap-4 p-4">
                    <Link
                        class="relative inline-flex w-full items-center rounded-xl py-2 pl-4 pr-3 transition-colors duration-300 hover:bg-primary-500/5"
                        v-for="menu in items" :href="`${route(name, { slug: menu.slug })}`">
                    <div class="group flex items-center gap-2">
                        <div v-if="menu?.picture_url || menu?.icon"
                            class="h-24 w-24 flex-none rounded-full overflow-hidden border shadow-md">
                            <img class="h-full w-full object-cover rounded-full" v-if="menu?.picture_url"
                                :src="menu?.picture_url" :title="menu?.picture_url" />
                            <Icon v-if="menu?.icon && !menu?.picture_url" :type="menu?.icon"
                                class="h-full w-full object-contain text-primary-500" />
                        </div>
                        <div class="group flex-1 p-2">
                            <div v-text="menu?.name" class="font-medium uppercase text-primary-500"></div>
                            <div v-text="menu?.description" class="line-clamp-3 w-fit text-sm font-light text-gray-800">
                            </div>
                        </div>
                    </div>
                    </Link>
                </div>
            </div>
        </div>
    </div>
    <Link v-else :href="route(name)" :class="classes"
        class="inline-flex items-center px-3 py-2 text-base font-normal capitalize transition duration-150 ease-in-out focus:outline-none">
    <slot />
    </Link>
</template>
