import { createInertiaApp } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import '../css/app.css';
import { toast } from 'vue-sonner';
import { initializeTheme } from './composables/useAppearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';


createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

router.on('invalid', (event) => {
    event.preventDefault();
    toast.error('Beklenmeyen bir sunucu hatası oluştu. Lütfen daha sonra tekrar deneyin.');
});

router.on('exception', (event) => {
    event.preventDefault();
    toast.error('Sunucu ile iletişim kurulamadı veya bir hata oluştu.');
});

// This will set light / dark mode on page load...
initializeTheme();
