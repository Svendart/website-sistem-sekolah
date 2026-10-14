<?php
/**
 * kelas/hapus.php — Handler Hapus Data Kelas
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$id_kelas = (int) ($_GET['id'] ?? 0);

if ($id_kelas === 0) {
    header("Location: index.php");
    exit;
}

// Hapus data
$sql = "DELETE FROM kelas WHERE id_kelas = $id_kelas";
$query = mysqli_query($koneksi, $sql);

if ($query) {
    header("Location: index.php?pesan=hapus");
    exit;
} else {
    header("Location: index.php?pesan=gagal&error=" . urlencode(mysqli_error($koneksi)));
    exit;
}