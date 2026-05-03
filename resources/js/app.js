import './bootstrap';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

document.addEventListener('DOMContentLoaded', () => {
    tippy('[data-tippy-content]', {
        theme: 'light',
        animation: 'scale',
        duration: 200,
    });

    flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "Y-m-d"
    });

});