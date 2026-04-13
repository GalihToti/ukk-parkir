<?php
require_once '../layout/header.php';
require_once '../config/koneksi.php';

// Pastikan hanya petugas yang bisa akses
if ($_SESSION['role'] !== 'petugas') {
    echo "<script>alert('Anda tidak memiliki akses!'); window.history.back();</script>";
    exit;
}

// Mengambil Data Statistik Hari Ini
$tanggal_hari_ini = date('Y-m-d');

// 1. Kendaran Masuk Hari Ini
$q_masuk = mysqli_query($conn, "SELECT COUNT(id_parkir) as total FROM tb_ukk_galih_transaksi WHERE DATE(waktu_masuk) = '$tanggal_hari_ini'");
$masuk_hari_ini = mysqli_fetch_assoc($q_masuk)['total'];

// 2. Kendaraan Keluar Hari Ini
$q_keluar = mysqli_query($conn, "SELECT COUNT(id_parkir) as total FROM tb_ukk_galih_transaksi WHERE DATE(waktu_keluar) = '$tanggal_hari_ini' AND status = 'keluar'");
$keluar_hari_ini = mysqli_fetch_assoc($q_keluar)['total'];

// 3. Kendaraan Sedang Parkir (Status = Masuk, tidak peduli hari apa masuknya)
$q_parkir = mysqli_query($conn, "SELECT COUNT(id_parkir) as total FROM tb_ukk_galih_transaksi WHERE status = 'masuk'");
$sedang_parkir = mysqli_fetch_assoc($q_parkir)['total'];
?>

<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 no-print animate-in fade-in duration-500">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Dashboard Petugas</h2>
            <p class="text-[11px] text-gray-400">Panel Petugas | E-Parkir</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 animate-in slide-in-from-top-4 duration-500">

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col justify-between hover:border-green-300 transition-all">
            <div class="flex items-start justify-between mb-2">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Masuk Hari Ini</p>
                <div class="w-9 h-9 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                    <i class="fas fa-arrow-right-to-bracket text-sm"></i>
                </div>
            </div>
            <div>
                <h3 class="text-2xl font-black text-gray-900 leading-none"><?= $masuk_hari_ini; ?></h3>
                <p class="text-[10px] text-green-600 font-bold mt-2 uppercase tracking-tighter">
                    <i class="fas fa-car-side mr-1"></i> Unit Masuk
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col justify-between hover:border-blue-300 transition-all">
            <div class="flex items-start justify-between mb-2">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Keluar Hari Ini</p>
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-arrow-right-from-bracket text-sm"></i>
                </div>
            </div>
            <div>
                <h3 class="text-2xl font-black text-gray-900 leading-none"><?= $keluar_hari_ini; ?></h3>
                <p class="text-[10px] text-blue-600 font-bold mt-2 uppercase tracking-tighter">
                    <i class="fas fa-check-circle mr-1"></i> Unit Keluar
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col justify-between hover:border-orange-300 transition-all">
            <div class="flex items-start justify-between mb-2">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kendaraan</p>
                <div class="w-9 h-9 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                    <i class="fas fa-parking text-sm"></i>
                </div>
            </div>
            <div>
                <h3 class="text-2xl font-black text-gray-900 leading-none"><?= $sedang_parkir; ?></h3>
                <p class="text-[10px] text-orange-600 font-bold mt-2 uppercase tracking-tighter">
                    <i class="fas fa-car-side mr-1"></i> Sedang Parkir
                </p>
            </div>
        </div>

    </div>

    <div class="pt-4 border-t border-gray-200 mt-6">
        <h2 class="text-lg font-bold text-gray-900 tracking-tight uppercase">Akses Menu</h2>
        <p class="text-xs text-gray-500 mb-4">Pilih layanan operasional di bawah ini.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <a href="kendaraan_masuk.php" class="group bg-white rounded-xl border border-gray-200 p-4 md:p-5 shadow-sm hover:border-green-400 hover:shadow-md transition-colors duration-300 flex items-center gap-4 text-left animate-in slide-in-from-left-4 duration-500">

            <div class="w-14 h-14 shrink-0 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-2xl transition-colors duration-300 shadow-sm border border-green-100 group-hover:bg-green-600 group-hover:text-white group-hover:border-green-600">
                <i class="fas fa-arrow-right-to-bracket"></i>
            </div>

            <div class="flex-1">
                <h2 class="text-base font-black text-gray-900 group-hover:text-green-600 transition-colors tracking-tight uppercase mb-1">Kendaraan Masuk</h2>
                <p class="text-[10px] text-gray-500 font-medium leading-snug">Registrasi unit baru yang tiba di lokasi parkir dan terbitkan tiket masuk.</p>
            </div>

            <div class="hidden sm:flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 border border-gray-200 text-gray-400 transition-colors duration-300 group-hover:bg-green-100 group-hover:border-green-200 group-hover:text-green-600">
                <i class="fas fa-chevron-right text-xs"></i>
            </div>
        </a>

        <a href="kendaraan_keluar.php" class="group bg-white rounded-xl border border-gray-200 p-4 md:p-5 shadow-sm hover:border-blue-400 hover:shadow-md transition-colors duration-300 flex items-center gap-4 text-left animate-in slide-in-from-right-4 duration-500">

            <div class="w-14 h-14 shrink-0 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl transition-colors duration-300 shadow-sm border border-blue-100 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600">
                <i class="fas fa-arrow-right-from-bracket"></i>
            </div>

            <div class="flex-1">
                <h2 class="text-base font-black text-gray-900 group-hover:text-blue-600 transition-colors tracking-tight uppercase mb-1">Kendaraan Keluar</h2>
                <p class="text-[10px] text-gray-500 font-medium leading-snug">Proses pencarian plat nomor, kalkulasi tagihan, dan cetak struk parkir.</p>
            </div>

            <div class="hidden sm:flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 border border-gray-200 text-gray-400 transition-colors duration-300 group-hover:bg-blue-100 group-hover:border-blue-200 group-hover:text-blue-600">
                <i class="fas fa-chevron-right text-xs"></i>
            </div>
        </a>

    </div>

</div>

<?php require_once '../layout/footer.php'; ?>