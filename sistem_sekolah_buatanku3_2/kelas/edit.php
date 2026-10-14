<?php
/**
 * kelas/edit.php — Halaman Edit Data Kelas
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$id_kelas = (int) ($_GET['id'] ?? 0);

if ($id_kelas === 0) {
    header("Location: index.php");
    exit;
}

// Ambil data kelas
$sql = "SELECT k.*, j.nama_jurusan, g.nama_guru as wali_kelas 
        FROM kelas k 
        LEFT JOIN jurusan j ON k.id_jurusan = j.id_jurusan 
        LEFT JOIN guru g ON k.id_wali_kelas = g.id_guru 
        WHERE k.id_kelas = $id_kelas";
$query = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($query) === 0) {
    header("Location: index.php");
    exit;
}

$data = mysqli_fetch_assoc($query);

// Ambil data untuk dropdown
$jurusanList = [];
$resJur = mysqli_query($koneksi, "SELECT * FROM jurusan ORDER BY nama_jurusan");
if ($resJur) while ($r = mysqli_fetch_assoc($resJur)) $jurusanList[] = $r;

$guruList = [];
$resGuru = mysqli_query($koneksi, "SELECT * FROM guru WHERE status = 'Aktif' ORDER BY nama_guru");
if ($resGuru) while ($r = mysqli_fetch_assoc($resGuru)) $guruList[] = $r;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kelas - Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>✏️ Edit Data Kelas</h1>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'diperbarui'): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; border: 1px solid #c3e6cb; margin-bottom: 20px;">
            ✅ Data kelas berhasil diperbarui!
        </div>
    <?php endif; ?>

    <form action="update.php" method="POST">
        <input type="hidden" name="id_kelas" value="<?= $data['id_kelas'] ?>">

        <div style="margin-bottom: 20px;">
            <label>Nama Kelas <span style="color: #666;">*</span></label>
            <input type="text" name="nama_kelas" value="<?= htmlspecialchars($data['nama_kelas']) ?>" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label>Jurusan <span style="color: #666;">*</span></label>
            <select name="id_jurusan" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                <option value="">-- Pilih Jurusan --</option>
                <?php foreach ($jurusanList as $j): ?>
                    <option value="<?= $j['id_jurusan'] ?>" <?= $data['id_jurusan'] == $j['id_jurusan'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($j['nama_jurusan']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label>Wali Kelas</label>
            <select name="id_wali_kelas" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                <option value="">-- Pilih Wali Kelas (Opsional) --</option>
                <?php
                $resGuru = mysqli_query($koneksi, "SELECT * FROM guru WHERE status = 'Aktif' ORDER BY nama_guru");
                if ($resGuru) {
                    while ($g = mysqli_fetch_assoc($resGuru)) {
                        $selected = ($data['id_wali_kelas'] == $g['id_guru']) ? 'selected' : '';
                        echo '<option value="' . $g['id_guru'] . '" ' . $selected . '>' . htmlspecialchars($g['nama_guru']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label>Tahun Ajaran <span style="color: #666;">*</span></label>
            <input type="text" name="tahun_ajaran" value="<?= htmlspecialchars($data['tahun_ajaran']) ?>" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" placeholder="Contoh: 2024/2025">
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="submit" style="padding: 10px 20px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px;">Simpan Perubahan</button>
            <a href="index.php" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 14px;">Batal</a>
        </div>
    </form>
</div>
</body>
</html>