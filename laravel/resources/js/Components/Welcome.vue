<script lang="ts" setup>
import ApplicationMark from '@/Components/ApplicationMark.vue';

</script>

<template>
    <div>
        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
            <div class="block w-56">
                <ApplicationMark dark />
            </div>

            <h1 class="mt-8 text-2xl font-medium text-gray-900">
                Welcome to {{ $page.props.appName }}
            </h1>

            <p class="mt-6 text-gray-500 leading-relaxed">
                Manage content of your website here; add, edit and delete content items ranging from elements, slides
                and
                products, partners, goals etc
            </p>
        </div>

    </div>
</template>
