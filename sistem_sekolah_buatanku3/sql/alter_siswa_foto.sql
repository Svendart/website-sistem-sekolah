-- File: sql/alter_siswa_foto.sql
-- Jalankan di phpMyAdmin SETELAH backup database
-- Menambah kolom foto untuk upload gambar siswa

ALTER TABLE `siswa` 
ADD COLUMN `foto` VARCHAR(255) NULL DEFAULT NULL 
AFTER `no_hp`;

-- Catatan:
-- - VARCHAR(255) cukup untuk path file (mis: uploads/siswa/abc123.jpg)
-- - NULL default: siswa lama tidak wajib punya foto
-- - AFTER no_hp: urutan kolom rapi (opsional)