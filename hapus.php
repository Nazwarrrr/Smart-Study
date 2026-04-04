<?php
/**
 * Smart Study Planner — Hapus tugas → kembali ke daftar tugas
 */
require_once __DIR__ . '/auth.php';
require_login_siswa();

require_once __DIR__ . '/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tugas.php');
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id <= 0) {
    header('Location: tugas.php?error=' . rawurlencode('ID tugas tidak valid'));
    exit;
}

$sql = 'DELETE FROM tasks WHERE id = ?';
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    header('Location: tugas.php?error=' . rawurlencode('Gagal menyiapkan query'));
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $id);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($ok && mysqli_affected_rows($conn) > 0) {
    header('Location: tugas.php?deleted=1');
} else {
    header('Location: tugas.php?error=' . rawurlencode('Tugas tidak ditemukan atau gagal dihapus'));
}
exit;
