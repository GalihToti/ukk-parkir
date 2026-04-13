<?php
session_start();

// Cek apakah user sudah login atau belum
if (isset($_SESSION['role'])) {
    // Jika sudah login, langsung arahkan ke dashboard masing-masing role
    header("Location: " . $_SESSION['role'] . "/index.php");
} else {
    // Jika belum login, otomatis arahkan ke halaman login
    header("Location: auth/login.php");
}
exit;
?>