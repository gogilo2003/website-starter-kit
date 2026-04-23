<script setup lang="ts">
import Container from '@/Components/Container.vue';
</script>

<template>
    <div
        class="relative z-0 -mt-[5.785rem] bg-gray-200/30 px-8 pb-8 pt-56 text-3xl font-bold capitalize md:pb-10 md:pt-40"
    >
        <Container>
            <slot />
        </Container>
    </div>
</template>
