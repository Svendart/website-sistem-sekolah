<?php
require_once 'config/koneksi.php';

// Add updated_at column
$sql = "ALTER TABLE `kelas` ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`";
if (mysqli_query($koneksi, $sql)) {
    echo "✅ Added updated_at column\n";
} else {
    echo "⚠️ updated_at: " . mysqli_error($koneksi) . "\n";
}

// Add UNIQUE constraint
$sql = "ALTER TABLE `kelas` ADD UNIQUE KEY `uk_kelas_nama_tahun` (`nama_kelas`, `tahun_ajaran`)";
if (mysqli_query($koneksi, $sql)) {
    echo "✅ Added UNIQUE constraint (nama_kelas, tahun_ajaran)\n";
} else {
    echo "⚠️ UNIQUE constraint: " . mysqli_error($koneksi) . "\n";
}

// Add FK id_jurusan
$sql = "ALTER TABLE `kelas` ADD CONSTRAINT `fk_kelas_jurusan` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id_jurusan`) ON DELETE SET NULL";
if (mysqli_query($koneksi, $sql)) {
    echo "✅ Added FK id_jurusan -> jurusan\n";
} else {
    echo "⚠️ FK id_jurusan: " . mysqli_error($koneksi) . "\n";
}

// Add FK id_wali_kelas
$sql = "ALTER TABLE `kelas` ADD CONSTRAINT `fk_kelas_wali` FOREIGN KEY (`id_wali_kelas`) REFERENCES `guru` (`id_guru`) ON DELETE SET NULL";
if (mysqli_query($koneksi, $sql)) {
    echo "✅ Added FK id_wali_kelas -> guru\n";
} else {
    echo "⚠️ FK id_wali_kelas: " . mysqli_error($koneksi) . "\n";
}

// Verify
$res = mysqli_query($koneksi, "DESCRIBE kelas");
echo "\nStruktur kelas final:\n";
while ($row = mysqli_fetch_assoc($res)) {
    echo " - {$row['Field']} ({$row['Type']})" . ($row['Key'] ? " [{$row['Key']}]" : "") . "\n";
}

// Show constraints
$res = mysqli_query($koneksi, "SHOW CREATE TABLE kelas");
$row = mysqli_fetch_assoc($res);
echo "\nCREATE TABLE statement:\n" . $row['Create Table'] . "\n";
?>