<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import WebFooter from './WebFooter.vue';
import WebNavbar from './WebNavbar.vue';

defineProps({
    title: String,
});
</script>

<template>
    <div>
        <Head :title="title" />

        <div class="flex min-h-screen flex-col bg-white">
            <WebNavbar />
            <!-- Page Heading -->
            <header v-if="$slots.header" class="relative z-10 flex-none">
                <slot name="header" />
            </header>

            <!-- Page Content -->
            <main class="relative z-10 flex-1">
                <slot />
            </main>
            <WebFooter class="flex-none" />
        </div>
    </div>
</template>
