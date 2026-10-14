<?php
/**
 * jurusan/hapus.php — Handler Hapus Data Jurusan
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$id_jurusan = (int) ($_GET['id'] ?? 0);

if ($id_jurusan === 0) {
    header("Location: index.php");
    exit;
}

// Cek apakah masih digunakan di tabel kelas atau guru
$cekKelas = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kelas WHERE id_jurusan = $id_jurusan");
$totalKelas = mysqli_fetch_assoc($cekKelas)['total'] ?? 0;

$cekGuru = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM guru WHERE id_jurusan = $id_jurusan");
$totalGuru = mysqli_fetch_assoc($cekGuru)['total'] ?? 0;

if ($totalKelas > 0 || $totalGuru > 0) {
    header("Location: index.php?pesan=gagal&error=" . urlencode("Jurusan tidak bisa dihapus karena masih digunakan oleh $totalKelas kelas dan $totalGuru guru."));
    exit;
}

// Hapus data
$sql = "DELETE FROM jurusan WHERE id_jurusan = $id_jurusan";
$query = mysqli_query($koneksi, $sql);

if ($query) {
    header("Location: index.php?pesan=hapus");
    exit;
} else {
    header("Location: index.php?pesan=gagal&error=" . urlencode(mysqli_error($koneksi)));
    exit;
}