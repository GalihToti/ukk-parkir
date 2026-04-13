<?php
require_once '../layout/header.php';
require_once '../config/koneksi.php';

// Hanya petugas yang bisa akses
if ($_SESSION['role'] !== 'petugas') {
    echo "<script>alert('Anda tidak memiliki akses!'); window.location.href='../auth/login.php';</script>";
    exit;
}

$pesan = '';
$tipe_pesan = '';
$icon_pesan = '';

//jika submit ditekan
if (isset($_POST['simpan_kendaraan'])) {
    $plat_nomor = mysqli_real_escape_string($conn, $_POST['plat_nomor']);
    $jenis_kendaraan = $_POST['jenis_kendaraan'];
    $warna = mysqli_real_escape_string($conn, $_POST['warna']);
    $pemilik = mysqli_real_escape_string($conn, $_POST['pemilik']);
    $id_area = $_POST['id_area'];
    $id_user = $_SESSION['id_user'];
    $waktu_masuk = date('Y-m-d H:i:s');

    //1. Cek Kapasitas Area Parkir
    $cek_area = mysqli_query($conn, "SELECT * FROM tb_ukk_galih_area_parkir WHERE id_area = '$id_area'");
    $data_area = mysqli_fetch_assoc($cek_area);

    if ($data_area['terisi'] >= $data_area['kapasitas']) {
        $pesan = "Mohon Maaf, Area Parkir " . $data_area['nama_area'] . " Penuh!";
        $tipe_pesan = "bg-red-50 border-red-200 text-red-700";
        $icon_pesan = "fa-ban text-red-500";
    } else {
        // 2. Simpan ke tabel kendaraan
        $query_kendaraan = "INSERT INTO tb_ukk_galih_kendaraan (plat_nomor, jenis_kendaraan, warna, pemilik, id_user) 
                            VALUES ('$plat_nomor', '$jenis_kendaraan', '$warna', '$pemilik', '$id_user')";

        if (mysqli_query($conn, $query_kendaraan)) {
            $id_kendaraan = mysqli_insert_id($conn);

            // 3. Simpan ke tabel transaksi
            $query_transaksi = "INSERT INTO tb_ukk_galih_transaksi (id_kendaraan, waktu_masuk, status, id_user, id_area) 
                                VALUES ('$id_kendaraan', '$waktu_masuk', 'masuk', '$id_user', '$id_area')";

            if (mysqli_query($conn, $query_transaksi)) {
                // 4. Update jumlah terisi ke tabel area_parkir (+1)
                mysqli_query($conn, "UPDATE tb_ukk_galih_area_parkir SET terisi = terisi + 1 WHERE id_area = '$id_area'");

                // Menampilkan pesan sukses
                $pesan = "Berhasil! Kendaraan $plat_nomor tercatat masuk.";
                catat_log($conn, "Kendaraan Masuk: $plat_nomor");
                $tipe_pesan = "bg-green-50 border-green-200 text-green-700";
                $icon_pesan = "fa-check-circle text-green-500";
            } else {
                $pesan = "Gagal menyimpan transaksi: " . mysqli_error($conn);
                $tipe_pesan = "bg-red-50 border-red-200 text-red-700";
                $icon_pesan = "fa-exclamation-triangle text-red-500";
            }
        } else {
            $pesan = "Gagal menyimpan data kendaraan: " . mysqli_error($conn);
            $tipe_pesan = "bg-red-50 border-red-200 text-red-700";
            $icon_pesan = "fa-exclamation-triangle text-red-500";
        }
    }
}


// Mengambil Data Tarif untuk Kalkulasi Real-time
$query_tarif = mysqli_query($conn, "SELECT jenis_kendaraan, tarif_per_jam FROM tb_ukk_galih_tarif");
$daftar_tarif = [];
while ($row_tarif = mysqli_fetch_assoc($query_tarif)) {
    $daftar_tarif[$row_tarif['jenis_kendaraan']] = $row_tarif['tarif_per_jam'];
}

// Untuk data di tabel, mengambil data yg statusnya masuk
$query_parkir = mysqli_query($conn, "
    SELECT t.*, k.plat_nomor, k.jenis_kendaraan, k.warna, a.nama_area 
    FROM tb_ukk_galih_transaksi t
    JOIN tb_ukk_galih_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    JOIN tb_ukk_galih_area_parkir a ON t.id_area = a.id_area
    WHERE t.status = 'masuk'
    ORDER BY t.waktu_masuk DESC 
    LIMIT 20
");
?>

<div class="max-w-5xl mx-auto space-y-4 pb-8">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 animate-in slide-in-from-top-2 duration-500">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-gray-900">Kendaraan Masuk</h1>
            <p class="text-[11px] text-gray-400">Isi form untuk menerbitkan tiket parkir</p>
        </div>
    </div>

    <?php if ($pesan != '') : ?>
        <div class="border px-4 py-2 rounded-md relative flex items-center gap-2 <?= $tipe_pesan; ?> shadow-sm animate-in fade-in duration-300" role="alert">
            <i class="fas <?= $icon_pesan; ?> text-sm"></i>
            <span class="block sm:inline font-bold text-[10px] uppercase tracking-widest"><?= $pesan; ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden animate-in slide-in-from-bottom-4 duration-500">

        <div class="bg-gray-50/50 px-6 py-3 border-b border-gray-100">
            <h2 class="text-xs font-bold text-gray-800 flex items-center gap-2 uppercase tracking-widest">
                <i class="fas fa-file-signature text-gray-400"></i> Input Data Kendaraan
            </h2>
        </div>

        <form action="" method="POST" class="p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8">

                <div class="md:col-span-5 space-y-6">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">1. Plat Nomor</label>
                        <div class="relative group">
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-green-400 to-blue-500 rounded-xl blur opacity-0 group-focus-within:opacity-30 transition duration-500"></div>
                            <input type="text" name="plat_nomor" placeholder="AE 1234 XX"
                                class="relative w-full h-12 px-4 bg-white border-2 border-gray-200 rounded-xl focus:border-green-500 outline-none transition-all text-center text-2xl font-black text-gray-900 uppercase tracking-[0.15em] placeholder:font-normal placeholder:tracking-normal placeholder:text-gray-300 shadow-sm" required autofocus autocomplete="off">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-2 text-center font-medium">Ketik tanpa spasi atau dengan spasi.</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">2. Lokasi Parkir</label>
                        <div class="relative">
                            <select name="id_area" class="w-full h-12 pl-4 pr-10 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all text-sm appearance-none cursor-pointer text-gray-700 font-semibold" required>
                                <option value="" disabled selected>-- Pilih Area / Blok --</option>
                                <?php
                                $area = mysqli_query($conn, "SELECT * FROM tb_ukk_galih_area_parkir");
                                while ($row = mysqli_fetch_assoc($area)) {
                                    $sisa = $row['kapasitas'] - $row['terisi'];
                                    $disabled = ($sisa <= 0) ? "disabled" : "";
                                    $status_teks = ($sisa <= 0) ? "🔴 Penuh" : "🟢 Sisa $sisa";
                                    echo "<option value='{$row['id_area']}' $disabled>{$row['nama_area']} - $status_teks</option>";
                                }
                                ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden md:block md:col-span-1 flex justify-center">
                    <div class="w-px h-full bg-gradient-to-b from-transparent via-gray-200 to-transparent"></div>
                </div>

                <div class="md:col-span-6 space-y-5">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-1">3. Detail Fisik & Pemilik</label>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5">Jenis Kendaraan</label>
                            <div class="relative">
                                <select name="jenis_kendaraan" class="w-full h-12 pl-4 pr-8 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-green-500 outline-none transition-all text-sm appearance-none cursor-pointer text-gray-700 font-medium" required>
                                    <option value="motor">🏍️ Motor</option>
                                    <option value="mobil">🚗 Mobil</option>
                                    <option value="lainnya">🚚 Truk/Bus/Lainnya</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5">Warna Fisik</label>
                            <input type="text" name="warna" placeholder="Cth: Hitam / Merah" class="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-green-500 outline-none transition-all text-xs text-gray-700 placeholder-gray-400 font-medium" required autocomplete="off">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 flex justify-between">
                            <span>Nama Pemilik</span>
                            <span class="text-gray-400 italic font-normal">*Opsional</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="far fa-user text-gray-400 text-sm"></i>
                            </div>
                            <input type="text" name="pemilik" placeholder="Kosongkan jika tamu/tidak diketahui" class="w-full h-12 pl-11 pr-4 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-green-500 outline-none transition-all text-xs text-gray-700 placeholder-gray-400 font-medium" autocomplete="off">
                        </div>
                    </div>

                    <div class="pt-4 mt-auto">
                        <button type="submit" name="simpan_kendaraan" class="w-full flex items-center justify-center h-12 bg-gray-900 text-white font-bold rounded-xl overflow-hidden transition-all shadow-sm hover:shadow-md hover:bg-green-600 focus:scale-[0.98]">
                            <div class="flex items-center gap-2 text-xs uppercase tracking-widest">
                                <i class="fas fa-print text-sm"></i>
                                <span>Simpan & Rekam Kendaraan Masuk</span>
                            </div>
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <div class="pt-4 animate-in slide-in-from-bottom-8 duration-700 delay-100">
        <div class="flex items-end justify-between mb-4">
            <div>
                <h2 class="text-sm font-bold text-gray-900 tracking-tight uppercase">Kendaraan Sedang Parkir</h2>
                <p class="text-xs text-gray-500 italic mt-0.5">Daftar kendaraan yang sedang parkir.</p>
            </div>
            <div class="text-[10px] font-bold text-orange-600 bg-orange-50 px-3 py-1.5 rounded-lg border border-orange-100 flex items-center gap-2">
                <i class="fas fa-clock animate-pulse"></i> Tarif Otomatis Berjalan
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/50">
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500">Plat Nomor</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 text-center">Fisik</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 text-center">Area</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 text-center">Waktu Masuk</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 text-center">Durasi</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-tight text-gray-500 text-right">Tarif Sementara</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <?php
                        if (mysqli_num_rows($query_parkir) > 0) :
                            $waktu_sekarang = time();
                            while ($row = mysqli_fetch_assoc($query_parkir)) :
                                $ikon = ($row['jenis_kendaraan'] == 'motor') ? '🏍️' : '🚗';

                                // Algoritma Menghitung Durasi Parkir Real-time
                                $waktu_masuk_str = strtotime($row['waktu_masuk']);
                                $selisih_detik = $waktu_sekarang - $waktu_masuk_str;
                                $durasi_jam = ceil($selisih_detik / 3600); // Pembulatan ke atas
                                if ($durasi_jam <= 0) $durasi_jam = 1; // Minimal 1 jam

                                // Kalkulasi Tarif Real-time
                                $tarif_per_jam = isset($daftar_tarif[$row['jenis_kendaraan']]) ? $daftar_tarif[$row['jenis_kendaraan']] : 2000;
                                $tarif_sementara = $durasi_jam * $tarif_per_jam;
                        ?>
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-4 py-2.5">
                                        <span class="text-xs font-black tracking-widest text-gray-900 uppercase bg-gray-100 border border-gray-200 px-2 py-0.5 rounded shadow-sm group-hover:bg-white transition-colors">
                                            <?= $row['plat_nomor']; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <div class="flex flex-col items-center gap-0.5">
                                            <span class="text-[10px] text-gray-700 font-bold capitalize">
                                                <?= $ikon; ?> <?= $row['jenis_kendaraan']; ?>
                                            </span>
                                            <span class="text-[9px] text-gray-400 capitalize"><?= $row['warna']; ?></span>
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
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="text-[10px] font-bold text-orange-600 bg-orange-50 border border-orange-100 px-2 py-0.5 rounded">
                                            <?= $durasi_jam; ?> Jam
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-right">
                                        <code class="text-[10px] font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded">
                                            Rp <?= number_format($tarif_sementara, 0, ',', '.'); ?>
                                        </code>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else : ?>
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-[11px] text-gray-400 italic uppercase tracking-widest font-bold">Area Kosong. Belum ada kendaraan yang parkir saat ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../layout/footer.php'; ?>