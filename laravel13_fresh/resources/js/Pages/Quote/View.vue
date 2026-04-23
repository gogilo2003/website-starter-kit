// Quote.vue
<script setup lang="ts">
import Container from '@/Components/Container.vue';
import Heading1 from '@/Components/Web/Heading1.vue';
import type { iProduct, iQuote } from '@/interfaces';
import WebHeader from '@/Layouts/WebHeader.vue';
import WebLayout from '@/Layouts/WebLayout.vue';
import { useQuoteStore } from '@/stores/quote';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, onMounted, ref, watch } from 'vue';

// Props with proper typing
interface Props {
    quote?: iQuote;
    trackingCode?: string;
}

const props = defineProps<Props>();

// Use the quote store
const quoteStore = useQuoteStore();

// Reactive state
const isTrackingView = ref(!!props.trackingCode || !!props.quote);
const isLoading = ref(false);
const quoteData = ref<iQuote | null>(props.quote || null);

// Quote Request Form - Initialize from store draft if available
const quoteForm = useForm({
    name: quoteStore.draftQuote?.name || '',
    email: quoteStore.draftQuote?.email || '',
    phone: quoteStore.draftQuote?.phone || '',
    company: quoteStore.draftQuote?.company || '',
    message: quoteStore.draftQuote?.message || '',
    products: [] as Array<{
        product_id: number;
        quantity: number;
        price: number;
    }>,
});

// Tracking Form
const trackingForm = useForm({
    code: props.trackingCode || '',
});

// Computed properties
const totalQuoteAmount = computed(() => {
    if (quoteData.value?.total_amount !== undefined) {
        return quoteData.value.total_amount;
    }

    if (quoteData.value?.items) {
        return quoteData.value.items.reduce((total, item) => {
            return total + item.price * item.quantity;
        }, 0);
    }

    return quoteStore.totalValue;
});

const hasQuoteItems = computed(() => quoteStore.items.length > 0);

// Use formatted items if available in store, fallback to raw items
const quoteItems = computed(() => {
    return quoteStore.formatItemsForDisplay?.() || quoteStore.items;
});

// Save draft when form changes (debounced)
watch(
    () => [
        quoteForm.name,
        quoteForm.email,
        quoteForm.phone,
        quoteForm.company,
        quoteForm.message,
    ],
    ([name, email, phone, company, message]) => {
        clearTimeout((quoteForm as any)._saveTimeout);

        (quoteForm as any)._saveTimeout = setTimeout(() => {
            quoteStore.saveDraft({
                name,
                email,
                phone,
                company,
                message,
            });
        }, 500);
    },
);

// Methods
const fetchQuoteByCode = async (code: string) => {
    if (!code.trim()) return;

    isLoading.value = true;
    try {
        await router.get(route('quote-track', { code }));
    } catch (error) {
        console.error('Error fetching quote:', error);
        showSwalAlert(
            'Quote not found. Please check your tracking code.',
            'error',
        );
    } finally {
        isLoading.value = false;
    }
};

const submitQuoteRequest = () => {
    if (!hasQuoteItems.value) {
        showSwalAlert(
            'Please add products to your quote before submitting.',
            'error',
        );
        return;
    }

    quoteForm.products = quoteStore.prepareProductsForSubmission();

    quoteForm.post(route('quote-request'), {
        preserveScroll: true,
        only: ['notification', 'quote', 'errors'],
        onSuccess: () => {
            const data = props.quote;
            if (data) {
                quoteData.value = data;
                isTrackingView.value = true;

                Swal.fire({
                    icon: 'success',
                    title: 'Quote Request Submitted!',
                    html: `
                        <div class="text-left">
                            <p>Your quote request has been submitted successfully!</p>
                            <p class="mt-2"><strong>Tracking Code:</strong> ${data.code}</p>
                            <p class="mt-2">Use this code to track your quote status.</p>
                        </div>
                    `,
                    confirmButtonText: 'View Quote',
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                }).then((result) => {
                    quoteStore.clearAll();
                    if (result.isConfirmed) {
                        router.visit(route('quote-track', { code: data.code }));
                    }
                });

                quoteStore.clearAll();
            }
        },
        onError: () => {
            showSwalAlert(
                'There was an error submitting your quote request. Please try again.',
                'error',
            );
        },
    });
};

const submitTrackingCode = () => {
    if (!trackingForm.code.trim()) {
        showSwalAlert('Please enter a tracking code.', 'error');
        return;
    }

    router.get(route('quote-track', { code: trackingForm.code }));
};

// Updated: shows toast on quantity change
const updateItemQuantity = (id: number, quantity: number) => {
    if (quantity < 1) {
        quoteStore.removeItem(id);
        showSwalToast('Product removed from quote.', 'info');
        return;
    }

    quoteStore.updateQuantity(id, quantity);
    showSwalToast('Quantity updated!', 'success');
};

const clearQuote = () => {
    Swal.fire({
        title: 'Clear Quote?',
        text: 'Are you sure you want to remove all items from your quote?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, clear all',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            quoteStore.clearItems();
            quoteStore.clearDraft();
            showSwalToast('Quote cleared.', 'success');
        }
    });
};

// SweetAlert helpers
const showSwalAlert = (
    message: string,
    type: 'success' | 'error' | 'warning' | 'info' = 'info',
) => {
    Swal.fire({
        icon: type,
        title: type.charAt(0).toUpperCase() + type.slice(1),
        text: message,
        confirmButtonText: 'OK',
    });
};

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

const formatCurrency = (amount: number | string | undefined) => {
    const numAmount =
        typeof amount === 'string' ? parseFloat(amount) : amount || 0;

    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES',
    }).format(numAmount);
};

const formatDate = (dateString: string | Date | null | undefined) => {
    if (!dateString) return 'N/A';

    const date =
        typeof dateString === 'string' ? new Date(dateString) : dateString;

    return date.toLocaleDateString('en-KE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getProductImage = (product: iProduct) => {
    return product.picture || '/images/placeholder-product.png';
};

const getStatusColorClasses = (status: string) => {
    const colorMap: Record<string, string> = {
        pending: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
        sent: 'bg-blue-100 text-blue-800 border border-blue-200',
        viewed: 'bg-indigo-100 text-indigo-800 border border-indigo-200',
        accepted: 'bg-green-100 text-green-800 border border-green-200',
        rejected: 'bg-red-100 text-red-800 border border-red-200',
        expired: 'bg-gray-100 text-gray-800 border border-gray-200',
        draft: 'bg-purple-100 text-purple-800 border border-purple-200',
    };

    return (
        colorMap[status] || 'bg-gray-100 text-gray-800 border border-gray-200'
    );
};

const getStatusLabel = (status: string) => {
    const labelMap: Record<string, string> = {
        pending: 'Pending',
        sent: 'Sent',
        viewed: 'Viewed',
        accepted: 'Accepted',
        rejected: 'Rejected',
        expired: 'Expired',
        draft: 'Draft',
    };

    return labelMap[status] || status.charAt(0).toUpperCase() + status.slice(1);
};

// Lifecycle
onMounted(() => {
    if (props.trackingCode && !props.quote) {
        fetchQuoteByCode(props.trackingCode);
    }

    if (!quoteStore.isInitialized) {
        quoteStore.initialize();
    }
});

const copyTrackingLink = (tracking_url: string | null) => {
    navigator.clipboard.writeText(tracking_url || '').then(() => {
        showSwalToast('Tracking link copied to clipboard!', 'success');
    });
};
</script>

<template>
    <WebLayout
        :title="
            isTrackingView && quoteData
                ? `Quote #${quoteData.code}`
                : 'Request a Quote'
        "
    >
        <template #header>
            <WebHeader>
                <Heading1 v-if="isTrackingView && quoteData">
                    Quote #{{ quoteData.code }}
                </Heading1>
                <Heading1 v-else> Request a Quote </Heading1>
                <p class="mt-2 text-gray-600">
                    {{
                        isTrackingView && quoteData
                            ? 'Track your quote status and details'
                            : 'Build your quote with selected products'
                    }}
                </p>
            </WebHeader>
        </template>

        <div class="bg-white/90 py-8 md:py-16">
            <Container>
                <!-- Loading State -->
                <div v-if="isLoading" class="py-12 text-center">
                    <div
                        class="inline-block h-12 w-12 animate-spin rounded-full border-b-2 border-t-2 border-primary-600"
                    ></div>
                    <p class="mt-4 text-gray-600">Loading quote details...</p>
                </div>

                <!-- Tracking View -->
                <div v-else-if="isTrackingView && quoteData" class="space-y-8">
                    <div class="rounded-xl bg-white p-6 shadow-lg">
                        <div
                            class="mb-6 flex flex-col justify-between md:flex-row md:items-center"
                        >
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">
                                    Quote Details
                                </h2>
                                <p class="text-gray-600">
                                    Created on
                                    {{ formatDate(quoteData.created_at) }}
                                </p>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full px-3 py-1 text-sm font-medium',
                                        getStatusColorClasses(quoteData.status),
                                    ]"
                                >
                                    {{ getStatusLabel(quoteData.status) }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-4">
                                <div>
                                    <h3
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Tracking Code
                                    </h3>
                                    <p
                                        class="font-mono text-lg font-bold text-gray-900"
                                    >
                                        {{ quoteData.code }}
                                    </p>
                                </div>
                                <div>
                                    <h3
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Customer Information
                                    </h3>
                                    <p
                                        class="text-lg font-semibold text-gray-900"
                                    >
                                        {{ quoteData.name }}
                                    </p>
                                    <p class="text-gray-600">
                                        {{ quoteData.email }}
                                    </p>
                                    <p
                                        v-if="quoteData.phone"
                                        class="text-gray-600"
                                    >
                                        {{ quoteData.phone }}
                                    </p>
                                    <p
                                        v-if="quoteData.company"
                                        class="text-gray-600"
                                    >
                                        {{ quoteData.company }}
                                    </p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div v-if="quoteData.view_count !== undefined">
                                    <h3
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Views
                                    </h3>
                                    <p
                                        class="text-lg font-semibold text-gray-900"
                                    >
                                        {{ quoteData.view_count }}
                                    </p>
                                    <p
                                        v-if="quoteData.last_viewed_at"
                                        class="text-sm text-gray-500"
                                    >
                                        Last viewed:
                                        {{
                                            formatDate(quoteData.last_viewed_at)
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <h3
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Total Amount
                                    </h3>
                                    <p
                                        class="text-2xl font-bold text-primary-600"
                                    >
                                        {{
                                            formatCurrency(
                                                quoteData.total_amount ||
                                                    totalQuoteAmount,
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div v-if="quoteData.message" class="mb-8">
                            <h3 class="mb-2 text-sm font-medium text-gray-500">
                                Message
                            </h3>
                            <div class="rounded-lg bg-gray-50 p-4">
                                <p class="whitespace-pre-line text-gray-700">
                                    {{ quoteData.message }}
                                </p>
                            </div>
                        </div>

                        <div
                            v-if="quoteData.items && quoteData.items.length > 0"
                        >
                            <h3
                                class="mb-4 text-lg font-semibold text-gray-900"
                            >
                                Products
                            </h3>
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full divide-y divide-gray-200"
                                >
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Product
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Quantity
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Unit Price
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Total
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="divide-y divide-gray-200 bg-white"
                                    >
                                        <tr
                                            v-for="item in quoteData.items"
                                            :key="item.id"
                                        >
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div
                                                        class="h-10 w-10 flex-shrink-0"
                                                    >
                                                        <img
                                                            v-if="
                                                                item.product
                                                                    ?.picture
                                                            "
                                                            :src="
                                                                getProductImage(
                                                                    item.product,
                                                                )
                                                            "
                                                            :alt="
                                                                item.product
                                                                    ?.title
                                                            "
                                                            class="h-10 w-10 rounded-md object-cover"
                                                        />
                                                    </div>
                                                    <div class="ml-4">
                                                        <div
                                                            class="text-sm font-medium text-gray-900"
                                                        >
                                                            {{
                                                                item.product
                                                                    ?.title ||
                                                                `Product #${item.product_id}`
                                                            }}
                                                        </div>
                                                        <div
                                                            v-if="
                                                                item.product
                                                                    ?.summary
                                                            "
                                                            class="max-w-xs truncate text-sm text-gray-500"
                                                        >
                                                            {{
                                                                item.product
                                                                    .summary
                                                            }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td
                                                class="whitespace-nowrap px-6 py-4 text-sm text-gray-900"
                                            >
                                                {{ item.quantity }}
                                            </td>
                                            <td
                                                class="whitespace-nowrap px-6 py-4 text-sm text-gray-900"
                                            >
                                                {{ formatCurrency(item.price) }}
                                            </td>
                                            <td
                                                class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900"
                                            >
                                                {{
                                                    formatCurrency(
                                                        item.price *
                                                            item.quantity,
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-gray-50">
                                            <td
                                                colspan="3"
                                                class="px-6 py-4 text-right text-sm font-medium text-gray-900"
                                            >
                                                Total Amount:
                                            </td>
                                            <td
                                                class="whitespace-nowrap px-6 py-4 text-sm font-bold text-primary-600"
                                            >
                                                {{
                                                    formatCurrency(
                                                        quoteData.total_amount ||
                                                            totalQuoteAmount,
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div
                            v-else
                            class="rounded-lg bg-gray-50 py-8 text-center"
                        >
                            <p class="text-gray-500">
                                No products in this quote.
                            </p>
                        </div>

                        <div
                            class="mt-8 flex flex-col gap-4 border-t border-gray-200 pt-6 sm:flex-row"
                        >
                            <a
                                :href="route('quote')"
                                class="inline-flex flex-1 items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                            >
                                Request Another Quote
                            </a>
                            <button
                                v-if="quoteData.tracking_url"
                                @click="
                                    copyTrackingLink(quoteData.tracking_url)
                                "
                                class="inline-flex flex-1 items-center justify-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                            >
                                Copy Tracking Link
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quote Request Form View -->
                <div v-else class="mx-auto">
                    <!-- Track Existing Quote -->
                    <div class="mb-8 rounded-xl bg-white p-6 shadow-md">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">
                            Track Existing Quote
                        </h3>
                        <form
                            @submit.prevent="submitTrackingCode"
                            class="flex gap-4"
                        >
                            <div class="flex-1">
                                <input
                                    v-model="trackingForm.code"
                                    type="text"
                                    placeholder="Enter your tracking code (e.g., Q-ABC123)"
                                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-primary-500 sm:text-sm"
                                    :class="{
                                        'border-red-500':
                                            trackingForm.errors.code,
                                    }"
                                />
                                <p
                                    v-if="trackingForm.errors.code"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ trackingForm.errors.code }}
                                </p>
                            </div>
                            <button
                                type="submit"
                                :disabled="trackingForm.processing"
                                class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 disabled:opacity-50"
                            >
                                <span
                                    v-if="trackingForm.processing"
                                    class="mr-2 inline-block h-4 w-4 animate-spin rounded-full border-b-2 border-t-2 border-white"
                                ></span>
                                Track Quote
                            </button>
                        </form>
                    </div>

                    <!-- New Quote Request -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-lg">
                        <div
                            class="border-b border-gray-200 bg-gray-50 px-6 py-4"
                        >
                            <h2 class="text-xl font-bold text-gray-900">
                                New Quote Request
                            </h2>
                            <p class="mt-1 text-gray-600">
                                Fill in your details and we'll prepare a custom
                                quote
                            </p>
                        </div>

                        <div class="flex flex-col md:flex-row">
                            <div class="flex-1 p-6 md:px-10">
                                <!-- Quote Items -->
                                <div v-if="hasQuoteItems" class="mb-8 w-full">
                                    <div
                                        class="mb-4 flex items-center justify-between"
                                    >
                                        <div
                                            class="flex flex-1 items-center gap-4"
                                        >
                                            <h3
                                                class="flex-1 text-lg font-semibold text-gray-900"
                                            >
                                                Quote Items ({{
                                                    quoteStore.totalItems
                                                }}
                                                items)
                                            </h3>
                                            <button
                                                @click="clearQuote"
                                                class="flex-none text-sm text-red-600 hover:text-red-800"
                                            >
                                                Clear All
                                            </button>
                                        </div>
                                        <!-- <span class="text-sm text-gray-600">
                                            Total:
                                            {{
                                                formatCurrency(totalQuoteAmount)
                                            }}
                                        </span> -->
                                    </div>

                                    <div class="mb-6 space-y-4">
                                        <div
                                            v-for="item in quoteItems"
                                            :key="item.id"
                                            class="flex items-center justify-between rounded-lg bg-gray-50 p-4"
                                        >
                                            <div
                                                class="flex flex-1 items-center"
                                            >
                                                <div
                                                    class="mr-4 h-16 w-16 flex-shrink-0"
                                                >
                                                    <img
                                                        v-if="
                                                            item.product
                                                                ?.picture
                                                        "
                                                        :src="
                                                            getProductImage(
                                                                item.product,
                                                            )
                                                        "
                                                        :alt="
                                                            item.product?.title
                                                        "
                                                        class="h-16 w-16 rounded-md object-cover"
                                                    />
                                                </div>
                                                <div class="flex-1">
                                                    <h4
                                                        class="font-medium text-gray-900"
                                                    >
                                                        {{
                                                            item.product
                                                                ?.title ||
                                                            `Product #${item.product_id}`
                                                        }}
                                                    </h4>
                                                    <p
                                                        v-if="
                                                            item.product
                                                                ?.summary
                                                        "
                                                        class="line-clamp-2 text-sm text-gray-500"
                                                    >
                                                        {{
                                                            item.product.summary
                                                        }}
                                                    </p>
                                                    <p
                                                        class="mt-1 text-sm text-gray-600"
                                                    >
                                                        Unit Price:
                                                        {{
                                                            formatCurrency(
                                                                item.price,
                                                            )
                                                        }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div
                                                class="flex items-center gap-4"
                                            >
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <button
                                                        type="button"
                                                        @click="
                                                            updateItemQuantity(
                                                                item.id,
                                                                item.quantity -
                                                                    1,
                                                            )
                                                        "
                                                        class="flex h-8 w-8 items-center justify-center rounded-md border border-gray-300 hover:bg-gray-100"
                                                    >
                                                        -
                                                    </button>
                                                    <span
                                                        class="w-12 text-center font-medium"
                                                        >{{
                                                            item.quantity
                                                        }}</span
                                                    >
                                                    <button
                                                        type="button"
                                                        @click="
                                                            updateItemQuantity(
                                                                item.id,
                                                                item.quantity +
                                                                    1,
                                                            )
                                                        "
                                                        class="flex h-8 w-8 items-center justify-center rounded-md border border-gray-300 hover:bg-gray-100"
                                                    >
                                                        +
                                                    </button>
                                                </div>

                                                <div
                                                    class="min-w-24 text-right"
                                                >
                                                    <p
                                                        class="font-semibold text-gray-900"
                                                    >
                                                        {{
                                                            formatCurrency(
                                                                item.price *
                                                                    item.quantity,
                                                            )
                                                        }}
                                                    </p>
                                                </div>

                                                <button
                                                    type="button"
                                                    @click="
                                                        quoteStore.removeItem(
                                                            item.id,
                                                        )
                                                    "
                                                    class="p-2 text-red-600 hover:text-red-800"
                                                >
                                                    <svg
                                                        class="h-5 w-5"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                        />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center justify-between border-t border-gray-200 pt-4"
                                    >
                                        <div>
                                            <p class="text-sm text-gray-600">
                                                {{
                                                    quoteStore.uniqueProductsCount
                                                }}
                                                unique products
                                            </p>
                                        </div>
                                        <!-- <div class="text-right">
                                            <p class="text-sm text-gray-600">
                                                Estimated Total
                                            </p>
                                            <p
                                                class="text-2xl font-bold text-primary-600"
                                            >
                                                {{
                                                    formatCurrency(
                                                        totalQuoteAmount,
                                                    )
                                                }}
                                            </p>
                                        </div> -->
                                    </div>
                                </div>

                                <!-- Empty State -->
                                <div
                                    v-else
                                    class="mb-8 rounded-lg bg-gray-50 py-12 text-center"
                                >
                                    <svg
                                        class="mx-auto h-12 w-12 text-gray-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                        />
                                    </svg>
                                    <h3
                                        class="mt-4 text-lg font-medium text-gray-900"
                                    >
                                        Your quote is empty
                                    </h3>
                                    <p class="mt-2 text-gray-600">
                                        Add products to your quote to request a
                                        custom price.
                                    </p>
                                    <div class="mt-6">
                                        <a
                                            :href="route('products')"
                                            class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700"
                                        >
                                            Browse Products
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-none bg-gray-200 p-6">
                                <!-- Contact Form -->
                                <form @submit.prevent="submitQuoteRequest">
                                    <div
                                        class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2"
                                    >
                                        <div>
                                            <label
                                                for="name"
                                                class="block text-sm font-medium text-gray-700"
                                                >Full Name *</label
                                            >
                                            <input
                                                id="name"
                                                v-model="quoteForm.name"
                                                type="text"
                                                required
                                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-primary-500 sm:text-sm"
                                                :class="{
                                                    'border-red-500':
                                                        quoteForm.errors.name,
                                                }"
                                            />
                                            <p
                                                v-if="quoteForm.errors.name"
                                                class="mt-1 text-sm text-red-600"
                                            >
                                                {{ quoteForm.errors.name }}
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                for="email"
                                                class="block text-sm font-medium text-gray-700"
                                                >Email Address *</label
                                            >
                                            <input
                                                id="email"
                                                v-model="quoteForm.email"
                                                type="email"
                                                required
                                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-primary-500 sm:text-sm"
                                                :class="{
                                                    'border-red-500':
                                                        quoteForm.errors.email,
                                                }"
                                            />
                                            <p
                                                v-if="quoteForm.errors.email"
                                                class="mt-1 text-sm text-red-600"
                                            >
                                                {{ quoteForm.errors.email }}
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                for="phone"
                                                class="block text-sm font-medium text-gray-700"
                                                >Phone Number</label
                                            >
                                            <input
                                                id="phone"
                                                v-model="quoteForm.phone"
                                                type="tel"
                                                placeholder="+1 (555) 123-4567"
                                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-primary-500 sm:text-sm"
                                                :class="{
                                                    'border-red-500':
                                                        quoteForm.errors.phone,
                                                }"
                                            />
                                            <p
                                                v-if="quoteForm.errors.phone"
                                                class="mt-1 text-sm text-red-600"
                                            >
                                                {{ quoteForm.errors.phone }}
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                for="company"
                                                class="block text-sm font-medium text-gray-700"
                                                >Company</label
                                            >
                                            <input
                                                id="company"
                                                v-model="quoteForm.company"
                                                type="text"
                                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-primary-500 sm:text-sm"
                                                :class="{
                                                    'border-red-500':
                                                        quoteForm.errors
                                                            .company,
                                                }"
                                            />
                                            <p
                                                v-if="quoteForm.errors.company"
                                                class="mt-1 text-sm text-red-600"
                                            >
                                                {{ quoteForm.errors.company }}
                                            </p>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label
                                                for="message"
                                                class="block text-sm font-medium text-gray-700"
                                                >Additional Message</label
                                            >
                                            <textarea
                                                id="message"
                                                v-model="quoteForm.message"
                                                rows="4"
                                                placeholder="Any specific requirements or notes..."
                                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-primary-500 sm:text-sm"
                                                :class="{
                                                    'border-red-500':
                                                        quoteForm.errors
                                                            .message,
                                                }"
                                            ></textarea>
                                            <p
                                                v-if="quoteForm.errors.message"
                                                class="mt-1 text-sm text-red-600"
                                            >
                                                {{ quoteForm.errors.message }}
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        class="flex flex-col gap-4 border-t border-gray-200 pt-6 sm:flex-row"
                                    >
                                        <a
                                            :href="route('products')"
                                            class="inline-flex flex-1 items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                        >
                                            Continue Shopping
                                        </a>
                                        <button
                                            type="submit"
                                            :disabled="
                                                quoteForm.processing ||
                                                !hasQuoteItems
                                            "
                                            class="inline-flex flex-1 items-center justify-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 disabled:opacity-50"
                                            :class="{
                                                'cursor-not-allowed':
                                                    !hasQuoteItems,
                                            }"
                                        >
                                            <span
                                                v-if="quoteForm.processing"
                                                class="mr-2 inline-block h-4 w-4 animate-spin rounded-full border-b-2 border-t-2 border-white"
                                            ></span>
                                            <span v-if="quoteForm.processing"
                                                >Processing...</span
                                            >
                                            <span v-else
                                                >Submit Quote Request</span
                                            >
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div
                        class="mt-8 rounded-xl border border-blue-200 bg-blue-50 p-6"
                    >
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-5 w-5 text-blue-400"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    How it works
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc space-y-1 pl-5">
                                        <li>
                                            Add products to your quote from any
                                            product page
                                        </li>
                                        <li>
                                            Review and adjust quantities here
                                        </li>
                                        <li>
                                            Fill in your contact details
                                            (auto-saved)
                                        </li>
                                        <li>
                                            Submit the request and receive a
                                            tracking code
                                        </li>
                                        <li>
                                            Use the tracking code to monitor
                                            your quote status
                                        </li>
                                        <li>
                                            Our team will review and send you a
                                            detailed quote within 24 hours
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Container>
        </div>
    </WebLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
