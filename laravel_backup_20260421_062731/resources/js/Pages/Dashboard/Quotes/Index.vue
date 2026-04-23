<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import QuoteDetailsModal from '../../../Components/Dashboard/QuoteDetailsModal.vue';
import QuoteFilters from '../../../Components/Dashboard/QuoteFilters.vue';
import QuotesTable from '../../../Components/Dashboard/QuotesTable.vue';
import QuoteStatistics from '../../../Components/Dashboard/QuoteStatistics.vue';
import QuoteStatusModal from '../../../Components/Dashboard/QuoteStatusModal.vue';
import { useQuoteManagement } from '../../../Composables/useQuoteManagement';
import { iNotification, iQuote, iQuotes } from '../../../interfaces';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout.vue';

interface QuoteStatistics {
    total: number;
    pending: number;
    sent: number;
    viewed: number;
    completed: number;
    rejected: number;
    conversion_rate: number;
}

const props = defineProps<{
    quotes: iQuotes;
    filters: {
        status?: string;
        search?: string;
        date_from?: string;
        date_to?: string;
    };
    statistics: QuoteStatistics;
    notification?: iNotification;
}>();

// Use composable for shared logic
const {
    confirmAction,
    toast,
    formatDate,
    formatDateTime,
    formatCurrency,
    statusOptions,
    getStatusBadgeClass,
    getStatusLabel,
    copyTrackingUrl,
} = useQuoteManagement();

// Filter form
const filterForm = useForm({
    status: props.filters.status || '',
    search: props.filters.search || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
    per_page: props.quotes.per_page || 15,
});

// Status update form
const statusForm = useForm<{
    quote_id: number | null;
    status: string;
}>({
    quote_id: null,
    status: '',
});

// Modals state
const showDetailsModal = ref(false);
const showStatusModal = ref(false);
const selectedQuote = ref<iQuote | null>(null);

// Computed
const hasActiveFilters = computed(() => {
    return !!(
        filterForm.status ||
        filterForm.search ||
        filterForm.date_from ||
        filterForm.date_to
    );
});

// Filter functions
const applyFilters = () => {
    filterForm.get(route('dashboard-quotes'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    filterForm.status = '';
    filterForm.search = '';
    filterForm.date_from = '';
    filterForm.date_to = '';
    applyFilters();
};

const changePerPage = () => {
    applyFilters();
};

// Quote actions
const viewQuoteDetails = (quote: iQuote) => {
    selectedQuote.value = quote;
    showDetailsModal.value = true;
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
    selectedQuote.value = null;
};

const openStatusModal = (quote: iQuote) => {
    selectedQuote.value = quote;
    statusForm.quote_id = quote.id;
    statusForm.status = quote.status;
    showStatusModal.value = true;
};

const closeStatusModal = () => {
    showStatusModal.value = false;
    selectedQuote.value = null;
    statusForm.reset();
};

const updateStatus = () => {
    if (!statusForm.quote_id) return;

    statusForm.patch(route('dashboard-quotes-status', statusForm.quote_id), {
        preserveScroll: true,
        onSuccess: () => {
            toast('success', 'Quote status updated successfully');
            closeStatusModal();
        },
        onError: () => {
            toast('error', 'Failed to update quote status');
        },
    });
};

const deleteQuote = async (quote: iQuote) => {
    const confirmed = await confirmAction({
        title: 'Delete Quote',
        text: `Are you sure you want to delete quote ${quote.code}? This action cannot be undone.`,
        confirmButtonText: 'Yes, delete',
    });

    if (!confirmed) return;

    router.delete(route('dashboard-quotes-destroy', quote.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast('success', 'Quote deleted successfully');
        },
        onError: () => {
            toast('error', 'Failed to delete quote');
        },
    });
};
</script>

<template>
    <!-- View Details Modal -->
    <QuoteDetailsModal
        :show="showDetailsModal"
        :quote="selectedQuote"
        :format-date="formatDate"
        :format-date-time="formatDateTime"
        :format-currency="formatCurrency"
        :get-status-badge-class="getStatusBadgeClass"
        :get-status-label="getStatusLabel"
        @close="closeDetailsModal"
        @copy-tracking-url="copyTrackingUrl"
    />

    <!-- Update Status Modal -->
    <QuoteStatusModal
        :show="showStatusModal"
        :quote="selectedQuote"
        :status-form="statusForm"
        :status-options="statusOptions"
        @close="closeStatusModal"
        @submit="updateStatus"
    />

    <AuthenticatedLayout title="Quote Requests">
        <!-- Statistics Dashboard -->
        <div class="py-6">
            <div class="sm:px-6 lg:px-8">
                <QuoteStatistics :statistics="statistics" />
            </div>
        </div>

        <!-- Filters Section -->
        <div class="py-6">
            <div class="sm:px-6 lg:px-8">
                <QuoteFilters
                    :filter-form="filterForm"
                    :status-options="statusOptions"
                    @apply-filters="applyFilters"
                    @clear-filters="clearFilters"
                    @change-per-page="changePerPage"
                />
            </div>
        </div>

        <!-- Quotes List -->
        <div class="py-6">
            <div class="sm:px-6 lg:px-8">
                <QuotesTable
                    :quotes="quotes"
                    :has-active-filters="hasActiveFilters"
                    :format-date="formatDate"
                    :format-currency="formatCurrency"
                    :get-status-badge-class="getStatusBadgeClass"
                    :get-status-label="getStatusLabel"
                    @view-details="viewQuoteDetails"
                    @update-status="openStatusModal"
                    @delete="deleteQuote"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
