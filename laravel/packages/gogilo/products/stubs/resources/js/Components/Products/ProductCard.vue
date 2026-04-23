<script lang="ts" setup>
import { iProduct } from '@/interfaces';
import { useQuoteStore } from '@/stores/quote';
import { Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, ref } from 'vue';

const props = defineProps<{ product: iProduct }>();

const { title, picture, summary, url, price } = props.product;
const quoteStore = useQuoteStore();

// Local state
const showQuantityInput = ref(false);
const quantity = ref(1);
const isAdding = ref(false);
const isHovered = ref(false);

// Check if product is already in quote
const isInQuote = computed(() => quoteStore.isInQuote(props.product.id));

// Get current item details if in quote
const currentItem = computed(() =>
    quoteStore.getItemByProductId(props.product.id),
);

// Format price
const formattedPrice = computed(() => {
    if (!price) return 'Price on request';
    if (typeof price === 'string') return price;
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'KES',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(price);
});

// Update quantity in quote
const updateItemQuantity = async (newQuantity: number) => {
    if (!currentItem.value) return;

    if (newQuantity < 1) {
        removeFromQuote();
        return;
    }

    isAdding.value = true;
    try {
        quoteStore.updateQuantity(currentItem.value.id, newQuantity);
        showSwalToast(`Updated to ${newQuantity}!`, 'success');
        showQuantityInput.value = false;
    } catch (error) {
        console.error('Error updating quantity:', error);
        showSwalToast('Failed to update quantity. Please try again.', 'error');
    } finally {
        isAdding.value = false;
    }
};

// Add to quote function
const addToQuote = async () => {
    isAdding.value = true;

    try {
        quoteStore.addItem(props.product, quantity.value);
        showSwalToast('Added to quote!', 'success');
        showQuantityInput.value = false;
        quantity.value = 1;
    } catch (error) {
        console.error('Error adding to quote:', error);
        showSwalToast('Failed to add to quote. Please try again.', 'error');
    } finally {
        isAdding.value = false;
    }
};

// Remove from quote function
const removeFromQuote = () => {
    if (!currentItem.value) return;

    Swal.fire({
        title: 'Remove from quote?',
        text: `Remove "${title}" from your quote?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            confirmButton: 'bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg',
            cancelButton:
                'bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg',
        },
    }).then((result) => {
        let id: number = currentItem.value?.id as number;
        if (result.isConfirmed && id) {
            quoteStore.removeItem(id);
            showSwalToast('Removed from quote!', 'success');
        }
    });
};

// Quick add function (default quantity)
const quickAddToQuote = () => {
    if (currentItem.value) {
        quantity.value = currentItem.value.quantity;
        showQuantityInput.value = true;
    } else {
        quoteStore.addItem(props.product, 1);
        showSwalToast('Added to quote!', 'success');
    }
};

// Increase quantity
const increaseQuantity = () => {
    quantity.value++;
};

// Decrease quantity
const decreaseQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
    }
};

// Close quantity input
const closeQuantityInput = () => {
    showQuantityInput.value = false;
    quantity.value = 1;
};

// SweetAlert toast helper
const showSwalToast = (
    message: string,
    type: 'success' | 'error' | 'warning' | 'info' = 'info',
) => {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        },
    });

    Toast.fire({
        icon: type,
        title: message,
    });
};

// Get image source with fallback
const getImageSrc = (src: string | null) => {
    return src || '/images/placeholder-product.png';
};
</script>

<template>
    <div class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
        @mouseenter="isHovered = true" @mouseleave="isHovered = false">
        <!-- Quote Badge Indicator -->
        <div v-if="isInQuote"
            class="absolute left-3 top-3 z-10 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 px-3 py-1.5 text-xs font-semibold text-white shadow-md"
            title="This item is in your quote">
            <div class="flex items-center gap-1.5">
                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <span>In Quote ({{ currentItem?.quantity || 1 }})</span>
            </div>
        </div>

        <!-- Image Container -->
        <div class="relative h-56 overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 md:h-64">
            <!-- Main Image -->
            <img class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                :src="getImageSrc(picture)" :alt="title" loading="lazy" />

            <!-- Overlay Gradient -->
            <div
                class="absolute inset-0 bg-gradient-to-t from-black/10 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
            </div>

            <!-- Quick Actions Overlay -->
            <div
                class="absolute bottom-4 left-1/2 flex -translate-x-1/2 gap-2 opacity-0 transition-all duration-300 group-hover:bottom-4 group-hover:opacity-100">
                <button @click="quickAddToQuote"
                    class="flex items-center gap-2 rounded-full bg-white/95 px-4 py-2 text-sm font-semibold text-gray-900 shadow-lg backdrop-blur-sm transition-all hover:scale-105 hover:bg-white hover:shadow-xl">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ isInQuote ? 'Update' : 'Add to Quote' }}
                </button>
            </div>
        </div>

        <!-- Content Section -->
        <div class="flex flex-1 flex-col p-5">
            <!-- Title -->
            <h3 class="mb-2 line-clamp-2 font-bold text-gray-900 transition-colors group-hover:text-primary-600">
                {{ title }}
            </h3>

            <!-- Summary -->
            <p class="mb-4 line-clamp-3 flex-1 text-sm text-gray-600">
                {{ summary }}
            </p>

            <!-- Price & Badge -->
            <div class="mb-5 flex items-center justify-between">
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-primary-600">
                        {{ formattedPrice }}
                    </span>
                </div>

                <!-- Stock/Status Badge -->
                <span v-if="product.published"
                    class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                    Available
                </span>
            </div>

            <!-- Action Buttons -->
            <div class="mt-auto space-y-3">
                <!-- View Details Button -->
                <Link :href="url"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-3 font-semibold text-white transition-all hover:bg-primary-700 hover:shadow-md active:scale-95">
                    <span>View Details</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </Link>

                <!-- Quantity Input Section -->
                <div v-if="showQuantityInput" class="space-y-3">
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 p-2">
                        <button @click="decreaseQuantity"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-sm hover:bg-gray-50 disabled:opacity-50"
                            :disabled="quantity <= 1">
                            <svg class="h-5 w-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </button>

                        <div class="flex flex-col items-center">
                            <span class="text-sm text-gray-500">Quantity</span>
                            <input v-model.number="quantity" type="number" min="1"
                                class="w-20 border-0 bg-transparent text-center text-2xl font-bold text-gray-900 focus:outline-none focus:ring-0"
                                @keyup.enter="
                                    currentItem
                                        ? updateItemQuantity(quantity)
                                        : addToQuote()
                                    " />
                        </div>

                        <button @click="increaseQuantity"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-sm hover:bg-gray-50">
                            <svg class="h-5 w-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex gap-2">
                        <button @click="closeQuantityInput"
                            class="flex-1 rounded-xl border border-gray-300 bg-white px-4 py-2 font-medium text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button @click="
                            currentItem
                                ? updateItemQuantity(quantity)
                                : addToQuote()
                            " :disabled="isAdding"
                            class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-green-600 px-4 py-2 font-medium text-white hover:bg-green-700 disabled:opacity-50">
                            <svg v-if="isAdding" class="h-5 w-5 animate-spin text-white" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span v-else>{{
                                currentItem ? 'Update' : 'Add to Quote'
                                }}</span>
                        </button>
                    </div>
                </div>

                <!-- Quote Actions (when not showing quantity input) -->
                <div v-else class="flex gap-2">
                    <button v-if="isInQuote" @click="showQuantityInput = true"
                        class="group flex flex-1 items-center justify-center gap-2 rounded-xl border border-yellow-500 bg-yellow-50 px-4 py-3 font-medium text-yellow-700 transition-all hover:bg-yellow-100 hover:shadow-sm"
                        title="Update quantity">
                        <svg class="h-5 w-5 transition-transform group-hover:scale-110" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span class="hidden sm:inline">Update</span>
                    </button>
                    <button v-else @click="quickAddToQuote"
                        class="group flex flex-1 items-center justify-center gap-2 rounded-xl border border-green-500 bg-green-50 px-4 py-3 font-medium text-green-700 transition-all hover:bg-green-100 hover:shadow-sm"
                        title="Add to quote">
                        <svg class="h-5 w-5 transition-transform group-hover:scale-110" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="hidden sm:inline">Add to Quote</span>
                    </button>

                    <button v-if="isInQuote" @click="removeFromQuote"
                        class="group flex flex-1 items-center justify-center gap-2 rounded-xl border border-red-500 bg-red-50 px-4 py-3 font-medium text-red-700 transition-all hover:bg-red-100 hover:shadow-sm"
                        title="Remove from quote">
                        <svg class="h-5 w-5 transition-transform group-hover:scale-110" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span class="hidden sm:inline">Remove</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Special Features Badges (if any) -->
        <div v-if="product.front" class="absolute bottom-3 left-3 z-10">
            <span
                class="rounded-full bg-gradient-to-r from-blue-500 to-blue-600 px-3 py-1 text-xs font-semibold text-white shadow-md">
                Featured
            </span>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom scrollbar for quantity input */
input[type='number']::-webkit-inner-spin-button,
input[type='number']::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type='number'] {
    -moz-appearance: textfield;
}

/* Smooth transitions */
.group {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Hover effects */
.group:hover {
    transform: translateY(-4px);
    box-shadow:
        0 20px 25px -5px rgba(0, 0, 0, 0.1),
        0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Image zoom on hover */
.group img {
    transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Button hover effects */
button {
    transition: all 0.2s ease-in-out;
}

button:hover {
    transform: translateY(-1px);
}

/* Focus styles */
button:focus,
input:focus {
    outline: none;
    ring: 2px;
    ring-color: var(--color-primary-500);
}
</style>
