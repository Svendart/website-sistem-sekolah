<?php
require_once 'config/koneksi.php';

// Disable FK checks
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");
mysqli_query($koneksi, "DROP TABLE IF EXISTS `mapel`");
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");

$sql = "
CREATE TABLE `mapel` (
    `id_mapel` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode_mapel` VARCHAR(10) NOT NULL,
    `nama_mapel` VARCHAR(100) NOT NULL,
    `id_jurusan` INT UNSIGNED DEFAULT NULL,
    `tingkat` VARCHAR(10) NOT NULL DEFAULT 'X',
    `semester` ENUM('Ganjil','Genap') NOT NULL DEFAULT 'Ganjil',
    `jam_per_minggu` INT NOT NULL DEFAULT 2,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_mapel`),
    UNIQUE KEY `uk_mapel_kode` (`kode_mapel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

if (mysqli_query($koneksi, $sql)) {
    echo "✅ Tabel mapel berhasil dibuat (tanpa FK)\n";
} else {
    echo "❌ Gagal: " . mysqli_error($koneksi) . "\n";
    exit;
}

// Verifikasi
$res = mysqli_query($koneksi, "DESCRIBE mapel");
echo "\nStruktur mapel:\n";
while ($row = mysqli_fetch_assoc($res)) {
    echo " - {$row['Field']} ({$row['Type']})" . ($row['Key'] ? " [{$row['Key']}]" : "") . "\n";
}
?>