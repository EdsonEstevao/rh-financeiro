import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


// resources/js/app.js - adicione no final
if (import.meta.env.DEV) {
    // Ignora warnings de performance em desenvolvimento
    console.log('🚧 Ambiente de desenvolvimento');
}
