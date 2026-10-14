<?php
/**
 * guru/simpan.php — Handler Simpan Data Guru (Disediakan sesuai DB lama)
 * 
 * Proses penyimpanan data guru baru dari form tambah.php
 * --- Sesuai struktur tabel yang sudah ada di database ---
 */

require_once __DIR__ . '/../config/koneksi.php';

$nip = $_POST['nip'] ?? '';
$nama_guru = $_POST['nama_guru'] ?? '';
$jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$no_hp = $_POST['no_hp'] ?? '';
$email = $_POST['email'] ?? '';
$id_jurusan = (int) ($_POST['id_jurusan'] ?? 0);
$status = $_POST['status'] ?? 'Aktif';
$tanggal_masuk = $_POST['tanggal_masuk'] ?? '';

// Validasi field wajib (sesuai kolom di DB: nip, nama_guru, jenis_kelamin, id_jurusan, tanggal_masuk)
$pesanError = '';
$pesanSukses = '';

$fieldWajib = [
    'nip' => $nip,
    'nama_guru' => $nama_guru,
    'jenis_kelamin' => $jenis_kelamin,
    'id_jurusan' => $id_jurusan,
    'tanggal_masuk' => $tanggal_masuk,
];

$fieldWajibKosong = [];
foreach ($fieldWajib as $namaField => $isi) {
    if ($isi === '' || ($namaField === 'id_jurusan' && $isi === 0) || ($namaField === 'tanggal_masuk' && $isi === '')) {
        $fieldWajibKosong[] = $namaField;
    }
}

if (!empty($fieldWajibKosong)) {
    $pesanError = 'Field wajib diisi (*): ' . $fieldWajibKosong[0] . (count($fieldWajibKosong) > 1 ? ' & ' . end($fieldWajibKosong) : '');
} else {
    // Cek NIP duplikat
    $nipSafe = mysqli_real_escape_string($koneksi, $nip);
    $cek = mysqli_query($koneksi, "SELECT id_guru FROM guru WHERE nip = '$nipSafe'");
    if (mysqli_num_rows($cek) > 0) {
        $pesanError = 'NIP sudah terdaftar. Silakan gunakan NIP yang lain.';
    } else {
        // Cek email duplikat (jika email diisi)
        if ($email !== '') {
            $emailSafe = mysqli_real_escape_string($koneksi, $email);
            $cekEmail = mysqli_query($koneksi, "SELECT id_guru FROM guru WHERE email = '$emailSafe'");
            if (mysqli_num_rows($cekEmail) > 0) {
                $pesanError = 'Email sudah terdaftar. Silakan gunakan email yang lain.';
            }
        }
        
        if ($pesanError === '') {
            // Siapkan data insert sesuai kolom DB
            $namaSafe = mysqli_real_escape_string($koneksi, $nama_guru);
            $jkSafe = mysqli_real_escape_string($koneksi, $jenis_kelamin);
            $alamatSafe = mysqli_real_escape_string($koneksi, $alamat);
            $no_hpSafe = mysqli_real_escape_string($koneksi, $no_hp);
            $emailSafe = mysqli_real_escape_string($koneksi, $email);
            $juranIdSafe = mysqli_real_escape_string($koneksi, (string) $id_jurusan);
            $statusSafe = mysqli_real_escape_string($koneksi, $status);

            $sql = "INSERT INTO guru (nip, nama_guru, jenis_kelamin, alamat, no_hp, email, id_jurusan, status, tanggal_masuk) 
                    VALUES ('$nipSafe', '$namaSafe', '$jkSafe', '$alamatSafe', '$no_hpSafe', '$emailSafe', $juranIdSafe, '$statusSafe', '$tanggal_masuk')";

            if (mysqli_query($koneksi, $sql)) {
                $pesanSukses = '✅ Data guru berhasil disimpan!';
            } else {
                $pesanError = 'Gagal menyimpan ke database: ' . mysqli_error($koneksi);
            }
        }
    }
}

// Redirect kembali ke index.php
if ($pesanSukses) {
    header("Location: index.php?pesan=sukses");
    exit;
} else {
    header("Location: tambah.php?pesan=gagal&error=" . urlencode($pesanError));
    exit;
}