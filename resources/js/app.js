import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Start AlpineJS after all scripts (including Vite defer modules) have loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            window.Alpine.start();
        }, 10); // small tick to allow vite module execution
    });
} else {
    setTimeout(() => {
        window.Alpine.start();
    }, 10);
}
