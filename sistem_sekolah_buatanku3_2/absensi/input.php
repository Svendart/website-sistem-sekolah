<?php
/**
 * absensi/input.php — Halaman Input Absensi
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$error = '';
$success = '';

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

// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_siswa = (int) ($_POST['id_siswa'] ?? 0);
    $id_mapel = (int) ($_POST['id_mapel'] ?? 0);
    $id_guru = (int) ($_POST['id_guru'] ?? 0);
    $id_kelas = (int) ($_POST['id_kelas'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? '';
    $status = $_POST['status'] ?? 'Hadir';
    $keterangan = $_POST['keterangan'] ?? '';
    $semester = $_POST['semester'] ?? 'Ganjil';
    $tahun_ajaran = $_POST['tahun_ajaran'] ?? '2024/2025';

    if ($id_siswa === 0 || $id_mapel === 0 || $id_guru === 0 || $tanggal === '') {
        $error = 'Field wajib diisi (*): Siswa, Mata Pelajaran, Guru, Tanggal.';
    } else {
        // Cek duplikat (siswa + mapel + tanggal)
        $id_siswaSafe = $id_siswa;
        $id_mapelSafe = $id_mapel;
        $tanggalSafe = mysqli_real_escape_string($koneksi, $tanggal);
        
        $cek = mysqli_query($koneksi, "SELECT id_absensi FROM absensi WHERE id_siswa = $id_siswaSafe AND id_mapel = $id_mapelSafe AND tanggal = '$tanggalSafe'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Absensi untuk siswa, mata pelajaran, dan tanggal ini sudah ada.';
        } else {
            $id_siswaSafe = $id_siswa;
            $id_mapelSafe = $id_mapel;
            $id_guruSafe = $id_guru;
            $id_kelasSafe = (int) ($_POST['id_kelas'] ?? 0) ?: 'NULL';
            $tanggalSafe = mysqli_real_escape_string($koneksi, $tanggal);
            $statusSafe = mysqli_real_escape_string($koneksi, $status);
            $keteranganSafe = mysqli_real_escape_string($koneksi, $keterangan);
            $semesterSafe = mysqli_real_escape_string($koneksi, $semester);
            $tahunSafe = mysqli_real_escape_string($koneksi, $tahun_ajaran);

            $sql = "INSERT INTO absensi (id_siswa, id_mapel, id_guru, id_kelas, tanggal, status, keterangan, semester, tahun_ajaran) 
                    VALUES ($id_siswaSafe, $id_mapelSafe, $id_guruSafe, $id_kelasSafe, '$tanggalSafe', '$statusSafe', '$keteranganSafe', '$semesterSafe', '$tahun_ajaran')";

            if (mysqli_query($koneksi, $sql)) {
                $success = '✅ Data absensi berhasil disimpan!';
            } else {
                $error = 'Gagal menyimpan: ' . mysqli_error($koneksi);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Absensi - Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>📝 Input Absensi</h1>

    <?php if ($error): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; border: 1px solid #f5c6cb; margin-bottom: 20px;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; border: 1px solid #c3e6cb; margin-bottom: 20px;">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <form action="simpan.php" method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label>Siswa <span style="color: #666;">*</span></label>
                <select name="id_siswa" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="">-- Pilih Siswa --</option>
                    <?php
                    $resSiswa = mysqli_query($koneksi, "SELECT * FROM siswa ORDER BY nama_siswa");
                    if ($resSiswa) while ($r = mysqli_fetch_assoc($resSiswa)) {
                        echo '<option value="' . $r['id_siswa'] . '">' . htmlspecialchars($r['nis'] . ' - ' . $r['nama_siswa']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div>
                <label>Mata Pelajaran <span style="color: #666;">*</span></label>
                <select name="id_mapel" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    <?php
                    $resMapel = mysqli_query($koneksi, "SELECT * FROM mapel ORDER BY nama_mapel");
                    if ($resMapel) while ($r = mysqli_fetch_assoc($resMapel)) {
                        echo '<option value="' . $r['id_mapel'] . '">' . htmlspecialchars($r['kode_mapel'] . ' - ' . $r['nama_mapel']) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label>Guru Pengampu <span style="color: #666;">*</span></label>
                <select name="id_guru" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="">-- Pilih Guru --</option>
                    <?php
                    $resGuru = mysqli_query($koneksi, "SELECT * FROM guru WHERE status = 'Aktif' ORDER BY nama_guru");
                    if ($resGuru) while ($r = mysqli_fetch_assoc($resGuru)) {
                        echo '<option value="' . $r['id_guru'] . '">' . htmlspecialchars($r['nama_guru']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div>
                <label>Kelas (Opsional)</label>
                <select name="id_kelas" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="">-- Pilih Kelas --</option>
                    <?php
                    $resKelas = mysqli_query($koneksi, "SELECT * FROM kelas ORDER BY nama_kelas");
                    if ($resKelas) while ($r = mysqli_fetch_assoc($resKelas)) {
                        echo '<option value="' . $r['id_kelas'] . '">' . htmlspecialchars($r['nama_kelas']) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label>Tanggal <span style="color: #666;">*</span></label>
                <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>
            <div>
                <label>Status <span style="color: #666;">*</span></label>
                <select name="status" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="Hadir">Hadir</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Izin">Izin</option>
                    <option value="Alpa">Alpa</option>
                </select>
            </div>
            <div>
                <label>Semester</label>
                <select name="semester" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label>Tahun Ajaran</label>
                <input type="text" name="tahun_ajaran" value="2024/2025" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" placeholder="2024/2025">
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
            <textarea name="keterangan" rows="3" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;"></textarea>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="submit" style="padding: 10px 20px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px;">Simpan Absensi</button>
            <a href="index.php" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 14px;">Batal</a>
        </div>
    </form>
</div>
</body>
</html>