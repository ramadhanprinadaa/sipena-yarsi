import './bootstrap';

// Sidebar Toggle
document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.getElementById("sidebarToggle")
    const sidebar = document.getElementById("sidebar")
    const menuText = document.querySelectorAll('.menu-text');

    if (toggleButton && sidebar) {
        toggleButton.addEventListener("click", function (e) {
            e.preventDefault()
            sidebar.classList.toggle("w-64")
            sidebar.classList.toggle("w-16")
            menuText.forEach(function (item) {
                item.classList.toggle('hidden');
            });
        })
    }
})




