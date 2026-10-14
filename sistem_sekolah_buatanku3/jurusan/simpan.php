<?php
/**
 * jurusan/simpan.php — Handler Simpan Data Jurusan
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$kode = $_POST['kode_jurusan'] ?? '';
$nama = $_POST['nama_jurusan'] ?? '';

$pesanError = '';
$pesanSukses = '';

if ($kode === '' || $nama === '') {
    $pesanError = 'Field wajib diisi (*): Kode Jurusan, Nama Jurusan.';
} else {
    $kodeSafe = mysqli_real_escape_string($koneksi, $kode);
    $namaSafe = mysqli_real_escape_string($koneksi, $nama);
    
    // Cek duplikat kode
    $cek = mysqli_query($koneksi, "SELECT id_jurusan FROM jurusan WHERE kode_jurusan = '$kodeSafe'");
    if (mysqli_num_rows($cek) > 0) {
        $pesanError = 'Kode jurusan sudah terdaftar.';
    } else {
        // Cek duplikat nama
        $cek2 = mysqli_query($koneksi, "SELECT id_jurusan FROM jurusan WHERE nama_jurusan = '$namaSafe'");
        if (mysqli_num_rows($cek2) > 0) {
            $pesanError = 'Nama jurusan sudah terdaftar.';
        } else {
            $sql = "INSERT INTO jurusan (kode_jurusan, nama_jurusan) VALUES ('$kodeSafe', '$namaSafe')";
            if (mysqli_query($koneksi, $sql)) {
                $pesanSukses = '✅ Data jurusan berhasil disimpan!';
            } else {
                $pesanError = 'Gagal menyimpan: ' . mysqli_error($koneksi);
            }
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