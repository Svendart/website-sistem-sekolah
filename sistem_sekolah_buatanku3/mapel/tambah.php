<?php
/**
 * mapel/tambah.php — Halaman Tambah Mata Pelajaran
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$error = '';
$success = '';

// Ambil data jurusan untuk dropdown
$jurusanList = [];
$resJur = mysqli_query($koneksi, "SELECT * FROM jurusan ORDER BY nama_jurusan");
if ($resJur) while ($r = mysqli_fetch_assoc($resJur)) $jurusanList[] = $r;

// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = $_POST['kode_mapel'] ?? '';
    $nama = $_POST['nama_mapel'] ?? '';
    $id_jurusan = (int) ($_POST['id_jurusan'] ?? 0);
    $tingkat = $_POST['tingkat'] ?? 'X';
    $semester = $_POST['semester'] ?? 'Ganjil';
    $jam = (int) ($_POST['jam_per_minggu'] ?? 2);

    if ($kode === '' || $nama === '') {
        $error = 'Field wajib diisi (*): Kode Mapel, Nama Mapel.';
    } else {
        $kodeSafe = mysqli_real_escape_string($koneksi, $kode);
        $namaSafe = mysqli_real_escape_string($koneksi, $nama);
        
        // Cek duplikat kode
        $cek = mysqli_query($koneksi, "SELECT id_mapel FROM mapel WHERE kode_mapel = '$kodeSafe'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Kode mata pelajaran sudah terdaftar.';
        } else {
            $id_jurusanSafe = $id_jurusan ?: 'NULL';
            $tingkatSafe = mysqli_real_escape_string($koneksi, $tingkat);
            $semesterSafe = mysqli_real_escape_string($koneksi, $semester);

            $sql = "INSERT INTO mapel (kode_mapel, nama_mapel, id_jurusan, tingkat, semester, jam_per_minggu) 
                    VALUES ('$kodeSafe', '$namaSafe', $id_jurusanSafe, '$tingkatSafe', '$semesterSafe', $jam)";

            if (mysqli_query($koneksi, $sql)) {
                $success = '✅ Data mata pelajaran berhasil disimpan!';
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
    <title>Tambah Mata Pelajaran - Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>➕ Tambah Mata Pelajaran</h1>

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
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label>Kode Mapel <span style="color: #666;">*</span></label>
                <input type="text" name="kode_mapel" required maxlength="10" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" placeholder="Contoh: MTK">
            </div>
            <div>
                <label>Nama Mapel <span style="color: #666;">*</span></label>
                <input type="text" name="nama_mapel" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" placeholder="Contoh: Matematika">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label>Jurusan (Opsional - Kosongkan untuk mapel umum)</label>
            <select name="id_jurusan" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                <option value="">-- Mapel Umum (Semua Jurusan) --</option>
                <?php
                $resJur = mysqli_query($koneksi, "SELECT * FROM jurusan ORDER BY nama_jurusan");
                if ($resJur) while ($r = mysqli_fetch_assoc($resJur)) {
                    echo '<option value="' . $r['id_jurusan'] . '">' . htmlspecialchars($r['nama_jurusan']) . '</option>';
                }
                ?>
            </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label>Tingkat <span style="color: #666;">*</span></label>
                <select name="tingkat" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="X">X</option>
                    <option value="XI">XI</option>
                    <option value="XII">XII</option>
                </select>
            </div>
            <div>
                <label>Semester <span style="color: #666;">*</span></label>
                <select name="semester" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
            </div>
            <div>
                <label>Jam/Minggu <span style="color: #666;">*</span></label>
                <input type="number" name="jam_per_minggu" value="2" min="1" max="10" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="submit" style="padding: 10px 20px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px;">Simpan Data</button>
            <a href="index.php" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 14px;">Batal</a>
        </div>
    </form>
</div>
</body>
</html>