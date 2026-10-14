<?php
/**
 * cetak.php — Halaman Cetak/Print Data Siswa (Level 3)
 * 
 * Menampilkan data siswa dalam format yang bisa di-print.
 * Dilengkapi filter kelas/jurusan jika ada.
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

// Ambil parameter filter (seperti di index.php)
$cari = isset($_GET['cari']) && is_string($_GET['cari'])
    ? trim($_GET['cari'])
    : "";

$filterKelas = isset($_GET['kelas']) && is_string($_GET['kelas'])
    ? trim($_GET['kelas'])
    : "";

$filterJurusan = isset($_GET['jurusan']) && is_string($_GET['jurusan'])
    ? trim($_GET['jurusan'])
    : "";

// Bangun query WHERE dinamis (sama seperti index.php)
$whereClauses = [];

if ($cari !== "") {
    $cariQuery = mysqli_real_escape_string($koneksi, $cari);
    $whereClauses[] = "(nama_siswa LIKE '%$cariQuery%' OR nis LIKE '%$cariQuery%')";
}

if ($filterKelas !== "") {
    $kelasSafe = mysqli_real_escape_string($koneksi, $filterKelas);
    $whereClauses[] = "kelas = '$kelasSafe'";
}

if ($filterJurusan !== "") {
    $jurusanSafe = mysqli_real_escape_string($koneksi, $filterJurusan);
    $whereClauses[] = "jurusan = '$jurusanSafe'";
}

$sql = "SELECT * FROM siswa";
if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}
$sql .= " ORDER BY kelas, jurusan, nama_siswa";

$query = mysqli_query($koneksi, $sql);

// Ambil data untuk filter (dropdown) - sama seperti index.php
$kelasList = [];
$jurusanList = [];

$masterKelas = ['10', '11', '12', '13'];
$masterJurusan = ['PPLG', 'TJKT', 'GEO', 'TO', 'TM', 'TKP', 'DPIB', 'TE', 'TKL', 'TPFL'];

$resKelas = mysqli_query($koneksi, "SELECT DISTINCT kelas FROM siswa WHERE kelas IS NOT NULL AND kelas != '' ORDER BY kelas");
if ($resKelas) {
    while ($row = mysqli_fetch_assoc($resKelas)) {
        $kelasList[] = $row['kelas'];
    }
}

$resJurusan = mysqli_query($koneksi, "SELECT DISTINCT jurusan FROM siswa WHERE jurusan IS NOT NULL AND jurusan != '' ORDER BY jurusan");
if ($resJurusan) {
    while ($row = mysqli_fetch_assoc($resJurusan)) {
        $jurusanList[] = $row['jurusan'];
    }
}

// Gabungkan master + DB
$kelasList = array_unique(array_merge($masterKelas, $kelasList));
sort($kelasList, SORT_STRING);

$jurusanList = array_unique(array_merge($masterJurusan, $jurusanList));
sort($jurusanList, SORT_STRING);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Data Siswa - Sistem Informasi Sekolah</title>
    <style>
        @page {
            size: A4;
            margin: 20mm 15mm;
        }
        body { font-family: Arial, sans-serif; margin: 0; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { margin: 5px 0; color: #1e293b; }
        .header p { color: #64748b; font-size: 14px; }
        .filter-section { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #e2e8f0; }
        .filter-row { display: flex; gap: 15px; flex-wrap: wrap; justify-content: center; }
        .filter-group { display: flex; flex-direction: column; align-items: center; }
        .filter-group label { margin-bottom: 4px; font-size: 12px; color: #64748b; }
        .filter-group select, .filter-group input { padding: 6px 10px; font-size: 13px; }
        .filter-group button { margin-top: 6px; padding: 6px 12px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #d1d5db; padding: 8px 5px; text-align: left; font-size: 12px; }
        th { background: #f8fafc; color: #374151; font-weight: 600; }
        tr:nth-child(even) { background: #f8fafc; }
        tr:hover { background: #e2e8f0; }
        .no-print { display: none; }
        @media print {
            .no-print { display: table-cell; }
            body * { visibility: hidden; }
            .print-section, .print-section * { visibility: visible; }
            .print-section { position: absolute; left: 0; top: 0; width: 100%; }
            .btn-back { display: none; }
        }
    </style>
</head>
<body>
<div class="print-section">
    <div class="header">
        <h1>📋 LAPORAN DATA SISWA</h1>
        <p>Sistem Informasi Sekolah</p>
    </div>

    <a href="../dashboard.php" class="no-print" style="display: inline-block; margin-bottom: 15px; padding: 8px 16px; background: #6c757d; color: white; border-radius: 6px; text-decoration: none; font-size: 14px;">← Kembali ke Dashboard</a>

    <div class="filter-section">
        <form method="GET" action="cetak.php" class="filter-row" style="justify-content: center;">
            <div class="filter-group">
                <label>Cari Nama/NIS</label>
                <input type="text" name="cari" value="<?= htmlspecialchars($cari ?? '', ENT_QUOTES) ?>" placeholder="Cari...">
            </div>

            <div class="filter-group">
                <label>Filter Kelas</label>
                <select name="kelas">
                    <option value="">-- Semua Kelas --</option>
                    <?php foreach ($kelasList as $kls): ?>
                        <option value="<?= htmlspecialchars($kls) ?>" <?= $filterKelas === $kls ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kls) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Filter Jurusan</label>
                <select name="jurusan">
                    <option value="">-- Semua Jurusan --</option>
                    <?php foreach ($jurusanList as $jur): ?>
                        <option value="<?= htmlspecialchars($jur) ?>" <?= $filterJurusan === $jur ? 'selected' : '' ?>>
                            <?= htmlspecialchars($jur) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" style="padding: 6px 12px; font-size: 12px;">Terapkan Filter</button>
            <a href="index.php" style="padding: 6px 12px; font-size: 12px; margin-left: 10px;">Batal</a>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>Rombel</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($query && mysqli_num_rows($query) > 0): ?>
                <?php $no = 1; ?>
                <?php while ($data = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td>
                            <?php if (!empty($data['foto']) && file_exists(__DIR__ . '/../' . $data['foto'])): ?>
                                <img src="../<?= htmlspecialchars($data['foto']) ?>" alt="Foto" style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                            <?php else: ?>
                                <span style="color: #94a3b8;">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($data['nis']) ?></td>
                        <td><?= htmlspecialchars($data['nama_siswa']) ?></td>
                        <td><?= htmlspecialchars($data['kelas']) ?></td>
                        <td><?= htmlspecialchars($data['jurusan']) ?></td>
                        <td><?= htmlspecialchars($data['rombel']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= urlencode($data['id_siswa']) ?>" style="font-size: 11px; color: #0d6efd; text-decoration: none;">Edit</a> |
                            <a href="hapus.php" onclick="return confirm('Yakah hapus data ini?')" style="font-size: 11px; color: #dc3545; text-decoration: none;">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; color: #94a3b8;">Tidak ada data sesuai filter</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="no-print" style="text-align: center; margin-top: 40px; font-size: 12px; color: #64748b;">
    <a href="index.php" style="color: #0d6efd;">← Kembali ke Menu Utama</a>
    <br>
    <button onclick="window.print();" style="margin-left: 10px; padding: 6px 12px; background: #0d6efd; color: white; border: none; border-radius: 4px;">Cetak Halaman Ini</button>
</div>
</body>
</html>