<?php
/**
 * config/session.php — Manajemen session & autentikasi admin
 * 
 * Include di setiap file yang butuh proteksi login:
 *   require_once __DIR__ . '/session.php';
 *   require_login();
 * 
 * Fungsi tersedia:
 * - is_logged_in()        : bool
 * - current_admin()       : array|null (data admin dari DB)
 * - require_login()       : redirect ke login.php jika belum login
 * - login_admin($user)    : set session setelah login sukses
 * - logout_admin()        : destroy session
 */

// Konfigurasi keamanan session (HARUS sebelum session_start)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
// ini_set('session.cookie_secure', 1); // Aktifkan hanya jika pakai HTTPS

// Mulai session (hanya sekali per request)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Cek apakah user sudah login sebagai admin
 * @return bool
 */
function is_logged_in(): bool
{
    return isset($_SESSION['admin_id']) && is_int((int)$_SESSION['admin_id']);
}

/**
 * Ambil data admin yang sedang login dari database
 * Butuh $koneksi dari koneksi.php
 * @return array|null
 */
function current_admin(): ?array
{
    if (!is_logged_in()) {
        return null;
    }
    
    // Load koneksi di sini agar tidak circular dependency
    require_once __DIR__ . '/koneksi.php';
    global $koneksi;
    
    $id = (int)$_SESSION['admin_id'];
    $idSafe = mysqli_real_escape_string($koneksi, (string)$id);
    
    $sql = "SELECT id_admin, username, nama_lengkap, created_at 
            FROM admin WHERE id_admin = '$idSafe' LIMIT 1";
    $result = mysqli_query($koneksi, $sql);
    
    if ($result && mysqli_num_rows($result) === 1) {
        return mysqli_fetch_assoc($result);
    }
    
    // Data tidak valid (admin dihapus?), hancurkan session
    logout_admin();
    return null;
}

/**
 * Wajib login — redirect ke login.php jika belum
 * Simpan URL tujuan agar bisa redirect balik setelah login
 * @param string $redirectUrl (opsional) default: login.php
 */
function require_login(string $redirectUrl = 'login.php'): void
{
    if (!is_logged_in()) {
        // Simpan URL asal untuk redirect setelah login
        $currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
        $_SESSION['redirect_after_login'] = $currentUrl;
        
        header('Location: ' . $redirectUrl);
        exit;
    }
}

/**
 * Login admin — set session setelah verifikasi password
 * @param array $adminData Hasil query dari tabel admin (id_admin, username, nama_lengkap)
 */
function login_admin(array $adminData): void
{
    $_SESSION['admin_id'] = (int)$adminData['id_admin'];
    $_SESSION['admin_username'] = $adminData['username'];
    $_SESSION['admin_nama'] = $adminData['nama_lengkap'];
    $_SESSION['login_time'] = time();
    
    // Regenerate ID untuk mencegah session fixation
    session_regenerate_id(true);
}

/**
 * Logout admin — hancurkan session sepenuhnya
 */
function logout_admin(): void
{
    // Hapus semua data session
    $_SESSION = [];
    
    // Hapus session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    
    // Destroy session di server
    session_destroy();
}

/**
 * Ambil URL redirect setelah login, lalu hapus dari session
 * @return string URL (default: dashboard.php atau index.php)
 */
function get_redirect_after_login(): string
{
    $url = $_SESSION['redirect_after_login'] ?? 'dashboard.php';
    unset($_SESSION['redirect_after_login']);
    return $url;
}