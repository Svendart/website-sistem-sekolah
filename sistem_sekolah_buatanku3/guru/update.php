<?php
/**
 * guru/update.php — Handler Update Data Guru
 * 
 * Proses pengupdatean data guru dari form edit.php
 */

require_once __DIR__ . '/../config/koneksi.php';

$id_guru = (int) ($_POST['id_guru'] ?? 0);
$nip = $_POST['nip'] ?? '';
$nama_guru = $_POST['nama_guru'] ?? '';
$jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$no_hp = $_POST['no_hp'] ?? '';
$email = $_POST['email'] ?? '';
$id_jurusan = (int) ($_POST['id_jurusan'] ?? 0);
$status = $_POST['status'] ?? 'Aktif';

// Validasi field wajib
$pesanError = '';
$pesanSukses = '';

if ($id_guru === 0) {
    $pesanError = 'ID guru tidak valid.';
} elseif ($nip === '' || $nama_guru === '' || $jenis_kelamin === '' || $id_jurusan === 0) {
    $pesanError = 'Field wajib diisi (*): NIP, Nama, Jenis Kelamin, Jurusan.';
} else {
    // Cek NIP duplikat (kecuali untuk data sendiri)
    $nipSafe = mysqli_real_escape_string($koneksi, $nip);
    $cek = mysqli_query($koneksi, "SELECT id_guru FROM guru WHERE nip = '$nipSafe' AND id_guru != $id_guru");
    if (mysqli_num_rows($cek) > 0) {
        $pesanError = 'NIP sudah terpakai oleh guru lain. Gunakan NIP lain.';
    } else {
        // Update ke database
        $namaSafe = mysqli_real_escape_string($koneksi, $nama_guru);
        $jkSafe = mysqli_real_escape_string($koneksi, $jenis_kelamin);
        $alamatSafe = mysqli_real_escape_string($koneksi, $alamat);
        $no_hpSafe = mysqli_real_escape_string($koneksi, $no_hp);
        $emailSafe = mysqli_real_escape_string($koneksi, $email);
        $statusSafe = mysqli_real_escape_string($koneksi, $status);

        $sql = "UPDATE guru SET 
                nip = '$nipSafe', 
                nama_guru = '$namaSafe', 
                jenis_kelamin = '$jkSafe', 
                alamat = '$alamatSafe', 
                no_hp = '$no_hpSafe', 
                email = '$emailSafe', 
                id_jurusan = $id_jurusan, 
                status = '$statusSafe' 
                WHERE id_guru = $id_guru";

        if (mysqli_query($koneksi, $sql)) {
            $pesanSukses = '✅ Data guru berhasil diperbarui!';
        } else {
            $pesanError = 'Gagal memperbarui database: ' . mysqli_error($koneksi);
        }
    }
}

// Redirect kembali ke index.php
if ($pesanSukses) {
    header("Location: index.php?pesan=diperbarui");
    exit;
} else {
    header("Location: edit.php?id=$id_guru&pesan=gagal&error=" . urlencode($pesanError));
    exit;
}