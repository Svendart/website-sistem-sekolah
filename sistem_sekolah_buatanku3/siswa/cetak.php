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
    <link rel="stylesheet" href="../css/style.css">
    <style>
        @page {
            size: A4;
            margin: 20mm 15mm;
        }
        body { font-family: Arial, sans-serif; margin: 0; }
        
        .cetak-page {
            background: #f4f6f8;
            padding: 20px;
        }
        .cetak-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin: 0 auto;
            max-width: 900px;
        }
        .cetak-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        .cetak-header h1 {
            margin: 0;
            font-size: 28px;
            color: #1e293b;
        }
        .cetak-header p {
            color: #64748b;
            font-size: 14px;
        }
        .filter-section {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 15px;
        }
        .filter-section label {
            font-weight: 600;
            font-size: 14px;
            color: #374151;
        }
        .filter-section input,
        .filter-section select {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }
        .btn-filter {
            padding: 8px 16px;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h1 {
            color: #1e293b;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            font-size: 13px;
        }
        th {
            background: #f8fafc;
            color: #374151;
            font-weight: 600;
        }
        tr:nth-child(even) { background: #f8fafc; }
        tr:hover { background: #e2e8f0; }
        .no-data {
            text-align: center;
            color: #64748b;
            padding: 20px;
        }
        .foto-thumb {
            width: 30px;
            height: 30px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .btn-print {
            padding: 8px 16px;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
        }
        .btn-print:hover { background: #0b5ed7; }
        .kembali-btn {
            padding: 8px 16px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }
        .action-link {
            font-size: 11px;
            color: #0d6efd;
            text-decoration: none;
        }
        .action-link.delete {
            color: #dc3545;
        }
        .no-print { display: none; }
    </style>
</head>
<body>

<div class="cetak-page">
    <div class="cetak-container">
        
        <!-- Bagian atas: Header + Filter -->
        <div class="cetak-header">
            <h1>📋 LAPORAN DATA SISWA</h1>
            <p>Sistem Informasi Sekolah</p>
            
            <a href="../dashboard.php" class="kembali-btn">← Kembali ke Dashboard</a>
        </div>
        
        <!-- Filter section -->
        <div class="filter-section">
            <form method="GET" action="cetak.php">
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
                
                <button type="submit" class="btn-filter">Terapkan Filter</button>
                <a href="index.php" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 14px; margin-left: 15px;">Batal</a>
            </form>
        </div>
        
        <!-- Tabel data -->
        <div class="section">
            <h1>Data Siswa</h1>
            
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
                                        <img src="../<?= htmlspecialchars($data['foto']) ?>" alt="Foto" class="foto-thumb">
                                    <?php else: ?>
                                        <span style="color: #94a3b8;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($data['nis']) ?></td>
                                <td><?= htmlspecialchars($data['nama_siswa']) ?></td>
                                <td><?= htmlspecialchars($data['kelas']) ?></td>
                                <td><?= htmlspecialchars($data['jurusan']) ?></td>
                                <td><?= htmlspecialchars($data['rombel']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #64748b;">Tidak ada data sesuai filter</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Tombol Print di bawah -->
        <div style="text-align: right; margin-top: 40px;">
            <button onclick="window.print();" style="padding: 8px 16px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px;">Cetak Halaman Ini</button>
        </div>

    </div>
</div>

</body>
</html>