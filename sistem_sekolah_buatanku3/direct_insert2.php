<?php
// Simplified insert - only columns that definitely exist
require_once 'config/koneksi.php';

$nip = 'GUR003';
$nama_guru = 'Pak Budi';
$jenis_kelamin = 'Laki-Laki';
$alamat = 'Jl. Merdeka No. 5';
$no_hp = '082233445566';

// HANYA pakai kolom yang pasti ada: id_guru, nip, nama_guru, jenis_kelamin, id_jurusan, alamat
$sql = "INSERT INTO guru (nip, nama_guru, jenis_kelamin, id_jurusan, alamat) 
        VALUES ('$nip', '$nama_guru', '$jenis_kelamin', 1, '$alamat')";

if (mysqli_query($koneksi, $sql)) {
    echo "Berhasil! Baris terinsert: " . mysqli_insert_id($koneksi);
} else {
    echo "Error: " . mysqli_error($koneksi);
}
?>