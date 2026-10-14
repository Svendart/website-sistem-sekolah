-- Hapus tabel lama jika ada
DROP TABLE IF EXISTS `kelas`;

-- Buat tabel kelas
CREATE TABLE `kelas` (
    `id_kelas` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nama_kelas` VARCHAR(20) NOT NULL,
    `id_jurusan` INT UNSIGNED DEFAULT NULL,
    `id_wali_kelas` INT UNSIGNED DEFAULT NULL,
    `tahun_ajaran` VARCHAR(10) NOT NULL DEFAULT '2024/2025',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_kelas`),
    UNIQUE KEY `uk_kelas_nama_tahun` (`nama_kelas`, `tahun_ajaran`),
    FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id_jurusan`) ON DELETE SET NULL,
    FOREIGN KEY (`id_wali_kelas`) REFERENCES `guru` (`id_guru`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;