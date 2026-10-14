<?php
require_once 'config/koneksi.php';

// Check column types of referenced tables
$tables = ['siswa', 'mapel', 'guru', 'kelas'];

foreach ($tables as $table) {
    echo "\n=== $table ===\n";
    $res = mysqli_query($koneksi, "DESCRIBE `$table`");
    while ($row = mysqli_fetch_assoc($res)) {
        if (in_array($row['Field'], ['id_siswa', 'id_mapel', 'id_guru', 'id_kelas', 'id_jurusan'])) {
            echo " - {$row['Field']} ({$row['Type']})" . ($row['Key'] ? " [{$row['Key']}]" : "") . "\n";
        }
    }
}

// Check nilai table
echo "\n=== nilai ===\n";
$res = mysqli_query($koneksi, "DESCRIBE `nilai`");
while ($row = mysqli_fetch_assoc($res)) {
    echo " - {$row['Field']} ({$row['Type']})" . ($row['Key'] ? " [{$row['Key']}]" : "") . "\n";
}
?>