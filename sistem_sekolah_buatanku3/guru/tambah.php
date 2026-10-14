<?php
/**
 * guru/tambah.php — Halaman Tambah Guru (Level 4)
 * 
 * Form untuk menambah data guru baru beserta validasi dan navigasi.
 */

require_once __DIR__ . '/../config/koneksi.php';

$error = '';
$success = '';

// Proses simpan jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = $_POST['nip'] ?? '';
    $nama = $_POST['nama_lengkap'] ?? '';
    $jk = $_POST['jenis_kelamin'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $email = $_POST['email'] ?? '';
    $tanggal_masuk = $_POST['tanggal_masuk'] ?? '';
    $jurusan_id = $_POST['id_jurusan'] ?? '';
    $status = $_POST['status'] ?? 'Aktif';

    // Validasi
    if ($nip === '' || $nama === '' || $jk === '' || $tanggal_masuk === '' || $jurusan_id === '') {
        $error = 'Field wajib diisi (*)';
    } else {
        // Cek nip duplikat
        $nipSafe = mysqli_real_escape_string($koneksi, $nip);
        $cek = mysqli_query($koneksi, "SELECT id_guru FROM guru WHERE nip = '$nipSafe'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'NIP sudah terdaftar. Gunakan NIP lain.';
        } else {
            // Insert ke database
            $namaSafe = mysqli_real_escape_string($koneksi, $nama);
            $jkSafe = mysqli_real_escape_string($koneksi, $jk);
            $alamatSafe = mysqli_real_escape_string($koneksi, $alamat);
            $no_hpSafe = mysqli_real_escape_string($koneksi, $no_hp);
            $emailSafe = mysqli_real_escape_string($koneksi, $email);
            $juranIdSafe = (int) $jurusan_id;
            $statusSafe = mysqli_real_escape_string($koneksi, $status);

            $sql = "INSERT INTO guru (nip, nama_lengkap, jenis_kelamin, alamat, no_hp, email, id_jurusan, status, tanggal_masuk) 
                    VALUES ('$nipSafe', '$namaSafe', '$jkSafe', '$alamatSafe', '$no_hpSafe', '$emailSafe', $juranIdSafe, '$statusSafe', CURDATE())";

            if (mysqli_query($koneksi, $sql)) {
                $success = '✅ Data guru berhasil disimpan!';
            } else {
                $error = 'Gagal menyimpan: ' . mysqli_error($koneksi);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Guru - Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>➕ Tambah Guru</h1>

    <?php if ($error): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; border: 1px solid #f5c6cb; margin-bottom: 20px;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; border: 1px solid #c3e6cb; margin-bottom: 20px;">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <form action="simpan.php" method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label>NIP <span style="color: #666;">*</span></label>
                <input type="text" name="nip" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>
            <div>
                <label>Nama Guru <span style="color: #666;">*</span></label>
                <input type="text" name="nama_guru" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label>Jenis Kelamin <span style="color: #666;">*</span></label>
            <select name="jenis_kelamin" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                <option value="">-- Pilih --</option>
                <option value="Laki-Laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label>Email <span style="color: #666;">*</span></label>
            <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label>Nomor HP</label>
            <input type="tel" name="no_hp" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label>Alamat</label>
            <textarea name="alamat" rows="3" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;"></textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label>Tanggal Masuk <span style="color: #666;">*</span></label>
            <input type="date" name="tanggal_masuk" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label>Jurusan</span></label>
            <select name="id_jurusan" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                <option value="">-- Pilih Jurusan --</option>
                <?php
                // Ambil data jurusan dari database
                $res = mysqli_query($koneksi, "SELECT * FROM jurusan ORDER BY nama_jurusan");
                if ($res) {
                    while ($row = mysqli_fetch_assoc($res)) {
                        echo '<option value="' . $row['id_jurusan'] . '">' . htmlspecialchars($row['nama_jurusan']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label>Status</span></label>
            <select name="status" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                <option value="Aktif">Aktif</option>
                <option value="Tidak Aktif">Tidak Aktif</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="submit" style="padding: 10px 20px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: simpan;">Simpan Data</button>
            <a href="index.php" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 14px;">Batal</a>
        </div>
    </form>
</div>
</body>
</html>