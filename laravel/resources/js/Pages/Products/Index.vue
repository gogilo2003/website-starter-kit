<script setup lang="ts">
import Heading1 from '@/Components/Web/Heading1.vue';
import ProductCard from '@/Components/Web/ProductCard.vue';
import { iProduct, iProductCategory, iProducts } from '@/interfaces';
import { router } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import Container from '@/Components/Container.vue';
import WebHeader from '@/Layouts/WebHeader.vue';
import WebLayout from '@/Layouts/WebLayout.vue';

const props = defineProps<{
    products: iProducts;
    product_intro: string;
    category?: iProductCategory;
}>();

const title = computed(() => {
    const baseTitle = 'Our Products';
    if (props?.category?.name) {
        return `${baseTitle} > ${props?.category?.name}`;
    }
    return baseTitle;
});

const description = computed(() => {
    if (props?.category?.description) {
        return props?.category?.description;
    }
    return null;
});

// Reactive state
const productList = ref<iProduct[]>(props.products.data);
const currentPage = ref<number>(props.products.current_page);
const hasMore = ref<boolean>(props.products.next_page_url !== null);
const loading = ref<boolean>(false);

// Sentinel ref
const sentinel = ref<HTMLElement | null>(null);
const observer = ref<IntersectionObserver | null>(null);

// Load more using Inertia router
const loadMore = () => {
    if (loading.value || !hasMore.value) return;

    loading.value = true;

    const nextPage = currentPage.value + 1;
    const url = `${window.location.pathname}?page=${nextPage}&per_page=3`;

    router.visit(url, {
        method: 'get',
        preserveScroll: true,
        preserveState: true,
        only: ['products'],
        headers: {
            'X-Inertia-Partial-Data': 'products',
        },
        onSuccess: (page) => {
            productList.value.push(...props.products?.data);
            currentPage.value = props.products?.current_page;
            hasMore.value = props.products?.next_page_url !== null;
            setTimeout(() => {
                loading.value = false;
            }, 300);
        },
        onError: () => {
            console.error('Failed to load more products');
            loading.value = false;
        },
    });
};

// Setup Intersection Observer
const setupObserver = () => {
    if (!sentinel.value) return;

    if (observer.value) {
        observer.value.disconnect();
    }

    observer.value = new IntersectionObserver(
        (entries) => {
            if (entries[0].isIntersecting && hasMore.value && !loading.value) {
                loadMore();
            }
        },
        {
            root: null,
            rootMargin: '200px',
            threshold: 0,
        },
    );

    observer.value.observe(sentinel.value);
};

onMounted(async () => {
    await nextTick();
    setupObserver();
});

watch(hasMore, async () => {
    await nextTick();
    if (hasMore.value && sentinel.value && observer.value) {
        observer.value.observe(sentinel.value);
    }
});

onUnmounted(() => {
    if (observer.value) {
        observer.value.disconnect();
    }
});
</script>

<template>
    <WebLayout :title="title">
        <template #header>
            <WebHeader>
                <Heading1 v-if="title">{{ title }}</Heading1>
                <div class="text-lg md:text-xl" v-if="description">
                    {{ description }}
                </div>
            </WebHeader>
        </template>

        <div class="bg-white pb-20 pt-16">
            <Container>
                <div v-if="product_intro" class="mb-16 text-center text-lg" v-text="product_intro"></div>

                <!-- Products Grid -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <ProductCard v-for="product in productList" :key="product.id" :product="product" />
                </div>

                <!-- Sentinel -->
                <div ref="sentinel" class="-my-10 h-20" v-if="hasMore || loading"></div>

                <!-- Loading Indicator -->
                <div v-if="loading" class="mt-12 text-center">
                    <div class="inline-block h-8 w-8 animate-spin rounded-full border-b-2 border-gray-900"></div>
                    <p class="mt-2 text-gray-600">Loading more products...</p>
                </div>

                <!-- End of list -->
                <div v-if="!hasMore && !loading && productList.length > 0" class="mt-12 text-center text-gray-500">
                    <p>You've reached the end of the product list.</p>
                </div>

                <!-- No products -->
                <div v-if="productList.length === 0 && !loading" class="mt-12 text-center">
                    <p class="text-gray-500">No products found.</p>
                </div>
            </Container>
        </div>
    </WebLayout>
</template>
