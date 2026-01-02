<script setup lang="ts">
import { computed } from 'vue';
import InputLabel from '../InputLabel.vue';
import SecondaryButton from '../SecondaryButton.vue';
import TextInput from '../TextInput.vue';

interface FilterForm {
    status: string;
    search: string;
    date_from: string;
    date_to: string;
    per_page: number;
}

const props = defineProps<{
    filterForm: FilterForm;
    statusOptions: Array<{ value: string; label: string }>;
}>();

const emit = defineEmits<{
    applyFilters: [];
    clearFilters: [];
    changePerPage: [];
}>();

const hasActiveFilters = computed(() => {
    return !!(
        props.filterForm.status ||
        props.filterForm.search ||
        props.filterForm.date_from ||
        props.filterForm.date_to
    );
});
</script>

<template>
    <div class="overflow-hidden rounded-lg bg-white p-6 shadow">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
            <!-- Status Filter -->
            <div>
                <InputLabel value="Status" />
                <select
                    v-model="filterForm.status"
                    @change="emit('applyFilters')"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                >
                    <option value="">All Statuses</option>
                    <option
                        v-for="option in statusOptions"
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </option>
                </select>
            </div>

            <!-- Search -->
            <div>
                <InputLabel value="Search" />
                <TextInput
                    v-model="filterForm.search"
                    @keyup.enter="emit('applyFilters')"
                    placeholder="Name, email, code..."
                    class="mt-1 w-full"
                />
            </div>

            <!-- Date From -->
            <div>
                <InputLabel value="From Date" />
                <input
                    v-model="filterForm.date_from"
                    @change="emit('applyFilters')"
                    type="date"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                />
            </div>

            <!-- Date To -->
            <div>
                <InputLabel value="To Date" />
                <input
                    v-model="filterForm.date_to"
                    @change="emit('applyFilters')"
                    type="date"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                />
            </div>

            <!-- Per Page -->
            <div>
                <InputLabel value="Per Page" />
                <select
                    v-model="filterForm.per_page"
                    @change="emit('changePerPage')"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                >
                    <option :value="10">10</option>
                    <option :value="15">15</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                </select>
            </div>
        </div>

        <div class="mt-4 flex gap-2">
            <SecondaryButton @click="emit('applyFilters')"
                >Apply Filters</SecondaryButton
            >
            <SecondaryButton
                v-if="hasActiveFilters"
                @click="emit('clearFilters')"
                >Clear Filters</SecondaryButton
            >
        </div>
    </div>
</template>
