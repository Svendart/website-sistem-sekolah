<?php
/**
 * mapel/edit.php — Halaman Edit Data Mata Pelajaran
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$id_mapel = (int) ($_GET['id'] ?? 0);

if ($id_mapel === 0) {
    header("Location: index.php");
    exit;
}

// Ambil data mapel
$sql = "SELECT m.*, j.nama_jurusan FROM mapel m LEFT JOIN jurusan j ON m.id_jurusan = j.id_jurusan WHERE m.id_mapel = $id_mapel";
$query = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($query) === 0) {
    header("Location: index.php");
    exit;
}

$data = mysqli_fetch_assoc($query);

// Ambil data jurusan untuk dropdown
$jurusanList = [];
$resJur = mysqli_query($koneksi, "SELECT * FROM jurusan ORDER BY nama_jurusan");
if ($resJur) while ($r = mysqli_fetch_assoc($resJur)) $jurusanList[] = $r;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Mata Pelajaran - Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>✏️ Edit Mata Pelajaran</h1>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'diperbarui'): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; border: 1px solid #c3e6cb; margin-bottom: 20px;">
            ✅ Data mata pelajaran berhasil diperbarui!
        </div>
    <?php endif; ?>

    <form action="update.php" method="POST">
        <input type="hidden" name="id_mapel" value="<?= $data['id_mapel'] ?>">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label>Kode Mapel <span style="color: #666;">*</span></label>
                <input type="text" name="kode_mapel" value="<?= htmlspecialchars($data['kode_mapel']) ?>" required maxlength="10" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>
            <div>
                <label>Nama Mapel <span style="color: #666;">*</span></label>
                <input type="text" name="nama_mapel" value="<?= htmlspecialchars($data['nama_mapel']) ?>" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label>Jurusan (Opsional - Kosongkan untuk mapel umum)</label>
            <select name="id_jurusan" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                <option value="">-- Mapel Umum (Semua Jurusan) --</option>
                <?php
                $resJur = mysqli_query($koneksi, "SELECT * FROM jurusan ORDER BY nama_jurusan");
                if ($resJur) while ($r = mysqli_fetch_assoc($resJur)) {
                    $selected = ($data['id_jurusan'] == $r['id_jurusan']) ? 'selected' : '';
                    echo '<option value="' . $r['id_jurusan'] . '" ' . $selected . '>' . htmlspecialchars($r['nama_jurusan']) . '</option>';
                }
                ?>
            </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label>Tingkat <span style="color: #666;">*</span></label>
                <select name="tingkat" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="X" <?= $data['tingkat'] === 'X' ? 'selected' : '' ?>>X</option>
                    <option value="XI" <?= $data['tingkat'] === 'XI' ? 'selected' : '' ?>>XI</option>
                    <option value="XII" <?= $data['tingkat'] === 'XII' ? 'selected' : '' ?>>XII</option>
                </select>
            </div>
            <div>
                <label>Semester <span style="color: #666;">*</span></label>
                <select name="semester" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="Ganjil" <?= $data['semester'] === 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                    <option value="Genap" <?= $data['semester'] === 'Genap' ? 'selected' : '' ?>>Genap</option>
                </select>
            </div>
            <div>
                <label>Jam/Minggu <span style="color: #666;">*</span></label>
                <input type="number" name="jam_per_minggu" value="<?= (int)$data['jam_per_minggu'] ?>" min="1" max="10" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="submit" style="padding: 10px 20px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px;">Simpan Perubahan</button>
            <a href="index.php" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 14px;">Batal</a>
        </div>
    </form>
</div>
</body>
</html>