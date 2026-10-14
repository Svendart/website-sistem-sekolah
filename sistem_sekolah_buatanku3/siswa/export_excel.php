<?php
/**
 * export_excel.php — Export Data Siswa ke Excel (CSV Format)
 * 
 * File ini dapat dibuka oleh Microsoft Excel, Google Sheets, atau LibreOffice Calc.
 * Format CSV sederhana tanpa perlu library eksternal.
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_login();

// Ambil parameter filter (sama seperti cetak.php)
$cari = isset($_GET['cari']) && is_string($_GET['cari'])
    ? trim($_GET['cari'])
    : "";

$filterKelas = isset($_GET['kelas']) && is_string($_GET['kelas'])
    ? trim($_GET['kelas'])
    : "";

$filterJurusan = isset($_GET['jurusan']) && is_string($_GET['jurusan'])
    ? trim($_GET['jurusan'])
    : "";

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

// Set header untuk download file Excel (CSV)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="data_siswa_' . date("Y_m_d") . '.csv"');

// Output judul kolom CSV
$output = "Data Siswa - " . date("d F Y H:i") . "\n\n";

// Tulis judul kolom
$output .= "No,NIS,Nama,Kelas,Jurusan,Rombel,Alamat,No HP,Foto\n";

// Tulis data baris per baris
if ($query && mysqli_num_rows($query) > 0) {
    $no = 1;
    while ($data = mysqli_fetch_assoc($query)) {
        // Siapkan data - replace comma dengan semi-colon agar tidak break CSV
        $nis = str_replace(',', ';', htmlspecialchars($data['nis'], ENT_QUOTES));
        $nama = str_replace(',', ';', htmlspecialchars($data['nama_siswa'], ENT_QUOTES));
        $kelas = htmlspecialchars($data['kelas'], ENT_QUOTES);
        $jurusan = str_replace(',', ';', htmlspecialchars($data['jurusan'], ENT_QUOTES));
        $rombel = str_replace(',', ';', htmlspecialchars($data['rombel'], ENT_QUOTES));
        $alamat = str_replace(',', ';', htmlspecialchars($data['alamat'], ENT_QUOTES));
        $no_hp = str_replace(',', ';', htmlspecialchars($data['no_hp'], ENT_QUOTES));
        
        // Path foto (relative)
        $foto = $data['foto'] ? '../' . $data['foto'] : '-';
        
        $output .= "$no,$nis,$nama,$kelas,$jurusan,$rombel,$alamat,$no_hp,$foto\n";
        $no++;
    }
} else {
    $output .= "\nTidak ada data sesuai filter.\n";
}

// Kirim ke browser
echo $output;
exit;