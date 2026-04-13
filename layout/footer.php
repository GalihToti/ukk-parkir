</div>
</main>
</div>
</div>
<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const logoText = document.getElementById('logoText');
    const toggleBtn = document.getElementById('toggleBtn');
    const toggleIcon = document.getElementById('toggleIcon');
    const texts = document.querySelectorAll('.sidebar-text');

    let sidebarOpen = true;

    // Toggle Sidebar Desktop
    function toggleSidebar() {
        sidebarOpen = !sidebarOpen;

        if (!sidebarOpen) {
            // Saat ditutup -> Lebar w-16 (64px)
            sidebar.classList.remove('w-64');
            sidebar.classList.add('w-16');

            logoText.classList.add('hidden');
            texts.forEach(el => el.classList.add('hidden'));

            toggleBtn.classList.add('mx-auto');
            toggleIcon.classList.replace('fa-chevron-left', 'fa-chevron-right');
        } else {
            // Saat dibuka -> Lebar w-64
            sidebar.classList.remove('w-16');
            sidebar.classList.add('w-64');

            toggleBtn.classList.remove('mx-auto');
            toggleIcon.classList.replace('fa-chevron-right', 'fa-chevron-left');

            setTimeout(() => {
                logoText.classList.remove('hidden');
                texts.forEach(el => el.classList.remove('hidden'));
            }, 150);
        }
    }

    // Buka Sidebar Mobile
    function toggleMobileSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }

    // Tutup Sidebar Mobile
    function closeSidebarOnMobile() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    // Responsif Otomatis Berdasarkan Ukuran Layar
    window.addEventListener('resize', () => {
        let isMobile = window.innerWidth < 1024;

        if (isMobile) {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            if (!sidebarOpen) toggleSidebar();
        } else {
            sidebar.classList.remove('-translate-x-full');
        }
    });
</script>
</body>

</html>