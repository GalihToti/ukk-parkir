<?php
require_once '../layout/header.php';
require_once '../config/koneksi.php';

// Pastikan hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Anda tidak memiliki akses!'); window.history.back();</script>";
    exit;
}

$pesan = '';
$tipe_pesan = '';
$icon = '';

// 1. Tambah Tarif
if (isset($_POST['simpan_tarif'])) {
    $jenis_kendaraan = mysqli_real_escape_string($conn, $_POST['jenis_kendaraan']);
    $tarif_per_jam = (int)$_POST['tarif_per_jam'];

    // Cek apakah tarif untuk jenis kendaraan ini sudah ada di database
    $cek_tarif = mysqli_query($conn, "SELECT id_tarif FROM tb_ukk_galih_tarif WHERE jenis_kendaraan = '$jenis_kendaraan'");

    if (mysqli_num_rows($cek_tarif) > 0) {
        $query = "UPDATE tb_ukk_galih_tarif SET tarif_per_jam = '$tarif_per_jam' WHERE jenis_kendaraan = '$jenis_kendaraan'";
        $aksi_teks = "diperbarui";
    } else {
        $query = "INSERT INTO tb_ukk_galih_tarif (jenis_kendaraan, tarif_per_jam) VALUES ('$jenis_kendaraan', '$tarif_per_jam')";
        $aksi_teks = "ditambahkan";
    }

    catat_log($conn, "Tarif: $jenis_kendaraan menjadi Rp$tarif_per_jam");

    if (mysqli_query($conn, $query)) {
        $pesan = "Berhasil! Tarif $jenis_kendaraan berhasil $aksi_teks.";
        $tipe_pesan = "bg-green-50 text-green-700 border-green-200";
        $icon = "fa-check-circle text-green-500";
    } else {
        $pesan = "Gagal menyimpan data: " . mysqli_error($conn);
        $tipe_pesan = "bg-red-50 text-red-700 border-red-200";
        $icon = "fa-exclamation-circle text-red-500";
    }
}

// 2. Edit Tarif
if (isset($_POST['update_tarif'])) {
    $id_tarif_edit = mysqli_real_escape_string($conn, $_POST['id_tarif']);
    $jenis_kendaraan = mysqli_real_escape_string($conn, $_POST['jenis_kendaraan']);
    $tarif_per_jam = (int)$_POST['tarif_per_jam'];

    // Pastikan tidak duplikat dengan jenis kendaraan lain yang sudah ada
    $cek_duplikat = mysqli_query($conn, "SELECT id_tarif FROM tb_ukk_galih_tarif WHERE jenis_kendaraan = '$jenis_kendaraan' AND id_tarif != '$id_tarif_edit'");
    
    catat_log($conn, "Tarif: $jenis_kendaraan menjadi Rp$tarif_per_jam");

    if (mysqli_num_rows($cek_duplikat) > 0) {
        $pesan = "Gagal! Jenis kendaraan '$jenis_kendaraan' sudah memiliki tarif.";
        $tipe_pesan = "bg-red-50 text-red-700 border-red-200";
        $icon = "fa-exclamation-circle text-red-500";
    } else {
        $query_update = "UPDATE tb_ukk_galih_tarif SET jenis_kendaraan = '$jenis_kendaraan', tarif_per_jam = '$tarif_per_jam' WHERE id_tarif = '$id_tarif_edit'";
        if (mysqli_query($conn, $query_update)) {
            $pesan = "Tarif berhasil diperbarui!";
            $tipe_pesan = "bg-blue-50 text-blue-700 border-blue-200";
            $icon = "fa-check-circle text-blue-500";
        } else {
            $pesan = "Gagal memperbarui data: " . mysqli_error($conn);
            $tipe_pesan = "bg-red-50 text-red-700 border-red-200";
            $icon = "fa-exclamation-circle text-red-500";
        }
    }
}

// 3. LOGIKA HAPUS TARIF (DELETE)
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['id']);

    $hapus = mysqli_query($conn, "DELETE FROM tb_ukk_galih_tarif WHERE id_tarif = '$id_hapus'");
    if ($hapus) {
        echo "<script>alert('Tarif berhasil dihapus!'); window.location.href='kelola_tarif.php';</script>";
    }
}

// 4. MENGAMBIL DATA UNTUK FORM EDIT
$tarif_edit = null;
if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
    $id_edit = mysqli_real_escape_string($conn, $_GET['id']);
    $query_edit = mysqli_query($conn, "SELECT * FROM tb_ukk_galih_tarif WHERE id_tarif = '$id_edit'");
    $tarif_edit = mysqli_fetch_assoc($query_edit);
}

// Mengambil semua data tarif
$query_tarif = mysqli_query($conn, "SELECT * FROM tb_ukk_galih_tarif ORDER BY tarif_per_jam ASC");
?>

<div class="max-w-6xl mx-auto space-y-4">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 no-print">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-gray-900 flex items-center gap-2">
                Kelola Tarif Parkir
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">Manajemen harga tiket berdasarkan jenis kendaraan.</p>
        </div>
        <a href="index.php" class="inline-flex h-8 items-center justify-center rounded-md border border-gray-200 bg-white px-3 py-1 text-xs font-medium shadow-sm transition-colors hover:bg-gray-50 hover:text-gray-900 focus:outline-none w-full md:w-auto">
            <i class="fas fa-arrow-left mr-2 text-gray-400 text-[10px]"></i> Kembali
        </a>
    </div>

    <?php if ($pesan != '') : ?>
        <div class="border px-4 py-2 rounded-md relative flex items-center gap-2 <?= $tipe_pesan; ?> shadow-sm text-xs font-medium animate-in fade-in duration-300" role="alert">
            <i class="fas <?= $icon; ?>"></i>
            <span class="block sm:inline"><?= $pesan; ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <div class="lg:col-span-1">
            <?php if ($tarif_edit) : ?>
                <div class="bg-white p-4 rounded-md shadow-sm border border-blue-200 relative overflow-hidden animate-in fade-in duration-300">
                    <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                    <h2 class="text-[11px] font-bold text-gray-900 uppercase tracking-widest mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                        <i class="fas fa-pen text-blue-500"></i> Edit Tarif Parkir
                    </h2>
                    <form action="" method="POST" class="space-y-3">
                        <input type="hidden" name="id_tarif" value="<?= $tarif_edit['id_tarif']; ?>">
                        
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 mb-1.5 uppercase tracking-tight">Jenis Kendaraan</label>
                            <div class="relative">
                                <select name="jenis_kendaraan" class="w-full h-8 pl-3 pr-8 bg-gray-50 border border-gray-200 rounded text-xs focus:bg-white focus:ring-1 focus:ring-blue-500 outline-none transition-all appearance-none cursor-pointer text-gray-700 font-medium" required>
                                    <option value="motor" <?= ($tarif_edit['jenis_kendaraan'] == 'motor') ? 'selected' : ''; ?>>🏍️ Motor</option>
                                    <option value="mobil" <?= ($tarif_edit['jenis_kendaraan'] == 'mobil') ? 'selected' : ''; ?>>🚗 Mobil</option>
                                    <option value="lainnya" <?= ($tarif_edit['jenis_kendaraan'] == 'lainnya') ? 'selected' : ''; ?>>🚚 Lainnya</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 mb-1.5 uppercase tracking-tight">Tarif Per Jam (Rp)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-400 font-bold text-[10px]">Rp</span>
                                </div>
                                <input type="number" min="0" step="500" name="tarif_per_jam" value="<?= $tarif_edit['tarif_per_jam']; ?>" placeholder="2000" class="w-full h-8 pl-8 pr-3 bg-gray-50 border border-gray-200 rounded text-xs focus:bg-white focus:ring-1 focus:ring-blue-500 outline-none transition-all text-gray-900 font-bold" required>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-2 pt-1">
                            <button type="submit" name="update_tarif" class="flex-1 h-8 flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold rounded text-[10px] uppercase tracking-widest transition-colors shadow-sm gap-2">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="kelola_tarif.php" class="h-8 px-4 flex items-center justify-center bg-gray-100 hover:bg-gray-200 border border-gray-200 text-gray-600 font-bold rounded text-[10px] uppercase tracking-widest transition-colors shadow-sm" title="Batal Edit">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            <?php else : ?>
                <div class="bg-white p-4 rounded-md shadow-sm border border-gray-200">
                    <h2 class="text-[11px] font-bold text-gray-900 uppercase tracking-widest mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                        <i class="fas fa-plus text-gray-400"></i> Atur Tarif Baru
                    </h2>
                    <form action="" method="POST" class="space-y-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 mb-1.5 uppercase tracking-tight">Jenis Kendaraan</label>
                            <div class="relative">
                                <select name="jenis_kendaraan" class="w-full h-8 pl-3 pr-8 bg-gray-50 border border-gray-200 rounded text-xs focus:bg-white focus:ring-1 focus:ring-green-500 outline-none transition-all appearance-none cursor-pointer text-gray-700 font-medium" required>
                                    <option value="motor">🏍️ Motor</option>
                                    <option value="mobil">🚗 Mobil</option>
                                    <option value="lainnya">🚚 Lainnya</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 mb-1.5 uppercase tracking-tight">Tarif Per Jam (Rp)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-400 font-bold text-[10px]">Rp</span>
                                </div>
                                <input type="number" min="0" step="500" name="tarif_per_jam" placeholder="2000" class="w-full h-8 pl-8 pr-3 bg-gray-50 border border-gray-200 rounded text-xs focus:bg-white focus:ring-1 focus:ring-green-500 outline-none transition-all text-gray-900 font-bold" required>
                            </div>
                        </div>
                        <button type="submit" name="simpan_tarif" class="w-full h-8 flex items-center justify-center bg-gray-900 hover:bg-green-600 text-white font-bold rounded text-[10px] uppercase tracking-widest transition-colors shadow-sm mt-1 gap-2">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </form>

                    <div class="mt-4 p-3 bg-blue-50 border border-blue-100/50 rounded-md flex items-start gap-2">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5 text-[10px]"></i>
                        <p class="text-[10px] text-blue-800 leading-tight font-medium">
                            Jika kendaraan sudah ada, sistem akan otomatis memperbarui harga lamanya.
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-md shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50">
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 w-1/3">Jenis Kendaraan</th>
                                <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500">Tarif Per Jam</th>
                                <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 text-right w-1/4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php
                            if (mysqli_num_rows($query_tarif) > 0) {
                                while ($row = mysqli_fetch_assoc($query_tarif)) :
                                    $ikon_kendaraan = 'fa-car-side';
                                    if ($row['jenis_kendaraan'] == 'motor') $ikon_kendaraan = 'fa-motorcycle';
                                    if ($row['jenis_kendaraan'] == 'lainnya') $ikon_kendaraan = 'fa-truck';
                            ?>
                                    <tr class="group transition-colors hover:bg-gray-50/50">
                                        <td class="px-4 py-2.5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-7 h-7 rounded bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-500 text-[10px] group-hover:bg-white transition-colors">
                                                    <i class="fas <?= $ikon_kendaraan ?>"></i>
                                                </div>
                                                <span class="text-xs font-black uppercase text-gray-900 tracking-widest">
                                                    <?= $row['jenis_kendaraan']; ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <code class="text-[11px] font-bold text-blue-600 bg-blue-50/50 px-2 py-0.5 rounded border border-blue-100">
                                                Rp <?= number_format($row['tarif_per_jam'], 0, ',', '.'); ?>
                                            </code>
                                            <span class="text-[10px] text-gray-400 font-normal italic ml-1">/ jam</span>
                                        </td>
                                        <td class="px-4 py-2.5 text-right">
                                            <div class="flex justify-end gap-1.5">
                                                <a href="kelola_tarif.php?aksi=edit&id=<?= $row['id_tarif']; ?>"
                                                   class="inline-flex h-7 w-7 items-center justify-center bg-white border border-blue-100 text-blue-500 hover:bg-blue-500 hover:text-white rounded text-[10px] font-bold transition-all shadow-sm" title="Edit Tarif">
                                                    <i class="fas fa-pen text-[10px]"></i>
                                                </a>
                                                
                                                <a href="kelola_tarif.php?aksi=hapus&id=<?= $row['id_tarif']; ?>"
                                                    onclick="return confirm('Yakin ingin menghapus tarif ini?');"
                                                    class="inline-flex h-7 w-7 items-center justify-center bg-white border border-red-100 text-red-500 hover:bg-red-500 hover:text-white rounded text-[10px] font-bold uppercase transition-all shadow-sm" title="Hapus Tarif">
                                                    <i class="fas fa-trash-alt text-[10px]"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                endwhile;
                            } else {
                                echo "<tr><td colspan='3' class='py-8 text-center text-[10px] text-gray-400 italic font-medium uppercase tracking-widest'>Belum ada data tarif yang diatur.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once '../layout/footer.php'; ?>