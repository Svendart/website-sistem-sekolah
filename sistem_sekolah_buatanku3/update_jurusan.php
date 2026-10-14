<?php
require_once 'config/koneksi.php';

// Disable foreign key checks
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");
mysqli_query($koneksi, "TRUNCATE TABLE jurusan");
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");

// New jurusan data
$jurusanData = [
    ['PPLG', 'PPLG'],
    ['TJKT', 'TJKT'],
    ['TO', 'TO'],
    ['TM', 'TM'],
    ['TKL', 'TKL'],
    ['TE', 'TE'],
    ['TPFL', 'TPFL'],
    ['TKP', 'TKP'],
    ['GEO', 'GEO'],
    ['DPIB', 'DPIB'],
];

foreach ($jurusanData as $j) {
    $kode = mysqli_real_escape_string($koneksi, $j[0]);
    $nama = mysqli_real_escape_string($koneksi, $j[1]);
    if (mysqli_query($koneksi, "INSERT INTO jurusan (kode_jurusan, nama_jurusan) VALUES ('$kode', '$nama')")) {
        echo "✅ Inserted: $nama ($kode)\n";
    } else {
        echo "❌ Failed: $nama - " . mysqli_error($koneksi) . "\n";
    }
}

echo "\nSelesai!\n";
?>