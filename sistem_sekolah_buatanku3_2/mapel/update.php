<?php
/**
 * mapel/update.php — Handler Update Data Mata Pelajaran
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$id_mapel = (int) ($_POST['id_mapel'] ?? 0);
$kode = $_POST['kode_mapel'] ?? '';
$nama = $_POST['nama_mapel'] ?? '';
$id_jurusan = (int) ($_POST['id_jurusan'] ?? 0);
$tingkat = $_POST['tingkat'] ?? 'X';
$semester = $_POST['semester'] ?? 'Ganjil';
$jam = (int) ($_POST['jam_per_minggu'] ?? 2);

$pesanError = '';
$pesanSukses = '';

if ($id_mapel === 0) {
    $pesanError = 'ID mata pelajaran tidak valid.';
} elseif ($kode === '' || $nama === '') {
    $pesanError = 'Field wajib diisi (*): Kode Mapel, Nama Mapel.';
} else {
    $kodeSafe = mysqli_real_escape_string($koneksi, $kode);
    $namaSafe = mysqli_real_escape_string($koneksi, $nama);
    
    // Cek duplikat kode (kecuali data sendiri)
    $cek = mysqli_query($koneksi, "SELECT id_mapel FROM mapel WHERE kode_mapel = '$kodeSafe' AND id_mapel != $id_mapel");
    if (mysqli_num_rows($cek) > 0) {
        $pesanError = 'Kode mata pelajaran sudah terpakai.';
    } else {
        $id_jurusanSafe = $id_jurusan ?: 'NULL';
        $tingkatSafe = mysqli_real_escape_string($koneksi, $tingkat);
        $semesterSafe = mysqli_real_escape_string($koneksi, $semester);

        $sql = "UPDATE mapel SET 
                kode_mapel = '$kodeSafe', 
                nama_mapel = '$namaSafe', 
                id_jurusan = $id_jurusanSafe, 
                tingkat = '$tingkatSafe', 
                semester = '$semesterSafe', 
                jam_per_minggu = $jam 
                WHERE id_mapel = $id_mapel";

        if (mysqli_query($koneksi, $sql)) {
            $pesanSukses = '✅ Data mata pelajaran berhasil diperbarui!';
        } else {
            $pesanError = 'Gagal memperbarui: ' . mysqli_error($koneksi);
        }
    }
}

if ($pesanSukses) {
    header("Location: index.php?pesan=diperbarui");
    exit;
} else {
    header("Location: edit.php?id=$id_mapel&pesan=gagal&error=" . urlencode($pesanError));
    exit;
}