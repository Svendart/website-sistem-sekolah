<?php
/**
 * kelas/simpan.php — Handler Simpan Data Kelas
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$nama_kelas = $_POST['nama_kelas'] ?? '';
$id_jurusan = (int) ($_POST['id_jurusan'] ?? 0);
$id_wali_kelas = (int) ($_POST['id_wali_kelas'] ?? 0);
$tahun_ajaran = $_POST['tahun_ajaran'] ?? '';

$pesanError = '';
$pesanSukses = '';

if ($nama_kelas === '' || $id_jurusan === 0 || $tahun_ajaran === '') {
    $pesanError = 'Field wajib diisi (*): Nama Kelas, Jurusan, Tahun Ajaran.';
} else {
    // Cek duplikat (nama_kelas + tahun_ajaran)
    $namaSafe = mysqli_real_escape_string($koneksi, $nama_kelas);
    $tahunSafe = mysqli_real_escape_string($koneksi, $tahun_ajaran);
    $cek = mysqli_query($koneksi, "SELECT id_kelas FROM kelas WHERE nama_kelas = '$namaSafe' AND tahun_ajaran = '$tahunSafe'");
    if (mysqli_num_rows($cek) > 0) {
        $pesanError = 'Kelas dengan nama dan tahun ajaran yang sama sudah ada.';
    } else {
        $id_jurusanSafe = $id_jurusan ?: 'NULL';
        $id_waliSafe = (int)($_POST['id_wali_kelas'] ?? 0) ?: 'NULL';
        $tahunSafe = mysqli_real_escape_string($koneksi, $tahun_ajaran);

        $sql = "INSERT INTO kelas (nama_kelas, id_jurusan, id_wali_kelas, tahun_ajaran) 
                VALUES ('$namaSafe', $id_jurusanSafe, $id_waliSafe, '$tahunSafe')";

        if (mysqli_query($koneksi, $sql)) {
            $pesanSukses = '✅ Data kelas berhasil disimpan!';
        } else {
            $pesanError = 'Gagal menyimpan: ' . mysqli_error($koneksi);
        }
    }
}

if ($pesanSukses) {
    header("Location: index.php?pesan=sukses");
    exit;
} else {
    header("Location: tambah.php?pesan=gagal&error=" . urlencode($pesanError));
    exit;
}