<?php
/**
 * guru/hapus.php — Handler Hapus Data Guru
 * 
 * Proses penghapusan data guru dari sistem
 */

require_once __DIR__ . '/../config/koneksi.php';

$id_guru = (int) ($_GET['id'] ?? 0);

if ($id_guru === 0) {
    header("Location: index.php");
    exit;
}

// Ambil data foto lama sebelum dihapus (untuk cleanup)
$sql = "SELECT foto FROM guru WHERE id_guru = $id_guru";
$query = mysqli_query($koneksi, $sql);
$fotoLama = '';
if ($query && $row = mysqli_fetch_assoc($query)) {
    $fotoLama = $row['foto'] ?? '';
}

// Hapus data dari database
$sql = "DELETE FROM guru WHERE id_guru = $id_guru";
$query = mysqli_query($koneksi, $sql);

if ($query) {
    // Hapus file foto jika ada
    if ($fotoLama && file_exists(__DIR__ . '/../' . $fotoLama)) {
        unlink(__DIR__ . '/../' . $fotoLama);
    }
    header("Location: index.php?pesan=hapus");
    exit;
} else {
    header("Location: index.php?pesan=gagal&error=" . urlencode(mysqli_error($koneksi)));
    exit;
}