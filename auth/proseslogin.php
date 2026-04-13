<?php
session_start();
require_once '../config/koneksi.php';

if (isset($_POST['login'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $query = "SELECT * FROM tb_ukk_galih_user WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        //cek status user aktif
        if ($user['status_aktif']==1) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];

            //catat log aktivitas
            $id_user = $user['id_user'];
            $waktu = date('Y-m-d H:i:s');
            $log_query = "INSERT INTO tb_ukk_galih_log_aktivitas (id_user, aktivitas, waktu_aktivitas) VALUES ('$id_user', 'User Login', '$waktu')";
            mysqli_query($conn, $log_query);
            
            //mengarah ke folder
            if ($user['role']=='admin') {
                header("Location: ../admin/index.php");
            } elseif ($user['role']=='petugas') {
                header("Location: ../petugas/index.php");
            } elseif ($user['role']=='owner') {
                header("Location: ../owner/index.php");
            }
            exit;
        } else {
            $_SESSION['error'] = "Akun anda tidak aktif. Hubungi Admin.";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Username atau Password salah!";
        header("Location: login.php");
        exit;
    } 
} else {
    header("Location: login.php");
    exit;
}
?>