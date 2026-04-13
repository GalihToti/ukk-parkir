<?php
require_once '../layout/header.php';
require_once '../config/koneksi.php';

// hanya owner yang bisa akses halaman ini
if ($_SESSION['role'] !== 'owner') {
    echo "<script>alert('Anda tidak memiliki akses ke halaman Owner!'); window.history.back();</script>";
    exit;
}

// Set default filter tanggal
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

// Mengambil data transaksi
$query_laporan = "
    SELECT t.*, k.plat_nomor, k.jenis_kendaraan, u.nama_lengkap AS nama_petugas
    FROM tb_ukk_galih_transaksi t
    JOIN tb_ukk_galih_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    JOIN tb_ukk_galih_user u ON t.id_user = u.id_user
    WHERE t.status = 'keluar' 
    AND DATE(t.waktu_keluar) BETWEEN '$tgl_awal' AND '$tgl_akhir'
    ORDER BY t.waktu_keluar DESC
";
$result_laporan = mysqli_query($conn, $query_laporan);

// Hitung total sebelum merender HTML
$total_pendapatan = 0;
$total_kendaraan = mysqli_num_rows($result_laporan);
$data_tabel = [];
if ($total_kendaraan > 0) {
    while ($row = mysqli_fetch_assoc($result_laporan)) {
        $total_pendapatan += $row['biaya_total'];
        $data_tabel[] = $row; // Simpan ke array untuk ditampilkan di tabel nanti
    }
}
?>

<style>
    @media print {
        #sidebar, header, .no-print { display: none !important; }
        body, main, .max-w-6xl { margin: 0 !important; padding: 0 !important; background-color: white !important; width: 100% !important; max-width: 100% !important; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .print-area { box-shadow: none !important; border: none !important; padding: 0 !important; }
        .print-table th, .print-table td { border: 1px solid #e5e7eb !important; }
    }
</style>

<div class="max-w-6xl mx-auto space-y-4">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 no-print mb-2">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
                <i class="fas fa-chart-pie text-green-600"></i> Laporan Keuangan
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="inline-flex items-center justify-center gap-1.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 font-medium px-3 py-1.5 rounded-lg text-xs transition-colors shadow-sm w-full md:w-auto">
                <i class="fas fa-print text-gray-400"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 no-print">
        
        <div class="md:col-span-1 bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col justify-center">
            <h2 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2.5">Filter Tanggal</h2>
            <form action="" method="GET" class="space-y-2.5">
                <div class="flex gap-2">
                    <div class="flex-1">
                        <input type="date" name="tgl_awal" value="<?= $tgl_awal; ?>" class="w-full px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-md focus:bg-white focus:ring-1 focus:ring-green-500 focus:border-transparent outline-none transition-all text-xs text-gray-700" required>
                    </div>
                    <div class="flex items-center text-gray-400 text-xs">-</div>
                    <div class="flex-1">
                        <input type="date" name="tgl_akhir" value="<?= $tgl_akhir; ?>" class="w-full px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-md focus:bg-white focus:ring-1 focus:ring-green-500 focus:border-transparent outline-none transition-all text-xs text-gray-700" required>
                    </div>
                </div>
                <button type="submit" class="w-full bg-gray-900 hover:bg-green-600 text-white font-medium py-1.5 px-4 rounded-md transition-colors text-xs shadow-sm flex justify-center items-center gap-1.5">
                    <i class="fas fa-filter"></i> Terapkan
                </button>
            </form>
        </div>

        <div class="bg-green-600 p-5 rounded-xl shadow-sm text-white flex flex-col justify-center relative overflow-hidden">
            <i class="fas fa-wallet absolute -right-4 -bottom-4 text-[80px] opacity-20 pointer-events-none"></i>
            <p class="text-[11px] font-semibold text-green-100 uppercase tracking-wider mb-1 relative z-10">Total Pendapatan Bersih</p>
            <p class="text-3xl font-black tracking-tight relative z-10 leading-none mt-1">
                <span class="text-sm mr-1 font-semibold text-green-200">Rp</span><?= number_format($total_pendapatan, 0, ',', '.'); ?>
            </p>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex flex-col justify-center">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Volume Kendaraan Keluar</p>
                    <p class="text-3xl font-black text-gray-900 tracking-tight leading-none mt-1">
                        <?= $total_kendaraan; ?> <span class="text-sm font-medium text-gray-400 ml-0.5">Unit</span>
                    </p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                    <i class="fas fa-car-side text-sm"></i>
                </div>
            </div>
        </div>
        
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 print-area overflow-hidden">
        
        <div class="hidden print:block text-center mb-5 pt-4 border-b-2 border-gray-800 pb-4 mx-6">
            <h2 class="text-lg font-black uppercase tracking-widest text-gray-900">Rekapitulasi Keuangan Parkir</h2>
            <p class="text-[11px] text-gray-500 mt-1">Periode Transaksi: <?= date('d/m/Y', strtotime($tgl_awal)); ?> s.d <?= date('d/m/Y', strtotime($tgl_akhir)); ?></p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full w-full print-table">
                <thead class="bg-gray-50/80 text-gray-500 uppercase text-[10px] font-bold tracking-wider border-b border-gray-200">
                    <tr>
                        <th class="py-2.5 px-4 text-center w-10">No</th>
                        <th class="py-2.5 px-4 text-left">Waktu Keluar</th>
                        <th class="py-2.5 px-4 text-left">Plat Nomor</th>
                        <th class="py-2.5 px-4 text-center">Jenis</th>
                        <th class="py-2.5 px-4 text-center">Durasi</th>
                        <th class="py-2.5 px-4 text-right">Biaya Parkir</th>
                        <th class="py-2.5 px-4 text-left no-print w-32">Kasir</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-xs divide-y divide-gray-100">
                    <?php 
                    if ($total_kendaraan > 0) {
                        $no = 1;
                        foreach ($data_tabel as $row) : 
                    ?>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-2.5 px-4 text-center text-gray-400 font-medium"><?= $no++; ?></td>
                        <td class="py-2.5 px-4 text-left font-mono"><?= date('d/m/y - H:i', strtotime($row['waktu_keluar'])); ?></td>
                        <td class="py-2.5 px-4 text-left font-bold uppercase text-gray-900"><?= $row['plat_nomor']; ?></td>
                        <td class="py-2.5 px-4 text-center capitalize text-gray-500">
                            <?php 
                            if($row['jenis_kendaraan'] == 'motor') echo "🏍️";
                            elseif($row['jenis_kendaraan'] == 'mobil') echo "🚗";
                            else echo "🚚";
                            ?> <span class="ml-1"><?= $row['jenis_kendaraan']; ?></span>
                        </td>
                        <td class="py-2.5 px-4 text-center">
                            <span class="bg-gray-100 px-1.5 py-0.5 rounded font-semibold"><?= $row['durasi_jam']; ?> Jam</span>
                        </td>
                        <td class="py-2.5 px-4 text-right font-bold text-green-700">
                            Rp <?= number_format($row['biaya_total'], 0, ',', '.'); ?>
                        </td>
                        <td class="py-2.5 px-4 text-left text-gray-500 no-print truncate max-w-[120px]"><?= $row['nama_petugas']; ?></td>
                    </tr>
                    <?php 
                        endforeach; 
                    } else {
                        echo "<tr><td colspan='7' class='py-10 text-center text-gray-400'>Belum ada transaksi di periode ini.</td></tr>";
                    }
                    ?>
                </tbody>
                <tfoot class="print:table-footer-group">
                    <tr class="bg-gray-50/50 border-t border-gray-200">
                        <td colspan="5" class="py-3 px-4 text-right text-[10px] font-bold uppercase tracking-wider text-gray-500">Total Pemasukan:</td>
                        <td class="py-3 px-4 text-right text-green-600 text-sm font-black tracking-tight">Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></td>
                        <td class="bg-white no-print"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="hidden print:flex justify-between items-center text-[10px] text-gray-500 p-4 border-t border-gray-100 mt-2">
            <p class="font-medium">Total Kendaraan: <?= $total_kendaraan; ?> Unit</p>
            <p>Dicetak otomatis pada: <?= date('d/m/Y H:i:s'); ?></p>
        </div>
    </div>

</div>

<?php require_once '../layout/footer.php'; ?>