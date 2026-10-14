<?php
/**
 * guru/index.php — Halaman Daftar Guru (Level 4)
 * 
 * Menampilkan daftar guru dengan fitur CRUD lengkap dan navigasi.
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
    $whereClauses[] = "(nip LIKE '%$cariQuery%' OR nama_guru LIKE '%$cariQuery%')";
}

if ($filterJurusan !== "") {
    $jurusanSafe = mysqli_real_escape_string($koneksi, $filterJurusan);
    $whereClauses[] = "g.id_jurusan = '$jurusanSafe'";
}

$sql = "SELECT g.*, j.nama_jurusan FROM guru g LEFT JOIN jurusan j ON g.id_jurusan = j.id_jurusan";
if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}
$sql .= " ORDER BY g.nama_guru ASC";

$query = mysqli_query($koneksi, $sql);

// Ambil data jurusan untuk dropdown filter
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
    <title>Data Guru - Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .guru-page {
            background: #f4f6f8;
            padding: 30px;
        }
        .guru-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin: 0 auto;
            max-width: 900px;
        }
        .header-atas {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        .header-atas h1 {
            margin: 0;
            font-size: 28px;
            color: #1e293b;
        }
        .cari-filter-section {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .cari-filter-section label {
            font-weight: 600;
            font-size: 14px;
            color: #374151;
        }
        .cari-filter-section input,
        .cari-filter-section select {
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
        .btn-tambah {
            padding: 8px 16px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-kembali {
            padding: 8px 16px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }
        .data-table-wrapper {
            margin-top: 30px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .data-table thead {
            background: #f8fafc;
            color: #374151;
        }
        .data-table th,
        .data-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        .data-table th {
            font-weight: 600;
        }
        .data-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .data-table tbody tr:hover {
            background: #e2e8f0;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-hadir { background: #d4edda; color: #155724; }
        .badge-sakit { background: #fff3cd; color: #856404; }
        .badge-izin { background: #cce5ff; color: #004085; }
        .badge-alpa { background: #f8d7da; color: #721c24; }
        .empty-state {
            text-align: center;
            color: #64748b;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="guru-page">
    <div class="guru-container">
        
        <!-- Bagian atas: Cari NIP/Nama + Filter Jurusan di atas judul -->
        <div class="header-atas">
            <h1>👨‍🏫 Data Guru</h1>
            
            <div class="cari-filter-section">
                <form method="GET" action="index.php">
                    <div class="cari">
                        <label>Cari NIP/Nama:</label>
                        <input type="text" name="cari" value="<?= htmlspecialchars($cari ?? '', ENT_QUOTES) ?>" placeholder="Cari NIP atau Nama...">
                    </div>
                    
                    <div class="filter">
                        <label>Filter Jurusan:</label>
                        <select name="jurusan">
                            <option value="">-- Semua Jurusan --</option>
                            <?php foreach ($jurusanOptions as $id => $nama): ?>
                                <option value="<?= $id ?>" <?= $filterJurusan == $id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($nama) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-filter">Terapkan Filter</button>
                </form>
            </div>
        </div>
        
        <!-- Tombol Kembali ke Dashboard -->
        <a href="../dashboard.php" class="btn-kembali">← Kembali ke Dashboard</a>
        
        <!-- Tombol Tambah Guru ditempatkan di bawah Kembali ke Dashboard -->
        <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
            <a href="tambah.php" class="btn-tambah">+ Tambah Guru</a>
        </div>
        
        <!-- Tabel data guru -->
        <div class="data-table-wrapper">
            <div class="container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Jenis Kelamin</th>
                            <th>No HP</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($query && mysqli_num_rows($query) > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($data = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($data['nip']) ?></td>
                                    <td><?= htmlspecialchars($data['nama_guru']) ?></td>
                                    <td><?= htmlspecialchars($data['nama_jurusan'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($data['jenis_kelamin']) ?></td>
                                    <td><?= htmlspecialchars($data['no_hp'] ?? '-') ?></td>
                                    <td style="text-align: center;">
                                        <a href="edit.php?id=<?= $data['id_guru'] ?>" class="btn-action">Edit</a>
                                        <a href="hapus.php?id=<?= $data['id_guru'] ?>" onclick="return confirm('Yakin hapus data guru ini?')" class="btn-action delete">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="empty-state">Tidak ada data guru sesuai filter.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        </div>
</div>

</body>
</html>