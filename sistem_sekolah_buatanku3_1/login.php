<?php
/**
 * login.php — Halaman login administrator
 * 
 * Alur:
 * 1. Jika sudah login → redirect ke dashboard.php
 * 2. Tampil form login (username + password)
 * 3. POST → validasi ke DB → password_verify() → set session → redirect
 */

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/session.php';

// Jika sudah login, redirect ke dashboard
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$pesanError = '';
$usernameInput = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($username === '' || $password === '') {
        $pesanError = 'Username dan password wajib diisi.';
    } else {
        // Query admin berdasarkan username
        $usernameSafe = mysqli_real_escape_string($koneksi, $username);
        $sql = "SELECT id_admin, username, password_hash, nama_lengkap 
                FROM admin WHERE username = '$usernameSafe' LIMIT 1";
        $result = mysqli_query($koneksi, $sql);
        
        if ($result && mysqli_num_rows($result) === 1) {
            $admin = mysqli_fetch_assoc($result);
            
            // Verifikasi password dengan password_verify()
            if (password_verify($password, $admin['password_hash'])) {
                // Login berhasil
                login_admin($admin);
                
                // Redirect ke halaman yang diminta (atau dashboard)
                $redirect = get_redirect_after_login();
                header('Location: ' . $redirect);
                exit;
            }
        }
        
        // Username tidak ditemukan atau password salah
        $pesanError = 'Username atau password salah.';
    }
    
    $usernameInput = htmlspecialchars($username);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Administrator</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .container { max-width: 420px; margin: 80px auto; }
        .login-card { background: white; padding: 35px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .login-header { text-align: center; margin-bottom: 30px; }
        .login-header h1 { margin: 0 0 8px; color: #1e293b; font-size: 26px; }
        .login-header p { margin: 0; color: #64748b; font-size: 14px; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 14px; }
        input[type="text"], input[type="password"] { 
            width: 100%; padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px; 
            font-size: 15px; box-sizing: border-box; transition: border-color 0.2s, box-shadow 0.2s;
        }
        input:focus { outline: none; border-color: #0d6efd; box-shadow: 0 0 0 3px rgba(13,110,253,0.15); }
        .btn-login { 
            width: 100%; padding: 14px; background: #0d6efd; color: white; border: none; 
            border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; 
            transition: background 0.2s;
        }
        .btn-login:hover { background: #0b5ed7; }
        .footer-note { text-align: center; margin-top: 24px; color: #64748b; font-size: 13px; }
        .footer-note a { color: #0d6efd; text-decoration: none; }
        .footer-note a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="container">
    <div class="login-card">
        <div class="login-header">
            <h1>🔐 Login Administrator</h1>
            <p>Sistem Informasi Sekolah</p>
        </div>

        <?php if ($pesanError): ?>
            <div class="alert-error"><?= $pesanError ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= $usernameInput ?>" required autocomplete="username" autofocus placeholder="Masukkan username">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Masukkan password">
            </div>

            <button type="submit" class="btn-login">Masuk</button>
        </form>

        <div class="footer-note">
            Belum punya akun? Jalankan <code>setup_admin.php</code> (hapus setelah dipakai).
        </div>
    </div>
</div>
</body>
</html>