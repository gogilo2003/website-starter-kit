<script lang="ts" setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ChartData } from 'chart.js';
import Card from '../Components/Card.vue';
import BarChart from '../Components/Charts/BarChart.vue';
const props = defineProps<{
    pageVisits: {
        route_name: string;
        total_unique_visits: number;
        total_visits: number;
        last_updated: string;
        repeat_visits: number;
    }[];
    summary: any;
}>();
const chartData: ChartData = {
    labels: props.pageVisits.map((stat) => stat.route_name),
    datasets: [
        {
            label: 'Unique Visits',
            data: props.pageVisits.map((stat) => stat.total_unique_visits),
            backgroundColor: 'rgba(241, 97, 51,0.5)',
        },
        {
            label: 'Repeat Visits',
            data: props.pageVisits.map((stat) => stat.repeat_visits),
            backgroundColor: 'rgba(246, 168, 32,0.5)',
        },
    ],
};
</script>

<template>
    <AuthenticatedLayout title="Dashboard">
        <div class="px-8 py-12">
            <div class="overflow-hidden bg-white p-8 shadow-xl sm:rounded-lg">
                <div class="space-y-6">
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <Card class="bg-white p-4">
                            <h3 class="text-sm font-medium text-gray-500">
                                Tracked Routes
                            </h3>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ summary.totalRoutes }}
                            </p>
                        </Card>

                        <Card class="bg-white p-4">
                            <h3 class="text-sm font-medium text-gray-500">
                                Total Unique Visits
                            </h3>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ summary.totalUniqueVisits }}
                            </p>
                        </Card>

                        <Card class="bg-white p-4">
                            <h3 class="text-sm font-medium text-gray-500">
                                Total Visits
                            </h3>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ summary.totalVisits }}
                            </p>
                        </Card>

                        <Card class="bg-white p-4">
                            <h3 class="text-sm font-medium text-gray-500">
                                Most Visited
                            </h3>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ summary.mostVisited?.route_name }} ({{
                                    summary.mostVisited?.total_visits
                                }})
                            </p>
                        </Card>
                    </div>

                    <div class="flex flex-col gap-4 md:flex-row">
                        <!-- Bar Chart -->
                        <Card class="flex-1 bg-white p-4">
                            <h2 class="mb-4 text-lg font-medium text-gray-900">
                                Visits by Route
                            </h2>
                            <BarChart :data="chartData" />
                        </Card>

                        <!-- Detailed Stats Table -->
                        <Card
                            class="w-full flex-none overflow-hidden bg-white shadow-sm sm:rounded-lg md:w-5/12"
                        >
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full divide-y divide-gray-200"
                                >
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Route
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Unique Visits
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Repeat Visits
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Total Visits
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Last Updated
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="divide-y divide-gray-200 bg-white"
                                    >
                                        <tr
                                            v-for="stat in pageVisits"
                                            :key="stat.route_name"
                                        >
                                            <td
                                                class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900"
                                            >
                                                {{ stat.route_name }}
                                            </td>
                                            <td
                                                class="whitespace-nowrap px-6 py-4 text-sm text-gray-500"
                                            >
                                                {{ stat.total_unique_visits }}
                                            </td>
                                            <td
                                                class="whitespace-nowrap px-6 py-4 text-sm text-gray-500"
                                            >
                                                {{ stat.repeat_visits }}
                                            </td>
                                            <td
                                                class="whitespace-nowrap px-6 py-4 text-sm text-gray-500"
                                            >
                                                {{ stat.total_visits }}
                                            </td>
                                            <td
                                                class="whitespace-nowrap px-6 py-4 text-sm text-gray-500"
                                            >
                                                {{
                                                    new Date(
                                                        stat.last_updated,
                                                    ).toLocaleDateString()
                                                }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
