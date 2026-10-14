<?php
/**
 * kelas/tambah.php — Halaman Tambah Kelas
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$error = '';
$success = '';

// Ambil data untuk dropdown
$jurusanList = [];
$resJur = mysqli_query($koneksi, "SELECT * FROM jurusan ORDER BY nama_jurusan");
if ($resJur) while ($r = mysqli_fetch_assoc($resJur)) $jurusanList[] = $r;

$guruList = [];
$resGuru = mysqli_query($koneksi, "SELECT * FROM guru WHERE status = 'Aktif' ORDER BY nama_guru");
if ($resGuru) while ($r = mysqli_fetch_assoc($resGuru)) $guruList[] = $r;

// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kelas = $_POST['nama_kelas'] ?? '';
    $id_jurusan = (int) ($_POST['id_jurusan'] ?? 0);
    $id_wali_kelas = (int) ($_POST['id_wali_kelas'] ?? 0);
    $tahun_ajaran = $_POST['tahun_ajaran'] ?? '2024/2025';

    if ($nama_kelas === '' || $id_jurusan === 0 || $tahun_ajaran === '') {
        $error = 'Field wajib diisi (*): Nama Kelas, Jurusan, Tahun Ajaran.';
    } else {
        // Cek duplikat (nama_kelas + tahun_ajaran)
        $namaSafe = mysqli_real_escape_string($koneksi, $nama_kelas);
        $tahunSafe = mysqli_real_escape_string($koneksi, $tahun_ajaran);
        $cek = mysqli_query($koneksi, "SELECT id_kelas FROM kelas WHERE nama_kelas = '$namaSafe' AND tahun_ajaran = '$tahunSafe'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Kelas dengan nama dan tahun ajaran yang sama sudah ada.';
        } else {
            $id_jurusanSafe = $id_jurusan ?: 'NULL';
            $id_waliSafe = $id_wali_kelas ?: 'NULL';
            $tahunSafe = mysqli_real_escape_string($koneksi, $tahun_ajaran);

            $sql = "INSERT INTO kelas (nama_kelas, id_jurusan, id_wali_kelas, tahun_ajaran) 
                    VALUES ('$namaSafe', $id_jurusanSafe, $id_waliSafe, '$tahunSafe')";

            if (mysqli_query($koneksi, $sql)) {
                $success = '✅ Data kelas berhasil disimpan!';
            } else {
                $error = 'Gagal menyimpan: ' . mysqli_error($koneksi);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kelas - Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>➕ Tambah Kelas</h1>

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
            <label>Nama Kelas <span style="color: #666;">*</span></label>
            <input type="text" name="nama_kelas" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" placeholder="Contoh: X, XI, XII">
        </div>

        <div style="margin-bottom: 20px;">
            <label>Jurusan <span style="color: #666;">*</span></label>
            <select name="id_jurusan" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                <option value="">-- Pilih Jurusan --</option>
                <?php foreach ($jurusanList as $j): ?>
                    <option value="<?= $j['id_jurusan'] ?>"><?= htmlspecialchars($j['nama_jurusan']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label>Wali Kelas</label>
            <select name="id_wali_kelas" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                <option value="">-- Pilih Wali Kelas (Opsional) --</option>
                <?php foreach ($guruList as $g): ?>
                    <option value="<?= $g['id_guru'] ?>"><?= htmlspecialchars($g['nama_guru']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label>Tahun Ajaran <span style="color: #666;">*</span></label>
            <input type="text" name="tahun_ajaran" value="2024/2025" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" placeholder="Contoh: 2024/2025">
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="submit" style="padding: 10px 20px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px;">Simpan Data</button>
            <a href="index.php" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 14px;">Batal</a>
        </div>
    </form>
</div>
</body>
</html>