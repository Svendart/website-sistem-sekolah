<?php
/**
 * mapel/hapus.php — Handler Hapus Data Mata Pelajaran
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$id_mapel = (int) ($_GET['id'] ?? 0);

if ($id_mapel === 0) {
    header("Location: index.php");
    exit;
}

// Hapus data
$sql = "DELETE FROM mapel WHERE id_mapel = $id_mapel";
$query = mysqli_query($koneksi, $sql);

if ($query) {
    header("Location: index.php?pesan=hapus");
    exit;
} else {
    header("Location: index.php?pesan=gagal&error=" . urlencode(mysqli_error($koneksi)));
    exit;
}