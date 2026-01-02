// composables/usePhoneNumbers.ts
import { parsePhoneNumbers } from '@/utils/phoneFormatter';
import { computed } from 'vue';

export function usePhoneNumbers(phoneString: string) {
    const phones = computed(() => parsePhoneNumbers(phoneString));
    const primaryPhone = computed(() =>
        phones.value.length > 0 ? phones.value[0] : null,
    );

    return {
        phones,
        primaryPhone,
        hasPhones: computed(() => phones.value.length > 0),
        phoneCount: computed(() => phones.value.length),
    };
}
