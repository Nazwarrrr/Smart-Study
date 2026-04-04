<?php
/**
 * Pintu masuk: sudah login → dashboard, belum → login
 */
require_once __DIR__ . '/auth.php';

if (!empty($_SESSION['siswa_login'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
