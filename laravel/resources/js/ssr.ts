import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { renderToString } from '@vue/server-renderer';
import { createPinia } from 'pinia';
import { createSSRApp, DefineComponent, h } from 'vue';
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

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => `${title} - ${appName}`,
        resolve: resolvePage,
        setup({ App, props, plugin }) {
            return createSSRApp({ render: () => h(App, props) })
                .use(plugin)
                .use(pinia)
                .use(ZiggyVue, {
                    ...page.props.ziggy,
                    location: new URL(page.props.ziggy.location),
                })
                .use(VueClickAway);
        },
    }),
);
