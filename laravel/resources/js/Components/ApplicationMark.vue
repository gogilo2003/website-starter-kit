<script lang setup>
import { computed } from 'vue';
const props = defineProps({
    dark: {
        type: Boolean,
        default: false
    }
})

const logo = computed(() => props.dark ? '/logo.png' : '/logo.png')

</script>
<template>
    <img class="h-full w-full object-contain" :src="logo" alt="">
</template>
