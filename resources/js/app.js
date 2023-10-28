import './bootstrap';
import '../css/app.css';

// Importez les dépendances nécessaires
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faPlus } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// Ajoutez l'icône FontAwesome à la bibliothèque
library.add(faPlus);

// Initialisez l'application Inertia
createInertiaApp({
    title: (title) => `${title} - ${import.meta.env.VITE_APP_NAME || 'Laravel'}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        // Créez l'application Vue
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component('font-awesome-icon', FontAwesomeIcon) // Ajoutez le composant FontAwesome à l'application
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
