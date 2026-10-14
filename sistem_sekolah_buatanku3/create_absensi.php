<?php
require_once 'config/koneksi.php';

// Disable FK checks
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");
mysqli_query($koneksi, "DROP TABLE IF EXISTS `absensi`");
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");

$sql = "
CREATE TABLE `absensi` (
    `id_absensi` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_siswa` INT NOT NULL,
    `id_mapel` INT UNSIGNED NOT NULL,
    `id_guru` INT NOT NULL,
    `id_kelas` INT DEFAULT NULL,
    `tanggal` DATE NOT NULL,
    `status` ENUM('Hadir','Sakit','Izin','Alpa') NOT NULL DEFAULT 'Hadir',
    `keterangan` VARCHAR(255) DEFAULT NULL,
    `semester` ENUM('Ganjil','Genap') NOT NULL DEFAULT 'Ganjil',
    `tahun_ajaran` VARCHAR(10) NOT NULL DEFAULT '2024/2025',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_absensi`),
    UNIQUE KEY `uk_absensi_unique` (`id_siswa`, `id_mapel`, `tanggal`),
    FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE,
    FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`) ON DELETE CASCADE,
    FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE,
    FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

if (mysqli_query($koneksi, $sql)) {
    echo "✅ Tabel absensi berhasil dibuat\n";
} else {
    echo "❌ Gagal: " . mysqli_error($koneksi) . "\n";
    exit;
}

// Verifikasi
$res = mysqli_query($koneksi, "DESCRIBE absensi");
echo "\nStruktur absensi:\n";
while ($row = mysqli_fetch_assoc($res)) {
    echo " - {$row['Field']} ({$row['Type']})" . ($row['Key'] ? " [{$row['Key']}]" : "") . "\n";
}
?>