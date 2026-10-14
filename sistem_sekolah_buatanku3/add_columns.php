<?php
// Add missing columns to guru table
$conn = new mysqli('localhost', 'root', '', 'database_sekolah');

$sqls = [
    "ALTER TABLE guru ADD COLUMN email VARCHAR(50) UNIQUE AFTER no_hp",
    "ALTER TABLE guru ADD COLUMN tanggal_masuk DATE AFTER email",
    "ALTER TABLE guru ADD COLUMN status ENUM('Aktif', 'Tidak Aktif') DEFAULT 'Aktif' AFTER tanggal_masuk",
    "ALTER TABLE guru CHANGE COLUMN nama_guru nama_lengkap VARCHAR(100) NOT NULL AFTER nip"
];

foreach ($sqls as $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "BERHASIL: " . substr($sql, 0, 50) . "...\n";
    } else {
        echo "GAGAL: " . $sql . "<br>" . mysqli_error($conn) . "\n";
    }
}

mysqli_close($conn);