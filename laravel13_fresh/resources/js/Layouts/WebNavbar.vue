<!-- WebNavbar.vue - Updated with proper quote store initialization -->
<script setup lang="ts">
import ApplicationMark from '@/Components/ApplicationMark.vue';
import MegaNavLink from '@/Components/MegaNavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { iQuoteItem } from '@/interfaces';
import { useQuoteStore } from '@/stores/quote';
import { Link } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useLinks } from '../Composables/links';

const links = useLinks();
const quoteStore = useQuoteStore();

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
const isSticky = ref(false);
const menubarRef = ref<HTMLElement | null>(null);
const menuBarClass = ref('');
const showingQuoteDropdown = ref(false);

// Initialize quote store on mount
onMounted(() => {
    quoteStore.initialize();

    // Listen for quote updates from other components
    if (typeof window !== 'undefined') {
        window.addEventListener('quote-updated', handleQuoteUpdate);
    }
});

onUnmounted(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('quote-updated', handleQuoteUpdate);
    }
});

const handleQuoteUpdate = () => {
    // Force reactivity update
    // No need to do anything specific as computed properties will update
};

// Computed properties for quote
const uniqueProductsCount = computed(() => quoteStore.uniqueProductsCount);
const quoteItems = computed(() => quoteStore.items);
const totalItemsCount = computed(() => quoteStore.totalItems);

const handleScroll = () => {
    if (typeof window !== 'undefined') {
        if (window.scrollY > 200) {
            isSticky.value = true;
            menuBarClass.value =
                'fixed top-0 left-0 right-0 bg-white shadow-lg';
        } else {
            isSticky.value = false;
            menuBarClass.value = 'relative';
        }
    }
};

const clearQuote = () => {
    if (uniqueProductsCount.value === 0) return;

    if (confirm('Are you sure you want to clear all items from your quote?')) {
        quoteStore.clearItems();
        showingQuoteDropdown.value = false;
    }
};

// Close dropdown when clicking outside
const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as HTMLElement;
    if (
        !target.closest('.quote-dropdown') &&
        !target.closest('.quote-button')
    ) {
        showingQuoteDropdown.value = false;
    }
};

onMounted(() => {
    if (typeof window !== 'undefined') {
        window.addEventListener('scroll', handleScroll);
        document.addEventListener('click', handleClickOutside);
    }
});

onUnmounted(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('scroll', handleScroll);
        document.removeEventListener('click', handleClickOutside);
    }
});
const getProductPicture = (item: iQuoteItem): string => {
    return (
        (item?.product?.picture as string) ?? '/images/placeholder-product.png'
    );
};
const getProductTitle = (item: iQuoteItem): string => {
    return item?.product?.title as string;
};
const onProductPictureError = (e: Event) => {
    let element = e.target as HTMLImageElement;
    element.src = '/images/placeholder-product.png';
};
</script>

<template>
    <div class="absolute bottom-0 left-0 right-0 top-0 z-0 bg-white">
        <div
            class="absolute -left-[50px] -top-[100px] h-[256px] w-[256px] rounded-full bg-secondary-500/10 md:left-0 md:h-[480px] md:w-[480px]"
        ></div>
    </div>
    <div
        class="z-30 w-full px-8 transition-all duration-500"
        ref="menubarRef"
        :class="menuBarClass"
    >
        <nav class="z-0 w-full">
            <!-- Primary Navigation Menu -->
            <div
                class="mx-auto bg-transparent transition-all duration-500"
                :class="{
                    'max-w-7xl': !isSticky,
                    'w-full max-w-full': isSticky,
                }"
            >
                <div class="mpx-4 sm:mpx-6 lg:mpx-8 relative mx-auto max-w-7xl">
                    <div class="">
                        <div class="flex justify-between">
                            <div class="flex justify-between md:w-full">
                                <!-- Logo -->
                                <div
                                    class="relative flex shrink-0 items-center"
                                >
                                    <Link :href="route('home')">
                                        <div class="block h-20 w-auto py-2">
                                            <ApplicationMark
                                                class="h-full w-full object-contain"
                                            />
                                        </div>
                                    </Link>
                                </div>

                                <!-- Navigation Links -->
                                <div class="ml-3 hidden sm:flex">
                                    <MegaNavLink
                                        v-for="{
                                            name,
                                            caption,
                                            items,
                                        } in links"
                                        :name="name"
                                        :items="items"
                                    >
                                        {{ caption }}
                                    </MegaNavLink>
                                </div>
                            </div>

                            <!-- Quote Button for Desktop -->
                            <div class="hidden items-center md:inline-flex">
                                <div class="quote-dropdown relative">
                                    <button
                                        @click="
                                            showingQuoteDropdown =
                                                !showingQuoteDropdown
                                        "
                                        class="quote-button relative flex items-center whitespace-nowrap rounded-md bg-secondary-500 px-5 py-2 font-medium capitalize text-white transition-colors duration-200 hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2"
                                    >
                                        <svg
                                            class="mr-2 h-5 w-5"
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
                                            :class="[
                                                'bg-primary-500 text-white',
                                            ]"
                                        >
                                            {{ uniqueProductsCount }}
                                        </span>
                                    </button>

                                    <!-- Quote Dropdown -->
                                    <div
                                        v-if="showingQuoteDropdown"
                                        class="absolute right-0 top-full z-50 mt-2 w-80 origin-top-right rounded-lg bg-white shadow-xl ring-1 ring-black ring-opacity-5 transition-all duration-200"
                                    >
                                        <div class="p-4">
                                            <div
                                                class="mb-3 flex items-center justify-between"
                                            >
                                                <h4
                                                    class="font-semibold text-gray-900"
                                                >
                                                    Quote Summary
                                                </h4>
                                                <span
                                                    class="text-sm text-gray-500"
                                                    >{{
                                                        totalItemsCount
                                                    }}
                                                    items</span
                                                >
                                            </div>

                                            <!-- Items list -->
                                            <div
                                                v-if="quoteItems.length > 0"
                                                class="max-h-60 space-y-3 overflow-y-auto"
                                            >
                                                <div
                                                    v-for="item in quoteItems.slice(
                                                        0,
                                                        3,
                                                    )"
                                                    :key="item.id"
                                                    class="flex items-center gap-3"
                                                >
                                                    <div
                                                        class="h-10 w-10 flex-none overflow-hidden rounded-md"
                                                    >
                                                        <img
                                                            :src="
                                                                getProductPicture(
                                                                    item,
                                                                )
                                                            "
                                                            :alt="
                                                                getProductTitle(
                                                                    item,
                                                                )
                                                            "
                                                            class="h-full w-full object-cover"
                                                            @error="
                                                                onProductPictureError
                                                            "
                                                        />
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p
                                                            class="truncate text-sm font-medium text-gray-900"
                                                        >
                                                            {{
                                                                getProductTitle(
                                                                    item,
                                                                )
                                                            }}
                                                        </p>
                                                        <p
                                                            class="text-xs text-gray-500"
                                                        >
                                                            Qty:
                                                            {{ item.quantity }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Show "more items" indicator -->
                                                <div
                                                    v-if="quoteItems.length > 3"
                                                    class="text-center"
                                                >
                                                    <span
                                                        class="text-sm text-gray-500"
                                                        >+{{
                                                            quoteItems.length -
                                                            3
                                                        }}
                                                        more items</span
                                                    >
                                                </div>
                                            </div>

                                            <!-- Empty state -->
                                            <div
                                                v-else
                                                class="py-8 text-center"
                                            >
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
                                                <p
                                                    class="mt-2 text-sm text-gray-500"
                                                >
                                                    No items in quote
                                                </p>
                                            </div>

                                            <!-- Actions -->
                                            <div
                                                class="mt-4 space-y-2 border-t border-gray-100 pt-4"
                                            >
                                                <Link
                                                    :href="route('quote')"
                                                    @click="
                                                        showingQuoteDropdown = false
                                                    "
                                                    class="block w-full rounded-lg bg-primary-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-primary-700"
                                                >
                                                    View Full Quote
                                                </Link>
                                                <button
                                                    v-if="quoteItems.length > 0"
                                                    @click="clearQuote"
                                                    class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50"
                                                >
                                                    Clear All
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hamburger -->
                            <div class="-me-2 mr-3 flex items-center sm:hidden">
                                <button
                                    class="inline-flex items-center justify-center rounded-md p-2 text-primary-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-primary-500 focus:bg-gray-100 focus:text-primary-500 focus:outline-none"
                                    @click="
                                        showingNavigationDropdown =
                                            !showingNavigationDropdown
                                    "
                                >
                                    <svg
                                        class="h-6 w-6"
                                        stroke="currentColor"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            :class="{
                                                hidden: showingNavigationDropdown,
                                                'inline-flex':
                                                    !showingNavigationDropdown,
                                            }"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"
                                        />
                                        <path
                                            :class="{
                                                hidden: !showingNavigationDropdown,
                                                'inline-flex':
                                                    showingNavigationDropdown,
                                            }"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        'absolute left-0 right-0 top-[100%]':
                            showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="z-20 sm:hidden"
                >
                    <div class="space-y-1 bg-primary-500 pb-3 pt-2">
                        <!-- Mobile Quote Button -->
                        <div class="px-4 py-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-white"
                                    >Quote Items</span
                                >
                                <span
                                    class="rounded-full bg-primary-400 px-2 py-1 text-xs font-bold text-white"
                                >
                                    {{ uniqueProductsCount }}
                                </span>
                            </div>
                            <div class="mt-2 space-y-2">
                                <Link
                                    :href="route('quote')"
                                    @click="showingNavigationDropdown = false"
                                    class="block w-full rounded-lg bg-secondary-500 px-4 py-2 text-center text-sm font-medium text-white hover:bg-secondary-600"
                                >
                                    View Quote
                                </Link>
                                <button
                                    v-if="quoteItems.length > 0"
                                    @click="clearQuote"
                                    class="block w-full rounded-lg border border-primary-400 bg-transparent px-4 py-2 text-center text-sm font-medium text-white hover:bg-primary-600"
                                >
                                    Clear Quote
                                </button>
                            </div>
                        </div>

                        <ResponsiveNavLink
                            v-for="{ name, caption } in links"
                            :href="route(name)"
                            :active="route().current(name)"
                            @click="showingNavigationDropdown = false"
                        >
                            {{ caption }}
                        </ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</template>

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

/* Animation for dropdown */
.quote-dropdown > div:last-child {
    animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
