<?php
require_once 'config/koneksi.php';

// Add id_jurusan column
$sql = "ALTER TABLE guru ADD COLUMN `id_jurusan` INT UNSIGNED DEFAULT NULL AFTER `email`";
if (mysqli_query($koneksi, $sql)) {
    echo "✅ Added id_jurusan column\n";
} else {
    echo "⚠️ id_jurusan: " . mysqli_error($koneksi) . "\n";
}

// Verify
$res = mysqli_query($koneksi, "DESCRIBE guru");
echo "\nStruktur guru:\n";
while ($row = mysqli_fetch_assoc($res)) {
    echo " - {$row['Field']} ({$row['Type']})" . ($row['Key'] ? " [{$row['Key']}]" : "") . "\n";
}
?>