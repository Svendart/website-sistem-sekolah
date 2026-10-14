<?php
require_once 'config/koneksi.php';

// Disable foreign key checks first
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");

// Drop table
if (mysqli_query($koneksi, "DROP TABLE IF EXISTS `guru`")) {
    echo "✅ Tabel guru lama dihapus\n";
} else {
    echo "⚠️ Gagal hapus tabel: " . mysqli_error($koneksi) . "\n";
}

// Re-enable foreign key checks
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");

// Buat tabel baru
$sql = "
CREATE TABLE `guru` (
    `id_guru` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nip` VARCHAR(30) NOT NULL,
    `nama_guru` VARCHAR(100) NOT NULL,
    `jenis_kelamin` ENUM('Laki-Laki','Perempuan') NOT NULL DEFAULT 'Laki-Laki',
    `alamat` TEXT,
    `no_hp` VARCHAR(20),
    `email` VARCHAR(50) UNIQUE,
    `id_jurusan` INT UNSIGNED DEFAULT NULL,
    `status` ENUM('Aktif','Tidak Aktif') DEFAULT 'Aktif',
    `tanggal_masuk` DATE,
    `foto` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_guru`),
    UNIQUE KEY `uk_guru_nip` (`nip`),
    UNIQUE KEY `uk_guru_email` (`email`),
    FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id_jurusan`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

if (mysqli_query($koneksi, $sql)) {
    echo "✅ Tabel guru baru berhasil dibuat\n";
} else {
    echo "❌ Gagal buat tabel: " . mysqli_error($koneksi) . "\n";
}

// Verifikasi struktur
$res = mysqli_query($koneksi, "DESCRIBE guru");
echo "\nStruktur tabel guru:\n";
while ($row = mysqli_fetch_assoc($res)) {
    echo " - {$row['Field']} ({$row['Type']})" . ($row['Key'] ? " [{$row['Key']}]" : "") . "\n";
}
?>