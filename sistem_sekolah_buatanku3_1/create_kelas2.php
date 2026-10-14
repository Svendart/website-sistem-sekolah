<?php
require_once 'config/koneksi.php';

// Disable FK checks
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");
mysqli_query($koneksi, "DROP TABLE IF EXISTS `kelas`");
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");

$sql = "
CREATE TABLE `kelas` (
    `id_kelas` INT NOT NULL AUTO_INCREMENT,
    `nama_kelas` VARCHAR(20) NOT NULL,
    `id_jurusan` INT DEFAULT NULL,
    `id_wali_kelas` INT DEFAULT NULL,
    `tahun_ajaran` VARCHAR(10) NOT NULL DEFAULT '2024/2025',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_kelas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if (mysqli_query($koneksi, $sql)) {
    echo "✅ Tabel kelas berhasil dibuat (super minimal)\n";
} else {
    echo "❌ Gagal: " . mysqli_error($koneksi) . "\n";
    exit;
}

// Verifikasi
$res = mysqli_query($koneksi, "DESCRIBE kelas");
echo "\nStruktur kelas:\n";
while ($row = mysqli_fetch_assoc($res)) {
    echo " - {$row['Field']} ({$row['Type']})" . ($row['Key'] ? " [{$row['Key']}]" : "") . "\n";
}
?>