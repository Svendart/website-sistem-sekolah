<?php
/**
 * jurusan/edit.php — Halaman Edit Data Jurusan
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$id_jurusan = (int) ($_GET['id'] ?? 0);

if ($id_jurusan === 0) {
    header("Location: index.php");
    exit;
}

// Ambil data jurusan
$sql = "SELECT * FROM jurusan WHERE id_jurusan = $id_jurusan";
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
    <title>Edit Jurusan - Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>✏️ Edit Jurusan</h1>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'diperbarui'): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; border: 1px solid #c3e6cb; margin-bottom: 20px;">
            ✅ Data jurusan berhasil diperbarui!
        </div>
    <?php endif; ?>

    <form action="update.php" method="POST">
        <input type="hidden" name="id_jurusan" value="<?= $data['id_jurusan'] ?>">

        <div style="margin-bottom: 20px;">
            <label>Kode Jurusan <span style="color: #666;">*</span></label>
            <input type="text" name="kode_jurusan" value="<?= htmlspecialchars($data['kode_jurusan']) ?>" required maxlength="10" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label>Nama Jurusan <span style="color: #666;">*</span></label>
            <input type="text" name="nama_jurusan" value="<?= htmlspecialchars($data['nama_jurusan']) ?>" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="submit" style="padding: 10px 20px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px;">Simpan Perubahan</button>
            <a href="index.php" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 14px;">Batal</a>
        </div>
    </form>
</div>
</body>
</html>