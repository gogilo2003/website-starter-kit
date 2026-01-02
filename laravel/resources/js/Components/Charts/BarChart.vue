<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';
import { ChartData } from 'chart.js';

const props = defineProps<{
    data: ChartData
}>();

const chartRef = ref<HTMLCanvasElement | null>(null);

onMounted(() => {
    const ctx = chartRef.value ? chartRef.value?.getContext('2d') : null;
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: props.data ?? { datasets: [], labels: [] },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Visit Statistics'
                    },
                },
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>

<template>
    <div class="">
        <canvas ref="chartRef"></canvas>
    </div>
</template>
