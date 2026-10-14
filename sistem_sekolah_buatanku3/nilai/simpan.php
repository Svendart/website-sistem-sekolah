<?php
/**
 * nilai/simpan.php — Handler Simpan Data Nilai
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$id_siswa = (int) ($_POST['id_siswa'] ?? 0);
$id_mapel = (int) ($_POST['id_mapel'] ?? 0);
$id_guru = (int) ($_POST['id_guru'] ?? 0);
$id_kelas = (int) ($_POST['id_kelas'] ?? 0);
$semester = $_POST['semester'] ?? 'Ganjil';
$tahun_ajaran = $_POST['tahun_ajaran'] ?? '2024/2025';
$nilai_harian = (float) ($_POST['nilai_harian'] ?? 0);
$nilai_uts = (float) ($_POST['nilai_uts'] ?? 0);
$nilai_uas = (float) ($_POST['nilai_uas'] ?? 0);
$keterangan = $_POST['keterangan'] ?? '';

$pesanError = '';
$pesanSukses = '';

if ($id_siswa === 0 || $id_mapel === 0 || $id_guru === 0) {
    $pesanError = 'Field wajib diisi (*): Siswa, Mata Pelajaran, Guru.';
} else {
    // Cek duplikat
    $id_siswaSafe = $id_siswa;
    $id_mapelSafe = $id_mapel;
    $semesterSafe = mysqli_real_escape_string($koneksi, $semester);
    $tahunSafe = mysqli_real_escape_string($koneksi, $tahun_ajaran);
    
    $cek = mysqli_query($koneksi, "SELECT id_nilai FROM nilai WHERE id_siswa = $id_siswaSafe AND id_mapel = $id_mapelSafe AND semester = '$semesterSafe' AND tahun_ajaran = '$tahunSafe'");
    if (mysqli_num_rows($cek) > 0) {
        $pesanError = 'Nilai untuk siswa, mata pelajaran, semester, dan tahun ajaran ini sudah ada.';
    } else {
        // Hitung nilai akhir
        $nilai_harian = (float) ($_POST['nilai_harian'] ?? 0);
        $nilai_uts = (float) ($_POST['nilai_uts'] ?? 0);
        $nilai_uas = (float) ($_POST['nilai_uas'] ?? 0);
        $nilai_akhir = round(($nilai_harian * 0.3) + ($nilai_uts * 0.3) + ($nilai_uas * 0.4), 2);
        
        $id_siswaSafe = $id_siswa;
        $id_mapelSafe = $id_mapel;
        $id_guruSafe = $id_guru;
        $id_kelasSafe = (int) ($_POST['id_kelas'] ?? 0) ?: 'NULL';
        $semesterSafe = mysqli_real_escape_string($koneksi, $semester);
        $tahunSafe = mysqli_real_escape_string($koneksi, $tahun_ajaran);
        $nilai_harianSafe = $nilai_harian;
        $nilai_utsSafe = $nilai_uts;
        $nilai_uasSafe = $nilai_uas;
        $nilai_akhirSafe = $nilai_akhir;
        $keteranganSafe = mysqli_real_escape_string($koneksi, $keterangan);

        $sql = "INSERT INTO nilai (id_siswa, id_mapel, id_guru, id_kelas, semester, tahun_ajaran, nilai_harian, nilai_uts, nilai_uas, nilai_akhir, keterangan) 
                VALUES ($id_siswaSafe, $id_mapelSafe, $id_guruSafe, $id_kelasSafe, '$semesterSafe', '$tahunSafe', $nilai_harianSafe, $nilai_utsSafe, $nilai_uasSafe, $nilai_akhirSafe, '$keteranganSafe')";

        if (mysqli_query($koneksi, $sql)) {
            $pesanSukses = '✅ Data nilai berhasil disimpan! (Nilai akhir: ' . number_format($nilai_akhir, 2) . ')';
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