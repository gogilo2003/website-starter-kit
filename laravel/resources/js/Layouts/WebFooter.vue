<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Container from '@/Components/Container.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Icon from '../Components/Icons/Icon.vue';

const phone = computed(() => usePage().props?.contact?.phone);
const email = computed(() => usePage().props?.contact?.email);
const address = computed(() => usePage().props?.contact?.address);
const location = computed(() => usePage().props?.contact?.location);
const intro = computed(() => usePage().props?.intro);
const appName = computed(() => usePage().props?.appName);

const socialmedia = computed(() => usePage().props?.socialmedia);
</script>
<template>
    <footer v-bind="$attrs" class="z-[1] bg-gray-800 text-gray-200">
        <Container>
            <div class="grid grid-cols-1 py-8 md:grid-cols-3 md:divide-x">
                <div
                    class="flex flex-col items-center gap-2 px-6 py-3 text-center md:px-12 md:py-6"
                >
                    <ApplicationLogo class="w-36" />
                    <div
                        class="line-clamp-4 text-center text-sm font-semibold text-gray-50"
                        v-html="intro"
                    ></div>
                    <Link :href="route('about')">Read More</Link>
                </div>
                <div class="px-6 py-3 md:px-12 md:py-6">
                    <div
                        class="py-4 text-center text-xl font-semibold capitalize text-secondary-default md:text-left"
                    >
                        Contact Us
                    </div>
                    <div class="flex flex-col gap-2">
                        <div v-if="phone" class="flex items-center gap-2">
                            <div
                                class="h-10 w-10 flex-none overflow-hidden p-1"
                            >
                                <Icon
                                    class="h-full w-full object-contain text-secondary-500"
                                    type="phone"
                                />
                            </div>
                            <span v-text="phone"></span>
                        </div>
                        <div v-if="email" class="flex items-center gap-2">
                            <div
                                class="h-10 w-10 flex-none overflow-hidden p-1"
                            >
                                <Icon
                                    class="h-full w-full object-contain text-secondary-500"
                                    type="email"
                                />
                            </div>
                            <span v-text="email"></span>
                        </div>
                        <div v-if="address" class="flex items-center gap-2">
                            <div
                                class="h-10 w-10 flex-none overflow-hidden p-1"
                            >
                                <Icon
                                    class="h-full w-full object-contain text-secondary-500"
                                    type="map"
                                />
                            </div>
                            <span v-text="address"></span>
                        </div>
                        <div v-if="location" class="flex items-center gap-2">
                            <div
                                class="h-10 w-10 flex-none overflow-hidden p-1"
                            >
                                <Icon
                                    class="h-full w-full object-contain text-secondary-500"
                                    type="location"
                                />
                            </div>
                            <span v-text="location"></span>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-3 md:px-12 md:py-6">
                    <div
                        class="py-4 text-center text-xl font-semibold capitalize text-secondary-default md:text-left"
                    >
                        Follow us on Social media
                    </div>
                    <div
                        class="flex flex-row flex-wrap justify-center gap-4 md:flex-col md:justify-start"
                    >
                        <a
                            :href="url"
                            v-for="{ url, icon, title } in socialmedia"
                            class="group flex flex-col flex-nowrap items-center gap-2 md:flex-row"
                            target="_BLANK"
                        >
                            <Icon
                                :type="icon"
                                class="h-10 w-10 flex-none object-contain transition duration-500 ease-in-out group-hover:rotate-12 group-hover:scale-[1.2]"
                            />
                            <span
                                v-text="title"
                                class="hidden flex-1 md:inline-flex"
                            ></span>
                        </a>
                    </div>
                </div>
            </div>
        </Container>
    </footer>
    <div class="z-[1] bg-gray-700">
        <Container>
            <div
                class="flex flex-col items-center py-3 text-xs text-white md:flex-row md:justify-between"
            >
                <div class="border-b py-1 md:border-b-transparent">
                    &copy; {{ new Date().getFullYear() }} {{ appName }}
                </div>
                <div class="py-1">
                    Designed and Developed by
                    <a
                        class="transition[color] duration-300 hover:text-orange-400"
                        href="https://micartech.co.ke"
                        target="_blank"
                        >Micartech Ltd.</a
                    >
                </div>
            </div>
        </Container>
    </div>
</template>
