<script setup lang="ts">
import { iSlide } from '@/interfaces';
import { initFlowbite } from 'flowbite';
import { onMounted } from 'vue';
import FeaturedCustomers from '../Components/Web/FeaturedCustomers.vue';

defineProps<{
    slides: iSlide[];
}>();

const options = {
    interval: 10000,
};

onMounted(() => {
    initFlowbite();
});

const setComponent = (mediaType: string) => {
    let element = 'img';

    if (mediaType == 'video') {
        element = 'video';
    }
    return element;
};
</script>

<template>
    <div
        id="default-carousel"
        class="relative z-0 w-full"
        data-carousel="slide"
        data-carousel-interval="5000"
    >
        <!-- Carousel wrapper -->
        <div
            class="relative h-[calc(100svh_-_6rem)] overflow-hidden rounded-none md:h-screen 2xl:h-[calc(100svh_-_11.9375rem)]"
        >
            <!-- Item 1 -->
            <div
                v-for="{ picture, title, caption, media_type } in slides"
                class="hidden h-full duration-[1s] ease-in-out"
                data-carousel-item
            >
                <component
                    :is="setComponent(media_type)"
                    :src="picture"
                    class="absolute block h-full w-full object-cover md:right-0 md:top-0 md:h-[calc(100%_-_5rem)] md:w-[75%] md:rounded-tl-[10rem]"
                    :alt="title"
                    :autoplay="media_type == 'video'"
                ></component>
                <div
                    v-if="title || caption"
                    class="absolute bottom-16 mx-8 flex w-[calc(100%_-_4rem)] items-center justify-center md:bottom-0 md:left-0 md:mx-0 md:w-[60%]"
                >
                    <div
                        class="bg-gray-800/80 px-2 py-8 text-center md:block md:rounded-tr-[4rem] md:py-24 md:pl-56 md:pr-16 md:text-left"
                    >
                        <div
                            v-if="title"
                            v-text="title"
                            class="py-3 text-lg font-bold uppercase text-secondary-500 md:text-2xl"
                        ></div>
                        <div
                            v-if="caption"
                            v-text="caption"
                            class="py-3 text-3xl font-light text-gray-50 md:text-7xl md:font-thin"
                        ></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Slider indicators -->
        <div
            class="absolute bottom-5 left-1/2 z-30 flex -translate-x-1/2 space-x-3 rtl:space-x-reverse"
        >
            <button
                v-for="(item, index) in slides"
                type="button"
                class="h-3 w-3 rounded-full"
                :aria-current="index == 0"
                :aria-label="`Slide ${index + 1}`"
                :data-carousel-slide-to="index"
            ></button>
        </div>
        <!-- Slider controls -->
        <button
            type="button"
            class="group absolute start-0 top-0 z-30 flex h-full cursor-pointer items-center justify-center px-4 focus:outline-none"
            data-carousel-prev
        >
            <span
                class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/30 group-hover:bg-white/50 group-focus:outline-none group-focus:ring-4 group-focus:ring-white dark:bg-gray-800/30 dark:group-hover:bg-gray-800/60 dark:group-focus:ring-gray-800/70"
            >
                <svg
                    class="h-4 w-4 text-white rtl:rotate-180 dark:text-gray-800"
                    aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 6 10"
                >
                    <path
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 1 1 5l4 4"
                    />
                </svg>
                <span class="sr-only">Previous</span>
            </span>
        </button>
        <button
            type="button"
            class="group absolute end-0 top-0 z-30 flex h-full cursor-pointer items-center justify-center px-4 focus:outline-none"
            data-carousel-next
        >
            <span
                class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/30 group-hover:bg-white/50 group-focus:outline-none group-focus:ring-4 group-focus:ring-white dark:bg-gray-800/30 dark:group-hover:bg-gray-800/60 dark:group-focus:ring-gray-800/70"
            >
                <svg
                    class="h-4 w-4 text-white rtl:rotate-180 dark:text-gray-800"
                    aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 6 10"
                >
                    <path
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m1 9 4-4-4-4"
                    />
                </svg>
                <span class="sr-only">Next</span>
            </span>
        </button>
    </div>
    <FeaturedCustomers
        class="right-0 mx-2 block w-[calc(100%_-_1rem)] md:absolute md:bottom-24 md:w-[calc(40%_-_1rem)]"
    />
</template>
