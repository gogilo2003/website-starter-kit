<script setup lang="ts">
const props = defineProps<{
    class?: string
    padded?: boolean
}>()

const baseClass = 'rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden'
const paddingClass = props.padded !== false ? 'p-4 sm:p-6' : ''
</script>

<template>
    <div :class="[baseClass, paddingClass, props.class]">
        <slot />
    </div>
</template>
