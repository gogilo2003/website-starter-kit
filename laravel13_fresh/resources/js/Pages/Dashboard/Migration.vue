<script setup lang="ts">
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { iNotification } from '@/interfaces';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    output?: string | null;
    notification?: iNotification;
}>();

const step = ref<number | null>(null);
const fresh = ref(false);
const seed = ref(false);

watch(fresh, (value) => {
    if (value) {
        step.value = null; // step makes no sense with fresh
    }
});

const computedOutput = computed(() => props.output ?? '');

const runAction = async (action: 'migrate' | 'rollback' | 'seed') => {
    let warningText = `Are you sure you want to ${action}?`;

    if (action === 'migrate' && fresh.value) {
        warningText =
            'This will DROP ALL TABLES and re-run all migrations. This action is irreversible!';
    }

    const confirm = await Swal.fire({
        icon: 'warning',
        title: 'Confirm Action',
        text: warningText,
        showCancelButton: true,
        confirmButtonColor: fresh.value ? '#d33' : undefined,
        confirmButtonText: 'Yes, continue',
    });

    if (!confirm.isConfirmed) return;

    router.post(
        route('dashboard-migrations-execute'),
        {
            action,
            step: action !== 'seed' ? step.value : null,
            fresh: action === 'migrate' ? fresh.value : false,
            seed: seed.value,
        },
        {
            preserveScroll: true,
            preserveState: true,
            only: ['output', 'notification'],
            onSuccess: () => {
                if (props.notification?.success) {
                    Swal.fire({
                        title: 'Success',
                        text: props.notification.success,
                        icon: 'success',
                        toast: true,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        timer: 400,
                    });
                }
                if (props.notification?.error) {
                    Swal.fire({
                        title: 'Error',
                        text: props.notification.error,
                        icon: 'error',
                        toast: true,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        timer: 400,
                    });
                }
            },
        },
    );
};
</script>

<template>
    <AuthenticatedLayout title="Migrations">
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">
                Migration & Seeding Manager
            </h2>
        </template>

        <div class="mx-4 flex h-full flex-col space-y-6 py-4">
            <div
                class="flex flex-none flex-col justify-between rounded-xl bg-gray-50 p-5 shadow-sm md:flex-row"
            >
                <div class="flex flex-wrap items-center gap-4">
                    <input
                        v-model.number="step"
                        type="number"
                        min="1"
                        :disabled="fresh"
                        placeholder="Step (optional)"
                        class="w-40 rounded-md border-gray-300 disabled:opacity-50"
                    />

                    <label class="flex items-center gap-2">
                        <input type="checkbox" v-model="fresh" />
                        <span class="text-sm text-red-600"
                            >Fresh migration</span
                        >
                    </label>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" v-model="seed" />
                        <span class="text-sm">Run seeders</span>
                    </label>
                </div>

                <div class="flex flex-wrap gap-3">
                    <SecondaryButton @click="runAction('migrate')">
                        Migrate
                    </SecondaryButton>

                    <SecondaryButton @click="runAction('rollback')">
                        Rollback
                    </SecondaryButton>

                    <SecondaryButton @click="runAction('seed')">
                        Seed Only
                    </SecondaryButton>
                </div>
            </div>

            <div
                class="min-h-56 flex-1 rounded-xl bg-gray-800 p-4 text-sm text-lime-600"
            >
                <pre v-if="computedOutput.length > 0" class="text-lime-600">
                    {{ computedOutput }}
                </pre>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
