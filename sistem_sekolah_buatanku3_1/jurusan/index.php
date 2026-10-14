<?php
/**
 * jurusan/index.php — Halaman Daftar Jurusan
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

// Ambil parameter pencarian
$cari = isset($_GET['cari']) && is_string($_GET['cari'])
    ? trim($_GET['cari'])
    : "";

// Bangun query
$whereClauses = [];

if ($cari !== "") {
    $cariQuery = mysqli_real_escape_string($koneksi, $cari);
    $whereClauses[] = "(kode_jurusan LIKE '%$cariQuery%' OR nama_jurusan LIKE '%$cariQuery%')";
}

$sql = "SELECT j.*, COUNT(k.id_kelas) as jumlah_kelas, COUNT(g.id_guru) as jumlah_guru
        FROM jurusan j
        LEFT JOIN kelas k ON j.id_jurusan = k.id_jurusan
        LEFT JOIN guru g ON j.id_jurusan = g.id_jurusan";

if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}
$sql .= " GROUP BY j.id_jurusan ORDER BY j.nama_jurusan ASC";

$query = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Jurusan - Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>📚 Data Jurusan</h1>
    <a href="../dashboard.php" style="display: inline-block; margin-bottom: 15px; padding: 8px 16px; background: #6c757d; color: white; border-radius: 6px; text-decoration: none; font-size: 14px;">← Kembali ke Dashboard</a>

    <div style="margin: 20px 0; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
        <form style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap; justify-content: center;" method="GET" action="index.php">
            <div style="display: flex; align-items: center; gap: 10px;">
                <label style="font-weight: 600; font-size: 14px; color: #374151;">Cari Kode/Nama Jurusan:</label>
                <input type="text" name="cari" value="<?= htmlspecialchars($cari ?? '', ENT_QUOTES) ?>" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" placeholder="Cari...">
            </div>

            <button type="submit" style="padding: 8px 16px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px;">Terapkan Filter</button>
            <a href="tambah.php" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 14px;">+ Tambah Jurusan</a>
        </form>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background: #f8fafc; color: #374151;">
                <th style="padding: 12px; text-align: left;">No</th>
                <th style="padding: 12px; text-align: left;">Kode</th>
                <th style="padding: 12px; text-align: left;">Nama Jurusan</th>
                <th style="padding: 12px; text-align: center;">Jumlah Kelas</th>
                <th style="padding: 12px; text-align: center;">Jumlah Guru</th>
                <th style="padding: 12px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($query && mysqli_num_rows($query) > 0): ?>
                <?php $no = 1; ?>
                <?php while ($data = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= $no++; ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><strong><?= htmlspecialchars($data['kode_jurusan']) ?></strong></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($data['nama_jurusan']) ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center;"><?= (int)$data['jumlah_kelas'] ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center;"><?= (int)$data['jumlah_guru'] ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center;">
                            <a href="edit.php?id=<?= $data['id_jurusan'] ?>" style="margin-right: 8px; color: #0d6efd; text-decoration: none; font-size: 13px;">Edit</a>
                            <a href="hapus.php?id=<?= $data['id_jurusan'] ?>" onclick="return confirm('Yakin hapus data jurusan ini?')" style="color: #dc3545; text-decoration: none; font-size: 13px;">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #647483; padding: 20px;">
                        Tidak ada data jurusan.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>