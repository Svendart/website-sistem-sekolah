<?php
require_once 'config/koneksi.php';

// Disable FK checks
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");
mysqli_query($koneksi, "DROP TABLE IF EXISTS `nilai`");
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");

// Create nilai table with matching signed INT types
$sql = "
CREATE TABLE `nilai` (
    `id_nilai` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_siswa` INT NOT NULL,
    `id_mapel` INT UNSIGNED NOT NULL,
    `id_guru` INT NOT NULL,
    `id_kelas` INT DEFAULT NULL,
    `semester` ENUM('Ganjil','Genap') NOT NULL DEFAULT 'Ganjil',
    `tahun_ajaran` VARCHAR(10) NOT NULL DEFAULT '2024/2025',
    `nilai_harian` DECIMAL(5,2) DEFAULT 0.00,
    `nilai_uts` DECIMAL(5,2) DEFAULT 0.00,
    `nilai_uas` DECIMAL(5,2) DEFAULT 0.00,
    `nilai_akhir` DECIMAL(5,2) DEFAULT 0.00,
    `keterangan` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_nilai`),
    UNIQUE KEY `uk_nilai_unique` (`id_siswa`, `id_mapel`, `semester`, `tahun_ajaran`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

if (mysqli_query($koneksi, $sql)) {
    echo "✅ Tabel nilai dibuat (signed INT untuk FK)\n";
} else {
    echo "❌ Gagal: " . mysqli_error($koneksi) . "\n";
    exit;
}

// Add FKs
$fks = [
    "ALTER TABLE `nilai` ADD CONSTRAINT `fk_nilai_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE",
    "ALTER TABLE `nilai` ADD CONSTRAINT `fk_nilai_mapel` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`) ON DELETE CASCADE",
    "ALTER TABLE `nilai` ADD CONSTRAINT `fk_nilai_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE",
    "ALTER TABLE `nilai` ADD CONSTRAINT `fk_nilai_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE SET NULL",
];

foreach ($fks as $sql) {
    if (mysqli_query($koneksi, $sql)) {
        echo "✅ " . substr($sql, 0, 60) . "...\n";
    } else {
        echo "⚠️ " . substr($sql, 0, 60) . "... - " . mysqli_error($koneksi) . "\n";
    }
}

// Verify
$res = mysqli_query($koneksi, "DESCRIBE nilai");
echo "\nStruktur nilai final:\n";
while ($row = mysqli_fetch_assoc($res)) {
    echo " - {$row['Field']} ({$row['Type']})" . ($row['Key'] ? " [{$row['Key']}]" : "") . "\n";
}
?>