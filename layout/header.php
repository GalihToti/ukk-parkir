<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama_lengkap'];

// Mengecek halaman aktif untuk memberi warna nyala pada menu
$current_page = basename($_SERVER['PHP_SELF']);
$menu_aktif = "bg-green-600 text-white shadow-md";
$menu_nonaktif = "text-gray-600 hover:bg-gray-100 hover:text-gray-900";
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Parkir App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        #sidebar {
            transition: width 0.3s ease, transform 0.3s ease;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden">

    <div class="flex h-screen bg-gray-50">

        <div id="sidebarOverlay" onclick="closeSidebarOnMobile()" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden transition-opacity"></div>

        <aside id="sidebar" class="bg-white border-r border-gray-200 transition-all duration-300 flex flex-col fixed lg:relative h-full z-50 overflow-hidden w-64 -translate-x-full lg:translate-x-0">

            <div class="p-4 border-b border-gray-100 flex items-center justify-between h-[65px]">
                <h3 id="logoText" class="text-sm font-semibold flex items-center text-gray-800 whitespace-nowrap transition-opacity">
                    <i class="fas fa-parking text-green-600 mr-2 text-lg"></i> E-Parkir
                </h3>
                <button onclick="toggleSidebar()" id="toggleBtn" class="p-2 hover:bg-gray-100 rounded-md hidden lg:block text-gray-500 transition-colors">
                    <i id="toggleIcon" class="fas fa-chevron-left"></i>
                </button>
            </div>

            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <a href="../<?= $role ?>/index.php" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm font-medium <?= ($current_page == 'index.php') ? $menu_aktif : $menu_nonaktif; ?>">
                    <i class="fas fa-border-all w-5 text-center text-lg"></i>
                    <span class="sidebar-text whitespace-nowrap">Dashboard</span>
                </a>

                <?php if ($role == 'admin'): ?>
                    <a href="../admin/kelola_user.php" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm font-medium <?= ($current_page == 'kelola_user.php') ? $menu_aktif : $menu_nonaktif; ?>">
                        <i class="fas fa-users w-5 text-center text-lg"></i>
                        <span class="sidebar-text whitespace-nowrap">Kelola User</span>
                    </a>
                    <a href="../admin/kelola_kendaraan.php" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm font-medium <?= ($current_page == 'kelola_kendaraan.php') ? $menu_aktif : $menu_nonaktif; ?>">
                        <i class="fas fa-car w-5 text-center text-lg"></i>
                        <span class="sidebar-text whitespace-nowrap font-semibold">Data Kendaraan</span>
                    </a>
                    <a href="../admin/kelola_area.php" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm font-medium <?= ($current_page == 'kelola_area.php') ? $menu_aktif : $menu_nonaktif; ?>">
                        <i class="fas fa-building w-5 text-center text-lg"></i>
                        <span class="sidebar-text whitespace-nowrap">Area Parkir</span>
                    </a>
                    <a href="../admin/kelola_tarif.php" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm font-medium <?= ($current_page == 'kelola_tarif.php') ? $menu_aktif : $menu_nonaktif; ?>">
                        <i class="fas fa-tags w-5 text-center text-lg"></i>
                        <span class="sidebar-text whitespace-nowrap">Tarif Parkir</span>
                    </a>
                    <a href="../admin/log_aktivitas.php" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm font-medium <?= ($current_page == 'log_aktivitas.php') ? $menu_aktif : $menu_nonaktif; ?>">
                        <i class="fas fa-history w-5 text-center text-lg"></i>
                        <span class="sidebar-text whitespace-nowrap">Log Aktivitas</span>
                    </a>
                <?php endif; ?>

                <?php if ($role == 'petugas'): ?>
                    <a href="../petugas/kendaraan_masuk.php" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm font-medium <?= ($current_page == 'kendaraan_masuk.php') ? $menu_aktif : $menu_nonaktif; ?>">
                        <i class="fas fa-arrow-right-to-bracket w-5 text-center text-lg"></i>
                        <span class="sidebar-text whitespace-nowrap">Kendaraan Masuk</span>
                    </a>
                    <a href="../petugas/kendaraan_keluar.php" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm font-medium <?= ($current_page == 'kendaraan_keluar.php') ? $menu_aktif : $menu_nonaktif; ?>">
                        <i class="fas fa-arrow-right-from-bracket w-5 text-center text-lg"></i>
                        <span class="sidebar-text whitespace-nowrap">Kendaraan Keluar</span>
                    </a>
                <?php endif; ?>
            </nav>

            <div class="p-4 border-t border-gray-100 flex items-center gap-3 mt-auto">
                <div class="w-9 h-9 rounded-full bg-green-100 text-green-700 border border-green-100 flex items-center justify-center font-bold text-sm uppercase shrink-0">
                    <?= substr($nama, 0, 2); ?>
                </div>
                <div class="flex-1 min-w-0 sidebar-text transition-opacity">
                    <p class="text-sm font-bold text-gray-800 truncate"><?= $nama; ?></p>
                    <p class="text-xs text-gray-500 capitalize"><?= $role; ?></p>
                </div>
            </div>

        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">

            <header class="h-[65px] bg-white border-b border-gray-200 flex items-center justify-between px-4 z-10">
                <div class="flex items-center">
                    <button onclick="toggleMobileSidebar()" class="lg:hidden p-2 hover:bg-gray-100 rounded-md transition-colors mr-2 text-gray-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>

                <a href="../auth/logout.php" class="text-sm text-gray-600 hover:text-red-600 hover:bg-red-50 font-medium px-4 py-2 rounded-md transition-colors flex items-center gap-2">
                    <i class="fas fa-power-off"></i> <span class="hidden sm:inline">Logout</span>
                </a>
            </header>

            <main class="flex-1 overflow-auto bg-gray-50/50">
                <div class="p-4 md:p-8 max-w-7xl mx-auto">