<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    active: Boolean,
    href: String,
    as: String,
});

const classes = computed(() => {
    return props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-primary-400 text-start text-base font-medium text-primary-50  focus:outline-none focus:text-primary-800 focus:border-primary-700 transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-50  hover:text-gray-100 hover: hover:border-primary-900 focus:outline-none focus:text-primary-800 focus:border-primary-300 transition duration-150 ease-in-out';
});
</script>

<template>
    <div>
        <button v-if="as == 'button'" :class="classes" class="text-start">
            <slot />
        </button>

        <Link v-else :href="href" :class="classes">
        <slot />
        </Link>
    </div>
</template>
