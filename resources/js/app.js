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

document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const menuText = document.querySelectorAll('.menu-text');
    const icons = document.querySelectorAll('.sidebar-icon');

    // Restore sidebar state dari localStorage
    const sidebarState = localStorage.getItem('sidebar') || 'expanded';
    if (sidebar && sidebarState === 'collapsed') {
        sidebar.classList.remove('w-64');
        sidebar.classList.add('w-16');
        menuText.forEach(item => item.classList.add('hidden'));
        icons.forEach(item => item.classList.add('text-center'));
    }

    // Sidebar Toggle
    if (toggleButton && sidebar) {
        toggleButton.addEventListener("click", function (e) {
            e.preventDefault();
            sidebar.classList.toggle("w-64");
            sidebar.classList.toggle("w-16");
            
            menuText.forEach(item => item.classList.toggle('hidden'));
            icons.forEach(item => item.classList.toggle('text-center'));
            
            // Simpan state ke localStorage
            const isCollapsed = sidebar.classList.contains('w-16');
            localStorage.setItem('sidebar', isCollapsed ? 'collapsed' : 'expanded');
        });
    }
});