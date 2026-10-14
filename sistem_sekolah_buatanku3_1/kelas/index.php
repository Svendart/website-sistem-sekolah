<?php
/**
 * kelas/index.php — Halaman Daftar Kelas
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

// Ambil parameter pencarian dan filter
$cari = isset($_GET['cari']) && is_string($_GET['cari'])
    ? trim($_GET['cari'])
    : "";

$filterJurusan = isset($_GET['jurusan']) && is_string($_GET['jurusan'])
    ? trim($_GET['jurusan'])
    : "";

// Bangun query WHERE dinamis
$whereClauses = [];

if ($cari !== "") {
    $cariQuery = mysqli_real_escape_string($koneksi, $cari);
    $whereClauses[] = "(k.nama_kelas LIKE '%$cariQuery%')";
}

if ($filterJurusan !== "") {
    $jurusanSafe = mysqli_real_escape_string($koneksi, $filterJurusan);
    $whereClauses[] = "k.id_jurusan = '$jurusanSafe'";
}

$sql = "SELECT k.*, j.nama_jurusan, g.nama_guru as wali_kelas 
        FROM kelas k 
        LEFT JOIN jurusan j ON k.id_jurusan = j.id_jurusan 
        LEFT JOIN guru g ON k.id_wali_kelas = g.id_guru";

if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}
$sql .= " ORDER BY k.nama_kelas ASC";

$query = mysqli_query($koneksi, $sql);

// Ambil data jurusan untuk dropdown
$jurusanOptions = [];
$resJurusan = mysqli_query($koneksi, "SELECT * FROM jurusan ORDER BY nama_jurusan");
if ($resJurusan) {
    while ($row = mysqli_fetch_assoc($resJurusan)) {
        $jurusanOptions[$row['id_jurusan']] = $row['nama_jurusan'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kelas - Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>🏫 Data Kelas</h1>
    <a href="../dashboard.php" style="display: inline-block; margin-bottom: 15px; padding: 8px 16px; background: #6c757d; color: white; border-radius: 6px; text-decoration: none; font-size: 14px;">← Kembali ke Dashboard</a>

    <div style="margin: 20px 0; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
        <form style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap; justify-content: center;" method="GET" action="index.php">
            <div style="display: flex; align-items: center; gap: 10px;">
                <label style="font-weight: 600; font-size: 14px; color: #374151;">Cari Nama Kelas:</label>
                <input type="text" name="cari" value="<?= htmlspecialchars($cari ?? '', ENT_QUOTES) ?>" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" placeholder="Cari...">
            </div>

            <div style="display: flex; align-items: center; gap: 10px;">
                <label style="font-weight: 600; font-size: 14px; color: #374151;">Filter Jurusan:</label>
                <select name="jurusan" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="">-- Semua Jurusan --</option>
                    <?php foreach ($jurusanOptions as $id => $nama): ?>
                        <option value="<?= $id ?>" <?= $filterJurusan == $id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($nama) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" style="padding: 8px 16px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px;">Terapkan Filter</button>
            <a href="tambah.php" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 14px;">+ Tambah Kelas</a>
        </form>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background: #f8fafc; color: #374151;">
                <th style="padding: 12px; text-align: left;">No</th>
                <th style="padding: 12px; text-align: left;">Nama Kelas</th>
                <th style="padding: 12px; text-align: left;">Jurusan</th>
                <th style="padding: 12px; text-align: left;">Wali Kelas</th>
                <th style="padding: 12px; text-align: left;">Tahun Ajaran</th>
                <th style="padding: 12px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($query && mysqli_num_rows($query) > 0): ?>
                <?php $no = 1; ?>
                <?php while ($data = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= $no++; ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($data['nama_kelas']) ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                            <?= $data['nama_jurusan'] ?? '—' ?>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                            <?= $data['wali_kelas'] ?? '—' ?>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($data['tahun_ajaran']) ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center;">
                            <a href="edit.php?id=<?= $data['id_kelas'] ?>" style="margin-right: 8px; color: #0d6efd; text-decoration: none; font-size: 13px;">Edit</a>
                            <a href="hapus.php?id=<?= $data['id_kelas'] ?>" onclick="return confirm('Yakin hapus data kelas ini?')" style="color: #dc3545; text-decoration: none; font-size: 13px;">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #647483; padding: 20px;">
                        Tidak ada data kelas sesuai filter.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>