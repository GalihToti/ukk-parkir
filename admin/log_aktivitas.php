<?php
require_once '../layout/header.php';
require_once '../config/koneksi.php';

// hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Anda tidak memiliki akses!'); window.history.back();</script>";
    exit;
}

// Mengambil data log aktivitas, digabung dengan tabel user agar nama dan role-nya ketahuan
$query_log = mysqli_query($conn, "
    SELECT l.*, u.nama_lengkap, u.role 
    FROM tb_ukk_galih_log_aktivitas l 
    JOIN tb_ukk_galih_user u ON l.id_user = u.id_user 
    ORDER BY l.waktu_aktivitas DESC 
    LIMIT 100
");
?>

<div class="max-w-6xl mx-auto space-y-4">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 no-print">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-gray-900 flex items-center gap-2">
                Log Aktivitas Sistem
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">Pantau riwayat aktivitas dan akses login pengguna.</p>
        </div>
        <a href="index.php" class="inline-flex h-8 items-center justify-center rounded-md border border-gray-200 bg-white px-3 py-1 text-xs font-medium shadow-sm transition-colors hover:bg-gray-50 hover:text-gray-900 focus:outline-none w-full md:w-auto">
            <i class="fas fa-arrow-left mr-2 text-gray-400 text-[10px]"></i> Kembali
        </a>
    </div>

    <div class="bg-blue-50 border border-blue-100/50 rounded-md p-3 flex items-start gap-2 shadow-sm animate-in fade-in duration-300">
        <i class="fas fa-info-circle text-blue-500 mt-0.5 text-[10px]"></i>
        <div>
            <h3 class="text-[10px] font-bold text-blue-900 uppercase tracking-widest">Informasi Log</h3>
            <p class="text-[10px] text-blue-800 leading-tight font-medium mt-0.5">Menampilkan <span class="font-bold text-blue-900">100</span> riwayat aktivitas terakhir di dalam sistem untuk menjaga performa halaman.</p>
        </div>
    </div>

    <div class="bg-white rounded-md shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 w-10 text-center">#</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 w-40">Waktu Aktivitas</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500">Nama Pengguna</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 text-center">Hak Akses</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500">Deskripsi Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                    if (mysqli_num_rows($query_log) > 0) {
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query_log)) :
                    ?>
                            <tr class="group transition-colors hover:bg-gray-50/50">
                                <td class="px-4 py-2.5 text-[10px] text-gray-400 text-center font-mono italic">
                                    <?= $no++; ?>
                                </td>

                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 group-hover:bg-white transition-colors">
                                            <i class="far fa-clock text-[9px]"></i>
                                        </div>
                                        <div class="font-mono text-[10px] text-gray-500 leading-tight">
                                            <p><?= date('d/m/Y', strtotime($row['waktu_aktivitas'])); ?></p>
                                            <p class="font-bold text-gray-700"><?= date('H:i:s', strtotime($row['waktu_aktivitas'])); ?></p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-2.5">
                                    <span class="text-xs font-bold text-gray-900 tracking-tight">
                                        <?= $row['nama_lengkap']; ?>
                                    </span>
                                </td>

                                <td class="px-4 py-2.5 text-center">
                                    <?php
                                    // Menentukan warna dan ikon berdasarkan role
                                    if ($row['role'] == 'admin') {
                                        $warna = 'bg-blue-50 text-blue-700 border-blue-200/50';
                                        $ikon_role = 'fa-user-shield';
                                    } elseif ($row['role'] == 'petugas') {
                                        $warna = 'bg-green-50 text-green-700 border-green-200/50';
                                        $ikon_role = 'fa-user-tie';
                                    } else {
                                        $warna = 'bg-purple-50 text-purple-700 border-purple-200/50';
                                        $ikon_role = 'fa-user';
                                    }
                                    ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 border rounded text-[9px] font-bold uppercase tracking-widest <?= $warna; ?>">
                                        <i class="fas <?= $ikon_role; ?> text-[9px]"></i> <?= $row['role']; ?>
                                    </span>
                                </td>

                                <td class="px-4 py-2.5 text-[11px] text-gray-600 font-medium">
                                    <?= $row['aktivitas']; ?>
                                </td>
                            </tr>
                    <?php
                        endwhile;
                    } else {
                        echo "<tr><td colspan='5' class='py-8 text-center text-[10px] text-gray-400 italic font-medium uppercase tracking-widest'>Belum ada log aktivitas yang tercatat.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../layout/footer.php'; ?>