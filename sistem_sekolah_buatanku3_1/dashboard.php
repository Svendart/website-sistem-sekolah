<?php
/**
 * dashboard.php — Halaman utama setelah login (Level 2)
 * 
 * Menampilkan:
 * - Ringkasan jumlah total siswa
 * - Ringkasan per Kelas & per Jurusan
 * - Navigasi ke modul Data Siswa
 * - Info admin yang login
 */

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/session.php';
require_login();

// Hitung total siswa
$totalSiswa = 0;
$sqlCount = "SELECT COUNT(*) AS total FROM siswa";
$result = mysqli_query($koneksi, $sqlCount);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalSiswa = (int)$row['total'];
}

// Ringkasan per Kelas
$kelasStats = [];
$resKelas = mysqli_query($koneksi, "SELECT kelas, COUNT(*) AS total FROM siswa WHERE kelas IS NOT NULL AND kelas != '' GROUP BY kelas ORDER BY kelas");
if ($resKelas) {
    while ($row = mysqli_fetch_assoc($resKelas)) {
        $kelasStats[] = $row;
    }
}

// Ringkasan per Jurusan
$jurusanStats = [];
$resJurusan = mysqli_query($koneksi, "SELECT jurusan, COUNT(*) AS total FROM siswa WHERE jurusan IS NOT NULL AND jurusan != '' GROUP BY jurusan ORDER BY jurusan");
if ($resJurusan) {
    while ($row = mysqli_fetch_assoc($resJurusan)) {
        $jurusanStats[] = $row;
    }
}

// Hitung total guru
$totalGuru = 0;
$resGuru = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM guru WHERE status = 'Aktif'");
if ($resGuru) {
    $row = mysqli_fetch_assoc($resGuru);
    $totalGuru = (int)$row['total'];
}

// Data admin login
$admin = current_admin();
$namaAdmin = $admin['nama_lengkap'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .container { max-width: 900px; }
        .dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .dashboard-header h1 { margin: 0; color: #1e293b; }
        .admin-info { display: flex; align-items: center; gap: 15px; color: #64748b; font-size: 14px; }
        .admin-info strong { color: #1e293b; }
        .btn-logout { background: #6c757d; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 14px; }
        .btn-logout:hover { background: #5a6268; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(15,23,42,0.04); }
        .stat-card.primary { border-left: 5px solid #0d6efd; }
        .stat-label { font-size: 14px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
        .stat-value { font-size: 36px; font-weight: 700; color: #1e293b; line-height: 1.2; }
        .stat-icon { font-size: 28px; opacity: 0.7; }
        
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; }
        .menu-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s; }
        .menu-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(15,23,42,0.08); border-color: #0d6efd; }
        .menu-icon { font-size: 32px; margin-bottom: 12px; }
        .menu-title { font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 6px; }
        .menu-desc { font-size: 14px; color: #64748b; }
        .coming-soon { opacity: 0.5; pointer-events: none; }
        .coming-soon .menu-title::after { content: ' (Belum Tersedia)'; font-weight: 400; color: #94a3b8; font-size: 13px; }
        
        /* Stats Section (Level 2.4) */
        .stats-section { margin-bottom: 30px; }
        .section-title { font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 16px; padding-bottom: 8px; border-bottom: 2px solid #e2e8f0; }
        .stats-grid-sm { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 16px; }
        .stats-grid-sm .stat-card { padding: 18px; }
        .stats-grid-sm .stat-value { font-size: 28px; }
        .stats-grid-sm .stat-icon { font-size: 24px; }
        
        /* Empty state */
        .empty-state { text-align: center; padding: 40px; color: #94a3b8; }
    </style>
</head>
<body>
<div class="container">
    <div class="dashboard-header">
        <h1>📊 Dashboard</h1>
        <div class="admin-info">
            <span>👤 <strong><?= htmlspecialchars($namaAdmin) ?></strong></span>
            <a href="logout.php" class="btn-logout">Keluar</a>
        </div>
    </div>

    <!-- Ringkasan Statistik Utama -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-label">Total Siswa</div>
            <div class="stat-value"><?= number_format($totalSiswa) ?></div>
            <div class="stat-icon">👨‍🎓</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Kelas Aktif</div>
            <div class="stat-value"><?= count($kelasStats) ?></div>
            <div class="stat-icon">🏫</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Jurusan Aktif</div>
            <div class="stat-value"><?= count($jurusanStats) ?></div>
            <div class="stat-icon">📚</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Guru</div>
            <div class="stat-value"><?= number_format($totalGuru) ?></div>
            <div class="stat-icon">👨‍🏫</div>
        </div>
    </div>

    <!-- Aksi Cepat: Cetak & Export -->
    <div style="margin: 25px 0; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
        <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
            <a href="siswa/cetak.php" style="padding: 10px 20px; background: #198754; color: white; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 14px;">
                📄 Cetak Laporan
            </a>
            <a href="siswa/export_excel.php" style="padding: 10px 20px; background: #0d6efd; color: white; border-radius: 8px; text-decoration: none; font-size: 14px;">
                📊 Export Excel
            </a>
            <a href="siswa/laporan_per_kelas.php" style="padding: 10px 20px; background: #6c757d; color: white; border-radius: 8px; text-decoration: none; font-size: 14px;">
                📋 Laporan per Kelas
            </a>
        </div>
    </div>

    <!-- Ringkasan Siswa per Kelas -->
    <?php if (!empty($kelasStats)): ?>
    <section class="stats-section">
        <h2 class="section-title">📊 Siswa per Kelas</h2>
        <div class="stats-grid stats-grid-sm">
            <?php foreach ($kelasStats as $ks): ?>
                <div class="stat-card">
                    <div class="stat-label">Kelas <?= htmlspecialchars($ks['kelas']) ?></div>
                    <div class="stat-value"><?= number_format($ks['total']) ?></div>
                    <div class="stat-icon">👥</div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Ringkasan Siswa per Jurusan -->
    <?php if (!empty($jurusanStats)): ?>
    <section class="stats-section">
        <h2 class="section-title">📚 Siswa per Jurusan</h2>
        <div class="stats-grid stats-grid-sm">
            <?php foreach ($jurusanStats as $js): ?>
                <div class="stat-card">
                    <div class="stat-label"><?= htmlspecialchars($js['jurusan']) ?></div>
                    <div class="stat-value"><?= number_format($js['total']) ?></div>
                    <div class="stat-icon">🎓</div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Menu Navigasi -->
    <div class="menu-grid">
        <a href="siswa/index.php" class="menu-card">
            <div class="menu-icon">👨‍🎓</div>
            <div class="menu-title">Data Siswa</div>
            <div class="menu-desc">Kelola data siswa (CRUD, pencarian, filter kelas/jurusan)</div>
        </a>
        
        <a href="kelas/index.php" class="menu-card">
            <div class="menu-icon">🏫</div>
            <div class="menu-title">Data Kelas</div>
            <div class="menu-desc">Kelola data kelas (CRUD, filter jurusan, wali kelas)</div>
        </a>
        
        <a href="jurusan/index.php" class="menu-card">
            <div class="menu-icon">📚</div>
            <div class="menu-title">Data Jurusan</div>
            <div class="menu-desc">Kelola data jurusan (CRUD, relasi kelas & guru)</div>
        </a>
        
        <a href="guru/index.php" class="menu-card">
            <div class="menu-icon">👨‍🏫</div>
            <div class="menu-title">Data Guru</div>
            <div class="menu-desc">Kelola data guru (CRUD, filter jurusan)</div>
        </a>
        
        <a href="mapel/index.php" class="menu-card">
            <div class="menu-icon">📝</div>
            <div class="menu-title">Data Mata Pelajaran</div>
            <div class="menu-desc">Kelola mata pelajaran (CRUD, filter jurusan & tingkat)</div>
        </a>
        
        <a href="nilai/index.php" class="menu-card">
            <div class="menu-icon">📊</div>
            <div class="menu-title">Data Nilai</div>
            <div class="menu-desc">Input & kelola nilai siswa (CRUD, auto hitung nilai akhir)</div>
        </a>
        
        <a href="absensi/index.php" class="menu-card">
            <div class="menu-icon">📅</div>
            <div class="menu-title">Data Absensi</div>
            <div class="menu-desc">Kelola absensi siswa (CRUD, filter status & tanggal)</div>
        </a>
        
        <a href="siswa/cetak.php" class="menu-card">
            <div class="menu-icon">📄</div>
            <div class="menu-title">Laporan & Export</div>
            <div class="menu-desc">Cetak, Export Excel/PDF, Laporan per kelas (Level 3)</div>
        </a>
    </div>
</div>
</body>
</html>