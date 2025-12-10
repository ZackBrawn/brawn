import '../css/app.css';
import './bootstrap';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';


const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        console.log('Resolving component:', name);
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        
        // Log all available paths for debugging
        console.log('Available paths:', Object.keys(pages));
        
        // Handle User pages
        if (name.startsWith('User/')) {
            // Remove 'User/' prefix and handle the rest
            const userPageName = name.substring(5); // Remove 'User/'
            const userPath = `./Pages/User/${userPageName}.vue`;
            console.log('Trying user path:', userPath);
            if (userPath in pages) {
                console.log('Found user component at:', userPath);
                return pages[userPath];
            }
        }
        
        // Handle Dashboard and Profile routes
        if (name === 'Dashboard' || name === 'Profile') {
            const userPath = `./Pages/User/${name}/Index.vue`;
            console.log('Trying path:', userPath);
            if (userPath in pages) {
                console.log('Found component at:', userPath);
                return pages[userPath];
            }
            
            // Also try the direct path for backward compatibility
            const directPath = `./Pages/${name}/Index.vue`;
            console.log('Trying direct path:', directPath);
            if (directPath in pages) {
                console.log('Found component at:', directPath);
                return pages[directPath];
            }
        }
        
        // Try the direct path first
        let path = `./Pages/${name}.vue`;
        if (path in pages) {
            console.log('Found component at direct path:', path);
            return pages[path];
        }
        
        // Try with Index.vue
        path = `./Pages/${name}/Index.vue`;
        if (path in pages) {
            console.log('Found component at index path:', path);
            return pages[path];
        }
        
        // Try with User/ prefix for other potential user routes
        if (!name.startsWith('User/')) {
            const userPath = `./Pages/User/${name}.vue`;
            console.log('Trying user path:', userPath);
            if (userPath in pages) {
                console.log('Found component at user path:', userPath);
                return pages[userPath];
            }
            
            const userIndexPath = `./Pages/User/${name}/Index.vue`;
            console.log('Trying user index path:', userIndexPath);
            if (userIndexPath in pages) {
                console.log('Found component at user index path:', userIndexPath);
                return pages[userIndexPath];
            }
        }
        
        // Final fallback to the original location
        const fallbackPath = `./Pages/${name}.vue`;
        console.log('Trying fallback path:', fallbackPath);
        if (fallbackPath in pages) {
            console.log('Found component at fallback path:', fallbackPath);
            return pages[fallbackPath];
        }

        console.error('Component not found for:', name);
        throw new Error(`Page not found: ${name}`);
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        
        // Configure toast options
        const toastOptions = {
            position: 'top-right',
            timeout: 3000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: true,
            draggable: true,
            draggablePercent: 0.6,
            showCloseButtonOnHover: false,
            hideProgressBar: false,
            closeButton: 'button',
            icon: true,
            rtl: false
        };
        
        app.use(plugin)
           .use(ZiggyVue, Ziggy)
           .use(Toast, toastOptions)
           .mount(el);
    },
    progress: {
        color: '#66a6ff',
    },
});
