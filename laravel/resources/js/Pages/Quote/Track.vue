// Pages/Track.vue - FIXED version
<script setup lang="ts">
import Container from '@/Components/Container.vue';
import Heading1 from '@/Components/Web/Heading1.vue';
import type { iProduct, iQuote, iQuoteItem } from '@/interfaces';
import WebHeader from '@/Layouts/WebHeader.vue';
import WebLayout from '@/Layouts/WebLayout.vue';
import { useQuoteStore } from '@/stores/quote';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, onMounted, ref } from 'vue';

// Props with proper typing
interface Props {
    quote?: iQuote;
    trackingCode?: string;
    notFound?: boolean;
}

const props = defineProps<Props>();

// Use the quote store
const quoteStore = useQuoteStore();

// Reactive state
const isLoading = ref(false);
const quoteData = ref<iQuote | null>(props.quote || null);
const hasError = ref(props.notFound || false);

// Tracking Form
const trackingForm = useForm({
    code: props.trackingCode || '',
});

// Computed properties
const totalQuoteAmount = computed(() => {
    if (quoteData.value?.total_amount !== undefined) {
        return quoteData.value.total_amount;
    }

    // Calculate from items if total_amount not provided
    if (quoteData.value?.items) {
        return quoteData.value.items.reduce(
            (total: number, item: iQuoteItem) => {
                return total + item.price * item.quantity;
            },
            0,
        );
    }

    return 0;
});

const quoteStatus = computed(() => {
    return quoteData.value?.status || 'unknown';
});

// Methods
const fetchQuoteByCode = async (code: string) => {
    if (!code.trim()) {
        showSwalAlert('Please enter a tracking code.', 'warning');
        return;
    }

    isLoading.value = true;
    hasError.value = false;

    try {
        await router.get(
            route('quote-track', { code }),
            {},
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    } catch (error) {
        console.error('Error fetching quote:', error);
        hasError.value = true;
        showSwalAlert(
            'Quote not found. Please check your tracking code and try again.',
            'error',
        );
    } finally {
        isLoading.value = false;
    }
};

const submitTrackingCode = () => {
    if (!trackingForm.code.trim()) {
        showSwalAlert('Please enter a tracking code.', 'warning');
        return;
    }

    // Navigate to the track route with the code
    router.visit(route('quote-track', { code: trackingForm.code }));
};

const requestNewQuote = () => {
    router.visit(route('quote'));
};

const copyTrackingLink = () => {
    if (!quoteData.value?.tracking_url) return;

    navigator.clipboard
        .writeText(quoteData.value.tracking_url)
        .then(() => {
            showSwalToast('Tracking link copied to clipboard!', 'success');
        })
        .catch((err) => {
            console.error('Failed to copy: ', err);
            showSwalToast('Failed to copy link.', 'error');
        });
};

const copyTrackingCode = () => {
    if (!quoteData.value?.code) return;

    navigator.clipboard
        .writeText(quoteData.value.code)
        .then(() => {
            showSwalToast('Tracking code copied to clipboard!', 'success');
        })
        .catch((err) => {
            console.error('Failed to copy: ', err);
            showSwalToast('Failed to copy code.', 'error');
        });
};

// SweetAlert helper functions
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

    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'KES',
    }).format(numAmount);
};

const formatDate = (dateString: string | Date | null | undefined) => {
    if (!dateString) return 'N/A';

    const date =
        typeof dateString === 'string' ? new Date(dateString) : dateString;

    if (isNaN(date.getTime())) return 'Invalid Date';

    return date.toLocaleDateString('en-US', {
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

// Status badge color mapping
const getStatusColorClasses = (status: string) => {
    const colorMap: Record<string, string> = {
        pending: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
        sent: 'bg-blue-100 text-blue-800 border border-blue-200',
        viewed: 'bg-indigo-100 text-indigo-800 border border-indigo-200',
        accepted: 'bg-green-100 text-green-800 border border-green-200',
        rejected: 'bg-red-100 text-red-800 border border-red-200',
        expired: 'bg-gray-100 text-gray-800 border border-gray-200',
        draft: 'bg-purple-100 text-purple-800 border border-purple-200',
        unknown: 'bg-gray-100 text-gray-800 border border-gray-200',
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
        unknown: 'Unknown',
    };

    return labelMap[status] || status.charAt(0).toUpperCase() + status.slice(1);
};

const getStatusDescription = (status: string) => {
    const descriptions: Record<string, string> = {
        pending: 'Your quote request is being reviewed by our team.',
        sent: 'A detailed quote has been sent to your email.',
        viewed: 'You have viewed the quote. Please review the details.',
        accepted: 'You have accepted the quote. We will contact you shortly.',
        rejected:
            'You have declined the quote. Feel free to request a new one.',
        expired: 'This quote has expired. Please request a new quote.',
        draft: 'This is a draft quote. Please submit it to get a final quote.',
        unknown: 'Status information is not available.',
    };

    return descriptions[status] || 'Your quote is being processed.';
};

// Lifecycle hooks
onMounted(() => {
    if (props.trackingCode && !props.quote && !props.notFound) {
        fetchQuoteByCode(props.trackingCode);
    }

    // Set quoteData from props if available
    if (props.quote) {
        quoteData.value = props.quote;
    }
});
</script>

<template>
    <WebLayout
        :title="
            quoteData ? `Track Quote #${quoteData.code}` : 'Track Your Quote'
        "
    >
        <template #header>
            <WebHeader>
                <Heading1 v-if="quoteData">
                    Track Quote #{{ quoteData.code }}
                </Heading1>
                <Heading1 v-else> Track Your Quote </Heading1>
                <p class="mt-2 text-gray-600">
                    {{
                        quoteData
                            ? 'Monitor your quote status and details'
                            : 'Enter your tracking code to view your quote'
                    }}
                </p>
            </WebHeader>
        </template>
        <div class="py-8 md:py-16">
            <Container>
                <!-- Loading State -->
                <div v-if="isLoading" class="py-12 text-center">
                    <div
                        class="inline-block h-12 w-12 animate-spin rounded-full border-b-2 border-t-2 border-primary-600"
                    ></div>
                    <p class="mt-4 text-gray-600">Loading quote details...</p>
                </div>

                <!-- Quote Found View -->
                <div v-else-if="quoteData && !hasError" class="space-y-8">
                    <!-- Status Overview -->
                    <div class="rounded-xl bg-white p-6 shadow-lg">
                        <div
                            class="mb-6 flex flex-col justify-between md:flex-row md:items-center"
                        >
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">
                                    Quote Status
                                </h2>
                                <p class="mt-1 text-gray-600">
                                    {{ getStatusDescription(quoteStatus) }}
                                </p>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold',
                                        getStatusColorClasses(quoteStatus),
                                    ]"
                                >
                                    {{ getStatusLabel(quoteStatus) }}
                                </span>
                            </div>
                        </div>

                        <!-- Status Timeline -->
                        <div class="mb-8">
                            <h3
                                class="mb-4 text-lg font-semibold text-gray-900"
                            >
                                Quote Timeline
                            </h3>
                            <div
                                class="flex flex-col gap-4 border-b pb-4 md:flex-row md:gap-10"
                            >
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100"
                                        >
                                            <svg
                                                class="h-5 w-5 text-green-600"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            Quote Request Submitted
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{
                                                formatDate(quoteData.created_at)
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    v-if="quoteData.last_viewed_at"
                                    class="flex items-start"
                                >
                                    <div class="flex-shrink-0">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100"
                                        >
                                            <svg
                                                class="h-5 w-5 text-blue-600"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    d="M10 12a2 2 0 100-4 2 2 0 000 4z"
                                                />
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            Quote Viewed
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{
                                                formatDate(
                                                    quoteData.last_viewed_at,
                                                )
                                            }}
                                        </p>
                                        <p
                                            v-if="
                                                quoteData.view_count !==
                                                undefined
                                            "
                                            class="text-sm text-gray-500"
                                        >
                                            Viewed
                                            {{ quoteData.view_count }} time(s)
                                        </p>
                                    </div>
                                </div>

                                <div
                                    v-if="
                                        quoteData.updated_at &&
                                        quoteData.updated_at !==
                                            quoteData.created_at
                                    "
                                    class="flex items-start"
                                >
                                    <div class="flex-shrink-0">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100"
                                        >
                                            <svg
                                                class="h-5 w-5 text-purple-600"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            Last Updated
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{
                                                formatDate(quoteData.updated_at)
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Info Cards -->
                        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div class="rounded-lg bg-gray-50 p-4">
                                <h3 class="text-sm font-medium text-gray-500">
                                    Tracking Code
                                </h3>
                                <div class="mt-2 flex items-center">
                                    <p
                                        class="font-mono text-lg font-bold text-gray-900"
                                    >
                                        {{ quoteData.code }}
                                    </p>
                                    <button
                                        @click="copyTrackingCode"
                                        class="ml-2 text-gray-400 hover:text-gray-600"
                                        title="Copy tracking code"
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
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="rounded-lg bg-gray-50 p-4">
                                <h3 class="text-sm font-medium text-gray-500">
                                    Total Amount
                                </h3>
                                <p
                                    class="mt-2 text-2xl font-bold text-primary-600"
                                >
                                    {{
                                        formatCurrency(
                                            quoteData.total_amount ||
                                                totalQuoteAmount,
                                        )
                                    }}
                                </p>
                            </div>

                            <div class="rounded-lg bg-gray-50 p-4">
                                <h3 class="text-sm font-medium text-gray-500">
                                    Created
                                </h3>
                                <p
                                    class="mt-2 text-lg font-semibold text-gray-900"
                                >
                                    {{
                                        formatDate(quoteData.created_at).split(
                                            ' at ',
                                        )[0]
                                    }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{
                                        formatDate(quoteData.created_at).split(
                                            ' at ',
                                        )[1]
                                    }}
                                </p>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="mb-8">
                            <h3
                                class="mb-4 text-lg font-semibold text-gray-900"
                            >
                                Customer Information
                            </h3>
                            <div class="rounded-lg bg-gray-50 p-6">
                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                >
                                    <div>
                                        <p class="text-sm text-gray-500">
                                            Name
                                        </p>
                                        <p
                                            class="text-lg font-semibold text-gray-900"
                                        >
                                            {{ quoteData.name }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">
                                            Email
                                        </p>
                                        <p
                                            class="text-lg font-semibold text-gray-900"
                                        >
                                            {{ quoteData.email }}
                                        </p>
                                    </div>
                                    <div v-if="quoteData.phone">
                                        <p class="text-sm text-gray-500">
                                            Phone
                                        </p>
                                        <p
                                            class="text-lg font-semibold text-gray-900"
                                        >
                                            {{ quoteData.phone }}
                                        </p>
                                    </div>
                                    <div v-if="quoteData.company">
                                        <p class="text-sm text-gray-500">
                                            Company
                                        </p>
                                        <p
                                            class="text-lg font-semibold text-gray-900"
                                        >
                                            {{ quoteData.company }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Message -->
                        <div v-if="quoteData.message" class="mb-8">
                            <h3
                                class="mb-4 text-lg font-semibold text-gray-900"
                            >
                                Additional Message
                            </h3>
                            <div class="rounded-lg bg-gray-50 p-6">
                                <p class="whitespace-pre-line text-gray-700">
                                    {{ quoteData.message }}
                                </p>
                            </div>
                        </div>

                        <!-- Products Table -->
                        <div
                            v-if="quoteData.items && quoteData.items.length > 0"
                        >
                            <div class="mb-4 flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Products ({{ quoteData.items.length }})
                                </h3>
                                <span class="text-sm text-gray-600">
                                    Total:
                                    {{
                                        formatCurrency(
                                            quoteData.total_amount ||
                                                totalQuoteAmount,
                                        )
                                    }}
                                </span>
                            </div>

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
                                                class="whitespace-nowrap px-6 py-4"
                                            >
                                                <div
                                                    class="text-sm text-gray-900"
                                                >
                                                    {{ item.quantity }}
                                                </div>
                                            </td>
                                            <td
                                                class="whitespace-nowrap px-6 py-4"
                                            >
                                                <div
                                                    class="text-sm text-gray-900"
                                                >
                                                    {{
                                                        formatCurrency(
                                                            item.price,
                                                        )
                                                    }}
                                                </div>
                                            </td>
                                            <td
                                                class="whitespace-nowrap px-6 py-4"
                                            >
                                                <div
                                                    class="text-sm font-semibold text-gray-900"
                                                >
                                                    {{
                                                        formatCurrency(
                                                            item.price *
                                                                item.quantity,
                                                        )
                                                    }}
                                                </div>
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

                        <!-- Actions -->
                        <div
                            class="mt-8 flex flex-col gap-4 border-t border-gray-200 pt-6 sm:flex-row"
                        >
                            <button
                                @click="requestNewQuote"
                                class="inline-flex flex-1 items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
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
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                    />
                                </svg>
                                Request New Quote
                            </button>

                            <button
                                v-if="quoteData.tracking_url"
                                @click="copyTrackingLink"
                                class="inline-flex flex-1 items-center justify-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
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
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                                    />
                                </svg>
                                Copy Tracking Link
                            </button>

                            <a
                                :href="route('products')"
                                class="inline-flex flex-1 items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
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
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                    />
                                </svg>
                                Browse Products
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Track Another Quote / Not Found -->
                <div v-else class="mx-auto max-w-2xl">
                    <div class="overflow-hidden rounded-xl bg-white shadow-lg">
                        <!-- Header -->
                        <div
                            class="border-b border-gray-200 bg-gray-50 px-6 py-4"
                        >
                            <h2 class="text-xl font-bold text-gray-900">
                                Track Your Quote
                            </h2>
                            <p class="mt-1 text-gray-600">
                                {{
                                    hasError
                                        ? 'Quote not found. Please check your tracking code.'
                                        : 'Enter your tracking code to view your quote status and details'
                                }}
                            </p>
                        </div>

                        <div class="p-6">
                            <!-- Error State -->
                            <div
                                v-if="hasError"
                                class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4"
                            >
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg
                                            class="h-5 w-5 text-red-400"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3
                                            class="text-sm font-medium text-red-800"
                                        >
                                            Quote Not Found
                                        </h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <p>
                                                The tracking code you entered
                                                doesn't match any existing
                                                quote. Please check the code and
                                                try again.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tracking Form -->
                            <form @submit.prevent="submitTrackingCode">
                                <div class="mb-6">
                                    <label
                                        for="tracking-code"
                                        class="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Tracking Code
                                    </label>
                                    <div class="flex gap-4">
                                        <div class="flex-1">
                                            <input
                                                id="tracking-code"
                                                v-model="trackingForm.code"
                                                type="text"
                                                placeholder="Enter your tracking code (e.g., Q-ABC123)"
                                                class="mt-1 block w-full rounded-md border border-gray-300 px-4 py-3 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-primary-500 sm:text-sm"
                                                :class="{
                                                    'border-red-500':
                                                        trackingForm.errors
                                                            .code || hasError,
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
                                            class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-6 py-3 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50"
                                        >
                                            <span
                                                v-if="trackingForm.processing"
                                                class="mr-2 inline-block h-4 w-4 animate-spin rounded-full border-b-2 border-t-2 border-white"
                                            ></span>
                                            Track Quote
                                        </button>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Your tracking code was provided in your
                                        quote confirmation email.
                                    </p>
                                </div>
                            </form>

                            <!-- Help Information -->
                            <div
                                class="rounded-lg border border-blue-200 bg-blue-50 p-6"
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
                                        <h3
                                            class="text-sm font-medium text-blue-800"
                                        >
                                            Need help finding your tracking
                                            code?
                                        </h3>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <ul
                                                class="list-disc space-y-1 pl-5"
                                            >
                                                <li>
                                                    Check your email for the
                                                    quote confirmation message
                                                </li>
                                                <li>
                                                    Look for a code starting
                                                    with "Q-" followed by
                                                    letters and numbers
                                                </li>
                                                <li>
                                                    If you can't find it,
                                                    contact our support team
                                                </li>
                                                <li>
                                                    You can also
                                                    <a
                                                        :href="route('quote')"
                                                        class="font-medium underline"
                                                        >request a new quote</a
                                                    >
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Alternative Actions -->
                            <div
                                class="mt-8 flex flex-col gap-4 border-t border-gray-200 pt-6 sm:flex-row"
                            >
                                <Link
                                    :href="route('quote')"
                                    class="inline-flex flex-1 items-center justify-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-center text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                >
                                    Request a New Quote
                                </Link>
                                <a
                                    :href="route('products')"
                                    class="inline-flex flex-1 items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                >
                                    Browse Products
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </Container>
        </div>
    </WebLayout>
</template>

<style scoped>
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
