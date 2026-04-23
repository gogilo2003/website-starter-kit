<script lang="ts" setup>
import { computed, onMounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import Icon from './Icons/Icon.vue';

const props = defineProps<{
    name: string,
    items: Array<{
        name: string,
        caption: string
    }>
}>();

const classes = computed(() => {
    return route().current(props.name)
        ? 'border-secondary-600 text-secondary-600 focus:border-secondary-600'
        : 'border-transparent text-primary-700 hover:text-primary-800 hover:border-primary-800 focus:text-primary-800 focus:border-primary-800';
});

</script>

<template>
    <Dropdown v-if="items?.length" width="96">
        <template #trigger>
            <span :class="classes"
                class="gap-2 inline-flex items-center cursor-pointer px-1 py-8 border-b-2 transition duration-150 ease-in-out focus:outline-none text-lg font-medium  uppercase">
                <span>
                    <slot />
                </span>
                <Icon class="h-6 w-4" type="chevron-down" />
            </span>
        </template>
        <template #content>
            <div class="py-3 flex flex-col gap-1 w-fit z-40">
                <Link
                    class="uppercase relative w-full pr-3 whitespace-nowrap inline-flex items-center bg-gray-50 hover:to-primary-100 hover:via-gray-100 hover:bg-gradient-to-br hover:from-gray-100 transition duration-300 ease-in-out before:rounded-r before:w-2 before:bg-primary-500 before:absolute before:left-0 before:h-full pl-4 py-2 "
                    v-for="menu in items" :href="route(name, menu.name)">{{
                        menu.caption }}
                </Link>
            </div>
        </template>
    </Dropdown>
    <Link v-else :href="route(name)" :class="classes"
        class="inline-flex items-center px-1 py-8 border-b-2 transition duration-150 ease-in-out focus:outline-none text-lg font-medium  uppercase">
    <slot />
    </Link>
</template>
