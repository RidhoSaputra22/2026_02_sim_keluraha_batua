import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Start AlpineJS after all scripts (including Vite defer modules) have loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            window.Alpine.start();
        }, 250); // increased tick to allow heavy vite modules (like Turf.js) to execute
    });
} else {
    setTimeout(() => {
        window.Alpine.start();
    }, 250);
}
