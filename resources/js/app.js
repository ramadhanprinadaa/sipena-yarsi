import './bootstrap';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

document.addEventListener('DOMContentLoaded', () => {
    tippy('[data-tippy-content]', {
        theme: 'light',
        animation: 'scale',
        duration: 200,
    });
});