<script setup lang="ts">
import { iQuote } from '../../interfaces';
import Icon from '../Icons/Icon.vue';
import InputError from '../InputError.vue';
import InputLabel from '../InputLabel.vue';
import Modal from '../Modal.vue';
import PrimaryButton from '../PrimaryButton.vue';
import SecondaryButton from '../SecondaryButton.vue';

defineProps<{
    show: boolean;
    quote: iQuote | null;
    statusForm: {
        status: string;
        processing: boolean;
        errors: {
            status?: string;
        };
    };
    statusOptions: Array<{ value: string; label: string }>;
}>();

const emit = defineEmits<{
    close: [];
    submit: [];
}>();
</script>

<template>
    <Modal :show="show" max-width="md" @close="emit('close')">
        <template #title>
            <div class="flex items-center justify-between bg-gray-100 p-4">
                <h3 class="text-lg font-semibold">Update Quote Status</h3>
                <Icon
                    type="close"
                    @click="emit('close')"
                    class="h-6 w-6 cursor-pointer rounded-full border border-gray-300 text-gray-600 transition-colors duration-300 hover:border-gray-500 hover:text-gray-800"
                />
            </div>
        </template>

        <form @submit.prevent="emit('submit')">
            <div class="p-6">
                <div v-if="quote" class="mb-4">
                    <p class="text-sm text-gray-600">
                        Quote:
                        <span class="font-semibold">{{ quote.code }}</span>
                    </p>
                    <p class="text-sm text-gray-600">
                        Customer:
                        <span class="font-semibold">{{ quote.name }}</span>
                    </p>
                </div>

                <div>
                    <InputLabel value="New Status" />
                    <select
                        v-model="statusForm.status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                        <option value="" disabled>Select status</option>
                        <option
                            v-for="option in statusOptions"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                    <InputError :message="statusForm.errors.status" />
                </div>
            </div>

            <div
                class="flex justify-between gap-2 border-t border-gray-200 p-4"
            >
                <SecondaryButton @click="emit('close')">Cancel</SecondaryButton>
                <PrimaryButton :disabled="statusForm.processing"
                    >Update Status</PrimaryButton
                >
            </div>
        </form>
    </Modal>
</template>
