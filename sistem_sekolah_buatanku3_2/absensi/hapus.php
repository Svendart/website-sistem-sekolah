<?php
/**
 * absensi/hapus.php — Handler Hapus Data Absensi
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$id_absensi = (int) ($_GET['id'] ?? 0);

if ($id_absensi === 0) {
    header("Location: index.php");
    exit;
}

// Hapus data
$sql = "DELETE FROM absensi WHERE id_absensi = $id_absensi";
$query = mysqli_query($koneksi, $sql);

if ($query) {
    header("Location: index.php?pesan=hapus");
    exit;
} else {
    header("Location: index.php?pesan=gagal&error=" . urlencode(mysqli_error($koneksi)));
    exit;
}