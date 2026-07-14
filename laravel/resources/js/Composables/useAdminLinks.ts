import { iMenuItem } from '@/interfaces';
import { usePage } from '@inertiajs/vue3';

export const useAdminLinks = (): iMenuItem[] => {
    const menu = (usePage().props.menu?.admin ?? []) as iMenuItem[];

    return menu.map((item) => {
        let active = route().current(item.name);

        if (!active && Array.isArray(item.alt_names)) {
            active = item.alt_names.some((name: string) =>
                route().current(name),
            );
        }

        return { ...item, active: Boolean(active) };
    });
};
