<?php
require_once 'config/koneksi.php';
$res = mysqli_query($koneksi, "SELECT id_siswa, nama_siswa, foto FROM siswa WHERE foto IS NOT NULL AND foto != '' ORDER BY id_siswa DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($res)) {
    echo "ID: {$row['id_siswa']} | Nama: {$row['nama_siswa']} | Foto: {$row['foto']}\n";
}