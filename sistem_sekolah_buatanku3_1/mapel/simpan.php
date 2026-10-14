<?php
/**
 * mapel/simpan.php — Handler Simpan Data Mata Pelajaran
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$kode = $_POST['kode_mapel'] ?? '';
$nama = $_POST['nama_mapel'] ?? '';
$id_jurusan = (int) ($_POST['id_jurusan'] ?? 0);
$tingkat = $_POST['tingkat'] ?? 'X';
$semester = $_POST['semester'] ?? 'Ganjil';
$jam = (int) ($_POST['jam_per_minggu'] ?? 2);

$pesanError = '';
$pesanSukses = '';

if ($kode === '' || $nama === '') {
    $pesanError = 'Field wajib diisi (*): Kode Mapel, Nama Mapel.';
} else {
    $kodeSafe = mysqli_real_escape_string($koneksi, $kode);
    $namaSafe = mysqli_real_escape_string($koneksi, $nama);
    
    // Cek duplikat kode
    $cek = mysqli_query($koneksi, "SELECT id_mapel FROM mapel WHERE kode_mapel = '$kodeSafe'");
    if (mysqli_num_rows($cek) > 0) {
        $pesanError = 'Kode mata pelajaran sudah terdaftar.';
    } else {
        $id_jurusanSafe = $id_jurusan ?: 'NULL';
        $tingkatSafe = mysqli_real_escape_string($koneksi, $tingkat);
        $semesterSafe = mysqli_real_escape_string($koneksi, $semester);

        $sql = "INSERT INTO mapel (kode_mapel, nama_mapel, id_jurusan, tingkat, semester, jam_per_minggu) 
                VALUES ('$kodeSafe', '$namaSafe', $id_jurusanSafe, '$tingkatSafe', '$semesterSafe', $jam)";

        if (mysqli_query($koneksi, $sql)) {
            $pesanSukses = '✅ Data mata pelajaran berhasil disimpan!';
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