<?php
require_once '../layout/header.php';
require_once '../config/koneksi.php';

if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak!'); window.history.back();</script>";
    exit;
}

// 1. Edit Kendaraan
if (isset($_POST['update_data'])) {
    $id_parkir = mysqli_real_escape_string($conn, $_POST['id_parkir']);
    $id_kendaraan = mysqli_real_escape_string($conn, $_POST['id_kendaraan']);
    $plat_nomor = mysqli_real_escape_string($conn, $_POST['plat_nomor']);
    $pemilik = mysqli_real_escape_string($conn, $_POST['pemilik']);
    $jenis_kendaraan = mysqli_real_escape_string($conn, $_POST['jenis_kendaraan']);
    $warna = mysqli_real_escape_string($conn, $_POST['warna']);

    $query_update = "UPDATE tb_ukk_galih_kendaraan 
                     SET plat_nomor = '$plat_nomor', pemilik = '$pemilik', jenis_kendaraan = '$jenis_kendaraan', warna = '$warna' 
                     WHERE id_kendaraan = '$id_kendaraan'";

    catat_log($conn, "Edit Kendaraan Plat: $plat_nomor");

    if (mysqli_query($conn, $query_update)) {
        echo "<script>alert('Data kendaraan berhasil diperbarui!'); window.location.href='kelola_kendaraan.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui data!');</script>";
    }
}

// 2. Hapus Transaksi
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['id']);

    // Cek status transaksi, jika masih 'masuk', kembalikan kapasitas area
    $cek_transaksi = mysqli_query($conn, "SELECT id_area, status FROM tb_ukk_galih_transaksi WHERE id_parkir = '$id_hapus'");
    if ($row_cek = mysqli_fetch_assoc($cek_transaksi)) {
        if ($row_cek['status'] == 'masuk') {
            mysqli_query($conn, "UPDATE tb_ukk_galih_area_parkir SET terisi = terisi - 1 WHERE id_area = '{$row_cek['id_area']}'");
        }
    }

    catat_log($conn, "Hapus Transaksi: $id_hapus");

    $hapus = mysqli_query($conn, "DELETE FROM tb_ukk_galih_transaksi WHERE id_parkir = '$id_hapus'");
    if ($hapus) {
        echo "<script>alert('Data transaksi berhasil dihapus!'); window.location.href='kelola_kendaraan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!'); window.location.href='kelola_kendaraan.php';</script>";
    }
}

// 3. Mengambil Data untuk Form EDIT
$data_edit = null;
if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
    $id_edit = mysqli_real_escape_string($conn, $_GET['id']);
    $query_edit = mysqli_query($conn, "
        SELECT t.id_parkir, k.* FROM tb_ukk_galih_transaksi t 
        JOIN tb_ukk_galih_kendaraan k ON t.id_kendaraan = k.id_kendaraan 
        WHERE t.id_parkir = '$id_edit'
    ");
    $data_edit = mysqli_fetch_assoc($query_edit);
}

// Fitur Filter & Pencarian
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Bangun Query SQL
$query = "SELECT k.*, t.id_parkir, t.id_area, t.status, t.waktu_masuk, t.waktu_keluar, a.nama_area 
          FROM tb_ukk_galih_transaksi t
          JOIN tb_ukk_galih_kendaraan k ON t.id_kendaraan = k.id_kendaraan
          JOIN tb_ukk_galih_area_parkir a ON t.id_area = a.id_area WHERE 1=1";

if ($filter == 'parkir') {
    $query .= " AND t.status = 'masuk'";
} elseif ($filter == 'keluar') {
    $query .= " AND t.status = 'keluar'";
}

if ($search) {
    $query .= " AND k.plat_nomor LIKE '%$search%'";
}

$query .= " ORDER BY t.id_parkir DESC";
$result = mysqli_query($conn, $query);
?>

<div class="max-w-7xl mx-auto space-y-5 pb-10">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 no-print">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Data Kendaraan</h2>
            <p class="text-xs text-gray-500 mt-0.5">Histori transaksi parkir real-time.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <div class="flex bg-gray-100 p-1 rounded-md border border-gray-200">
                <a href="?filter=semua" class="px-3 py-1 text-[10px] font-bold rounded <?= $filter == 'semua' ? 'bg-white shadow-sm text-green-600' : 'text-gray-500' ?>">SEMUA</a>
                <a href="?filter=parkir" class="px-3 py-1 text-[10px] font-bold rounded <?= $filter == 'parkir' ? 'bg-white shadow-sm text-green-600' : 'text-gray-500' ?>">PARKIR</a>
                <a href="?filter=keluar" class="px-3 py-1 text-[10px] font-bold rounded <?= $filter == 'keluar' ? 'bg-white shadow-sm text-green-600' : 'text-gray-500' ?>">KELUAR</a>
            </div>

            <form action="" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="filter" value="<?= $filter; ?>">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]"></i>
                    <input type="text" name="search" value="<?= $search; ?>" placeholder="Cari Plat..."
                        class="h-8 w-32 md:w-40 rounded-md border border-gray-200 bg-white pl-8 pr-3 py-1 text-xs outline-none focus:ring-1 focus:ring-green-500">
                </div>
                <button type="submit" class="h-8 bg-gray-900 text-white px-3 rounded-md text-xs font-medium hover:bg-gray-800 transition-colors">
                    Cari
                </button>
            </form>
        </div>
    </div>

    <?php if ($data_edit): ?>
        <div class="bg-white rounded-xl shadow-sm border border-blue-200 p-5 animate-in slide-in-from-top-4 duration-500 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
            <h3 class="text-xs font-bold text-gray-900 mb-4 uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-pen text-blue-500"></i> Edit Data Kendaraan
            </h3>

            <form action="kelola_kendaraan.php" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <input type="hidden" name="id_parkir" value="<?= $data_edit['id_parkir']; ?>">
                <input type="hidden" name="id_kendaraan" value="<?= $data_edit['id_kendaraan']; ?>">

                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Plat Nomor</label>
                    <input type="text" name="plat_nomor" value="<?= $data_edit['plat_nomor']; ?>" class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none text-xs font-bold text-gray-900 uppercase transition-all" required autocomplete="off">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Nama Pemilik</label>
                    <input type="text" name="pemilik" value="<?= $data_edit['pemilik']; ?>" class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none text-xs font-medium text-gray-700 transition-all" placeholder="Kosongkan jika tidak ada" autocomplete="off">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Jenis Kendaraan</label>
                    <select name="jenis_kendaraan" class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none text-xs font-semibold text-gray-700 transition-all appearance-none cursor-pointer" required>
                        <option value="motor" <?= ($data_edit['jenis_kendaraan'] == 'motor') ? 'selected' : ''; ?>>🏍️ Motor</option>
                        <option value="mobil" <?= ($data_edit['jenis_kendaraan'] == 'mobil') ? 'selected' : ''; ?>>🚗 Mobil</option>
                        <option value="lainnya" <?= ($data_edit['jenis_kendaraan'] == 'lainnya') ? 'selected' : ''; ?>>🚚 Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Warna Fisik</label>
                    <input type="text" name="warna" value="<?= $data_edit['warna']; ?>" class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none text-xs font-medium text-gray-700 transition-all" required autocomplete="off">
                </div>

                <div class="flex gap-2">
                    <button type="submit" name="update_data" class="h-10 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-[10px] uppercase tracking-widest flex-1 transition-colors shadow-sm">
                        Simpan
                    </button>
                    <a href="kelola_kendaraan.php" class="h-10 px-4 bg-gray-100 hover:bg-gray-200 border border-gray-200 text-gray-600 font-bold rounded-lg text-[10px] uppercase tracking-widest flex items-center justify-center transition-colors shadow-sm" title="Batal">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <div class="rounded-md border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 w-10 text-center">#</th>
                        <th class="px-4 py-3 text-[11px] font-semibold text-gray-900">Plat & Pemilik</th>
                        <th class="px-4 py-3 text-[11px] font-semibold text-gray-900 text-center">Unit Kendaraan</th>
                        <th class="px-4 py-3 text-[11px] font-semibold text-gray-900 text-center">Area</th>
                        <th class="px-4 py-3 text-[11px] font-semibold text-gray-900 text-center">Status</th>
                        <th class="px-4 py-3 text-[11px] font-semibold text-gray-900 text-center">Waktu Masuk</th>
                        <th class="px-4 py-3 text-[11px] font-semibold text-gray-900 text-center">Waktu Keluar</th>
                        <th class="px-4 py-3 text-[11px] font-semibold text-gray-900 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($result) > 0) :
                        while ($row = mysqli_fetch_assoc($result)) :
                            $status_class = ($row['status'] == 'masuk')
                                ? "bg-green-50 text-green-700 ring-green-600/20"
                                : "bg-gray-50 text-gray-500 ring-gray-500/10";
                    ?>
                            <tr class="group transition-colors hover:bg-gray-50/50">
                                <td class="px-4 py-2.5 text-[10px] text-gray-400 text-center font-mono">
                                    <?= str_pad($no++, 2, '0', STR_PAD_LEFT); ?>
                                </td>

                                <td class="px-4 py-2.5">
                                    <span class="text-xs font-black tracking-widest text-gray-900 uppercase bg-gray-100 border border-gray-200 px-2 py-0.5 rounded shadow-sm">
                                        <?= $row['plat_nomor']; ?>
                                    </span>
                                    <p class="text-[10px] text-gray-500 mt-1.5 font-medium flex items-center gap-1.5">
                                        <i class="far fa-user text-gray-400"></i> <?= !empty($row['pemilik']) ? $row['pemilik'] : '<span class="italic opacity-60">Tidak diketahui</span>'; ?>
                                    </p>
                                </td>

                                <td class="px-4 py-2.5 text-center">
                                    <div class="flex flex-col items-center justify-center gap-0.5">
                                        <span class="text-xs font-bold text-gray-700 capitalize">
                                            <?= ($row['jenis_kendaraan'] == 'motor') ? '🏍️' : (($row['jenis_kendaraan'] == 'mobil') ? '🚗' : '🚚'); ?> <?= $row['jenis_kendaraan']; ?>
                                        </span>
                                        <span class="text-[9px] text-gray-400 italic capitalize"><?= $row['warna']; ?></span>
                                    </div>
                                </td>

                                <td class="px-4 py-2.5 text-center">
                                    <code class="text-[10px] font-medium text-blue-600 bg-blue-50/50 px-2 py-0.5 rounded border border-blue-100">
                                        <?= $row['nama_area']; ?>
                                    </code>
                                </td>

                                <td class="px-4 py-2.5 text-center">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold uppercase tracking-tight ring-1 ring-inset <?= $status_class; ?>">
                                        <?= ($row['status'] == 'masuk') ? '🟢 Parkir' : '⚪ Keluar'; ?>
                                    </span>
                                </td>

                                <td class="px-4 py-2.5 text-center font-mono text-[10px] text-gray-700 leading-tight">
                                    <p class="font-bold text-gray-900"><?= date('d M Y', strtotime($row['waktu_masuk'])); ?></p>
                                    <p class="opacity-70"><?= date('H:i', strtotime($row['waktu_masuk'])); ?> WIB</p>
                                </td>

                                <td class="px-4 py-2.5 text-center font-mono text-[10px] text-gray-700 leading-tight">
                                    <?php if ($row['status'] == 'keluar') : ?>
                                        <p class="font-bold text-gray-900"><?= date('d M Y', strtotime($row['waktu_keluar'])); ?></p>
                                        <p class="opacity-70"><?= date('H:i', strtotime($row['waktu_keluar'])); ?> WIB</p>
                                    <?php else : ?>
                                        <span class="text-gray-300 font-bold text-lg">-</span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-4 py-2.5 text-right">
                                    <div class="flex justify-end gap-1.5">
                                        <a href="kelola_kendaraan.php?aksi=edit&id=<?= $row['id_parkir']; ?>"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-white text-[10px] font-bold text-blue-500 border border-blue-100 shadow-sm transition-all hover:bg-blue-500 hover:text-white" title="Edit Data">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <a href="kelola_kendaraan.php?aksi=hapus&id=<?= $row['id_parkir']; ?>"
                                            onclick="return confirm('Yakin ingin menghapus data kendaraan ini secara permanen?');"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-white text-[10px] font-bold text-red-500 border border-red-100 shadow-sm transition-all hover:bg-red-500 hover:text-white" title="Hapus Data">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile;
                    else : ?>
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-[11px] text-gray-400 italic">Data transaksi tidak ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../layout/footer.php'; ?>