<?php
/**
 * absensi/edit.php — Halaman Edit Data Absensi
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$id_absensi = (int) ($_GET['id'] ?? 0);

if ($id_absensi === 0) {
    header("Location: index.php");
    exit;
}

// Ambil data absensi
$sql = "SELECT a.*, s.nis, s.nama_siswa, m.nama_mapel, m.kode_mapel, k.nama_kelas, g.nama_guru
        FROM absensi a
        JOIN siswa s ON a.id_siswa = s.id_siswa
        JOIN mapel m ON a.id_mapel = m.id_mapel
        JOIN guru g ON a.id_guru = g.id_guru
        LEFT JOIN kelas k ON a.id_kelas = k.id_kelas
        WHERE a.id_absensi = $id_absensi";
$query = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($query) === 0) {
    header("Location: index.php");
    exit;
}

$data = mysqli_fetch_assoc($query);

// Ambil data untuk dropdown
$siswaList = [];
$resSiswa = mysqli_query($koneksi, "SELECT * FROM siswa ORDER BY nama_siswa");
if ($resSiswa) while ($r = mysqli_fetch_assoc($resSiswa)) $siswaList[] = $r;

$mapelList = [];
$resMapel = mysqli_query($koneksi, "SELECT * FROM mapel ORDER BY nama_mapel");
if ($resMapel) while ($r = mysqli_fetch_assoc($resMapel)) $mapelList[] = $r;

$guruList = [];
$resGuru = mysqli_query($koneksi, "SELECT * FROM guru WHERE status = 'Aktif' ORDER BY nama_guru");
if ($resGuru) while ($r = mysqli_fetch_assoc($resGuru)) $guruList[] = $r;

$kelasList = [];
$resKelas = mysqli_query($koneksi, "SELECT * FROM kelas ORDER BY nama_kelas");
if ($resKelas) while ($r = mysqli_fetch_assoc($resKelas)) $kelasList[] = $r;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Absensi - Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>✏️ Edit Absensi</h1>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'diperbarui'): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; border: 1px solid #c3e6cb; margin-bottom: 20px;">
            ✅ Data absensi berhasil diperbarui!
        </div>
    <?php endif; ?>

    <form action="update.php" method="POST">
        <input type="hidden" name="id_absensi" value="<?= $data['id_absensi'] ?>">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label>Siswa <span style="color: #666;">*</span></label>
                <select name="id_siswa" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <?php foreach ($siswaList as $s): ?>
                        <option value="<?= $s['id_siswa'] ?>" <?= $data['id_siswa'] == $s['id_siswa'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama_siswa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Mata Pelajaran <span style="color: #666;">*</span></label>
                <select name="id_mapel" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <?php foreach ($mapelList as $m): ?>
                        <option value="<?= $m['id_mapel'] ?>" <?= $data['id_mapel'] == $m['id_mapel'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['kode_mapel'] . ' - ' . $m['nama_mapel']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label>Guru Pengampu <span style="color: #666;">*</span></label>
                <select name="id_guru" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <?php foreach ($guruList as $g): ?>
                        <option value="<?= $g['id_guru'] ?>" <?= $data['id_guru'] == $g['id_guru'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['nama_guru']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Kelas (Opsional)</label>
                <select name="id_kelas" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelasList as $k): ?>
                        <option value="<?= $k['id_kelas'] ?>" <?= $data['id_kelas'] == $k['id_kelas'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['nama_kelas']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label>Tanggal <span style="color: #666;">*</span></label>
                <input type="date" name="tanggal" value="<?= htmlspecialchars($data['tanggal']) ?>" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>
            <div>
                <label>Status <span style="color: #666;">*</span></label>
                <select name="status" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="Hadir" <?= $data['status'] === 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                    <option value="Sakit" <?= $data['status'] === 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                    <option value="Izin" <?= $data['status'] === 'Izin' ? 'selected' : '' ?>>Izin</option>
                    <option value="Alpa" <?= $data['status'] === 'Alpa' ? 'selected' : '' ?>>Alpa</option>
                </select>
            </div>
            <div>
                <label>Semester</label>
                <select name="semester" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="Ganjil" <?= $data['semester'] === 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                    <option value="Genap" <?= $data['semester'] === 'Genap' ? 'selected' : '' ?>>Genap</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label>Tahun Ajaran</label>
                <input type="text" name="tahun_ajaran" value="<?= htmlspecialchars($data['tahun_ajaran']) ?>" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" placeholder="2024/2025">
            </div>
            <div>
                <label>&nbsp;</label>
                <div style="background: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 12px; color: #6c757d;">
                    <strong>Status:</strong> Hadir / Sakit / Izin / Alpa
                </div>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label>Keterangan</label>
            <textarea name="keterangan" rows="3" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;"><?= htmlspecialchars($data['keterangan'] ?? '') ?></textarea>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="submit" style="padding: 10px 20px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px;">Simpan Perubahan</button>
            <a href="index.php" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 14px;">Batal</a>
        </div>
    </form>
</div>
</body>
</html>