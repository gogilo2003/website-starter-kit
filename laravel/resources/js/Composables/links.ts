import { usePage } from '@inertiajs/vue3';

interface iPublicLink {
    name: string;
    caption: string;
    items: any[];
}

export function useLinks(): iPublicLink[] {
    const menu = (usePage().props.menu?.public ?? []) as iPublicLink[];

    return menu.map((item) => ({
        name: item.name,
        caption: item.caption,
        items: item.items ?? [],
    }));
}
