<?php
/**
 * nilai/update.php — Handler Update Data Nilai
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

$id_nilai = (int) ($_POST['id_nilai'] ?? 0);
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

if ($id_nilai === 0) {
    $pesanError = 'ID nilai tidak valid.';
} elseif ($id_siswa === 0 || $id_mapel === 0 || $id_guru === 0) {
    $pesanError = 'Field wajib diisi (*): Siswa, Mata Pelajaran, Guru.';
} else {
    // Cek duplikat (kecuali data sendiri)
    $id_siswaSafe = $id_siswa;
    $id_mapelSafe = $id_mapel;
    $semesterSafe = mysqli_real_escape_string($koneksi, $semester);
    $tahunSafe = mysqli_real_escape_string($koneksi, $tahun_ajaran);
    
    $cek = mysqli_query($koneksi, "SELECT id_nilai FROM nilai WHERE id_siswa = $id_siswaSafe AND id_mapel = $id_mapelSafe AND semester = '$semesterSafe' AND tahun_ajaran = '$tahunSafe' AND id_nilai != $id_nilai");
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
        $semesterSafe = $semester;
        $tahunSafe = mysqli_real_escape_string($koneksi, $tahun_ajaran);
        $nilai_harianSafe = $nilai_harian;
        $nilai_utsSafe = $nilai_uts;
        $nilai_uasSafe = $nilai_uas;
        $nilai_akhirSafe = $nilai_akhir;
        $keteranganSafe = mysqli_real_escape_string($koneksi, $keterangan);

        $sql = "UPDATE nilai SET 
                id_siswa = $id_siswaSafe, 
                id_mapel = $id_mapelSafe, 
                id_guru = $id_guruSafe, 
                id_kelas = $id_kelasSafe, 
                semester = '$semesterSafe', 
                tahun_ajaran = '$tahunSafe', 
                nilai_harian = $nilai_harianSafe, 
                nilai_uts = $nilai_utsSafe, 
                nilai_uas = $nilai_uasSafe, 
                nilai_akhir = $nilai_akhirSafe, 
                keterangan = '$keteranganSafe' 
                WHERE id_nilai = $id_nilai";

        if (mysqli_query($koneksi, $sql)) {
            $pesanSukses = '✅ Data nilai berhasil diperbarui! (Nilai akhir: ' . number_format($nilai_akhir, 2) . ')';
        } else {
            $pesanError = 'Gagal memperbarui: ' . mysqli_error($koneksi);
        }
    }
}

if ($pesanSukses) {
    header("Location: index.php?pesan=diperbarui");
    exit;
} else {
    header("Location: edit.php?id=$id_nilai&pesan=gagal&error=" . urlencode($pesanError));
    exit;
}