<?php

require_once "../config/session.php";
require_login();

include "../config/koneksi.php";

$nis            = $_POST['nis'];
$nama_siswa     = $_POST['nama_siswa'];
$jenis_kelamin  = $_POST['jenis_kelamin'];
$kelas          = $_POST['kelas'];
$jurusan        = $_POST['jurusan'];
$rombel         = $_POST['rombel'];
$alamat         = $_POST['alamat'];
$no_hp          = $_POST['no_hp'];

// Handle file upload
$fotoPath = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['foto'];
    
    // Validasi tipe file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        header("Location: tambah.php?pesan=gagal&error=tipe_file_tidak_diizinkan");
        exit;
    }
    
    // Validasi ukuran (max 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        header("Location: tambah.php?pesan=gagal&error=file_terlalu_besar");
        exit;
    }
    
    // Generate unique filename
    $ext = match($fileType) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        default => 'jpg'
    };
    $filename = 'siswa_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    
    // Upload directory: inside the siswa folder itself
    $uploadDir = __DIR__ . '/uploadsfoto/';
    
    // Ensure directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $destination = $uploadDir . $filename;
    
    error_log("UPLOAD DEBUG: tmp={$file['tmp_name']} dest=$destination");
    
    $moveResult = move_uploaded_file($file['tmp_name'], $destination);
    
    error_log("UPLOAD DEBUG: move_result=" . ($moveResult ? 'true' : 'false') . " exists_after=" . (file_exists($destination) ? 'yes' : 'no'));
    
    if ($moveResult && file_exists($destination)) {
        $fotoPath = 'uploadsfoto/' . $filename;
        error_log("UPLOAD DEBUG: fotoPath set to $fotoPath");
    } else {
        header("Location: tambah.php?pesan=gagal&error=upload_gagal");
        exit;
    }
}

// Escape all inputs
$nisSafe = mysqli_real_escape_string($koneksi, $nis);
$namaSafe = mysqli_real_escape_string($koneksi, $nama_siswa);
$jkSafe = mysqli_real_escape_string($koneksi, $jenis_kelamin);
$kelasSafe = mysqli_real_escape_string($koneksi, $kelas);
$jurusanSafe = mysqli_real_escape_string($koneksi, $jurusan);
$rombelSafe = mysqli_real_escape_string($koneksi, $rombel);
$alamatSafe = mysqli_real_escape_string($koneksi, $alamat);
$hpSafe = mysqli_real_escape_string($koneksi, $no_hp);
$fotoSafe = $fotoPath ? "'" . mysqli_real_escape_string($koneksi, $fotoPath) . "'" : 'NULL';

$sql = "INSERT INTO siswa
        (nis, nama_siswa, jenis_kelamin, kelas, jurusan, rombel, alamat, no_hp, foto)
        VALUES
        ('$nisSafe', '$namaSafe', '$jkSafe', '$kelasSafe', 
        '$jurusanSafe', '$rombelSafe', '$alamatSafe', '$hpSafe', $fotoSafe)";

error_log("SQL DEBUG: $sql");

$query = false;
$kodeError = 0;
$pesanError = "";

try {
    $query = mysqli_query($koneksi, $sql);
} catch (mysqli_sql_exception $e) {
    $kodeError = (int) $e->getCode();
    $pesanError = $e->getMessage();
}

if ($query) {
    error_log("SQL DEBUG: INSERT SUCCESS");
    header("Location: index.php?pesan=sukses");
    exit;
} else {
    if ($kodeError === 0) {
        $kodeError = mysqli_errno($koneksi);
        $pesanError = mysqli_error($koneksi);
    }
    
    error_log("SQL DEBUG: INSERT FAILED - $pesanError (code: $kodeError)");
    
    // Cleanup uploaded file if DB insert failed
    if ($fotoPath && file_exists(__DIR__ . '/uploadsfoto/' . basename($fotoPath))) {
        unlink(__DIR__ . '/uploadsfoto/' . basename($fotoPath));
        error_log("CLEANUP: deleted file");
    }
    header("Location: tambah.php?pesan=gagal");
    exit;
}