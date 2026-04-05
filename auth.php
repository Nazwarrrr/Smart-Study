<?php
/**
 * Smart Study Planner — Session & proteksi halaman (1 role: Siswa)
 * Sertakan di halaman yang wajib login (dashboard, detail, proses CRUD).
 */

if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/**
 * Generate atau ambil CSRF token dari session.
 */
function get_csrf_token()
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

/**
 * Verifikasi CSRF token.
 */
function verify_csrf_token($token)
{
    if (empty($_SESSION['_csrf_token']) || !is_string($token)) {
        return false;
    }
    return hash_equals($_SESSION['_csrf_token'], $token);
}

/**
 * Jika belum login → kirim ke login.php
 */
function require_login_siswa()
{
    if (empty($_SESSION['siswa_login'])) {
        header('Location: login.php');
        exit;
    }
}
