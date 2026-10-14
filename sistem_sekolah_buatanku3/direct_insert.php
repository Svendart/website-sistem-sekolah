<?php
// Direct database insert script
require_once 'config/koneksi.php';

$nip = 'GUR003';
$nama_guru = 'Pak Andi';
$jenis_kelamin = 'Laki-Laki';
$alamat = 'Jl. Sudirman No. 10';
$no_hp = '081122334455';
$email = 'andi@sekolah.sch.id';
$id_jurusan = 1; // Asumsi jurusan pertama
$status = 'Aktif';
$tanggal_masuk = '2024-03-01';

$sql = "INSERT INTO guru (nip, nama_guru, jenis_kelamin, alamat, no_hp, email, id_jurusan, status, tanggal_masuk) 
        VALUES ('$nip', '$nama_guru', '$jenis_kelamin', '$alamat', '$no_hp', '$email', $id_jurusan, '$status', '$tanggal_masuk')";

if (mysqli_query($koneksi, $sql)) {
    echo "Berhasil disimpan! ID: " . mysqli_insert_id($koneksi);
} else {
    echo "Error: " . mysqli_error($koneksi);
}
?>