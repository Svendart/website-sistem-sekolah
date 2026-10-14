<?php
require_once 'config/koneksi.php';

// Drop and recreate guru table completely
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");
mysqli_query($koneksi, "DROP TABLE IF EXISTS `guru`");
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");

// Create guru table with MINIMAL columns - no id_jurusan for now
$sql = "
CREATE TABLE `guru` (
    `id_guru` INT NOT NULL AUTO_INCREMENT,
    `nip` VARCHAR(30) NOT NULL,
    `nama_guru` VARCHAR(100) NOT NULL,
    `jenis_kelamin` ENUM('Laki-Laki','Perempuan') NOT NULL DEFAULT 'Laki-Laki',
    `alamat` TEXT,
    `no_hp` VARCHAR(20),
    `email` VARCHAR(50),
    `status` ENUM('Aktif','Tidak Aktif') DEFAULT 'Aktif',
    `tanggal_masuk` DATE,
    `foto` VARCHAR(255),
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_guru`),
    UNIQUE KEY `uk_guru_nip` (`nip`),
    UNIQUE KEY `uk_guru_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

if (mysqli_query($koneksi, $sql)) {
    echo "✅ Tabel guru baru berhasil dibuat (minimal, no FK)\n";
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

// Test insert
$test = "INSERT INTO guru (nip, nama_guru, jenis_kelamin, alamat, no_hp, email, status, tanggal_masuk) 
         VALUES ('GUR001', 'Test Guru', 'Laki-Laki', 'Alamat Test', '08123456789', 'test@test.com', 'Aktif', '2024-01-01')";

if (mysqli_query($koneksi, $test)) {
    echo "\n✅ Test INSERT berhasil! ID: " . mysqli_insert_id($koneksi);
} else {
    echo "\n❌ Test INSERT gagal: " . mysqli_error($koneksi);
}
?>