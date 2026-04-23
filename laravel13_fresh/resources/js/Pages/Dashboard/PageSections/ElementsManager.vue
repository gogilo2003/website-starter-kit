<script setup lang="ts">
import Icon from '@/Components/Icons/Icon.vue';
import Modal from '@/Components/Modal.vue';
import { iElement, iPageSection } from '@/interfaces';
import { router, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, ref, watch } from 'vue';
import SecondaryButton from '../../../Components/SecondaryButton.vue';

const props = defineProps<{
    pageSection: iPageSection | null;
    show: boolean;
}>();

const emit = defineEmits(['close']);

const unselectedElements = ref<iElement[]>([]);
const selectedElements = ref<iElement[]>([]);

const allElements = computed(
    (): iElement[] => usePage().props.elements as iElement[],
);

watch(
    () => props.pageSection,
    () => {
        if (props?.pageSection?.id) {
            selectedElements.value = props?.pageSection?.elements ?? [];
            unselectedElements.value = allElements?.value?.filter(
                (element) =>
                    !selectedElements.value.some(
                        (selected) => selected.id === element.id,
                    ),
            );
        }
    },
);

const addElement = (element: iElement) => {
    selectedElements.value.push(element);
    unselectedElements.value = unselectedElements.value.filter(
        (e) => e.id !== element.id,
    );
};

const removeElement = (element: iElement) => {
    unselectedElements.value.push(element);
    selectedElements.value = selectedElements.value.filter(
        (e) => e.id !== element.id,
    );
};

const addAllElements = () => {
    selectedElements.value = [
        ...selectedElements.value,
        ...unselectedElements.value,
    ];
    unselectedElements.value = [];
};

const removeAllElements = () => {
    unselectedElements.value = [
        ...unselectedElements.value,
        ...selectedElements.value,
    ];
    selectedElements.value = [];
};

const handleSave = () => {
    if (!props.pageSection) return;

    const pageSectionId = props.pageSection.id;
    const elementIds = selectedElements.value.map((element) => element.id);

    try {
        router.patch(
            route('dashboard-page-sections-sync-elements', pageSectionId),
            { page_section: pageSectionId, elements: elementIds },
            {
                preserveScroll: true,
                preserveState: true,
                only: ['notification', 'page_sections'],
                onSuccess: () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        title: 'Success!',
                        text:
                            usePage().props?.notification?.success ??
                            'Elements updated successfully',
                    });
                    handleClose();
                },
                onError: (error) => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        title: 'Error!',
                        text: error.message ?? 'An error occurred.',
                    });
                },
            },
        );
    } catch (error) {
        console.error(error);
    }
};

const handleClose = () => {
    unselectedElements.value = [];
    selectedElements.value = [];
    emit('close');
};
</script>

<template>
    <Modal :show="show" max-width="5xl">
        <div class="mx-4 flex justify-between border-b py-4">
            <div>
                <div class="text-lg font-semibold">Manage Elements</div>
            </div>
            <button @click="handleClose">
                <Icon type="close" class="h-5 w-5" />
            </button>
        </div>
        <div class="p-4">
            <div class="text-sm">
                Manage elements for "<em>{{ pageSection?.title }}</em
                >" page section
            </div>
        </div>
        <div class="p-4">
            <div class="flex justify-between space-x-4">
                <div class="flex-1">
                    <h3 class="mb-2 text-lg font-medium">
                        Unselected Elements
                    </h3>
                    <ul class="h-64 overflow-y-auto rounded-md border p-2">
                        <li
                            v-for="element in unselectedElements"
                            :key="element.id"
                            class="cursor-pointer rounded border-b p-2 even:bg-gray-100/50 hover:bg-stone-800/5"
                            @click="addElement(element)"
                        >
                            {{ element.title }}
                        </li>
                    </ul>
                </div>
                <div
                    class="flex flex-none flex-col items-center justify-center space-y-4"
                >
                    <SecondaryButton
                        @click="addElement(unselectedElements[0])"
                        :disabled="unselectedElements?.length === 0"
                    >
                        <Icon type="curved-arrow-right" class="h-4 w-4" />
                    </SecondaryButton>
                    <SecondaryButton
                        @click="removeElement(selectedElements[0])"
                        :disabled="selectedElements?.length === 0"
                    >
                        <Icon type="curved-arrow-left" class="h-4 w-4" />
                    </SecondaryButton>
                    <SecondaryButton
                        @click="addAllElements"
                        :disabled="unselectedElements?.length === 0"
                    >
                        <Icon
                            type="curved-arrow-right-double"
                            class="h-4 w-4"
                        />
                    </SecondaryButton>
                    <SecondaryButton
                        @click="removeAllElements"
                        :disabled="selectedElements?.length === 0"
                    >
                        <Icon type="curved-arrow-left-double" class="h-4 w-4" />
                    </SecondaryButton>
                </div>
                <div class="flex-1">
                    <h3 class="mb-2 text-lg font-medium">Selected Elements</h3>
                    <ul class="h-64 overflow-y-auto rounded-md border p-2">
                        <li
                            v-for="element in selectedElements"
                            :key="element.id"
                            class="cursor-pointer rounded border-b p-2 even:bg-gray-100/50 hover:bg-stone-800/5"
                            @click="removeElement(element)"
                        >
                            {{ element.title }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mx-4 flex justify-end space-x-2 border-t py-4">
            <SecondaryButton variant="outline" @click="handleClose"
                >Cancel</SecondaryButton
            >
            <SecondaryButton @click="handleSave">Save</SecondaryButton>
        </div>
    </Modal>
</template>
