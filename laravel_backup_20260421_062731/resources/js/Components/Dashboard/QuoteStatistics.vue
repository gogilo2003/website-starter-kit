<script setup lang="ts">
import Icon from '../Icons/Icon.vue';

interface QuoteStatistics {
    total: number;
    pending: number;
    sent: number;
    viewed: number;
    completed: number;
    rejected: number;
    conversion_rate: number;
}

defineProps<{
    statistics: QuoteStatistics;
}>();
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Quotes -->
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-full bg-primary-500 p-3">
                            <Icon type="document" class="h-6 w-6 text-white" />
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt
                                class="truncate text-sm font-medium text-gray-500"
                            >
                                Total Quotes
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ statistics.total }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-full bg-yellow-500 p-3">
                            <Icon type="clock" class="h-6 w-6 text-white" />
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt
                                class="truncate text-sm font-medium text-gray-500"
                            >
                                Pending
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ statistics.pending }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-full bg-green-500 p-3">
                            <Icon type="check" class="h-6 w-6 text-white" />
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt
                                class="truncate text-sm font-medium text-gray-500"
                            >
                                Completed
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ statistics.completed }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversion Rate -->
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-full bg-blue-500 p-3">
                            <Icon type="chart-bar" class="h-6 w-6 text-white" />
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt
                                class="truncate text-sm font-medium text-gray-500"
                            >
                                Conversion Rate
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ statistics.conversion_rate }}%
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
