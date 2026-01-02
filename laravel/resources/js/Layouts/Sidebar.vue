<script lang="ts" setup>
import Icon from '@/Components/Icons/Icon.vue';
import { iMenuItem } from '@/interfaces';
import { Link } from '@inertiajs/vue3';
defineProps<{
    links: iMenuItem[];
    closed: boolean;
}>();
</script>
<template>
    <div
        :class="{ 'w-72': !closed, 'mr-4 w-0': closed }"
        class="sticky top-0 h-screen bg-gray-800 text-primary-500 transition-all duration-500 ease-in-out"
    >
        <div class="my-8 ml-3 overflow-hidden">
            <div class="flex items-center gap-2">
                <img class="w-10 flex-none" src="/logo.png" alt="" />
                <div
                    class="flex-1 whitespace-nowrap text-lg font-light uppercase text-gray-200"
                >
                    <slot name="title" />
                </div>
            </div>
            <div class="flex flex-col gap-2 py-16">
                <Link
                    :class="{
                        'bg-primary-500 text-gray-50 hover:bg-primary-500/75':
                            active,
                        'bg-gray-100 text-gray-600 hover:bg-gray-100/75':
                            !active,
                    }"
                    class="flex items-center gap-2 whitespace-nowrap rounded-l-[2rem] px-6 py-4 text-base font-semibold uppercase transition-colors duration-300"
                    v-for="{ active, name, caption, icon } in links"
                    :href="route(name)"
                >
                    <Icon
                        class="h-6 w-6 flex-none object-contain"
                        :type="icon"
                    />
                    <span class="flex-1" v-text="caption"></span>
                </Link>
            </div>
        </div>
    </div>
</template>
