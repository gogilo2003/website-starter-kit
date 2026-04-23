<script setup lang="ts">
import FunFact from '@/Components/Web/FunFact.vue';
import { iElement, iPartner } from '@/interfaces';
import Container from '../Components/Container.vue';
import Heading1 from '../Components/Web/Heading1.vue';
import Heading3 from '../Components/Web/Heading3.vue';
import usePageVisits from '../Composables/usePageVisits';
import WebHeader from '../Layouts/WebHeader.vue';
import WebLayout from '../Layouts/WebLayout.vue';

usePageVisits();

const props = defineProps<{
    welcome: iElement;
    whoWeAre: iElement;
    coreValues: iElement[];
    numbers: iElement[];
    partners: iPartner[];
}>();
</script>

<template>
    <WebLayout title="About Young Olive">
        <template #header>
            <WebHeader>
                <Heading1>About Us</Heading1>
                <div>About Young Olive</div>
            </WebHeader>
        </template>
        <div class="bg-white py-8 md:py-16">
            <Container>
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div>
                        <img
                            :src="welcome?.photo ?? ''"
                            alt=""
                            class="h-full w-full rounded-3xl object-cover shadow-lg"
                        />
                    </div>
                    <div
                        class="prose prose-p:text-justify px-0 py-6 md:p-16"
                        v-html="welcome?.content"
                    ></div>
                </div>
            </Container>
        </div>
        <div class="bg-gray-100 py-8 md:py-16">
            <Container>
                <div class="gap-6 md:gap-0" v-if="whoWeAre">
                    <div
                        v-if="whoWeAre?.content"
                        class=""
                        :class="{
                            'grid-cols-1 md:grid-cols-2':
                                coreValues?.length == 0,
                            'grid-cols-1 md:grid-cols-3':
                                coreValues?.length > 0,
                        }"
                    >
                        <div>
                            <Heading3>{{ whoWeAre?.title }}</Heading3>
                            <div
                                class="prose prose-p:mb-3 prose-p:text-justify mt-6 max-w-full text-lg"
                                v-html="whoWeAre?.content"
                            ></div>
                        </div>
                        <div
                            v-if="coreValues?.length"
                            class="mt-6 flex flex-col gap-4 md:flex-row md:gap-8"
                        >
                            <div
                                class="group flex flex-row flex-nowrap items-center md:w-[20%] md:flex-col md:justify-center md:text-center"
                                v-for="{ content, photo } in coreValues"
                            >
                                <img
                                    :src="photo ?? ''"
                                    alt=""
                                    class="h-16 w-16 flex-none rounded-full border border-secondary-500 object-cover shadow md:h-40 md:w-full md:rounded-none md:group-odd:rounded-bl-[3rem] md:group-odd:rounded-tr-[3rem] md:group-even:rounded-br-[3rem] md:group-even:rounded-tl-[3rem]"
                                />
                                <div
                                    v-text="content"
                                    class="flex-1 p-3 text-xl font-medium capitalize"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </Container>
        </div>
        <div class="bg-secondary-600 py-8 text-white md:py-16">
            <Container>
                <div class="grid grid-cols-1 md:grid-cols-3">
                    <div class="mb-6 text-center md:text-left">
                        <div class="text-sm font-semibold uppercase">
                            Fun facts
                        </div>
                        <div
                            class="text-xl font-light text-primary-600 md:text-2xl"
                        >
                            Numbers Speak For Themselves
                        </div>
                    </div>
                    <div
                        class="col-span-2 grid grid-cols-1 gap-3 md:grid-cols-3"
                    >
                        <FunFact
                            v-for="fact in numbers.map(
                                ({ title, content }) => ({
                                    label: title,
                                    value: parseInt(content ?? '0'),
                                }),
                            )"
                            :fact="fact"
                        />
                    </div>
                </div>
            </Container>
        </div>
        <div class="bg-white pb-20 pt-1">
            <Container>
                <div v-if="partners.length" class="mt-16">
                    <Heading3>Our Partners</Heading3>
                    <div
                        class="mt-8 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            class="flex flex-col items-center gap-3 border border-gray-800/10 p-6 text-center shadow"
                            v-for="{
                                title,
                                logo,
                                website,
                                description,
                            } in partners"
                        >
                            <div class="h-32">
                                <img
                                    :src="logo"
                                    :alt="title"
                                    class="h-full w-full object-contain"
                                />
                            </div>
                            <div class="flex flex-col gap-3">
                                <!-- <div v-text="title" class="flex-none"></div> -->
                                <div
                                    v-text="description"
                                    class="line-clamp-3 flex-1"
                                ></div>
                                <div class="flex-none">
                                    <a
                                        target="_blank"
                                        :href="website"
                                        class="text-primary-600 transition-colors duration-300 hover:text-secondary-600"
                                        >More Details</a
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Container>
        </div>
    </WebLayout>
</template>
