<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { iQuote, iQuotes } from '../../interfaces';
import Icon from '../Icons/Icon.vue';

defineProps<{
    quotes: iQuotes;
    hasActiveFilters: boolean;
    formatDate: (date: string | Date | undefined | null) => string;
    formatCurrency: (amount: number | undefined) => string;
    getStatusBadgeClass: (status: string) => string;
    getStatusLabel: (status: string) => string;
}>();

const emit = defineEmits<{
    viewDetails: [quote: iQuote];
    updateStatus: [quote: iQuote];
    delete: [quote: iQuote];
}>();
</script>

<template>
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <!-- Empty State -->
        <div v-if="quotes.data.length === 0" class="p-12 text-center">
            <Icon type="document" class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900">
                No quotes found
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                {{
                    hasActiveFilters
                        ? 'Try adjusting your filters'
                        : 'No quote requests have been submitted yet'
                }}
            </p>
        </div>

        <!-- Quotes Table -->
        <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            Code
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            Customer
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            Email
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            Company
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            Status
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            Total
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            Views
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            Created
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr
                        v-for="quote in quotes.data"
                        :key="quote.id"
                        class="hover:bg-gray-50"
                    >
                        <td class="whitespace-nowrap px-6 py-4">
                            <span
                                class="font-mono text-sm font-semibold text-primary-600"
                                >{{ quote.code }}</span
                            >
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ quote.name }}
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-gray-500">
                                {{ quote.email }}
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-gray-500">
                                {{ quote.company || 'N/A' }}
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span
                                :class="[
                                    'inline-flex rounded-full border px-3 py-1 text-xs font-semibold',
                                    getStatusBadgeClass(quote.status),
                                ]"
                            >
                                {{ getStatusLabel(quote.status) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">
                                {{ formatCurrency(quote.total_amount) }}
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-gray-500">
                                {{ quote.view_count || 0 }}
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-gray-500">
                                {{ formatDate(quote.created_at) }}
                            </div>
                        </td>
                        <td
                            class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium"
                        >
                            <div class="flex justify-end gap-2">
                                <button
                                    @click="emit('viewDetails', quote)"
                                    class="text-primary-600 hover:text-primary-900"
                                    title="View Details"
                                >
                                    <Icon type="eye" class="h-5 w-5" />
                                </button>
                                <button
                                    @click="emit('updateStatus', quote)"
                                    class="text-blue-600 hover:text-blue-900"
                                    title="Update Status"
                                >
                                    <Icon
                                        type="pencil-square"
                                        class="h-5 w-5"
                                    />
                                </button>
                                <button
                                    @click="emit('delete', quote)"
                                    class="text-red-600 hover:text-red-900"
                                    title="Delete"
                                >
                                    <Icon type="trash" class="h-5 w-5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            v-if="quotes.data.length > 0"
            class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6"
        >
            <div
                class="flex flex-col items-center justify-between gap-4 sm:flex-row"
            >
                <div class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ quotes.from }}</span>
                    to
                    <span class="font-medium">{{ quotes.to }}</span>
                    of
                    <span class="font-medium">{{ quotes.total }}</span>
                    results
                </div>
                <div class="flex gap-2">
                    <Link
                        v-for="link in quotes.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        :class="[
                            'rounded-md border px-3 py-2 text-sm font-medium transition-colors',
                            link.active
                                ? 'border-primary-500 bg-primary-500 text-white'
                                : link.url
                                  ? 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                                  : 'cursor-not-allowed border-gray-200 bg-gray-100 text-gray-400',
                        ]"
                        :disabled="!link.url"
                        v-html="link.label"
                        preserve-scroll
                    />
                </div>
            </div>
        </div>
    </div>
</template>
