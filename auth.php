<?php
/**
 * Smart Study Planner — Session & proteksi halaman (1 role: Siswa)
 * Sertakan di halaman yang wajib login (dashboard, detail, proses CRUD).
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
