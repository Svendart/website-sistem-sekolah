<?php
/**
 * absensi/index.php — Halaman Daftar Absensi
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

// Ambil parameter pencarian dan filter
$cari = isset($_GET['cari']) && is_string($_GET['cari'])
    ? trim($_GET['cari'])
    : "";

$filterKelas = isset($_GET['kelas']) && is_string($_GET['kelas'])
    ? trim($_GET['kelas'])
    : "";

$filterMapel = isset($_GET['mapel']) && is_string($_GET['mapel'])
    ? trim($_GET['mapel'])
    : "";

$filterStatus = isset($_GET['status']) && is_string($_GET['status'])
    ? trim($_GET['status'])
    : "";

$filterTanggal = isset($_GET['tanggal']) && is_string($_GET['tanggal'])
    ? trim($_GET['tanggal'])
    : "";

// Bangun query WHERE dinamis
$whereClauses = [];

if ($cari !== "") {
    $cariQuery = mysqli_real_escape_string($koneksi, $cari);
    $whereClauses[] = "(s.nama_siswa LIKE '%$cariQuery%' OR s.nis LIKE '%$cariQuery%')";
}

if ($filterKelas !== "") {
    $kelasSafe = mysqli_real_escape_string($koneksi, $filterKelas);
    $whereClauses[] = "k.id_kelas = '$kelasSafe'";
}

if ($filterMapel !== "") {
    $mapelSafe = mysqli_real_escape_string($koneksi, $filterMapel);
    $whereClauses[] = "m.id_mapel = '$mapelSafe'";
}

if ($filterStatus !== "") {
    $statusSafe = mysqli_real_escape_string($koneksi, $filterStatus);
    $whereClauses[] = "a.status = '$statusSafe'";
}

if ($filterTanggal !== "") {
    $tanggalSafe = mysqli_real_escape_string($koneksi, $filterTanggal);
    $whereClauses[] = "a.tanggal = '$tanggalSafe'";
}

$sql = "SELECT a.*, s.nis, s.nama_siswa, m.nama_mapel, m.kode_mapel, k.nama_kelas, g.nama_guru
        FROM absensi a
        JOIN siswa s ON a.id_siswa = s.id_siswa
        JOIN mapel m ON a.id_mapel = m.id_mapel
        JOIN guru g ON a.id_guru = g.id_guru
        LEFT JOIN kelas k ON a.id_kelas = k.id_kelas";

if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}
$sql .= " ORDER BY a.tanggal DESC, s.nama_siswa";

$query = mysqli_query($koneksi, $sql);

// Ambil data untuk dropdown
$kelasOptions = [];
$resKelas = mysqli_query($koneksi, "SELECT * FROM kelas ORDER BY nama_kelas");
if ($resKelas) while ($r = mysqli_fetch_assoc($resKelas)) $kelasOptions[$r['id_kelas']] = $r['nama_kelas'];

$mapelOptions = [];
$resMapel = mysqli_query($koneksi, "SELECT * FROM mapel ORDER BY nama_mapel");
if ($resMapel) while ($r = mysqli_fetch_assoc($resMapel)) $mapelOptions[$r['id_mapel']] = $r['nama_mapel'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Absensi - Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>📅 Data Absensi</h1>
    <a href="../dashboard.php" style="display: inline-block; margin-bottom: 15px; padding: 8px 16px; background: #6c757d; color: white; border-radius: 6px; text-decoration: none; font-size: 14px;">← Kembali ke Dashboard</a>

    <div style="margin: 20px 0; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
        <form style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap; justify-content: center;" method="GET" action="index.php">
            <div style="display: flex; align-items: center; gap: 10px;">
                <label style="font-weight: 600; font-size: 14px; color: #374151;">Cari NIS/Nama:</label>
                <input type="text" name="cari" value="<?= htmlspecialchars($cari ?? '', ENT_QUOTES) ?>" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" placeholder="Cari...">
            </div>

            <div style="display: flex; align-items: center; gap: 10px;">
                <label style="font-weight: 600; font-size: 14px; color: #374151;">Kelas:</label>
                <select name="kelas" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="">-- Semua --</option>
                    <?php foreach ($kelasOptions as $id => $nama): ?>
                        <option value="<?= $id ?>" <?= $filterKelas == $id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($nama) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; align-items: center; gap: 10px;">
                <label style="font-weight: 600; font-size: 14px; color: #374151;">Mapel:</label>
                <select name="mapel" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="">-- Semua --</option>
                    <?php
                    $resMapel = mysqli_query($koneksi, "SELECT * FROM mapel ORDER BY nama_mapel");
                    if ($resMapel) while ($r = mysqli_fetch_assoc($resMapel)) {
                        $selected = ($filterMapel == $r['id_mapel']) ? 'selected' : '';
                        echo '<option value="' . $r['id_mapel'] . '" ' . $selected . '>' . htmlspecialchars($r['nama_mapel']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div style="display: flex; align-items: center; gap: 10px;">
                <label style="font-weight: 600; font-size: 14px; color: #374151;">Status:</label>
                <select name="status" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="">-- Semua --</option>
                    <option value="Hadir" <?= $filterStatus === 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                    <option value="Sakit" <?= $filterStatus === 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                    <option value="Izin" <?= $filterStatus === 'Izin' ? 'selected' : '' ?>>Izin</option>
                    <option value="Alpa" <?= $filterStatus === 'Alpa' ? 'selected' : '' ?>>Alpa</option>
                </select>
            </div>

            <div style="display: flex; align-items: center; gap: 10px;">
                <label style="font-weight: 600; font-size: 14px; color: #374151;">Tanggal:</label>
                <input type="date" name="tanggal" value="<?= htmlspecialchars($filterTanggal ?? '', ENT_QUOTES) ?>" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>

            <button type="submit" style="padding: 8px 16px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px;">Terapkan Filter</button>
            <a href="input.php" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 14px;">+ Input Absensi</a>
        </form>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background: #f8fafc; color: #374151;">
                <th style="padding: 12px; text-align: left;">No</th>
                <th style="padding: 12px; text-align: left;">Tanggal</th>
                <th style="padding: 12px; text-align: left;">NIS</th>
                <th style="padding: 12px; text-align: left;">Nama Siswa</th>
                <th style="padding: 12px; text-align: left;">Mata Pelajaran</th>
                <th style="padding: 12px; text-align: center;">Kelas</th>
                <th style="padding: 12px; text-align: center;">Status</th>
                <th style="padding: 12px; text-align: center;">Keterangan</th>
                <th style="padding: 12px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($query && mysqli_num_rows($query) > 0): ?>
                <?php $no = 1; ?>
                <?php while ($data = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= $no++; ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($data['tanggal']) ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($data['nis']) ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($data['nama_siswa']) ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($data['nama_mapel']) ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center;"><?= htmlspecialchars($data['nama_kelas'] ?? '—') ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center;">
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; 
                                <?= $data['status'] === 'Hadir' ? 'background:#d4edda;color:#155724' : '' ?>
                                <?= $data['status'] === 'Sakit' ? 'background:#fff3cd;color:#856404' : '' ?>
                                <?= $data['status'] === 'Izin' ? 'background:#cce5ff;color:#004085' : '' ?>
                                <?= $data['status'] === 'Alpa' ? 'background:#f8d7da;color:#721c24' : '' ?>
                            ">
                                <?= htmlspecialchars($data['status']) ?>
                            </span>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center;"><?= htmlspecialchars($data['keterangan'] ?? '-') ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center;">
                            <a href="edit.php?id=<?= $data['id_absensi'] ?>" style="margin-right: 8px; color: #0d6efd; text-decoration: none; font-size: 13px;">Edit</a>
                            <a href="hapus.php?id=<?= $data['id_absensi'] ?>" onclick="return confirm('Yakin hapus data absensi ini?')" style="color: #dc3545; text-decoration: none; font-size: 13px;">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align: center; color: #647483; padding: 20px;">
                        Tidak ada data absensi sesuai filter.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>