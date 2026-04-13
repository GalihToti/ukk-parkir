<?php
require_once '../layout/header.php';
require_once '../config/koneksi.php';

// hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Anda tidak memiliki akses!'); window.history.back();</script>";
    exit;
}

$pesan = '';
$tipe_pesan = '';
$icon = '';

// 1. Tambah Area
if (isset($_POST['tambah_area'])) {
    $nama_area = mysqli_real_escape_string($conn, $_POST['nama_area']);
    $kapasitas = (int)$_POST['kapasitas'];

    $query_tambah = "INSERT INTO tb_ukk_galih_area_parkir (nama_area, kapasitas, terisi) VALUES ('$nama_area', '$kapasitas', 0)";

    catat_log($conn, "Tambah Area: $nama_area");

    if (mysqli_query($conn, $query_tambah)) {
        $pesan = "Area Parkir baru berhasil ditambahkan!";
        $tipe_pesan = "bg-green-50 text-green-700 border-green-200";
        $icon = "fa-check-circle text-green-500";
    } else {
        $pesan = "Gagal menambahkan data: " . mysqli_error($conn);
        $tipe_pesan = "bg-red-50 text-red-700 border-red-200";
        $icon = "fa-exclamation-circle text-red-500";
    }
}

// 2. Edit Area
if (isset($_POST['update_area'])) {
    $id_area_edit = mysqli_real_escape_string($conn, $_POST['id_area']);
    $nama_area_edit = mysqli_real_escape_string($conn, $_POST['nama_area']);
    $kapasitas_edit = (int)$_POST['kapasitas'];

    // Cek agar kapasitas tidak kurang dari yang sudah terisi
    $cek_isi = mysqli_query($conn, "SELECT terisi FROM tb_ukk_galih_area_parkir WHERE id_area = '$id_area_edit'");
    $data_isi = mysqli_fetch_assoc($cek_isi);

    catat_log($conn, "Edit Area: $nama_area_edit");

    if ($kapasitas_edit < $data_isi['terisi']) {
        $pesan = "Gagal! Kapasitas tidak boleh lebih kecil dari kendaraan yang sedang parkir ({$data_isi['terisi']} unit).";
        $tipe_pesan = "bg-red-50 text-red-700 border-red-200";
        $icon = "fa-exclamation-circle text-red-500";
    } else {
        $query_update = "UPDATE tb_ukk_galih_area_parkir SET nama_area = '$nama_area_edit', kapasitas = '$kapasitas_edit' WHERE id_area = '$id_area_edit'";
        
        if (mysqli_query($conn, $query_update)) {
            $pesan = "Area Parkir berhasil diperbarui!";
            $tipe_pesan = "bg-blue-50 text-blue-700 border-blue-200";
            $icon = "fa-check-circle text-blue-500";
        } else {
            $pesan = "Gagal memperbarui data: " . mysqli_error($conn);
            $tipe_pesan = "bg-red-50 text-red-700 border-red-200";
            $icon = "fa-exclamation-circle text-red-500";
        }
    }
}

// 3. Hapus Area
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['id']);

    $cek_isi = mysqli_query($conn, "SELECT terisi FROM tb_ukk_galih_area_parkir WHERE id_area = '$id_hapus'");
    $data_isi = mysqli_fetch_assoc($cek_isi);

    catat_log($conn, "Hapus Area: $id_hapus");

    if ($data_isi['terisi'] > 0) {
        $pesan = "Gagal dihapus! Area ini masih memiliki kendaraan yang parkir.";
        $tipe_pesan = "bg-red-50 text-red-700 border-red-200";
        $icon = "fa-exclamation-circle text-red-500";
    } else {
        $hapus = mysqli_query($conn, "DELETE FROM tb_ukk_galih_area_parkir WHERE id_area = '$id_hapus'");
        if ($hapus) {
            echo "<script>alert('Area berhasil dihapus!'); window.location.href='kelola_area.php';</script>";
        }
    }
}

// 4. Mengambil Data untuk Form EDIT
$area_edit = null;
if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
    $id_edit = mysqli_real_escape_string($conn, $_GET['id']);
    $query_edit = mysqli_query($conn, "SELECT * FROM tb_ukk_galih_area_parkir WHERE id_area = '$id_edit'");
    $area_edit = mysqli_fetch_assoc($query_edit);
}

// Mengambil semua data area
$query_area = mysqli_query($conn, "SELECT * FROM tb_ukk_galih_area_parkir ORDER BY nama_area ASC");
?>

<div class="max-w-6xl mx-auto space-y-4">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 no-print">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-gray-900 flex items-center gap-2">
                Kelola Area Parkir
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">Manajemen kapasitas dan tata letak blok.</p>
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
            
            <?php if ($area_edit) : ?>
                <div class="bg-white p-4 rounded-md shadow-sm border border-blue-200 relative overflow-hidden animate-in fade-in duration-300">
                    <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                    <h2 class="text-[11px] font-bold text-gray-900 uppercase tracking-widest mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                        <i class="fas fa-pen text-blue-500"></i> Edit Area Parkir
                    </h2>
                    <form action="kelola_area.php" method="POST" class="space-y-3">
                        <input type="hidden" name="id_area" value="<?= $area_edit['id_area']; ?>">
                        
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 mb-1.5 uppercase tracking-tight">Nama Area / Blok</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-map-marker-alt text-gray-400 text-[10px]"></i>
                                </div>
                                <input type="text" name="nama_area" value="<?= $area_edit['nama_area']; ?>" placeholder="Cth: Lantai 1 / Area VIP" class="w-full h-8 pl-8 pr-3 bg-gray-50 border border-gray-200 rounded text-xs focus:bg-white focus:ring-1 focus:ring-blue-500 outline-none transition-all text-gray-900 font-bold uppercase placeholder:font-normal placeholder:normal-case" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 mb-1.5 uppercase tracking-tight">Kapasitas Maksimal</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-car-side text-gray-400 text-[10px]"></i>
                                </div>
                                <input type="number" min="1" name="kapasitas" value="<?= $area_edit['kapasitas']; ?>" placeholder="50" class="w-full h-8 pl-8 pr-3 bg-gray-50 border border-gray-200 rounded text-xs focus:bg-white focus:ring-1 focus:ring-blue-500 outline-none transition-all text-gray-900 font-bold" required>
                            </div>
                        </div>
                        
                        <div class="flex gap-2 mt-2 pt-1">
                            <button type="submit" name="update_area" class="flex-1 h-8 flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold rounded text-[10px] uppercase tracking-widest transition-colors shadow-sm gap-2">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="kelola_area.php" class="h-8 px-4 flex items-center justify-center bg-gray-100 hover:bg-gray-200 border border-gray-200 text-gray-600 font-bold rounded text-[10px] uppercase tracking-widest transition-colors shadow-sm" title="Batal Edit">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            <?php else : ?>
                <div class="bg-white p-4 rounded-md shadow-sm border border-gray-200">
                    <h2 class="text-[11px] font-bold text-gray-900 uppercase tracking-widest mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                        Tambah Area Baru
                    </h2>
                    <form action="kelola_area.php" method="POST" class="space-y-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 mb-1.5 uppercase tracking-tight">Nama Area / Blok</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-map-marker-alt text-gray-400 text-[10px]"></i>
                                </div>
                                <input type="text" name="nama_area" placeholder="Cth: Lantai 1 / Area VIP" class="w-full h-8 pl-8 pr-3 bg-gray-50 border border-gray-200 rounded text-xs focus:bg-white focus:ring-1 focus:ring-green-500 outline-none transition-all text-gray-900 font-bold uppercase placeholder:font-normal placeholder:normal-case" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 mb-1.5 uppercase tracking-tight">Kapasitas Maksimal</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-car-side text-gray-400 text-[10px]"></i>
                                </div>
                                <input type="number" min="1" name="kapasitas" placeholder="50" class="w-full h-8 pl-8 pr-3 bg-gray-50 border border-gray-200 rounded text-xs focus:bg-white focus:ring-1 focus:ring-green-500 outline-none transition-all text-gray-900 font-bold" required>
                            </div>
                        </div>
                        <button type="submit" name="tambah_area" class="w-full h-8 flex items-center justify-center bg-gray-900 hover:bg-green-600 text-white font-bold rounded text-[10px] uppercase tracking-widest transition-colors shadow-sm mt-1 gap-2">
                            <i class="fas fa-save"></i> Simpan Area
                        </button>
                    </form>
                </div>
            <?php endif; ?>

        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-md shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50">
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500">Nama Area</th>
                                <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 text-center">Kapasitas</th>
                                <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 text-center">Terisi</th>
                                <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 text-center">Sisa Slot</th>
                                <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php
                            if (mysqli_num_rows($query_area) > 0) {
                                while ($row = mysqli_fetch_assoc($query_area)) :
                                    $sisa = $row['kapasitas'] - $row['terisi'];
                            ?>
                                    <tr class="group transition-colors hover:bg-gray-50/50">
                                        <td class="px-4 py-2.5">
                                            <code class="text-[11px] font-bold text-gray-900 bg-gray-100 border border-gray-200 px-2 py-0.5 rounded uppercase tracking-wide">
                                                <?= $row['nama_area']; ?>
                                            </code>
                                        </td>
                                        <td class="px-4 py-2.5 text-center">
                                            <span class="text-xs font-semibold text-gray-500"><?= $row['kapasitas']; ?></span>
                                        </td>
                                        <td class="px-4 py-2.5 text-center">
                                            <?php if ($row['terisi'] > 0): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-700 border border-red-100">
                                                    <?= $row['terisi']; ?> Unit
                                                </span>
                                            <?php else: ?>
                                                <span class="text-[10px] text-gray-400 italic">Kosong</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2.5 text-center">
                                            <?php if ($sisa <= 0): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                                    Penuh
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700 border border-green-100">
                                                    <?= $sisa; ?> Slot
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2.5 text-right">
                                            <div class="flex justify-end gap-1.5">
                                                <a href="kelola_area.php?aksi=edit&id=<?= $row['id_area']; ?>"
                                                   class="inline-flex h-7 w-7 items-center justify-center bg-white border border-blue-100 text-blue-500 hover:bg-blue-500 hover:text-white rounded text-[10px] font-bold transition-all shadow-sm" title="Edit ">
                                                    <i class="fas fa-pen text-[10px]"></i>
                                                </a>
                                                
                                                <a href="kelola_area.php?aksi=hapus&id=<?= $row['id_area']; ?>"
                                                    onclick="return confirm('Yakin ingin menghapus area ini? Pastikan kosong terlebih dahulu.');"
                                                    class="inline-flex h-7 w-7 items-center justify-center bg-white border border-red-100 text-red-500 hover:bg-red-500 hover:text-white rounded text-[10px] font-bold transition-all shadow-sm" title="Hapus">
                                                    <i class="fas fa-trash-alt text-[10px]"></i> 
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                endwhile;
                            } else {
                                echo "<tr><td colspan='5' class='py-8 text-center text-[10px] text-gray-400 italic font-medium uppercase tracking-widest'>Belum ada data area parkir.</td></tr>";
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