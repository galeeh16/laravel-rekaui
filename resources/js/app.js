import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js';
import AppLayout from '@/layouts/AppLayout.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),

    // Every page uses the dashboard layout, except the guest pages under `pages/auth`.
    layout: (name) => (name.startsWith('auth/') ? null : AppLayout),

    // Makes `route()` available in every template (routes come from `@routes` in app.blade.php).
    withApp(app) {
        app.use(ZiggyVue);
    },

    progress: {
        color: '#17795a',
        showSpinner: false,
    },
});
