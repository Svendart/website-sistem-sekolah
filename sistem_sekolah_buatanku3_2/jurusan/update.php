<?php
/**
 * jurusan/update.php — Handler Update Data Jurusan
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$id_jurusan = (int) ($_POST['id_jurusan'] ?? 0);
$kode = $_POST['kode_jurusan'] ?? '';
$nama = $_POST['nama_jurusan'] ?? '';

$pesanError = '';
$pesanSukses = '';

if ($id_jurusan === 0) {
    $pesanError = 'ID jurusan tidak valid.';
} elseif ($kode === '' || $nama === '') {
    $pesanError = 'Field wajib diisi (*): Kode Jurusan, Nama Jurusan.';
} else {
    $kodeSafe = mysqli_real_escape_string($koneksi, $kode);
    $namaSafe = mysqli_real_escape_string($koneksi, $nama);
    
    // Cek duplikat kode (kecuali data sendiri)
    $cek = mysqli_query($koneksi, "SELECT id_jurusan FROM jurusan WHERE kode_jurusan = '$kodeSafe' AND id_jurusan != $id_jurusan");
    if (mysqli_num_rows($cek) > 0) {
        $pesanError = 'Kode jurusan sudah terpakai.';
    } else {
        // Cek duplikat nama (kecuali data sendiri)
        $cek2 = mysqli_query($koneksi, "SELECT id_jurusan FROM jurusan WHERE nama_jurusan = '$namaSafe' AND id_jurusan != $id_jurusan");
        if (mysqli_num_rows($cek2) > 0) {
            $pesanError = 'Nama jurusan sudah terpakai.';
        } else {
            $sql = "UPDATE jurusan SET kode_jurusan = '$kodeSafe', nama_jurusan = '$namaSafe' WHERE id_jurusan = $id_jurusan";
            if (mysqli_query($koneksi, $sql)) {
                $pesanSukses = '✅ Data jurusan berhasil diperbarui!';
            } else {
                $pesanError = 'Gagal memperbarui: ' . mysqli_error($koneksi);
            }
        }
    }
}

if ($pesanSukses) {
    header("Location: index.php?pesan=diperbarui");
    exit;
} else {
    header("Location: edit.php?id=$id_jurusan&pesan=gagal&error=" . urlencode($pesanError));
    exit;
}