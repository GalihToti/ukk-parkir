<?php
session_start();
require_once '../config/koneksi.php';

// Cek akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil ID Parkir dari URL
$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

// Query data lengkap untuk struk
$query = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, k.warna, a.nama_area, u.nama_lengkap AS petugas
          FROM tb_ukk_galih_transaksi t
          JOIN tb_ukk_galih_kendaraan k ON t.id_kendaraan = k.id_kendaraan
          JOIN tb_ukk_galih_area_parkir a ON t.id_area = a.id_area
          JOIN tb_ukk_galih_user u ON t.id_user = u.id_user
          WHERE t.id_parkir = '$id'";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Data transaksi tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Parkir - <?= $data['plat_nomor']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background: white;
            }

            .no-print {
                display: none;
            }

            .print-area {
                box-shadow: none;
                border: none;
                margin: 0;
                width: 100%;
            }
        }

        /* Font khusus struk agar lebih otentik */
        .font-receipt {
            font-family: 'Courier New', Courier, monospace;
        }
    </style>
</head>

<body class="bg-gray-100 p-4 sm:p-8 flex flex-col items-center">

    <div class="no-print mb-6 flex gap-3">
        <a href="kendaraan_keluar.php" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">
            ← Kembali
        </a>
        <button onclick="window.print()" class="bg-gray-900 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-green-600 transition-all shadow-md">
            <i class="fas fa-print mr-2"></i> Cetak Struk
        </button>
    </div>

    <div class="print-area bg-white w-full max-w-[350px] border border-gray-200 shadow-lg p-6 font-receipt text-gray-800">

        <div class="text-center border-b border-dashed border-gray-300 pb-4 mb-4">
            <h1 class="text-lg font-black uppercase tracking-widest">E-Parkir Modern</h1>
            <p class="text-[10px] text-gray-500 mt-1">Sistem Parkir Digital SMKN 1 Mejayan</p>
            <p class="text-[10px] text-gray-500 italic">Madiun, Jawa Timur</p>
        </div>

        <div class="text-[11px] space-y-1 mb-4">
            <div class="flex justify-between">
                <span>Waktu Masuk</span>
                <span><?= date('d/m/y H:i', strtotime($data['waktu_masuk'])); ?></span>
            </div>
            <div class="flex justify-between">
                <span>Waktu Keluar</span>
                <span><?= date('d/m/y H:i', strtotime($data['waktu_keluar'])); ?></span>
            </div>
            <div class="flex justify-between border-t border-dashed border-gray-200 pt-1 mt-1 font-bold">
                <span>Durasi</span>
                <span><?= $data['durasi_jam']; ?> Jam</span>
            </div>
        </div>

        <div class="bg-gray-50 p-3 rounded-md mb-4 border border-gray-100">
            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest mb-1 text-center">Nomor Kendaraan</p>
            <h2 class="text-2xl font-black text-center text-gray-900 tracking-[0.2em] uppercase"><?= $data['plat_nomor']; ?></h2>
            <div class="flex justify-center gap-2 mt-1 text-[10px] text-gray-500 capitalize">
                <span><?= $data['jenis_kendaraan']; ?></span>
                <span>•</span>
                <span><?= $data['warna']; ?></span>
            </div>
        </div>

        <div class="space-y-2 border-t border-dashed border-gray-300 pt-4 mb-6">
            <div class="flex justify-between text-sm font-bold">
                <span>TOTAL TAGIHAN</span>
                <span class="text-gray-900">Rp <?= number_format($data['biaya_total'], 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between text-[10px] text-gray-400 italic">
                <span>Status</span>
                <span class="text-green-600 font-bold uppercase">Lunas</span>
            </div>
        </div>

        <div class="text-center space-y-2 border-t border-dashed border-gray-300 pt-4">
            <div class="flex justify-center mb-2">
                <div class="border border-gray-300 px-4 py-1 text-[8px] tracking-[5px] text-gray-400">
                    *<?= $data['id_parkir']; ?>*
                </div>
            </div>
            <p class="text-[10px] font-bold">Terima Kasih</p>
            <p class="text-[9px] text-gray-400 italic">Simpan struk ini sebagai bukti pembayaran resmi.</p>
            <p class="text-[9px] text-gray-400 mt-2 border-t border-gray-100 pt-2 uppercase">Kasir: <?= $data['petugas']; ?></p>
        </div>

    </div>

    <script>
        window.onload = function() {
            // Uncomment baris di bawah jika ingin langsung nge-print saat halaman dibuka
            // window.print();
        };
    </script>

</body>

</html>