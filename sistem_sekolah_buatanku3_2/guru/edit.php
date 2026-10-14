<?php
/**
 * guru/edit.php — Halaman Edit Data Guru
 * 
 * Form edit data guru yang sudah ada, dengan pemulihan data dari database.
 */

require_once __DIR__ . '/../config/koneksi.php';

$id_guru = (int) ($_GET['id'] ?? 0);

if ($id_guru === 0) {
    header("Location: index.php");
    exit;
}

// Ambil data guru dari database
$sql = "SELECT g.*, j.nama_jurusan FROM guru g LEFT JOIN jurusan j ON g.id_jurusan = j.id_jurusan WHERE g.id_guru = $id_guru";
$query = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($query) === 0) {
    header("Location: index.php");
    exit;
}

$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Guru - Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>✏️ Edit Data Guru</h1>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'diperbarui'): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; border: 1px solid #c3e6cb; margin-bottom: 20px;">
            ✅ Data guru berhasil diperbarui!
        </div>
    <?php endif; ?>

    <form action="update.php" method="POST">
        <input type="hidden" name="id_guru" value="<?= $data['id_guru'] ?>">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label>NIP</label>
                <input type="text" name="nip" value="<?= htmlspecialchars($data['nip']) ?>" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>
            <div>
                <label>Nama Guru</label>
                <input type="text" name="nama_guru" value="<?= htmlspecialchars($data['nama_guru']) ?>" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                <option value="Laki-Laki" <?= $data['jenis_kelamin'] === 'Laki-Laki' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="Perempuan" <?= $data['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label>Nomor HP</label>
            <input type="tel" name="no_hp" value="<?= htmlspecialchars($data['no_hp'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label>Alamat</label>
            <textarea name="alamat" rows="3" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;"><?= htmlspecialchars($data['alamat'] ?? '') ?></textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label>Tanggal Masuk</label>
            <input type="date" name="tanggal_masuk" value="<?= htmlspecialchars($data['tanggal_masuk']) ?>" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label>Jurusan</label>
            <select name="id_jurusan" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                <option value="">-- Pilih Jurusan --</option>
                <?php
                // Ambil data jurusan dari database
                $resJur = mysqli_query($koneksi, "SELECT * FROM jurusan ORDER BY nama_jurusan");
                if ($resJur) {
                    while ($rowJur = mysqli_fetch_assoc($resJur)) {
                        $selected = ($data['id_jurusan'] == $rowJur['id_jurusan']) ? 'selected' : '';
                        echo '<option value="' . $rowJur['id_jurusan'] . '" ' . $selected . '>' . htmlspecialchars($rowJur['nama_jurusan']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label>Status</label>
            <select name="status" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                <option value="Aktif" <?= $data['status'] === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="Tidak Aktif" <?= $data['status'] === 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="submit" name="update_guru" style="padding: 10px 20px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: simpan;">Simpan Perubahan</button>
            <a href="index.php" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 14px;">Batal</a>
        </div>
    </form>
</div>
</body>
</html>