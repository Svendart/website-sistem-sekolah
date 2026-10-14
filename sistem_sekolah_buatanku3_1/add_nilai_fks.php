<?php
require_once 'config/koneksi.php';

$fks = [
    "ALTER TABLE `nilai` ADD CONSTRAINT `fk_nilai_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE",
    "ALTER TABLE `nilai` ADD CONSTRAINT `fk_nilai_mapel` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`) ON DELETE CASCADE",
    "ALTER TABLE `nilai` ADD CONSTRAINT `fk_nilai_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE",
    "ALTER TABLE `nilai` ADD CONSTRAINT `fk_nilai_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE SET NULL",
];

foreach ($fks as $sql) {
    if (mysqli_query($koneksi, $sql)) {
        echo "✅ " . substr($sql, 0, 60) . "...\n";
    } else {
        echo "⚠️ " . substr($sql, 0, 60) . "... - " . mysqli_error($koneksi) . "\n";
    }
}

// Verify
$res = mysqli_query($koneksi, "DESCRIBE nilai");
echo "\nStruktur nilai final:\n";
while ($row = mysqli_fetch_assoc($res)) {
    echo " - {$row['Field']} ({$row['Type']})" . ($row['Key'] ? " [{$row['Key']}]" : "") . "\n";
}
?>