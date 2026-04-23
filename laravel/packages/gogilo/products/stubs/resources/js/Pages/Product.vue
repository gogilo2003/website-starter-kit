<script lang="ts" setup>
import Heading1 from '@/Components/Web/Heading1.vue';
import { iPicture, iProduct, iQuoteItem } from '@/interfaces';
import { useQuoteStore } from '@/stores/quote';
import { parsePhoneNumbers } from '@/utils/phoneFormatter';
import { Link, router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, onMounted, ref } from 'vue';
import Container from '../Components/Container.vue';
import Heading4 from '../Components/Web/Heading4.vue';
import WebHeader from '../Layouts/WebHeader.vue';
import WebLayout from '../Layouts/WebLayout.vue';

interface QuoteRequestForm {
    name: string;
    email: string;
    phone: string;
    company?: string;
    message: string;
    items: iQuoteItem[];
}

const props = defineProps<{
    product: iProduct;
    products: iProduct[];
    call: string;
}>();

// State
const inWishlist = ref(false);
const currentImage = ref<string>('');
const showQuoteModal = ref(false);
const quoteStore = useQuoteStore();

const getProductPicture = (item: iQuoteItem): string => {
    return item?.product?.picture as string;
};

const getProductTitle = (item: iQuoteItem): string => {
    return item?.product?.title as string;
};

const onProductPictureError = (e: Event) => {
    let element = e.target as HTMLImageElement;
    element.src = '/images/placeholder-product.png';
};

// Quote Request Form
const quoteForm = useForm<QuoteRequestForm>({
    name: '',
    email: '',
    phone: '',
    company: '',
    message: '',
    items: [],
});

// Computed properties
const hasAttributes = computed(() => {
    return (
        props.product.features && Object.keys(props.product.features).length > 0
    );
});

const formattedAttributes = computed(() => {
    if (!props.product.features) return [];

    if (Array.isArray(props.product.features)) {
        return props.product.features;
    }

    return Object.entries(props.product.features).map(([name, value]) => ({
        name,
        value,
    }));
});

const isInQuote = computed(() => {
    return quoteStore.isInQuote(props.product.id);
});

const currentQuoteItem = computed(() => {
    return quoteStore.getItemByProductId(props.product.id);
});

// Methods
const setMainImage = (imageUrl: string) => {
    currentImage.value = imageUrl;
};

const handleImageError = (event: Event) => {
    const target = event.target as HTMLImageElement;
    target.src = '/images/placeholder-product.png';
};

const toggleWishlist = async () => {
    try {
        const endpoint = inWishlist.value ? 'wishlist-remove' : 'wishlist-add';

        router.post(
            route(endpoint),
            {
                product_id: props.product.id,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    inWishlist.value = !inWishlist.value;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        icon: 'success',
                        text: inWishlist.value
                            ? 'Added to wishlist!'
                            : 'Removed from wishlist!',
                    });
                },
                onError: () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        icon: 'error',
                        text: 'Failed to update wishlist',
                    });
                },
            },
        );
    } catch (error) {
        console.log(error);

        Swal.fire({
            toast: true,
            position: 'top-end',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            icon: 'error',
            text: 'Failed to update wishlist',
        });
    }
};

const shareProduct = () => {
    if (navigator.share) {
        navigator
            .share({
                title: props.product.title,
                text: `Check out ${props.product.title} on our store!`,
                url: window.location.href,
            })
            .catch(console.error);
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
                icon: 'success',
                text: 'Link copied to clipboard!',
            });
        });
    }
};

const openQuoteModal = () => {
    // Ensure current product is in quote
    if (!isInQuote.value) {
        addProductToQuote(props.product);
    }
    showQuoteModal.value = true;
};

const closeQuoteModal = () => {
    showQuoteModal.value = false;
    quoteForm.reset();
};

const submitQuoteRequest = () => {
    // Update form with current quote items from store
    quoteForm.items = quoteStore.items.map((item) => ({
        ...item,
        product: undefined,
    }));

    if (quoteForm.items.length === 0) {
        Swal.fire({
            title: 'No Products Selected',
            text: 'Please add at least one product to your quote request.',
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6',
        });
        return;
    }

    quoteForm.post(route('quote.request'), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: 'Quote Request Sent!',
                html: `
                    <div class="text-left">
                        <p class="mb-4">Thank you for your interest. We will contact you shortly with pricing for ${quoteForm.items.length} product(s).</p>
                        <div class="mt-4 text-sm text-gray-600">
                            <p>You will receive a confirmation email shortly.</p>
                        </div>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                width: 500,
            });
            closeQuoteModal();
        },
        onError: (errors) => {
            Swal.fire({
                title: 'Error',
                text: 'Please check your information and try again.',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33',
            });
        },
    });
};

const addProductToQuote = (product: iProduct) => {
    // Check if product already exists in quote
    if (quoteStore.isInQuote(product.id)) {
        // Update quantity of existing item
        const existingItem = quoteStore.getItemByProductId(product.id);
        if (existingItem) {
            quoteStore.updateQuantity(
                existingItem.id,
                existingItem.quantity + 1,
            );
            Swal.fire({
                toast: true,
                position: 'top-end',
                timer: 2000,
                showConfirmButton: false,
                icon: 'success',
                text: `Quantity increased for ${product.title}`,
            });
        }
    } else {
        // Add new product to quote
        quoteStore.addItem({
            ...product,
            picture: product.picture || '/images/placeholder-product.png',
        });

        Swal.fire({
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false,
            icon: 'success',
            text: `${product.title} added to quote`,
        });
    }
};

const removeProductFromQuote = (productId: number) => {
    const item = quoteStore.getItemByProductId(productId);
    if (item) {
        quoteStore.removeItem(item.id);
        Swal.fire({
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false,
            icon: 'info',
            text: `${getProductTitle(item)} removed from quote`,
        });
    }
};

const updateProductQuantity = (productId: number, newQuantity: number) => {
    const item = quoteStore.getItemByProductId(productId);
    if (item) {
        if (newQuantity < 1) {
            newQuantity = 1;
        }
        quoteStore.updateQuantity(item.id, newQuantity);
    }
};

// Initialize
onMounted(() => {
    if (props.product.picture) {
        currentImage.value = props.product.picture;
    }

    // Check if product is in wishlist
    if (typeof window !== 'undefined') {
        const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
        inWishlist.value = wishlist.includes(props.product.id);
    }
});

const phones = computed(() =>
    props.call ? parsePhoneNumbers(props.call) : [],
);
</script>

<template>
    <!-- Quote Request Modal -->
    <div v-if="showQuoteModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black bg-opacity-25" @click="closeQuoteModal"></div>

            <!-- Modal Content -->
            <div class="relative w-full max-w-4xl rounded-lg bg-white shadow-xl">
                <!-- Header -->
                <div class="sticky top-0 z-10 border-b border-gray-200 bg-white px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Request Quote
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Get pricing for
                                {{ quoteStore.uniqueProductsCount }} product{{
                                    quoteStore.uniqueProductsCount !== 1
                                        ? 's'
                                        : ''
                                }}
                            </p>
                        </div>
                        <button @click="closeQuoteModal"
                            class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="max-h-[calc(100vh-200px)] overflow-y-auto p-6">
                    <!-- Product Selection Section -->
                    <div class="mb-8">
                        <div class="mb-4 flex items-center justify-between">
                            <h4 class="font-medium text-gray-900">
                                Products for Quote
                            </h4>
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-gray-500">{{ quoteStore.uniqueProductsCount }}/10
                                    products</span>
                            </div>
                        </div>

                        <!-- Quote Items List -->
                        <div class="space-y-4">
                            <div v-for="item in quoteStore.items" :key="item.id"
                                class="rounded-lg border border-gray-200 p-4">
                                <div class="flex flex-wrap items-center gap-4">
                                    <!-- Product Image -->
                                    <div class="h-16 w-16 flex-none overflow-hidden rounded-md">
                                        <img :src="getProductPicture(item)" :alt="getProductTitle(item)"
                                            class="h-full w-full object-cover" @error="onProductPictureError" />
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h5 class="font-medium text-gray-900">
                                                    {{ getProductTitle(item) }}
                                                </h5>

                                                <!-- Product Notes -->
                                                <div class="mt-2">
                                                    <input type="text" :value="item.notes || ''
                                                        " @input="
                                                            quoteStore.updateNotes(
                                                                item.id,
                                                                (
                                                                    $event.target as HTMLInputElement
                                                                ).value,
                                                            )
                                                            " placeholder="Add notes (optional)"
                                                        class="w-full rounded border border-gray-300 px-3 py-1 text-sm focus:border-primary-500 focus:ring-primary-500" />
                                                </div>
                                            </div>

                                            <!-- Quantity Controls -->
                                            <div class="flex items-center gap-2">
                                                <button @click="
                                                    updateProductQuantity(
                                                        item.product_id,
                                                        item.quantity - 1,
                                                    )
                                                    " class="rounded border border-gray-300 p-1 hover:bg-gray-100">
                                                    <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M20 12H4" />
                                                    </svg>
                                                </button>
                                                <input type="number" :value="item.quantity" min="1" @input="
                                                    updateProductQuantity(
                                                        item.product_id,
                                                        parseInt(
                                                            (
                                                                $event.target as HTMLInputElement
                                                            ).value,
                                                        ) || 1,
                                                    )
                                                    "
                                                    class="w-16 rounded border border-gray-300 px-2 py-1 text-center" />
                                                <button @click="
                                                    updateProductQuantity(
                                                        item.product_id,
                                                        item.quantity + 1,
                                                    )
                                                    " class="rounded border border-gray-300 p-1 hover:bg-gray-100">
                                                    <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Remove Button -->
                                    <button @click="
                                        removeProductFromQuote(
                                            item.product_id,
                                        )
                                        " class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Add More Products Button -->
                            <Link :href="route('products')" @click="closeQuoteModal"
                                class="flex w-full items-center justify-center gap-2 rounded-lg border-2 border-dashed border-gray-300 p-4 text-gray-500 hover:border-primary-400 hover:text-primary-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Browse More Products
                            </Link>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="mb-4 font-medium text-gray-900">
                            Contact Information
                        </h4>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Your Name *
                                </label>
                                <input type="text" v-model="quoteForm.name" required
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-primary-500"
                                    placeholder="John Doe" />
                                <div v-if="quoteForm.errors.name" class="mt-1 text-sm text-red-600">
                                    {{ quoteForm.errors.name }}
                                </div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Email *
                                </label>
                                <input type="email" v-model="quoteForm.email" required
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-primary-500"
                                    placeholder="john@example.com" />
                                <div v-if="quoteForm.errors.email" class="mt-1 text-sm text-red-600">
                                    {{ quoteForm.errors.email }}
                                </div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Phone *
                                </label>
                                <input type="tel" v-model="quoteForm.phone" required
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-primary-500"
                                    placeholder="+1 (555) 123-4567" />
                                <div v-if="quoteForm.errors.phone" class="mt-1 text-sm text-red-600">
                                    {{ quoteForm.errors.phone }}
                                </div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Company
                                </label>
                                <input type="text" v-model="quoteForm.company"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-primary-500"
                                    placeholder="Your Company" />
                            </div>

                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Additional Message
                                </label>
                                <textarea v-model="quoteForm.message" rows="3"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-primary-500"
                                    placeholder="Please provide any additional details about your requirements..."></textarea>
                                <div v-if="quoteForm.errors.message" class="mt-1 text-sm text-red-600">
                                    {{ quoteForm.errors.message }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-500">
                            You're requesting quotes for
                            {{ quoteStore.uniqueProductsCount }} product(s)
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="closeQuoteModal"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="button" @click="submitQuoteRequest" :disabled="quoteForm.processing ||
                                quoteStore.uniqueProductsCount === 0
                                "
                                class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 disabled:opacity-50">
                                <span v-if="quoteForm.processing">Sending...</span>
                                <span v-else>Request Quote for
                                    {{ quoteStore.uniqueProductsCount }}
                                    Product(s)</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <WebLayout :title="product.title">
        <template #header>
            <WebHeader>
                <Heading1>{{ product.title }}</Heading1>
            </WebHeader>
        </template>
        <Container class="py-12">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
                <!-- Main Product Content -->
                <div class="lg:col-span-3">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <!-- Product Images -->
                        <div class="space-y-4">
                            <!-- Main Image -->
                            <div class="overflow-hidden rounded-lg bg-white shadow-lg">
                                <img class="h-auto w-full rounded-lg border border-primary-50 object-cover" :src="currentImage ||
                                    product.picture ||
                                    '/images/placeholder-product.png'
                                    " :alt="product.title" @error="handleImageError" />
                            </div>

                            <!-- Product Gallery (if you have multiple images) -->
                            <div v-if="
                                product.pictures &&
                                product.pictures.length > 0
                            " class="grid grid-cols-4 gap-2">
                                <div v-for="(
image, index
                                    ) in product.pictures as iPicture[]" :key="index" @click="
                                        setMainImage(image.url ?? image.name)
                                        " :class="[
                                        'cursor-pointer rounded-md border-2 p-1 transition-all duration-200',
                                        currentImage ===
                                            (image.url ?? image.name)
                                            ? 'border-primary-500'
                                            : 'border-gray-200 hover:border-primary-300',
                                    ]">
                                    <img :src="image.url ?? image.name" :alt="`${product.title} - Image ${index + 1}`"
                                        class="h-20 w-full rounded-md object-cover" @error="handleImageError" />
                                </div>
                            </div>

                            <!-- Product Description -->
                            <div class="mt-12">
                                <div class="prose max-w-none">
                                    <h2 class="mb-4 text-2xl font-bold text-gray-900">
                                        Product Description
                                    </h2>
                                    <div v-html="product.description" class="text-gray-700"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Details -->
                        <div class="space-y-6">
                            <!-- Product Summary -->
                            <div v-if="product.summary" class="text-gray-700">
                                <h3 class="mb-2 text-lg font-semibold text-gray-900">
                                    Product Overview
                                </h3>
                                <p class="text-gray-600">
                                    {{ product.summary }}
                                </p>
                            </div>

                            <!-- Product Attributes -->
                            <div v-if="hasAttributes" class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Product Specifications
                                </h3>
                                <div class="rounded-lg border border-gray-200">
                                    <dl class="divide-y divide-gray-200">
                                        <div v-for="(
attribute, index
                                            ) in formattedAttributes" :key="index"
                                            class="flex flex-col px-6 py-4 hover:bg-gray-50 sm:flex-row sm:items-center sm:justify-between">
                                            <dt class="mb-1 text-sm font-medium text-gray-500 sm:mb-0">
                                                {{ attribute.name }}
                                            </dt>
                                            <dd class="text-sm text-gray-900 sm:text-right">
                                                {{ attribute.value }}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-4 border-t border-gray-200 pt-6">
                                <!-- Quote Request Button -->
                                <div class="flex gap-3">
                                    <button @click="openQuoteModal"
                                        class="flex-1 items-center justify-center rounded-lg bg-primary-600 px-6 py-3 text-base font-medium text-white transition-colors hover:bg-primary-700">
                                        <svg class="mr-3 inline h-5 w-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        Request Quote
                                    </button>

                                    <button @click="addProductToQuote(product)" :class="[
                                        'inline-flex items-center justify-center rounded-lg border px-4 py-3 transition-colors',
                                        isInQuote
                                            ? 'border-primary-600 bg-primary-50 text-primary-600 hover:bg-primary-100'
                                            : 'border-primary-600 bg-white text-primary-600 hover:bg-primary-50',
                                    ]" :title="isInQuote
                                                ? 'Update quantity in quote'
                                                : 'Add to quote'
                                            ">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Secondary Actions -->
                                <div class="flex gap-3">
                                    <button @click="shareProduct"
                                        class="flex-1 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                                        <svg class="mr-2 inline h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                        </svg>
                                        Share
                                    </button>
                                </div>

                                <!-- Quick Contact -->
                                <div class="rounded-lg bg-blue-50 p-4">
                                    <div class="flex items-center">
                                        <svg class="mr-3 h-5 w-5 text-blue-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-blue-900">
                                                Need immediate assistance?
                                            </p>
                                            <div class="text-sm text-blue-700">
                                                Call us at
                                                <div class="inline-flex gap-2">
                                                    <a v-for="{
                                                        telLink,
                                                        humanReadable,
                                                    } in phones" :href="`tel:${telLink}`"
                                                        class="font-semibold hover:underline">{{ humanReadable }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div class="rounded-lg border border-gray-200 p-6 text-center">
                            <svg class="mx-auto h-8 w-8 text-primary-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                            </svg>
                            <h3 class="mt-4 font-semibold text-gray-900">
                                Bulk Pricing
                            </h3>
                            <p class="mt-2 text-sm text-gray-600">
                                Competitive pricing for bulk orders and custom
                                configurations
                            </p>
                        </div>

                        <div class="rounded-lg border border-gray-200 p-6 text-center">
                            <svg class="mx-auto h-8 w-8 text-primary-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <h3 class="mt-4 font-semibold text-gray-900">
                                Multi-Product Quotes
                            </h3>
                            <p class="mt-2 text-sm text-gray-600">
                                Get quotes for multiple products in a single
                                request
                            </p>
                        </div>

                        <div class="rounded-lg border border-gray-200 p-6 text-center">
                            <svg class="mx-auto h-8 w-8 text-primary-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <h3 class="mt-4 font-semibold text-gray-900">
                                Fast Response
                            </h3>
                            <p class="mt-2 text-sm text-gray-600">
                                Get quotes typically within 24 hours of request
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Sidebar - Related Products -->
                <div class="lg:col-span-1">
                    <div class="sticky top-6 rounded-lg bg-gray-50 p-6">
                        <Heading4 class="mb-6">Related Products</Heading4>
                        <div class="space-y-4">
                            <div v-for="relatedProduct in products" :key="relatedProduct.id"
                                class="group flex items-start gap-4 rounded-lg p-3 transition-colors hover:bg-white">
                                <div class="h-16 w-16 flex-none overflow-hidden rounded-lg">
                                    <img class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                        :src="relatedProduct.picture ||
                                            '/images/placeholder-product.png'
                                            " :alt="relatedProduct.title" @error="handleImageError" />
                                </div>
                                <div class="flex-1">
                                    <Link :href="relatedProduct.url"
                                        class="font-medium text-gray-900 group-hover:text-primary-600">
                                        {{ relatedProduct.title }}
                                    </Link>
                                    <p v-if="relatedProduct.summary" class="mt-1 line-clamp-2 text-sm text-gray-500">
                                        {{ relatedProduct.summary }}
                                    </p>

                                    <!-- Quick Add to Quote -->
                                    <button @click="
                                        addProductToQuote(relatedProduct)
                                        "
                                        class="mt-2 inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add to Quote
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Quote Summary -->
                        <div v-if="quoteStore.uniqueProductsCount > 0"
                            class="mt-8 rounded-lg border border-primary-200 bg-primary-50 p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-primary-900">
                                        Your Quote List
                                    </p>
                                    <p class="text-xs text-primary-700">
                                        {{ quoteStore.uniqueProductsCount }}
                                        product(s)
                                    </p>
                                </div>
                                <Link :href="route('quote')"
                                    class="rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-primary-700">
                                    View & Request
                                </Link>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="mt-8 space-y-4 border-t border-gray-200 pt-6">
                            <div class="flex items-start gap-3">
                                <svg class="h-6 w-6 flex-none text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <div>
                                    <h5 class="font-medium text-gray-900">
                                        Expert Support
                                    </h5>
                                    <p class="text-sm text-gray-500">
                                        Technical guidance available
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="h-6 w-6 flex-none text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <div>
                                    <h5 class="font-medium text-gray-900">
                                        Dedicated Account
                                    </h5>
                                    <p class="text-sm text-gray-500">
                                        Personal account manager
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="h-6 w-6 flex-none text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <h5 class="font-medium text-gray-900">
                                        B2B Focus
                                    </h5>
                                    <p class="text-sm text-gray-500">
                                        Specialized for business clients
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Container>
    </WebLayout>
</template>

<style scoped>
/* Smooth transitions */
* {
    transition: all 0.2s ease-in-out;
}

/* Line clamp for text truncation */
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

/* Custom scrollbar for modal */
.modal-scroll {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

.modal-scroll::-webkit-scrollbar {
    width: 6px;
}

.modal-scroll::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.modal-scroll::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 3px;
}
</style>
