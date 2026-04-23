import { usePage } from '@inertiajs/vue3';

export function useLinks() {
    return [
        {
            name: 'home',
            caption: 'Home',
            items: [],
        },
        {
            name: 'about',
            caption: 'About Us',
            items: [],
        },
        // {
        //     name: 'projects',
        //     caption: 'Projects',
        //     items: usePage()?.props?.menu?.projects
        // },
        {
            name: 'products',
            caption: 'Products',
            items: usePage()?.props?.menu?.products ?? [],
        },
        // {
        //     name: 'news',
        //     caption: 'News',
        //     items: []
        // },
        {
            name: 'downloads',
            caption: 'Downloads',
            items: usePage()?.props?.menu?.resources ?? [],
        },
        {
            name: 'contact',
            caption: 'Contact Us',
            items: [],
        },
    ];
}
