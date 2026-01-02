<script setup lang="ts">
import { iPageSection } from '@/interfaces';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { iElement } from '../../interfaces';

// Ensure a fallback object structure
const customers = computed<iPageSection | null>(() => {
    return (usePage().props?.customers as iPageSection) || null;
});

// Duplicate customers for seamless infinite scroll
const loopedCustomers = computed(() => {
    let data: iElement[] = [];
    if (customers.value) {
        data = [
            ...(customers.value?.elements ?? []),
            ...(customers.value?.elements ?? []),
        ];
    }
    return data;
});
</script>

<template>
    <div
        v-if="customers?.elements?.length"
        class="relative overflow-hidden py-2"
    >
        <div class="animate-scroll flex hover:[animation-play-state:paused]">
            <div
                v-for="(customer, index) in loopedCustomers"
                :key="index"
                class="mx-4 inline-flex h-16 opacity-80 transition hover:opacity-100 md:h-16"
            >
                <img
                    :src="customer.photo ?? ''"
                    :alt="customer.title"
                    class="h-full w-auto rounded-md object-contain"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes scroll {
    from {
        transform: translateX(0);
    }

    to {
        transform: translateX(-50%);
    }
}

.animate-scroll {
    display: flex;
    width: max-content;
    animation: scroll 25s linear infinite;
}
</style>
