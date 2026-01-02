<script lang="ts" setup>
import { useAdminLinks } from '@/Composables/useAdminLinks';
import { Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import NavBar from './NavBar.vue';
import Sidebar from './Sidebar.vue';

defineProps({
    title: String,
});

const links = useAdminLinks();

const toggleSidebar = ref<boolean>(
    localStorage.getItem('toggleSidebar') == '1' ? true : false,
);

const onToggle = (value: boolean) => {
    toggleSidebar.value = value;
};

watch(
    () => toggleSidebar.value,
    (value: boolean) => {
        let item: string | null = null;
        if (value) {
            item = '1';
            localStorage.setItem('toggleSidebar', item);
        } else {
            item = null;
            localStorage.removeItem('toggleSidebar');
        }
    },
);
</script>

<template>
    <div class="transition-[width] duration-500">
        <Head :title="title" />
        <div
            class="flex min-h-screen bg-gray-800 transition-[width] duration-500"
        >
            <Sidebar :closed="toggleSidebar" :links="links">
                <template #title>{{ title }}</template>
            </Sidebar>
            <div class="flex flex-1 flex-col transition-[width] duration-500">
                <NavBar :links="links" @toggle="onToggle" class="flex-none">
                    <template v-if="$slots.header" #header>
                        {{ title }}
                    </template>
                </NavBar>
                <!-- Page Content -->
                <main class="flex flex-1 pb-4 pr-4">
                    <div class="h-full flex-1 rounded-3xl bg-gray-100">
                        <slot />
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>
