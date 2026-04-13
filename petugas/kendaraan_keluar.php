<?php
require_once '../layout/header.php';
require_once '../config/koneksi.php';

// hanya petugas yang bisa akses
if ($_SESSION['role'] !== 'petugas') {
    echo "<script>alert('Anda tidak memiliki akses!'); window.location.href='../auth/login.php';</script>";
    exit;
}

$pesan = '';
$tipe_pesan = '';
$data_parkir = null;
$waktu_keluar = date('Y-m-d H:i:s');

// 1. Logika mencari plat nomor
if (isset($_POST['cari_kendaraan'])) {
    $plat_nomor = mysqli_real_escape_string($conn, $_POST['plat_nomor']);

    $query_cari = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, a.nama_area 
                   FROM tb_ukk_galih_transaksi t
                   JOIN tb_ukk_galih_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                   JOIN tb_ukk_galih_area_parkir a ON t.id_area = a.id_area
                   WHERE k.plat_nomor = '$plat_nomor' AND t.status = 'masuk'";

    $result_cari = mysqli_query($conn, $query_cari);

    if (mysqli_num_rows($result_cari) > 0) {
        $data_parkir = mysqli_fetch_assoc($result_cari);

        $waktu_masuk_str = strtotime($data_parkir['waktu_masuk']);
        $waktu_keluar_str = strtotime($waktu_keluar);

        $selisih_detik = $waktu_keluar_str - $waktu_masuk_str;
        $durasi_jam = ceil($selisih_detik / 3600);
        if ($durasi_jam <= 0) $durasi_jam = 1;

        $jenis = $data_parkir['jenis_kendaraan'];
        $query_tarif = mysqli_query($conn, "SELECT tarif_per_jam FROM tb_ukk_galih_tarif WHERE jenis_kendaraan = '$jenis'");
        $data_tarif = mysqli_fetch_assoc($query_tarif);

        $tarif_per_jam = $data_tarif ? $data_tarif['tarif_per_jam'] : 2000;

        $biaya_total = $durasi_jam * $tarif_per_jam;
    } else {
        $pesan = "Plat Nomor '$plat_nomor' tidak ditemukan atau sudah checkout.";
        $tipe_pesan = "bg-red-50 border-red-200 text-red-700";
    }
}

// 2. Logika untuk MEMPROSES PEMBAYARAN
if (isset($_POST['proses_keluar'])) {
    $id_parkir = $_POST['id_parkir'];
    $id_area = $_POST['id_area'];
    $durasi = $_POST['durasi_jam'];
    $total = $_POST['biaya_total'];
    $waktu_keluar_fix = $_POST['waktu_keluar'];

    $query_update = "UPDATE tb_ukk_galih_transaksi 
                     SET waktu_keluar = '$waktu_keluar_fix', durasi_jam = '$durasi', biaya_total = '$total', status = 'keluar' 
                     WHERE id_parkir = '$id_parkir'";

    if (mysqli_query($conn, $query_update)) {
        mysqli_query($conn, "UPDATE tb_ukk_galih_area_parkir SET terisi = terisi - 1 WHERE id_area = '$id_area'");

        catat_log($conn, "Kendaraan Keluar, ID Parkir: $id_parkir");

        echo "<script>
                alert('Pembayaran Berhasil! Mengalihkan ke Cetak Struk...');
                window.location.href='cetak_struk.php?id=$id_parkir';
              </script>";
        exit;
    } else {
        $pesan = "Gagal memproses transaksi: " . mysqli_error($conn);
        $tipe_pesan = "bg-red-50 border-red-200 text-red-700";
    }
}
?>

<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 animate-in slide-in-from-top-2 duration-500">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-gray-900">Kendaraan Keluar</h1>
            <p class="text-xs text-gray-400">Pencarian data dan kalkulasi tagihan parkir</p>
        </div>
    </div>

    <div class="bg-blue-50/80 backdrop-blur-sm border border-blue-200/50 rounded-xl p-4 flex items-start gap-3 shadow-sm animate-in fade-in duration-700">
        <div class="bg-blue-100 p-2 rounded-lg text-blue-600 shrink-0 shadow-inner">
            <i class="fas fa-bullhorn text-sm"></i>
        </div>
        <div class="pt-0.5">
            <h3 class="text-[11px] font-bold text-blue-900 uppercase tracking-widest">Tips Operasional</h3>
            <p class="text-[11px] text-blue-800/80 leading-relaxed font-medium mt-1">Pastikan selalu mengecek kecocokan <span class="font-bold text-blue-900 bg-blue-100/80 px-1.5 py-0.5 rounded">Plat Nomor</span> dengan fisik kendaraan saat memproses tiket keluar untuk keamanan.</p>
        </div>
    </div>

    <?php if ($pesan != '') : ?>
        <div class="border px-4 py-3 rounded-lg relative flex items-center gap-3 <?= $tipe_pesan; ?> shadow-sm animate-in fade-in duration-300" role="alert">
            <i class="fas fa-exclamation-circle text-lg"></i>
            <span class="block sm:inline font-bold text-[11px] uppercase tracking-wider"><?= $pesan; ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden animate-in slide-in-from-bottom-4 duration-500">
        <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
            <h2 class="text-xs font-bold text-gray-800 flex items-center gap-2 uppercase tracking-widest">
                <i class="fas fa-search text-gray-400"></i> Identifikasi Kendaraan
            </h2>
        </div>

        <form action="" method="POST" class="p-6 flex flex-col md:flex-row gap-5 items-center">
            <div class="w-full relative group flex-1">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-400 to-blue-600 rounded-xl blur opacity-0 group-focus-within:opacity-30 transition duration-500"></div>

                <input type="text" name="plat_nomor" placeholder="AE 1234 XX"
                    class="relative w-full h-12 px-4 bg-white border-2 border-gray-200 rounded-xl focus:border-blue-500 outline-none transition-all text-center text-xl font-black text-gray-900 uppercase tracking-[0.2em] placeholder:font-normal placeholder:tracking-normal placeholder:text-gray-300 shadow-sm" required autofocus autocomplete="off">
            </div>

            <button type="submit" name="cari_kendaraan" class="w-full md:w-auto shrink-0 h-12 px-8 bg-gray-900 hover:bg-blue-600 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg hover:shadow-blue-500/20 flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                <i class="fas fa-search text-sm"></i> Cari Tagihan
            </button>
        </form>
    </div>

    <?php if ($data_parkir != null) : ?>
        <div class="bg-white rounded-xl shadow-sm border-t-4 border-t-blue-500 border-x border-b border-gray-200 relative overflow-hidden animate-in slide-in-from-bottom-8 duration-700">
            <i class="fas fa-receipt absolute -right-10 -bottom-10 text-[150px] text-gray-50/50 pointer-events-none z-0"></i>

            <div class="relative z-10 p-6 md:p-8">
                <h2 class="text-sm font-bold text-gray-900 mb-6 border-b border-gray-100 pb-3 flex items-center gap-2 uppercase tracking-wider">
                    <i class="fas fa-file-invoice-dollar text-blue-500"></i> Rincian Invoice Parkir
                </h2>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-y-6 gap-x-4 mb-8">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Plat Nomor</p>
                        <p class="font-black text-lg text-gray-900 uppercase tracking-widest bg-gray-100 inline-block px-2 py-0.5 rounded border border-gray-200"><?= $data_parkir['plat_nomor']; ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Kategori Unit</p>
                        <p class="font-bold text-sm text-gray-700 capitalize">
                            <?= ($data_parkir['jenis_kendaraan'] == 'motor') ? "🏍️ " : (($data_parkir['jenis_kendaraan'] == 'mobil') ? "🚗 " : "🚚 "); ?>
                            <?= $data_parkir['jenis_kendaraan']; ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Lokasi Area</p>
                        <p class="font-bold text-sm text-gray-700"><?= $data_parkir['nama_area']; ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Durasi Inap</p>
                        <p class="font-bold text-sm text-orange-600 bg-orange-50 inline-block px-2 py-0.5 rounded border border-orange-100"><?= $durasi_jam; ?> Jam</p>
                    </div>

                    <div class="col-span-2 md:col-span-4 bg-gray-50 border border-gray-100 p-4 rounded-lg flex justify-between items-center">
                        <div>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Waktu Masuk</p>
                            <p class="font-mono text-xs font-bold text-gray-700"><?= date('d/m/Y - H:i', strtotime($data_parkir['waktu_masuk'])); ?></p>
                        </div>
                        <i class="fas fa-arrow-right text-gray-300"></i>
                        <div class="text-right">
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Waktu Keluar</p>
                            <p class="font-mono text-xs font-bold text-gray-700"><?= date('d/m/Y - H:i', strtotime($waktu_keluar)); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-blue-50 to-blue-100/50 border border-blue-200 p-6 rounded-xl mb-6 flex flex-col md:flex-row justify-between items-center gap-4 shadow-inner">
                    <div class="text-center md:text-left">
                        <p class="text-[11px] font-black text-blue-900 uppercase tracking-widest mb-1">Total Tagihan Bersih</p>
                        <p class="text-[10px] font-medium text-blue-600/80">Sudah termasuk pajak & asuransi keamanan</p>
                    </div>
                    <div class="text-center md:text-right">
                        <p class="text-4xl font-black text-blue-700 tracking-tight leading-none drop-shadow-sm">
                            <span class="text-xl text-blue-500 font-bold mr-1">Rp</span><?= number_format($biaya_total, 0, ',', '.'); ?>
                        </p>
                    </div>
                </div>

                <form action="" method="POST">
                    <input type="hidden" name="id_parkir" value="<?= $data_parkir['id_parkir']; ?>">
                    <input type="hidden" name="id_area" value="<?= $data_parkir['id_area']; ?>">
                    <input type="hidden" name="durasi_jam" value="<?= $durasi_jam; ?>">
                    <input type="hidden" name="biaya_total" value="<?= $biaya_total; ?>">
                    <input type="hidden" name="waktu_keluar" value="<?= $waktu_keluar; ?>">

                    <button type="submit" name="proses_keluar" class="w-full group relative flex items-center hover:bg-green-600 justify-center h-14 bg-gray-900 text-white font-bold rounded-xl overflow-hidden transition-all shadow-md hover:shadow-lg hover:shadow-green-500/20 focus:scale-[0.99]"
                        onclick="return confirm('Apakah pelanggan sudah membayar sejumlah Rp <?= number_format($biaya_total, 0, ',', '.'); ?>?');">
                        <div class="relative z-10 flex items-center gap-2 text-xs uppercase tracking-widest">
                            <i class="fas fa-check-circle text-lg"></i>
                            <span>Selesaikan Pembayaran & Cetak Struk</span>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../layout/footer.php'; ?>