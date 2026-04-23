<script setup lang="ts">
import { onMounted, ref } from 'vue';

const props = defineProps<{
    modelValue: string | number;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number): void;
}>();

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

const onInput = (event: Event) => {
    const target = event.target as HTMLInputElement;

    // If modelValue is a number, emit a number, otherwise a string
    const value =
        typeof props.modelValue === 'number'
            ? Number(target.value)
            : target.value;

    emit('update:modelValue', value);
};

defineExpose({
    focus: () => input.value?.focus(),
});
</script>

<template>
    <input
        ref="input"
        class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
        :value="modelValue"
        @input="onInput"
    />
</template>
