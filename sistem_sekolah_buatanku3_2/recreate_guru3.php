<?php
require_once 'config/koneksi.php';

// Cek apakah tabel jurusan ada
$res = mysqli_query($koneksi, "SHOW TABLES LIKE 'jurusan'");
if (mysqli_num_rows($res) === 0) {
    echo "⚠️ Tabel 'jurusan' TIDAK ADA!\n";
    echo "Membuat tabel jurusan dulu...\n";
    
    $sqlJurusan = "
    CREATE TABLE `jurusan` (
        `id_jurusan` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `nama_jurusan` VARCHAR(50) NOT NULL,
        `kode_jurusan` VARCHAR(10) UNIQUE,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id_jurusan`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    
    if (mysqli_query($koneksi, $sqlJurusan)) {
        echo "✅ Tabel jurusan dibuat\n";
        
        // Insert data jurusan default
        $jurusanData = [
            ['PPLG', 'PPLG'],
            ['TJKT', 'TJKT'],
            ['GEO', 'GEO'],
            ['TO', 'TO'],
            ['TM', 'TM'],
            ['TKP', 'TKP'],
            ['DPIB', 'DPIB'],
            ['TE', 'TE'],
            ['TKL', 'TKL'],
            ['TPFL', 'TPFL'],
        ];
        
        foreach ($jurusanData as $j) {
            $kode = mysqli_real_escape_string($koneksi, $j[0]);
            $nama = mysqli_real_escape_string($koneksi, $j[1]);
            mysqli_query($koneksi, "INSERT INTO jurusan (kode_jurusan, nama_jurusan) VALUES ('$kode', '$nama')");
        }
        echo "✅ Data jurusan default diinsert\n";
    } else {
        echo "❌ Gagal buat jurusan: " . mysqli_error($koneksi) . "\n";
    }
} else {
    echo "✅ Tabel jurusan sudah ada\n";
    
    // Cek struktur
    $res = mysqli_query($koneksi, "DESCRIBE jurusan");
    echo "Struktur jurusan:\n";
    while ($row = mysqli_fetch_assoc($res)) {
        echo " - {$row['Field']} ({$row['Type']})" . ($row['Key'] ? " [{$row['Key']}]" : "") . "\n";
    }
}

// Buat tabel guru tanpa foreign key dulu
echo "\nMembuat tabel guru (tanpa FK)...\n";
$sqlGuru = "
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

if (mysqli_query($koneksi, $sqlGuru)) {
    echo "✅ Tabel guru baru berhasil dibuat (tanpa FK)\n";
} else {
    echo "❌ Gagal: " . mysqli_error($koneksi) . "\n";
}

// Verifikasi
$res = mysqli_query($koneksi, "DESCRIBE guru");
echo "\nStruktur guru:\n";
while ($row = mysqli_fetch_assoc($res)) {
    echo " - {$row['Field']} ({$row['Type']})" . ($row['Key'] ? " [{$row['Key']}]" : "") . "\n";
}
?>