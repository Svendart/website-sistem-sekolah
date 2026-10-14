<?php

require_once "../config/session.php";
require_login();

include "../config/koneksi.php";

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || !isset($_POST['id'])
    || !is_string($_POST['id'])
    || !ctype_digit($_POST['id'])
) {
    header("Location: index.php");
    exit;
}

$id = $_POST['id'];

// Ambil foto sebelum dihapus
$fotoPath = null;
$res = mysqli_query($koneksi, "SELECT foto FROM siswa WHERE id_siswa = '" . mysqli_real_escape_string($koneksi, $id) . "'");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $fotoPath = $row['foto'] ?? null;
}

$sql = "DELETE FROM siswa WHERE id_siswa = '$id'";
$query = mysqli_query($koneksi, $sql);

if ($query) {
    // Hapus file foto jika ada
    if ($fotoPath && file_exists(__DIR__ . '/../../' . $fotoPath)) {
        unlink(__DIR__ . '/../../' . $fotoPath);
    }
    header("Location: index.php?pesan=hapus");
    exit;
} else {
    echo "Data gagal dihapus: " . mysqli_error($koneksi);
}

?>