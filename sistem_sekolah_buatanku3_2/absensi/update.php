<?php
/**
 * absensi/update.php — Handler Update Data Absensi
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$id_absensi = (int) ($_POST['id_absensi'] ?? 0);
$id_siswa = (int) ($_POST['id_siswa'] ?? 0);
$id_mapel = (int) ($_POST['id_mapel'] ?? 0);
$id_guru = (int) ($_POST['id_guru'] ?? 0);
$id_kelas = (int) ($_POST['id_kelas'] ?? 0);
$tanggal = $_POST['tanggal'] ?? '';
$status = $_POST['status'] ?? 'Hadir';
$keterangan = $_POST['keterangan'] ?? '';
$semester = $_POST['semester'] ?? 'Ganjil';
$tahun_ajaran = $_POST['tahun_ajaran'] ?? '2024/2025';

$pesanError = '';
$pesanSukses = '';

if ($id_absensi === 0) {
    $pesanError = 'ID absensi tidak valid.';
} elseif ($id_siswa === 0 || $id_mapel === 0 || $id_guru === 0 || $tanggal === '') {
    $pesanError = 'Field wajib diisi (*): Siswa, Mata Pelajaran, Guru, Tanggal.';
} else {
    // Cek duplikat (kecuali data sendiri)
    $id_siswaSafe = $id_siswa;
    $id_mapelSafe = $id_mapel;
    $tanggalSafe = mysqli_real_escape_string($koneksi, $tanggal);
    
    $cek = mysqli_query($koneksi, "SELECT id_absensi FROM absensi WHERE id_siswa = $id_siswaSafe AND id_mapel = $id_mapelSafe AND tanggal = '$tanggalSafe' AND id_absensi != $id_absensi");
    if (mysqli_num_rows($cek) > 0) {
        $pesanError = 'Absensi untuk siswa, mata pelajaran, dan tanggal ini sudah ada.';
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

        $sql = "UPDATE absensi SET 
                id_siswa = $id_siswaSafe, 
                id_mapel = $id_mapelSafe, 
                id_guru = $id_guruSafe, 
                id_kelas = $id_kelasSafe, 
                tanggal = '$tanggalSafe', 
                status = '$statusSafe', 
                keterangan = '$keteranganSafe', 
                semester = '$semesterSafe', 
                tahun_ajaran = '$tahunSafe' 
                WHERE id_absensi = $id_absensi";

        if (mysqli_query($koneksi, $sql)) {
            $pesanSukses = '✅ Data absensi berhasil diperbarui!';
        } else {
            $pesanError = 'Gagal memperbarui: ' . mysqli_error($koneksi);
        }
    }
}

if ($pesanSukses) {
    header("Location: index.php?pesan=diperbarui");
    exit;
} else {
    header("Location: edit.php?id=$id_absensi&pesan=gagal&error=" . urlencode($pesanError));
    exit;
}