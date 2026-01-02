<script setup lang="ts">
import Container from '@/Components/Container.vue';
import Icon from '@/Components/Icons/Icon.vue';
import ProductCategoryCard from '@/Components/Web/ProductCategoryCard.vue';
import { iElement, iProduct, iProductCategory, iSlide } from '@/interfaces';
import Hero from '@/Layouts/Hero.vue';
import WebLayout from '@/Layouts/WebLayout.vue';
import { Link } from '@inertiajs/vue3';
import Heading from '../Components/Web/Heading.vue';
import Heading1 from '../Components/Web/Heading1.vue';
import ProductCard from '../Components/Web/ProductCard.vue';

interface Section {
    title: string;
    description: string;
    elements: {
        id: number;
        title: string;
        content: string;
        icon: string;
        photo: string;
    }[];
}
defineProps<{
    products: iProduct[];
    categories: iProductCategory[];
    partners: Array<{
        id: number;
        logo: string;
        title: string;
        website: string;
    }>;
    customers: Array<{
        id: number;
        logo: string;
        title: string;
        website: string;
    }>;
    slides: iSlide[];
    product_intro: String;
    category_intro: String;
    welcome: iElement;
}>();
</script>

<template>
    <WebLayout title="Welcome">
        <template v-if="slides.length" #header>
            <div class="relative md:pb-24">
                <Hero :slides="slides" class="mb-16" />
            </div>
        </template>
        <div class="relative my-0 bg-gray-100 py-16">
            <Container>
                <div>
                    <Heading1>Product Categories</Heading1>
                    <div class="py-3" v-text="category_intro"></div>
                </div>
                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-4">
                    <ProductCategoryCard
                        :category="category"
                        v-for="category in categories"
                        :key="category.id"
                    />
                </div>
                <div class="mt-8 flex items-center justify-center md:mt-16">
                    <Link
                        :href="route('products')"
                        class="flex flex-nowrap items-center gap-1 rounded-full bg-secondary-600 px-4 py-2 text-base font-semibold capitalize text-gray-50"
                    >
                        <span>Browse Products</span>
                        <Icon class="h-5 w-5" type="arrow-right" />
                    </Link>
                </div>
            </Container>
        </div>
        <div class="relative my-0 bg-gray-50 py-16">
            <Container>
                <div
                    class="grid grid-cols-1 items-center gap-3 md:grid-cols-2 md:gap-8"
                >
                    <div class="relative">
                        <div
                            class="absolute inset-0 after:absolute after:-bottom-2 after:-right-2 after:z-10 after:block after:h-[50%] after:w-[50%] after:bg-secondary-500"
                        ></div>
                        <img
                            class="relative z-10 block h-full w-full rounded-bl-[4rem] rounded-tr-[4rem] object-cover"
                            :src="welcome.photo ?? ''"
                            alt=""
                        />
                    </div>
                    <div>
                        <Heading
                            class="my-6 text-center text-3xl font-semibold md:text-left md:text-4xl"
                            >Welcome to {{ $page.props.appName }}
                        </Heading>
                        <p
                            class="my-6 line-clamp-[8] text-justify"
                            v-html="welcome.content"
                        ></p>
                        <div
                            class="flex items-center justify-center pt-4 md:justify-start"
                        >
                            <Link
                                :href="route('about')"
                                class="rounded-full border bg-secondary-500 px-5 py-4 text-sm font-medium uppercase text-white"
                            >
                                Read More...
                            </Link>
                        </div>
                    </div>
                </div>
            </Container>
        </div>
        <Container v-if="products.length">
            <div class="py-8 md:py-16">
                <div>
                    <Heading1>Our Products</Heading1>
                    <div class="py-3" v-text="product_intro"></div>
                </div>
                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                    <ProductCard
                        v-for="product in products"
                        :product="product"
                    />
                </div>
                <div class="flex items-center justify-center pt-8">
                    <Link
                        :href="route('products')"
                        class="rounded-full border border-secondary-600 px-4 py-2 text-sm font-semibold uppercase text-secondary-600"
                    >
                        More products...
                    </Link>
                </div>
            </div>
        </Container>

        <div class="py-16" v-if="partners.length">
            <Container>
                <Heading1 class="mb-3 text-center"
                    >Our Partners/Clients</Heading1
                >
                <div
                    class="flex flex-wrap items-center justify-center gap-6 md:gap-8"
                >
                    <a
                        target="_BLANK"
                        :href="website"
                        class="h-36 w-full overflow-hidden rounded-bl-3xl rounded-tr-3xl border border-secondary-500/30 bg-white px-4 py-2 drop-shadow-md transition-all duration-300 hover:drop-shadow-xl md:w-fit"
                        v-for="{ logo, title, website } in partners"
                    >
                        <img
                            class="h-full w-full object-contain"
                            :src="logo"
                            :alt="title"
                            :title="title"
                        />
                    </a>
                </div>
            </Container>
        </div>
    </WebLayout>
</template>
