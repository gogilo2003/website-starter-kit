import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { createPinia } from 'pinia';
import { createApp, DefineComponent, h } from 'vue';
import VueClickAway from 'vue3-click-away';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia();

const pages = import.meta.glob<DefineComponent>([
    './Pages/**/*.vue',
    '../../packages/meacms/*/resources/js/Pages/**/*.vue',
]);

const resolvePage = (name: string) => {
    const hostKey = `./Pages/${name}.vue`;
    if (pages[hostKey]) {
        return typeof pages[hostKey] === 'function' ? (pages[hostKey] as () => Promise<any>)() : pages[hostKey];
    }

    for (const key in pages) {
        if (key.endsWith(`/resources/js/Pages/${name}.vue`)) {
            return typeof pages[key] === 'function' ? (pages[key] as () => Promise<any>)() : pages[key];
        }
    }

    throw new Error(`Page not found: ${name}`);
};

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: resolvePage,
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia)
            .use(VueClickAway)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
