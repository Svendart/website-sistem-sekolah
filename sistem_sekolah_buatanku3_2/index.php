<?php
/**
 * index.php — Landing page / Entry point
 * 
 * - Jika sudah login → redirect ke dashboard.php
 * - Jika belum login → tampil landing page dengan link ke Data Siswa (akan redirect ke login)
 */

require_once __DIR__ . '/config/session.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Informasi Sekolah</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>SISTEM INFORMASI SEKOLAH</h1>

    <p>
        Selamat datang di Sistem Informasi Sekolah.
    </p>

    <a class="btn tombol-dashboard" href="siswa/index.php">
        Data Siswa
    </a>

    <p style="margin-top: 20px; color: #64748b; font-size: 14px;">
        Silakan login sebagai administrator untuk mengakses fitur penuh.
        <br>
        <a href="login.php" style="color: #0d6efd;">→ Login Administrator</a>
    </p>
</div>
</body>
</html>