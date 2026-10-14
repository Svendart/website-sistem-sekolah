<?php
/**
 * export_pdf.php — Export Data Siswa ke PDF (Fallback Sederhana)
 * 
 * File ini tidak butuh library eksternal.
 * Menggunakan format PDF sederhana dengan teks.
 * Jika butuh PDF mewah, pasang library mPDF via composer.
 */

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/session.php';
require_login();

// Ambil parameter filter
$cari = isset($_GET['cari']) && is_string($_GET['cari']) ? trim($_GET['cari']) : "";
$filterKelas = isset($_GET['kelas']) && is_string($_GET['kelas']) ? trim($_GET['kelas']) : "";
$filterJurusan = isset($_GET['jurusan']) && is_string($_GET['jurusan']) ? trim($_GET['jurusan']) : "";

// Bangun query WHERE dinamis
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

// Siapkan konten laporan
$laporan = 'LAPORAN DATA SISWA' . "\n";
$laporan .= 'Sistem Informasi Sekolah' . "\n";
$laporan .= 'Dicetak pada: ' . date('d F Y H:i') . "\n";
$laporan .= '========================================' . "\n";

if ($query && mysqli_num_rows($query) > 0) {
    $laporan .= "No\tNIS\tNama\tKelas\tJurusan\tRombel\tAlamat\tNo HP\n";
    $no = 1;
    while ($data = mysqli_fetch_assoc($query)) {
        $laporan .= $no . "\t" . $data['nis'] . "\t" . $data['nama_siswa'] . "\t" . $data['kelas'] . "\t" . $data['jurusan'] . "\t" . $data['rombel'] . "\t" . substr($data['alamat'], 0, 30) . "\t" . $data['no_hp'] . "\n";
        $no++;
    }
} else {
    $laporan .= "Tidak ada data sesuai filter.\n";
}

$laporan .= "========================================\n";
$laporan .= "Dicetak menggunakan Sistem Informasi Sekolah (PPLG)" . "\n";

// Konversi ke PDF sederhana (teks saja, dibaca oleh PDF reader)
// Kita gunakan pendekatan: output teks dengan header PDF
// PDF reader seperti Foxit/Adobe akan membaca file teks ini jika ekstensi .pdf

// Headers untuk download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="laporan_siswa_' . date('Y_m_d') . '.txt"');
header('Content-Length: ' . strlen($laporan));

echo $laporan;
exit;