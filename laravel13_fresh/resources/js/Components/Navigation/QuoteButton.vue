<!-- Components/Navigation/QuoteButton.vue -->
<template>
    <div class="relative">
        <Link
            :href="route('quote.index')"
            class="group inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-colors duration-200"
            :class="['bg-primary-600 text-white hover:bg-primary-700']"
        >
            <svg
                class="h-5 w-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                />
            </svg>
            <span>Get a Quote</span>

            <!-- Badge with item count -->
            <span
                v-if="uniqueProductsCount > 0"
                class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold"
                :class="['bg-secondary-500 text-white']"
            >
                {{ uniqueProductsCount }}
            </span>
        </Link>

        <!-- Quick preview dropdown on hover -->
        <div
            class="absolute right-0 top-full z-50 mt-2 w-80 origin-top-right scale-0 rounded-lg bg-white shadow-xl transition-all duration-200 group-hover:scale-100"
        >
            <div class="p-4">
                <div class="mb-3 flex items-center justify-between">
                    <h4 class="font-semibold text-gray-900">Quote Summary</h4>
                    <span class="text-sm text-gray-500"
                        >{{ totalItems }} items</span
                    >
                </div>

                <!-- Items list -->
                <div
                    v-if="items.length > 0"
                    class="max-h-60 space-y-3 overflow-y-auto"
                >
                    <div
                        v-for="item in items.slice(0, 3)"
                        :key="item.id"
                        class="flex items-center gap-3"
                    >
                        <div
                            class="h-10 w-10 flex-none overflow-hidden rounded-md"
                        >
                            <img
                                :src="item?.product?.picture as string ?? ''"
                                :alt="item?.product?.title"
                                class="h-full w-full object-cover"
                            />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p
                                class="truncate text-sm font-medium text-gray-900"
                            >
                                {{ item?.product?.title as string ?? '' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Qty: {{ item.quantity }}
                            </p>
                        </div>
                    </div>

                    <!-- Show "more items" indicator -->
                    <div v-if="items.length > 3" class="text-center">
                        <span class="text-sm text-gray-500"
                            >+{{ items.length - 3 }} more items</span
                        >
                    </div>
                </div>

                <!-- Empty state -->
                <div v-else class="py-8 text-center">
                    <svg
                        class="mx-auto h-12 w-12 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No items in quote</p>
                </div>

                <!-- Actions -->
                <div class="mt-4 space-y-2 border-t border-gray-100 pt-4">
                    <Link
                        :href="route('quote.index')"
                        class="block w-full rounded-lg bg-primary-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-primary-700"
                    >
                        View Full Quote
                    </Link>
                    <button
                        v-if="items.length > 0"
                        @click="clearQuote"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Clear All
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useQuoteStore } from '@/stores/quote';
import { Link } from '@inertiajs/vue3';

const quoteStore = useQuoteStore();
const items = quoteStore.items;
const totalItems = quoteStore.totalItems;
const uniqueProductsCount = quoteStore.uniqueProductsCount;

const clearQuote = () => {
    if (confirm('Are you sure you want to clear all items from your quote?')) {
        quoteStore.clearItems();
    }
};
</script>

<style scoped>
/* Custom scrollbar for dropdown */
.max-h-60::-webkit-scrollbar {
    width: 4px;
}

.max-h-60::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.max-h-60::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.max-h-60::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
