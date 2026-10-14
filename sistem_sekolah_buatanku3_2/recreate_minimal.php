<?php
require_once 'config/koneksi.php';

// Disable FK checks and drop
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");
mysqli_query($koneksi, "DROP TABLE IF EXISTS `guru`");
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");

// SUPER MINIMAL - only essential columns, NO constraints except PK
$sql = "
CREATE TABLE `guru` (
    `id_guru` INT NOT NULL AUTO_INCREMENT,
    `nip` VARCHAR(30) NOT NULL,
    `nama_guru` VARCHAR(100) NOT NULL,
    `jenis_kelamin` ENUM('Laki-Laki','Perempuan') DEFAULT 'Laki-Laki',
    `alamat` TEXT,
    `no_hp` VARCHAR(20),
    `email` VARCHAR(50),
    `status` ENUM('Aktif','Tidak Aktif') DEFAULT 'Aktif',
    `tanggal_masuk` DATE,
    `foto` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_guru`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if (mysqli_query($koneksi, $sql)) {
    echo "✅ Tabel guru baru dibuat (minimal)\n";
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
$test = "INSERT INTO guru (nip, nama_guru, jenis_kelamin, alamat, no_hp, email) 
         VALUES ('GUR001', 'Pak Budi', 'Laki-Laki', 'Jl. Test', '08123456789', 'budi@test.com')";

if (mysqli_query($koneksi, $test)) {
    echo "\n✅ Test INSERT berhasil! ID: " . mysqli_insert_id($koneksi);
} else {
    echo "\n❌ Test INSERT gagal: " . mysqli_error($koneksi);
}
?>