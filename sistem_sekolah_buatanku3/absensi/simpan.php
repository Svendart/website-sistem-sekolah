<?php
/**
 * absensi/simpan.php — Handler Simpan Data Absensi
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

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

if ($id_siswa === 0 || $id_mapel === 0 || $id_guru === 0 || $tanggal === '') {
    $pesanError = 'Field wajib diisi (*): Siswa, Mata Pelajaran, Guru, Tanggal.';
} else {
    // Cek duplikat (siswa + mapel + tanggal)
    $id_siswaSafe = $id_siswa;
    $id_mapelSafe = $id_mapel;
    $tanggalSafe = mysqli_real_escape_string($koneksi, $tanggal);
    
    $cek = mysqli_query($koneksi, "SELECT id_absensi FROM absensi WHERE id_siswa = $id_siswaSafe AND id_mapel = $id_mapelSafe AND tanggal = '$tanggalSafe'");
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

        $sql = "INSERT INTO absensi (id_siswa, id_mapel, id_guru, id_kelas, tanggal, status, keterangan, semester, tahun_ajaran) 
                VALUES ($id_siswaSafe, $id_mapelSafe, $id_guruSafe, $id_kelasSafe, '$tanggalSafe', '$statusSafe', '$keteranganSafe', '$semesterSafe', '$tahunSafe')";

        if (mysqli_query($koneksi, $sql)) {
            $pesanSukses = '✅ Data absensi berhasil disimpan!';
        } else {
            $pesanError = 'Gagal menyimpan: ' . mysqli_error($koneksi);
        }
    }
}

if ($pesanSukses) {
    header("Location: index.php?pesan=sukses");
    exit;
} else {
    header("Location: input.php?pesan=gagal&error=" . urlencode($pesanError));
    exit;
}