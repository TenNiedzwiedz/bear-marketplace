import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import Toaster from './components/Toaster.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Bear Marketplace';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        const pages = import.meta.glob('./pages/**/*.vue', { eager: true });
        return pages[`./pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => [h(App, props), h(Toaster)] })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#4f46e5',
    },
});
