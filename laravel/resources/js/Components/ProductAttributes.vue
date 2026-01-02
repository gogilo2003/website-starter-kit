<template>
    <div class="space-y-4">
        <div
            v-for="(attribute, index) in features"
            :key="index"
            class="flex items-center gap-3"
        >
            <TextInput
                v-model="attribute.name"
                placeholder="Attribute name"
                @update:modelValue="
                    (value: string | number): string =>
                        updateAttribute(index, 'name', value)
                "
                size="sm"
                class="flex-1"
            />

            <TextInput
                v-model="attribute.value"
                placeholder="Attribute value"
                @update:modelValue="
                    (value: string | number) =>
                        updateAttribute(index, 'value', value)
                "
                size="sm"
                class="flex-1"
            />

            <button
                type="button"
                @click="removeAttribute(index)"
                class="flex h-8 w-8 items-center justify-center rounded-md border border-secondary-600 bg-secondary-500 text-white transition-colors hover:bg-secondary-600 disabled:cursor-not-allowed disabled:opacity-30 disabled:hover:bg-secondary-500"
                :disabled="features.length === 1"
                title="Remove attribute"
            >
                <Icon type="trash" class="h-4 w-4" />
            </button>
        </div>

        <button
            type="button"
            @click="addAttribute"
            class="inline-flex items-center rounded-md border border-transparent bg-primary-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
        >
            <Icon type="plus" class="mr-2 h-4 w-4" />
            Add Attribute
        </button>
    </div>
</template>

<script setup lang="ts">
import Icon from '@/Components/Icons/Icon.vue';
import TextInput from '@/Components/TextInput.vue';
import { iAttribute } from '@/interfaces';
import { ref, watch, type PropType } from 'vue';

const props = defineProps({
    modelValue: {
        type: Array as PropType<iAttribute[]>,
        required: true,
        default: () => [{ name: '', value: '' }],
    },
});

const emit = defineEmits<{
    'update:modelValue': [value: iAttribute[]];
}>();

const features = ref<iAttribute[]>([...props.modelValue]);

watch(
    () => props.modelValue,
    (newValue) => {
        features.value = [...newValue];
    },
    { deep: true },
);

watch(
    features,
    (newValue) => {
        emit('update:modelValue', [...newValue]);
    },
    { deep: true },
);

const updateAttribute = (
    index: number,
    field: keyof iAttribute,
    value: string | number,
): string => {
    // Convert to string if it's a number
    features.value[index][field] = String(value);
    return features.value[index][field];
};

const addAttribute = () => {
    features.value.push({ name: '', value: '' });
};

const removeAttribute = (index: number) => {
    if (features.value.length > 1) {
        features.value.splice(index, 1);
    }
};
</script>
