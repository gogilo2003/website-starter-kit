<script lang="ts" setup>
import { iFunFact } from '@/interfaces';
import { onMounted, ref, watch } from 'vue';

const props = defineProps<{
    fact: iFunFact;
}>();

const displayValue = ref<string>();
const duration = 2000; // total animation time in ms

const animateValue = (start: number, end: number, duration: number) => {
    const startTime = performance.now();

    const update = (currentTime: number) => {
        const progress = Math.min((currentTime - startTime) / duration, 1);
        displayValue.value = new Intl.NumberFormat().format(
            Math.floor(start + (end - start) * progress),
        );
        if (progress < 1) requestAnimationFrame(update);
    };

    requestAnimationFrame(update);
};

onMounted(() => animateValue(0, props.fact.value, duration));

watch(
    () => props.fact.value,
    (newVal, oldVal) => animateValue(oldVal ?? 0, newVal, duration),
);
</script>

<template>
    <div class="flex flex-col items-center">
        <div class="text-center text-7xl font-bold">
            {{ displayValue }}
        </div>
        <div class="text-center uppercase tracking-wide text-gray-600">
            {{ fact.label }}
        </div>
    </div>
</template>
