<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { ref, watch } from 'vue';
import { iQuote, iQuoteItem } from '../../interfaces';
import Icon from '../Icons/Icon.vue';
import Modal from '../Modal.vue';
import SecondaryButton from '../SecondaryButton.vue';
import TextInput from '../TextInput.vue';

const props = defineProps<{
    show: boolean;
    quote: iQuote | null;
    formatDate: (date: string | Date | undefined | null) => string;
    formatDateTime: (date: string | Date | undefined | null) => string;
    formatCurrency: (amount: number | undefined) => string;
    getStatusBadgeClass: (status: string) => string;
    getStatusLabel: (status: string) => string;
}>();

const emit = defineEmits<{
    close: [];
    copyTrackingUrl: [url: string];
}>();

const selectedItem = ref<number | null>();
const editItem = (item: number) => {
    selectedItem.value = item;
};

const items = ref<iQuoteItem[]>(props.quote?.items ?? []);

watch(
    () => props.quote?.items,
    (value) => {
        if (value) {
            items.value = value as iQuoteItem[];
        }
    },
);

const submit = (index: number) => {
    let item = items.value[index];
    router.patch(
        route('dashboard-quotes-items-update', { item: item.id }),
        item,
        {
            only: ['quotes', 'notification'],
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                Swal.fire({
                    text:
                        usePage().props?.notification.success ??
                        'Quote Item updated successfully',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: 'success',
                });
                selectedItem.value = null;
            },
            onError: () => {
                Swal.fire({
                    text:
                        usePage().props?.notification.error ??
                        'An error occurred. Please try again!',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: 'error',
                });
            },
        },
    );
};
</script>

<template>
    <Modal :show="show" max-width="4xl" @close="emit('close')">
        <template #title>
            <div class="flex items-center justify-between bg-gray-100 p-4">
                <h3 class="text-lg font-semibold">Quote Details</h3>
                <Icon
                    type="close"
                    @click="emit('close')"
                    class="h-6 w-6 cursor-pointer rounded-full border border-gray-300 text-gray-600 transition-colors duration-300 hover:border-gray-500 hover:text-gray-800"
                />
            </div>
        </template>
        <div v-if="quote" class="p-6">
            <!-- Quote Header -->
            <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xl font-bold text-gray-900">
                            {{ quote.code }}
                        </h4>
                        <p class="text-sm text-gray-600">
                            Created {{ formatDate(quote.created_at) }}
                        </p>
                    </div>
                    <span
                        :class="[
                            'rounded-full border px-4 py-2 text-sm font-semibold',
                            getStatusBadgeClass(quote.status),
                        ]"
                    >
                        {{ getStatusLabel(quote.status) }}
                    </span>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="mb-6">
                <h5 class="mb-3 text-lg font-semibold text-gray-900">
                    Customer Information
                </h5>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Name</p>
                        <p class="text-base text-gray-900">{{ quote.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Email</p>
                        <p class="text-base text-gray-900">{{ quote.email }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Phone</p>
                        <p class="text-base text-gray-900">{{ quote.phone }}</p>
                    </div>
                    <div v-if="quote.company">
                        <p class="text-sm font-medium text-gray-600">Company</p>
                        <p class="text-base text-gray-900">
                            {{ quote.company }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Message -->
            <div class="mb-6">
                <h5 class="mb-3 text-lg font-semibold text-gray-900">
                    Message
                </h5>
                <p class="rounded-lg bg-gray-50 p-4 text-gray-700">
                    {{ quote.message }}
                </p>
            </div>

            <!-- Products -->
            <div v-if="quote.items && quote.items.length > 0" class="mb-6">
                <h5 class="mb-3 text-lg font-semibold text-gray-900">
                    Products
                </h5>
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Product
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Quantity
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Price
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Subtotal
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="(item, index) in items" :key="item.id">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ item.product?.title || 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <span v-if="!(selectedItem == item.id)">{{
                                        item.quantity
                                    }}</span>
                                    <div v-else>
                                        <TextInput
                                            v-model="items[index].quantity"
                                        />
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <span v-if="!(selectedItem == item.id)">{{
                                        formatCurrency(item.price)
                                    }}</span>
                                    <div v-else>
                                        <TextInput
                                            v-model="items[index].price"
                                        />
                                    </div>
                                </td>
                                <td
                                    class="px-4 py-3 text-sm font-semibold text-gray-900"
                                >
                                    {{
                                        formatCurrency(
                                            item.quantity * item.price,
                                        )
                                    }}
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold">
                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="editItem(item.id)"
                                            class="text-blue-600"
                                        >
                                            <Icon
                                                type="pencil-square"
                                                class="h-5 w-5"
                                            />
                                        </button>
                                        <button
                                            v-if="selectedItem == item.id"
                                            @click="submit(index)"
                                        >
                                            <Icon
                                                type="check"
                                                class="h-5 w-5"
                                            />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td
                                    colspan="3"
                                    class="px-4 py-3 text-right text-sm font-semibold text-gray-900"
                                >
                                    Total Amount:
                                </td>
                                <td
                                    class="px-4 py-3 text-sm font-bold text-primary-600"
                                >
                                    {{ formatCurrency(quote.total_amount) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Tracking Information -->
            <div class="mb-6">
                <h5 class="mb-3 text-lg font-semibold text-gray-900">
                    Tracking Information
                </h5>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-sm font-medium text-gray-600">
                            View Count
                        </p>
                        <p class="text-base text-gray-900">
                            {{ quote.view_count || 0 }} views
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">
                            Last Viewed
                        </p>
                        <p class="text-base text-gray-900">
                            {{ formatDateTime(quote.last_viewed_at) }}
                        </p>
                    </div>
                </div>
                <div v-if="quote.tracking_url" class="mt-4">
                    <p class="text-sm font-medium text-gray-600">
                        Tracking URL
                    </p>
                    <div class="mt-1 flex items-center gap-2">
                        <input
                            type="text"
                            :value="quote.tracking_url"
                            readonly
                            class="flex-1 rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700"
                        />
                        <SecondaryButton
                            @click="
                                emit('copyTrackingUrl', quote.tracking_url!)
                            "
                        >
                            Copy
                        </SecondaryButton>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 border-t border-gray-200 p-4">
            <SecondaryButton @click="emit('close')">Close</SecondaryButton>
        </div>
    </Modal>
</template>
