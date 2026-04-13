<?php
session_start();
require_once '../config/koneksi.php'; 

if (isset($_SESSION['id_user'])) {
    catat_log($conn, "User Logout");
}

session_unset();
session_destroy();

echo "<script>alert('Anda telah berhasil logout.'); window.location.href='login.php';</script>";
exit;
?>