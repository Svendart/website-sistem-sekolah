<?php
require_once 'config/koneksi.php';

// Add UNIQUE constraints
$constraints = [
    "ALTER TABLE guru ADD UNIQUE KEY `uk_guru_nip` (`nip`)",
    "ALTER TABLE guru ADD UNIQUE KEY `uk_guru_email` (`email`)",
];

foreach ($constraints as $sql) {
    if (mysqli_query($koneksi, $sql)) {
        echo "✅ " . $sql . "\n";
    } else {
        echo "⚠️ " . $sql . " - " . mysqli_error($koneksi) . "\n";
    }
}

// Add updated_at column
$sql = "ALTER TABLE guru ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`";
if (mysqli_query($koneksi, $sql)) {
    echo "✅ Added updated_at column\n";
} else {
    echo "⚠️ updated_at: " . mysqli_error($koneksi) . "\n";
}

// Verify
$res = mysqli_query($koneksi, "DESCRIBE guru");
echo "\nStruktur guru final:\n";
while ($row = mysqli_fetch_assoc($res)) {
    echo " - {$row['Field']} ({$row['Type']})" . ($row['Key'] ? " [{$row['Key']}]" : "") . "\n";
}
?>