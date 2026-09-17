document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('sidebar');
    const mobileMenu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('sidebarOverlay');

    if (mobileMenu && sidebar && overlay) {
        mobileMenu.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', function () {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 800) {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            }
        });
    }

    // Sidebar Dropdown Accordion
    const dropdownToggles = document.querySelectorAll('.nav-dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            const parent = this.closest('.nav-dropdown');
            if (parent) {
                parent.classList.toggle('open');
            }
        });
    });

});