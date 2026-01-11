import 'vs-vue3-select/dist/vs-vue3-select.css'
import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type {Component, DefineComponent} from 'vue';
import { createApp, h } from 'vue';
import { createPinia } from 'pinia'
import { initializeTheme } from './composables';
import {TokenManager} from "@/lib/TokenManager";
import vSelect, {
    VSelectProps,
    VSelectEvents,
    VSelectSlots } from 'vs-vue3-select';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        const pinia = createPinia();
        app.component('vSelect', vSelect as Component<VSelectProps, VSelectEvents, VSelectSlots>);
        app.use(plugin);
        app.use(pinia);
        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});


// This will set light / dark mode on page load...
initializeTheme();
TokenManager.init();
