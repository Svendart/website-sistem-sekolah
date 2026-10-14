<?php
require_once 'config/koneksi.php';

// Disable FK checks and drop
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");
mysqli_query($koneksi, "DROP TABLE IF EXISTS `guru`");
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");

// Create guru table - STRUKTUR LENGKAP sesuai semua file PHP
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
    UNIQUE KEY `uk_guru_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

if (mysqli_query($koneksi, $sql)) {
    echo "✅ Tabel guru baru dibuat\n";
} else {
    echo "❌ Gagal: " . mysqli_error($koneksi) . "\n";
    exit;
}

// Verifikasi
$res = mysqli_query($koneksi, "DESCRIBE guru");
echo "\nStruktur guru:\n";
while ($row = mysqli_fetch_assoc($res)) {
    echo " - {$row['Field']} ({$row['Type']})" . ($row['Key'] ? " [{$row['Key']}]" : "") . "\n";
}

// Test insert langsung
$test = "INSERT INTO guru (nip, nama_guru, jenis_kelamin, alamat, no_hp, email, id_jurusan, status, tanggal_masuk) 
         VALUES ('GUR001', 'Pak Budi', 'Laki-Laki', 'Jl. Test', '08123456789', 'budi@test.com', 1, 'Aktif', '2024-01-01')";

if (mysqli_query($koneksi, $test)) {
    echo "\n✅ Test INSERT berhasil! ID: " . mysqli_insert_id($koneksi);
} else {
    echo "\n❌ Test INSERT gagal: " . mysqli_error($koneksi);
}
?>