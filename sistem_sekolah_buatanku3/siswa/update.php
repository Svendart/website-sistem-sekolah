<?php

require_once "../config/session.php";
require_login();

include "../config/koneksi.php";

$id_siswa       = $_POST['id_siswa'];
$nis            = $_POST['nis'];
$nama_siswa     = $_POST['nama_siswa'];
$jenis_kelamin  = $_POST['jenis_kelamin'];
$kelas          = $_POST['kelas'];
$jurusan        = $_POST['jurusan'];
$rombel         = $_POST['rombel'];
$alamat         = $_POST['alamat'];
$no_hp          = $_POST['no_hp'];

// Ambil foto lama dari database
$fotoLama = '';
$res = mysqli_query($koneksi, "SELECT foto FROM siswa WHERE id_siswa = '" . mysqli_real_escape_string($koneksi, $id_siswa) . "'");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $fotoLama = $row['foto'] ?? '';
}

// Handle file upload
$fotoPath = $fotoLama; // default: keep existing
$newFileUploaded = false;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['foto'];
    
    // Validasi tipe file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        header("Location: edit.php?id=$id_siswa&pesan=gagal&error=tipe_file_tidak_diizinkan");
        exit;
    }
    
    // Validasi ukuran (max 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        header("Location: edit.php?id=$id_siswa&pesan=gagal&error=file_terlalu_besar");
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
    $uploadDir = __DIR__ . '/../../uploads/siswa/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $destination = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $fotoPath = 'uploads/siswa/' . $filename;
        $newFileUploaded = true;
    } else {
        header("Location: edit.php?id=$id_siswa&pesan=gagal&error=upload_gagal");
        exit;
    }
}

// Escape all inputs
$idSafe = mysqli_real_escape_string($koneksi, $id_siswa);
$nisSafe = mysqli_real_escape_string($koneksi, $nis);
$namaSafe = mysqli_real_escape_string($koneksi, $nama_siswa);
$jkSafe = mysqli_real_escape_string($koneksi, $jenis_kelamin);
$kelasSafe = mysqli_real_escape_string($koneksi, $kelas);
$jurusanSafe = mysqli_real_escape_string($koneksi, $jurusan);
$rombelSafe = mysqli_real_escape_string($koneksi, $rombel);
$alamatSafe = mysqli_real_escape_string($koneksi, $alamat);
$hpSafe = mysqli_real_escape_string($koneksi, $no_hp);
$fotoSafe = $fotoPath ? "'" . mysqli_real_escape_string($koneksi, $fotoPath) . "'" : 'NULL';

$sql = "UPDATE siswa SET
        nis             = '$nisSafe',
        nama_siswa      = '$namaSafe',
        jenis_kelamin   = '$jkSafe',
        kelas           = '$kelasSafe',
        jurusan         = '$jurusanSafe',
        rombel          = '$rombelSafe',                    
        alamat          = '$alamatSafe',
        no_hp           = '$hpSafe',
        foto            = $fotoSafe
        WHERE id_siswa  = '$idSafe'";

$query = mysqli_query($koneksi, $sql);

if ($query) {
    // Hapus file lama jika upload file baru berhasil
    if ($newFileUploaded && $fotoLama && file_exists(__DIR__ . '/../../' . $fotoLama)) {
        unlink(__DIR__ . '/../../' . $fotoLama);
    }
    header("Location: index.php?pesan=update");
    exit;
} else {
    // Hapus file baru yang tadi diupload jika query gagal
    if ($newFileUploaded && $fotoPath && file_exists(__DIR__ . '/../../' . $fotoPath)) {
        unlink(__DIR__ . '/../../' . $fotoPath);
    }
    echo "Data gagal diperbarui: " . mysqli_error($koneksi);
}

?>