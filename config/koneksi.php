<?php
date_default_timezone_set('Asia/Jakarta');
$host = "localhost";
$user = "root";
$pass = "";
$db = "galihtotiilhamprayoga_lsp_smkn1mejayan_sch_id";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn){
    die("Koneksi gagal: ". mysqli_connect_error());
}

// catat log aktivitas
function catat_log($conn, $deskripsi_aktivitas) {
    if (isset($_SESSION['id_user'])) {
        $id_user = $_SESSION['id_user'];
        $waktu = date('Y-m-d H:i:s');
        $deskripsi = mysqli_real_escape_string($conn, $deskripsi_aktivitas);
        
        $query_log = "INSERT INTO tb_ukk_galih_log_aktivitas (id_user, aktivitas, waktu_aktivitas) 
                      VALUES ('$id_user', '$deskripsi', '$waktu')";
        mysqli_query($conn, $query_log);
    }
}
?>