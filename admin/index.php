<?php
require_once '../layout/header.php';
require_once '../config/koneksi.php';

if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Anda tidak memiliki akses ke halaman Admin!'); window.history.back();</script>";
    exit;
}

// 1. Mengambil data statistik
$q_user = mysqli_query($conn, "SELECT COUNT(id_user) as total FROM tb_ukk_galih_user");
$total_user = mysqli_fetch_assoc($q_user)['total'];

$q_area = mysqli_query($conn, "SELECT COUNT(id_area) as total FROM tb_ukk_galih_area_parkir");
$total_area = mysqli_fetch_assoc($q_area)['total'];

$q_kendaraan = mysqli_query($conn, "SELECT COUNT(id_kendaraan) as total FROM tb_ukk_galih_kendaraan");
$total_kendaraan = mysqli_fetch_assoc($q_kendaraan)['total'];

$q_parkir = mysqli_query($conn, "SELECT COUNT(id_parkir) as total FROM tb_ukk_galih_transaksi WHERE status = 'masuk'");
$kendaraan_parkir = mysqli_fetch_assoc($q_parkir)['total'];

// 2. Mengambil 5 Transaksi Terbaru
$query_recent = mysqli_query($conn, "
    SELECT t.*, k.plat_nomor, k.jenis_kendaraan, a.nama_area 
    FROM tb_ukk_galih_transaksi t
    JOIN tb_ukk_galih_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    JOIN tb_ukk_galih_area_parkir a ON t.id_area = a.id_area
    ORDER BY t.waktu_masuk DESC 
    LIMIT 5
");
?>

<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Dashboard Admin</h2>
            <p class="text-xs text-gray-400 ">Admin Panel | E-Parkir</p>
        </div>
    </div>

    <!-- Dashboard Panel -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col justify-between hover:border-blue-300 transition-all">
            <div class="flex items-start justify-between mb-2">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total User</p>
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-users text-sm"></i>
                </div>
            </div>
            <div>
                <h3 class="text-2xl font-black text-gray-900 leading-none"><?= $total_user; ?></h3>
                <p class="text-[10px] text-blue-600 font-bold mt-2 uppercase tracking-tighter">
                    <i class="fas fa-user-check mr-1"></i> Akun Terdaftar
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col justify-between hover:border-green-300 transition-all">
            <div class="flex items-start justify-between mb-2">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Area Parkir</p>
                <div class="w-9 h-9 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                    <i class="fas fa-layer-group text-sm"></i>
                </div>
            </div>
            <div>
                <h3 class="text-2xl font-black text-gray-900 leading-none"><?= $total_area; ?></h3>
                <p class="text-[10px] text-green-600 font-bold mt-2 uppercase tracking-tighter">
                    <i class="fas fa-map-marker-alt mr-1"></i> Lokasi Aktif
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
                <h3 class="text-2xl font-black text-gray-900 leading-none"><?= $kendaraan_parkir; ?></h3>
                <p class="text-[10px] text-orange-600 font-bold mt-2 uppercase tracking-tighter">
                    <i class="fas fa-car-side mr-1"></i> Sedang Parkir
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col justify-between hover:border-purple-300 transition-all">
            <div class="flex items-start justify-between mb-2">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Transaksi</p>
                <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i class="fas fa-chart-line text-sm"></i>
                </div>
            </div>
            <div>
                <h3 class="text-2xl font-black text-gray-900 leading-none"><?= $total_kendaraan; ?></h3>
                <p class="text-[10px] text-purple-600 font-bold mt-2 uppercase tracking-tighter">
                    <i class="fas fa-history mr-1"></i> Histori Kendaraan
                </p>
            </div>
        </div>
    </div>

    <div class="space-y-4 pt-2">
        <div>
            <h2 class="text-lg font-bold text-gray-900 tracking-tight uppercase">Akses Menu</h2>
            <p class="text-xs text-gray-400 ">Pilih menu navigasi untuk manajemen data.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <a href="data_kendaraan.php" class="group bg-white rounded-2xl border border-gray-200 p-5 hover:border-green-500 hover:shadow-md transition-all flex flex-col items-center text-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center text-2xl group-hover:bg-green-600 group-hover:text-white transition-all duration-300 shadow-sm border border-gray-100 group-hover:border-green-600">
                    <i class="fas fa-car"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 group-hover:text-green-600 transition-colors text-xs uppercase tracking-wide">Kendaraan</h3>
                </div>
            </a>

            <a href="kelola_user.php" class="group bg-white rounded-2xl border border-gray-200 p-5 hover:border-green-500 hover:shadow-md transition-all flex flex-col items-center text-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center text-2xl group-hover:bg-green-600 group-hover:text-white transition-all duration-300 shadow-sm border border-gray-100 group-hover:border-green-600">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 group-hover:text-green-600 transition-colors text-xs uppercase tracking-wide">Kelola User</h3>
                </div>
            </a>

            <a href="kelola_area.php" class="group bg-white rounded-2xl border border-gray-200 p-5 hover:border-green-500 hover:shadow-md transition-all flex flex-col items-center text-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center text-2xl group-hover:bg-green-600 group-hover:text-white transition-all duration-300 shadow-sm border border-gray-100 group-hover:border-green-600">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 group-hover:text-green-600 transition-colors text-xs uppercase tracking-wide">Area Parkir</h3>
                </div>
            </a>

            <a href="kelola_tarif.php" class="group bg-white rounded-2xl border border-gray-200 p-5 hover:border-green-500 hover:shadow-md transition-all flex flex-col items-center text-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center text-2xl group-hover:bg-green-600 group-hover:text-white transition-all duration-300 shadow-sm border border-gray-100 group-hover:border-green-600">
                    <i class="fas fa-tags"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 group-hover:text-green-600 transition-colors text-xs uppercase tracking-wide">Tarif</h3>
                </div>
            </a>

            <a href="log_aktivitas.php" class="group bg-white rounded-2xl border border-gray-200 p-5 hover:border-green-500 hover:shadow-md transition-all flex flex-col items-center text-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center text-2xl group-hover:bg-green-600 group-hover:text-white transition-all duration-300 shadow-sm border border-gray-100 group-hover:border-green-600">
                    <i class="fas fa-history"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 group-hover:text-green-600 transition-colors text-xs uppercase tracking-wide">Log Sistem</h3>
                </div>
            </a>
        </div>
    </div>

    <div class="space-y-4 pt-6 border-t border-gray-200 mt-6">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900 tracking-tight uppercase">Transaksi Terbaru</h2>
                <p class="text-xs text-gray-400">5 aktivitas masuk/keluar terakhir di sistem.</p>
            </div>
            <a href="kelola_kendaraan.php" class="text-[10px] font-bold text-blue-600 hover:text-blue-800 uppercase tracking-widest flex items-center gap-1.5 transition-colors">
                Lihat Semua <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/50">
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500">Unit Kendaraan</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 text-center">Area</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 text-center">Waktu Masuk</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 text-center">Waktu Keluar</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 text-right">Biaya</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <?php 
                        if(mysqli_num_rows($query_recent) > 0) :
                            while ($row = mysqli_fetch_assoc($query_recent)) : 
                                $ikon = ($row['jenis_kendaraan'] == 'motor') ? '🏍️' : '🚗';
                        ?>
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-black tracking-widest text-gray-900 uppercase bg-gray-100 border border-gray-200 px-2 py-0.5 rounded shadow-sm group-hover:bg-white transition-colors">
                                        <?= $row['plat_nomor']; ?>
                                    </span>
                                    <span class="text-[10px] text-gray-500 font-bold capitalize">
                                        <?= $ikon; ?> <?= $row['jenis_kendaraan']; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                                    <?= $row['nama_area']; ?>
                                </span>
                            </td>
                            
                            <td class="px-4 py-2 text-center">
                                <div class="font-mono text-[10px] font-bold text-gray-700 leading-tight">
                                    <?= date('d/m/Y', strtotime($row['waktu_masuk'])); ?>
                                </div>
                                <div class="font-mono text-[9px] text-gray-500">
                                    <?= date('H:i', strtotime($row['waktu_masuk'])); ?> WIB
                                </div>
                            </td>
                            
                            <td class="px-4 py-2 text-center">
                                <?php if ($row['status'] == 'keluar') : ?>
                                    <div class="font-mono text-[10px] font-bold text-gray-700 leading-tight">
                                        <?= date('d/m/Y', strtotime($row['waktu_keluar'])); ?>
                                    </div>
                                    <div class="font-mono text-[9px] text-gray-500">
                                        <?= date('H:i', strtotime($row['waktu_keluar'])); ?> WIB
                                    </div>
                                <?php else : ?>
                                    <span class="text-gray-300 font-bold">-</span>
                                <?php endif; ?>
                            </td>

                            <td class="px-4 py-2.5 text-right">
                                <?php if ($row['status'] == 'keluar') : ?>
                                    <code class="text-[10px] font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded">
                                        Rp <?= number_format($row['biaya_total'], 0, ',', '.'); ?>
                                    </code>
                                <?php else : ?>
                                    <span class="text-[10px] text-gray-400 italic">Menunggu...</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <?php if ($row['status'] == 'masuk') : ?>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold uppercase tracking-tight bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-600 mr-1.5 animate-pulse"></span> Parkir
                                    </span>
                                <?php else : ?>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold uppercase tracking-tight bg-gray-50 text-gray-500 ring-1 ring-inset ring-gray-500/20">
                                        ⚪ Keluar
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; else : ?>
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-[11px] text-gray-400 italic uppercase tracking-widest font-bold">Belum ada transaksi hari ini.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
</div>

<?php require_once '../layout/footer.php'; ?>