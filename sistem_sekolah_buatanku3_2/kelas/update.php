<?php
/**
 * kelas/update.php — Handler Update Data Kelas
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$id_kelas = (int) ($_POST['id_kelas'] ?? 0);
$nama_kelas = $_POST['nama_kelas'] ?? '';
$id_jurusan = (int) ($_POST['id_jurusan'] ?? 0);
$id_wali_kelas = (int) ($_POST['id_wali_kelas'] ?? 0);
$tahun_ajaran = $_POST['tahun_ajaran'] ?? '';

$pesanError = '';
$pesanSukses = '';

if ($id_kelas === 0) {
    $pesanError = 'ID kelas tidak valid.';
} elseif ($nama_kelas === '' || $id_jurusan === 0 || $tahun_ajaran === '') {
    $pesanError = 'Field wajib diisi (*): Nama Kelas, Jurusan, Tahun Ajaran.';
} else {
    // Cek duplikat (kecuali data sendiri)
    $namaSafe = mysqli_real_escape_string($koneksi, $nama_kelas);
    $tahunSafe = mysqli_real_escape_string($koneksi, $tahun_ajaran);
    $cek = mysqli_query($koneksi, "SELECT id_kelas FROM kelas WHERE nama_kelas = '$namaSafe' AND tahun_ajaran = '$tahunSafe' AND id_kelas != $id_kelas");
    if (mysqli_num_rows($cek) > 0) {
        $pesanError = 'Kelas dengan nama dan tahun ajaran yang sama sudah ada.';
    } else {
        $id_jurusanSafe = $id_jurusan;
        $id_waliSafe = $id_wali_kelas ?: 'NULL';
        $tahunSafe = mysqli_real_escape_string($koneksi, $tahun_ajaran);

        $sql = "UPDATE kelas SET 
                nama_kelas = '$namaSafe', 
                id_jurusan = $id_jurusanSafe, 
                id_wali_kelas = $id_waliSafe, 
                tahun_ajaran = '$tahunSafe' 
                WHERE id_kelas = $id_kelas";

        if (mysqli_query($koneksi, $sql)) {
            $pesanSukses = '✅ Data kelas berhasil diperbarui!';
        } else {
            $pesanError = 'Gagal memperbarui: ' . mysqli_error($koneksi);
        }
    }
}

if ($pesanSukses) {
    header("Location: index.php?pesan=diperbarui");
    exit;
} else {
    header("Location: edit.php?id=$id_kelas&pesan=gagal&error=" . urlencode($pesanError));
    exit;
}