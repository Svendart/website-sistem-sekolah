<?php
/**
 * nilai/hapus.php — Handler Hapus Data Nilai
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$id_nilai = (int) ($_GET['id'] ?? 0);

if ($id_nilai === 0) {
    header("Location: index.php");
    exit;
}

// Hapus data
$sql = "DELETE FROM nilai WHERE id_nilai = $id_nilai";
$query = mysqli_query($koneksi, $sql);

if ($query) {
    header("Location: index.php?pesan=hapus");
    exit;
} else {
    header("Location: index.php?pesan=gagal&error=" . urlencode(mysqli_error($koneksi)));
    exit;
}