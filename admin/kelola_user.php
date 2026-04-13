<?php
require_once '../layout/header.php';
require_once '../config/koneksi.php';

// Pastikan hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Anda tidak memiliki akses!'); window.history.back();</script>";
    exit;
}

// 1. Logika untuk TAMBAH USER BARU
if (isset($_POST['tambah_user'])) {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username     = mysqli_real_escape_string($conn, $_POST['username']);
    $password     = md5(mysqli_real_escape_string($conn, $_POST['password'])); // Menggunakan MD5 standar UKK
    $role         = mysqli_real_escape_string($conn, $_POST['role']);

    // Cek apakah username sudah digunakan
    $cek_user = mysqli_query($conn, "SELECT id_user FROM tb_ukk_galih_user WHERE username = '$username'");

    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>alert('Gagal! Username sudah terdaftar, silakan gunakan username lain.'); window.history.back();</script>";
        exit;
    } else {
        $query_tambah = "INSERT INTO tb_ukk_galih_user (nama_lengkap, username, password, role, status_aktif) 
                         VALUES ('$nama_lengkap', '$username', '$password', '$role', '1')";

        if (mysqli_query($conn, $query_tambah)) {
            catat_log($conn, "Tambah Akun User Baru: $username");
            echo "<script>alert('Pengguna baru berhasil ditambahkan!'); window.location.href='kelola_user.php';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal menambahkan pengguna!');</script>";
        }
    }
}

// 2. Logika untuk UPDATE/EDIT
if (isset($_POST['update_user'])) {
    $id_user_edit = mysqli_real_escape_string($conn, $_POST['id_user']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username     = mysqli_real_escape_string($conn, $_POST['username']);
    $role         = mysqli_real_escape_string($conn, $_POST['role']);

    $query_update = "UPDATE tb_ukk_galih_user 
                     SET nama_lengkap = '$nama_lengkap', username = '$username', role = '$role' 
                     WHERE id_user = '$id_user_edit'";

    if (mysqli_query($conn, $query_update)) {
        catat_log($conn, "Edit Akun User: $username");
        echo "<script>alert('Data pengguna berhasil diperbarui!'); window.location.href='kelola_user.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui data!');</script>";
    }
}

// 3. Logika untuk mengubah status aktif 
if (isset($_GET['aksi']) && $_GET['aksi'] == 'ubah_status') {
    $id_target = mysqli_real_escape_string($conn, $_GET['id']);
    $status_baru = mysqli_real_escape_string($conn, $_GET['status']);

    if ($id_target == $_SESSION['id_user'] && $status_baru == '0') {
        echo "<script>alert('Anda tidak bisa menonaktifkan akun Anda sendiri!'); window.location.href='kelola_user.php';</script>";
    } else {
        $update = mysqli_query($conn, "UPDATE tb_ukk_galih_user SET status_aktif = '$status_baru' WHERE id_user = '$id_target'");
        if ($update) {
            $status_teks = ($status_baru == '1') ? 'Mengaktifkan' : 'Menonaktifkan';
            catat_log($conn, "$status_teks Akun User ID: $id_target");
            echo "<script>alert('Status diperbarui!'); window.location.href='kelola_user.php';</script>";
        }
    }
}

// 4. Logika untuk HAPUS USER
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['id']);

    // Cek agar tidak menghapus diri sendiri
    if ($id_hapus == $_SESSION['id_user']) {
        echo "<script>alert('Anda tidak bisa menghapus akun Anda sendiri!'); window.location.href='kelola_user.php';</script>";
    } else {
        $hapus = mysqli_query($conn, "DELETE FROM tb_ukk_galih_user WHERE id_user = '$id_hapus'");
        if ($hapus) {
            catat_log($conn, "Hapus Akun User ID: $id_hapus.");
            echo "<script>alert('Pengguna berhasil dihapus!'); window.location.href='kelola_user.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus data! User mungkin sudah memiliki histori transaksi.'); window.location.href='kelola_user.php';</script>";
        }
    }
}

// 5. Logika untuk MENGAMBIL DATA saat tombol Edit ditekan
$user_edit = null;
if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
    $id_edit = mysqli_real_escape_string($conn, $_GET['id']);
    $query_edit = mysqli_query($conn, "SELECT * FROM tb_ukk_galih_user WHERE id_user = '$id_edit'");
    $user_edit = mysqli_fetch_assoc($query_edit);
}

// Menampilkan semua user
$query_users = mysqli_query($conn, "SELECT * FROM tb_ukk_galih_user ORDER BY role ASC, nama_lengkap ASC");
?>

<div class="max-w-7xl mx-auto space-y-5 pb-10">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 no-print">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Kelola Pengguna</h2>
            <p class="text-xs text-gray-500 mt-0.5">Manajemen akses sistem dan status akun.</p>
        </div>

        <a href="index.php" class="inline-flex h-8 items-center justify-center rounded-md border border-gray-200 bg-white px-3 py-1 text-xs font-medium shadow-sm transition-colors hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-1 focus:ring-green-500 w-full md:w-auto">
            <i class="fas fa-arrow-left mr-2 text-gray-400 text-[10px]"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <div class="lg:col-span-1">
            <?php if ($user_edit): ?>
                <div class="bg-white rounded-md shadow-sm border border-blue-200 p-5 animate-in slide-in-from-top-4 duration-500 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                    <h3 class="text-[11px] font-bold text-gray-900 mb-4 uppercase tracking-widest flex items-center gap-2 border-b border-gray-100 pb-3">
                        <i class="fas fa-user-edit text-blue-500"></i> Edit Data Pengguna
                    </h3>

                    <form action="kelola_user.php" method="POST" class="space-y-4">
                        <input type="hidden" name="id_user" value="<?= $user_edit['id_user']; ?>">

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="<?= $user_edit['nama_lengkap']; ?>" class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none text-xs font-semibold text-gray-700 transition-all" required autocomplete="off">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Username</label>
                            <input type="text" name="username" value="<?= $user_edit['username']; ?>" class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none text-xs font-semibold text-gray-700 transition-all" required autocomplete="off">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Hak Akses (Role)</label>
                            <div class="relative">
                                <select name="role" class="w-full h-10 pl-3 pr-8 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none text-xs font-semibold text-gray-700 transition-all appearance-none cursor-pointer" required>
                                    <option value="owner" <?= ($user_edit['role'] == 'owner') ? 'selected' : ''; ?>>👑 Owner</option>
                                    <option value="admin" <?= ($user_edit['role'] == 'admin') ? 'selected' : ''; ?>>🛡️ Admin</option>
                                    <option value="petugas" <?= ($user_edit['role'] == 'petugas') ? 'selected' : ''; ?>>🧑‍💻 Petugas</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="submit" name="update_user" class="h-10 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-[10px] uppercase tracking-widest flex-1 transition-colors shadow-sm flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="kelola_user.php" class="h-10 px-4 bg-gray-100 hover:bg-gray-200 border border-gray-200 text-gray-600 font-bold rounded-lg text-[10px] uppercase tracking-widest flex items-center justify-center transition-colors shadow-sm" title="Batal">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>


            <?php else: ?>
                <div class="bg-white rounded-md shadow-sm border border-gray-200 p-5">
                    <h3 class="text-[11px] font-bold text-gray-900 mb-4 uppercase tracking-widest flex items-center gap-2 border-b border-gray-100 pb-3">
                        <i class="fas fa-user-plus text-gray-400"></i> Tambah Pengguna Baru
                    </h3>

                    <form action="kelola_user.php" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" placeholder="Masukkan nama" class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-md focus:bg-white focus:ring-2 focus:ring-green-500 outline-none text-xs font-semibold text-gray-700 transition-all" required autocomplete="off">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Username</label>
                            <input type="text" name="username" placeholder="Masukkan username" class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-md focus:bg-white focus:ring-2 focus:ring-green-500 outline-none text-xs font-semibold text-gray-700 transition-all" required autocomplete="off">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Password</label>
                            <input type="password" name="password" placeholder="••••••••" class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-md focus:bg-white focus:ring-2 focus:ring-green-500 outline-none text-xs font-semibold text-gray-700 transition-all" required>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Hak Akses (Role)</label>
                            <div class="relative">
                                <select name="role" class="w-full h-10 pl-3 pr-8 bg-gray-50 border border-gray-200 rounded-md focus:bg-white focus:ring-2 focus:ring-green-500 outline-none text-xs font-semibold text-gray-700 transition-all appearance-none cursor-pointer" required>
                                    <option value="" disabled selected>-- Pilih Role --</option>
                                    <option value="owner">👑 Owner</option>
                                    <option value="admin">🛡️ Admin</option>
                                    <option value="petugas">🧑‍💻 Petugas</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" name="tambah_user" class="w-full h-10 bg-gray-900 hover:bg-green-600 text-white font-bold rounded-md text-[10px] uppercase tracking-widest transition-colors shadow-sm flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i> Tambah User
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <div class="lg:col-span-2">
            <div class="rounded-md border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50">
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 w-10 text-center">No.</th>
                                <th class="px-4 py-3 text-[11px] font-semibold text-gray-900">Nama Lengkap</th>
                                <th class="px-4 py-3 text-[11px] font-semibold text-gray-900">Username</th>
                                <th class="px-4 py-3 text-[11px] font-semibold text-gray-900 text-center">Role</th>
                                <th class="px-4 py-3 text-[11px] font-semibold text-gray-900 text-center">Status</th>
                                <th class="px-4 py-3 text-[11px] font-semibold text-gray-900 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query_users)) :
                                $status_class = ($row['status_aktif'] == 1)
                                    ? "bg-green-50 text-green-700 ring-green-600/20"
                                    : "bg-gray-50 text-gray-600 ring-gray-500/10";
                            ?>
                                <tr class="group transition-colors hover:bg-gray-50/50">

                                    <td class="px-4 py-3 rounded-full text-[10px] text-gray-400 text-center font-mono">
                                        <?= str_pad($no++, 2, '0', STR_PAD_LEFT); ?>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-900 text-[10px] font-bold text-white shadow-sm">
                                                <?= strtoupper(substr($row['nama_lengkap'], 0, 1)); ?>
                                            </div>
                                            <span class="text-xs font-semibold text-gray-900"><?= $row['nama_lengkap']; ?></span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <code class="text-[10px] font-bold text-blue-600 bg-blue-50/50 px-2 py-0.5 rounded border border-blue-100">
                                            @<?= $row['username']; ?>
                                        </code>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-tighter border <?= ($row['role'] == 'admin') ? 'border-blue-200 text-blue-700 bg-blue-50/30' : (($row['role'] == 'owner') ? 'border-green-200 text-green-700 bg-green-50/30' : 'border-orange-200 text-orange-700 bg-orange-50/30') ?>">
                                            <?= strtoupper($row['role']); ?>
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest ring-1 ring-inset <?= $status_class; ?>">
                                            <?= ($row['status_aktif'] == 1) ? '🟢 Aktif' : '🔴 Off'; ?>
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-1.5">
                                            <a href="kelola_user.php?aksi=edit&id=<?= $row['id_user']; ?>"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-white text-[10px] font-bold text-blue-500 border border-blue-100 shadow-sm transition-all hover:bg-blue-500 hover:text-white" title="Edit Data">
                                                <i class="fas fa-pen"></i>
                                            </a>

                                            <a href="kelola_user.php?aksi=hapus&id=<?= $row['id_user']; ?>"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus user ini secara permanen?');"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-white text-[10px] font-bold text-red-500 border border-red-100 shadow-sm transition-all hover:bg-red-500 hover:text-white" title="Hapus User">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>

                                            <?php if ($row['status_aktif'] == 1) : ?>
                                                <a href="kelola_user.php?aksi=ubah_status&id=<?= $row['id_user']; ?>&status=0"
                                                    onclick="return confirm('Suspend akun ini?');"
                                                    class="inline-flex h-7 items-center justify-center rounded-md bg-white px-2.5 text-[9px] uppercase tracking-widest font-bold text-red-500 border border-red-100 shadow-sm transition-all hover:bg-red-500 hover:text-white" title="Nonaktifkan">
                                                    OFF
                                                </a>
                                            <?php else : ?>
                                                <a href="kelola_user.php?aksi=ubah_status&id=<?= $row['id_user']; ?>&status=1"
                                                    class="inline-flex h-7 items-center justify-center rounded-md bg-white px-2.5 text-[9px] uppercase tracking-widest font-bold text-green-500 border border-green-100 shadow-sm transition-all hover:bg-green-500 hover:text-white" title="Aktifkan">
                                                    ON
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once '../layout/footer.php'; ?>