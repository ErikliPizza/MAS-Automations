import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, Link, Head } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import MainLayout from "@/Layouts/MainLayout.vue";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import VueDragscroll from "vue-dragscroll";

// Vuetify
import vuetify from "@/Plugins/vuetify";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        return resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'))
            .then((component) => {
                component.default.layout = component.default.layout || MainLayout; // Set the default layout
                return component;
            });
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(vuetify)
            .use(Toast, {
                timeout: 1750,
                pauseOnFocusLoss: false,
                maxToasts: 3,
                // You can set your default options here
            })
            .use(VueDragscroll);

        app.component('Head', Head); // Register the Head component globally
        app.component('Link', Link); // Register the Link component globally

        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
