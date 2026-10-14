<?php
/**
 * jurusan/tambah.php — Halaman Tambah Jurusan
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$error = '';
$success = '';

// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = $_POST['kode_jurusan'] ?? '';
    $nama = $_POST['nama_jurusan'] ?? '';

    if ($kode === '' || $nama === '') {
        $error = 'Field wajib diisi (*): Kode Jurusan, Nama Jurusan.';
    } else {
        $kodeSafe = mysqli_real_escape_string($koneksi, $kode);
        $namaSafe = mysqli_real_escape_string($koneksi, $nama);
        
        // Cek duplikat kode
        $cek = mysqli_query($koneksi, "SELECT id_jurusan FROM jurusan WHERE kode_jurusan = '$kodeSafe'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Kode jurusan sudah terdaftar.';
        } else {
            // Cek duplikat nama
            $cek2 = mysqli_query($koneksi, "SELECT id_jurusan FROM jurusan WHERE nama_jurusan = '$namaSafe'");
            if (mysqli_num_rows($cek2) > 0) {
                $error = 'Nama jurusan sudah terdaftar.';
            } else {
                $sql = "INSERT INTO jurusan (kode_jurusan, nama_jurusan) VALUES ('$kodeSafe', '$namaSafe')";
                if (mysqli_query($koneksi, $sql)) {
                    $success = '✅ Data jurusan berhasil disimpan!';
                } else {
                    $error = 'Gagal menyimpan: ' . mysqli_error($koneksi);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Jurusan - Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>➕ Tambah Jurusan</h1>

    <?php if ($error): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; border: 1px solid #f5c6cb; margin-bottom: 20px;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; border: 1px solid #c3e6cb; margin-bottom: 20px;">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <form action="simpan.php" method="POST">
        <div style="margin-bottom: 20px;">
            <label>Kode Jurusan <span style="color: #666;">*</span></label>
            <input type="text" name="kode_jurusan" required maxlength="10" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" placeholder="Contoh: PPLG">
        </div>

        <div style="margin-bottom: 20px;">
            <label>Nama Jurusan <span style="color: #666;">*</span></label>
            <input type="text" name="nama_jurusan" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" placeholder="Contoh: PPLG">
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="submit" style="padding: 10px 20px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px;">Simpan Data</button>
            <a href="index.php" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 14px;">Batal</a>
        </div>
    </form>
</div>
</body>
</html>