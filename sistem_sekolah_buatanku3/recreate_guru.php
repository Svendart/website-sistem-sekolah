<?php
require_once 'config/koneksi.php';

$sql = file_get_contents(__DIR__ . '/sql/recreate_guru_table.sql');

// Split by semicolon and execute each statement
$statements = array_filter(array_map('trim', explode(';', $sql)));

$success = 0;
$failed = 0;

foreach ($statements as $stmt) {
    if (!empty($stmt)) {
        if (mysqli_query($koneksi, $stmt)) {
            $success++;
            echo "✅ Berhasil: " . substr($stmt, 0, 60) . "...\n";
        } else {
            $failed++;
            echo "❌ Gagal: " . substr($stmt, 0, 60) . "...\n";
            echo "   Error: " . mysqli_error($koneksi) . "\n";
        }
    }
}

echo "\nTotal: $success berhasil, $failed gagal\n";
?>