<?php
/**
 * laporan_per_kelas.php — Laporan Siswa per Kelas (Level 3)
 * 
 * Menampilkan jumlah siswa per kelas beserta detail nama dan foto.
 * Halaman ini bisa di-print atau di-export.
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

// Ambil data siswa yang akan dikelompokkan per kelas
$sql = "SELECT kelas, COUNT(*) as total FROM siswa WHERE kelas IS NOT NULL AND kelas != '' GROUP BY kelas ORDER BY kelas";
$query = mysqli_query($koneksi, $sql);

// Ambil daftar kelas unik untuk dropdown
$kelasList = [];
$resKelas = mysqli_query($koneksi, "SELECT DISTINCT kelas FROM siswa WHERE kelas IS NOT NULL AND kelas != '' ORDER BY kelas");
if ($resKelas) {
    while ($row = mysqli_fetch_assoc($resKelas)) {
        $kelasList[] = $row['kelas'];
    }
}

// Tangkap parameter kelas di URL
$kelas_spesifik = isset($_GET['kelas']) && is_string($_GET['kelas']) ? trim($_GET['kelas']) : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Siswa per Kelas - Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .laporan-page {
            background: #f4f6f8;
            padding: 30px 20px;
        }
        .laporan-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin: 0 auto;
            max-width: 900px;
        }
        .laporan-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        .laporan-header h1 {
            margin: 0;
            font-size: 28px;
            color: #1e293b;
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
        .filter-section select,
        .filter-section input {
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
        .section h2 {
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
            width: 40px;
            height: 40px;
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
        .no-print { display: none; }
    </style>
</head>
<body>

<div class="laporan-page">
    <div class="laporan-container">
        
        <!-- Bagian atas: Judul + Filter -->
        <div class="laporan-header">
            <h1>📊 Laporan Siswa per Kelas</h1>
            
            <div class="filter-section">
                <form method="GET" action="laporan_per_kelas.php">
                    <label>Filter Kelas:</label>
                    <select name="kelas">
                        <option value="">-- Semua Kelas --</option>
                        <?php foreach ($kelasList as $kls): ?>
                            <option value="<?= htmlspecialchars($kls) ?>" <?= $kelas_spesifik === $kls ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kls) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button type="submit" class="btn-filter">Terapkan Filter</button>
                </form>
            </div>
        </div>
        
        <!-- Tombol Kembali ke Dashboard -->
        <a href="../dashboard.php" class="kembali-btn">← Kembali ke Dashboard</a>
        
        <!-- Konten kelas -->
        <?php if ($kelas_spesifik): ?>
            <?php
            // Definisikan variabel detail di sini agar tersedia di seluruh blok
            $detailSql = "SELECT * FROM siswa WHERE kelas = '$kelas_spesifik' ORDER BY nama_siswa";
            $query_detail = mysqli_query($koneksi, $detailSql);
            $totalSiswaKelas = $query_detail ? mysqli_num_rows($query_detail) : 0;
            ?>
            <div class="section">
                <h2>Kelas <?= htmlspecialchars($kelas_spesifik) ?> — Total Siswa: <?= $totalSiswaKelas ?></h2>
    
                <?php if ($query_detail && mysqli_num_rows($query_detail) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>NIS</th>
                                <th>Nama</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php while ($r = mysqli_fetch_assoc($query_detail)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><img src="../<?= $r['foto'] ? $r['foto'] : 'uploads/siswa/default.jpg' ?>" alt="Foto" class="foto-thumb"></td>
                                    <td><?= htmlspecialchars($r['nis']) ?></td>
                                    <td><?= htmlspecialchars($r['nama_siswa']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
    
                    <p style="margin-top: 20px; font-size: 12px; color: #64748b;">
                        <a href="cetak.php?kelas=<?= urlencode($kelas_spesifik) ?>" class="btn-print no-print">Cetak Halaman Ini</a>
                        <a href="export_excel.php?kelas=<?= urlencode($kelas_spesifik) ?>" style="margin-left: 10px; color: #0d6efd; text-decoration: none;">Export Excel</a>
                    </p>
                <?php else: ?>
                    <p style="color: #64748b;">Tidak ada data untuk kelas ini.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="section">
                <p>Klik salah satu kelas di atas untuk melihat detail siswanya.</p>
    
                <table style="width:100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Kelas</th>
                            <th style="width: 25%;">Jumlah Siswa</th>
                            <th style="width: 60%;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($query): ?>
                            <?php while ($r = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td style="padding: 10px;">Kelas <?= htmlspecialchars($r['kelas']) ?></td>
                                    <td style="padding: 10px; text-align: center;"><?= $r['total'] ?> siswa</td>
                                    <td style="padding: 10px;">
                                        <a href="laporan_per_kelas.php?kelas=<?= htmlspecialchars($r['kelas']) ?>" style="color: #0d6efd; text-decoration: none;">Lihat Detail</a>
                                        <span style="margin-left: 10px;">·</span>
                                        <a href="cetak.php?kelas=<?= htmlspecialchars($r['kelas']) ?>" style="color: #64748b; text-decoration: none; font-size: 12px; margin-left: 10px;">Cetak</a>
                                        <span style="margin-left: 10px;">·</span>
                                        <a href="export_excel.php?kelas=<?= htmlspecialchars($r['kelas']) ?>" style="color: #0d6efd; text-decoration: none; font-size: 12px; margin-left: 10px;">Excel</a>
                                    </span>
                                    <br>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center; color:#64748b;">Tidak ada data kelas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <!-- Tombol Kembali ke Menu Utama di bawah -->
        <p style="text-align: right; margin-top: 30px; font-size: 13px; color: #64748b;">
            <a href="index.php">← Kembali ke Menu Utama</a>
        </p>

    </div>
</div>

</body>
</html>