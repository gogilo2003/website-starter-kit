import { iMenuItem } from '@/interfaces';

export const useAdminLinks = () => {
    let items: iMenuItem[] = [
        {
            name: 'dashboard',
            caption: 'Dashboard',
            icon: 'dashboard',
            active: false,
        },
        {
            name: 'dashboard-quotes',
            caption: 'Quotes',
            icon: 'clipboard',
            active: false,
        },
        {
            name: 'dashboard-page-sections',
            caption: 'Page Sections',
            icon: 'sections',
            active: false,
        },
        {
            name: 'dashboard-elements',
            caption: 'Elements',
            icon: 'elements',
            active: false,
        },
        {
            name: 'dashboard-slides',
            caption: 'Slides',
            icon: 'image',
            active: false,
        },
        // {
        //     name: 'dashboard-products-categories',
        //     caption: 'Product Categories',
        //     icon: 'projects',
        //     active: false,
        // },
        {
            name: 'dashboard-brands',
            caption: 'Brands',
            icon: 'rectangle-group',
            active: false,
        },
        {
            name: 'dashboard-products-categories',
            caption: 'Products',
            icon: 'product',
            active: false,
            alt_names: ['dashboard-products'],
        },
        {
            name: 'dashboard-partners',
            caption: 'Partners',
            icon: 'partners',
            active: false,
        },
        // {
        //     name: 'dashboard-news_articles',
        //     caption: 'News Articles',
        //     icon: 'news',
        //     active: false,
        // },
        {
            name: 'dashboard-downloads-categories',
            caption: 'Downloads',
            icon: 'downloads',
            active: false,
        },
        // {
        //     name: 'dashboard-downloads',
        //     caption: 'Downloads',
        //     icon: 'downloads',
        //     active: false,
        // },,
        {
            name: 'dashboard-migrations',
            caption: 'Migrations',
            icon: 'command-line',
            active: false,
        },
    ];

    return items.map((item) => {
        let active = route().current(item.name);

        if (!active && Array.isArray(item.alt_names)) {
            active = item.alt_names.some((name: string) =>
                route().current(name),
            );
        }

        return { ...item, active };
    });
};
