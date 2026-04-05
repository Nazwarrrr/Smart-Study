<?php
/**
 * Smart Study Planner — Update status tugas (tandai selesai)
 */
require_once __DIR__ . '/auth.php';
require_login_siswa();

require_once __DIR__ . '/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tugas.php');
    exit;
}

$csrf = isset($_POST['_csrf_token']) ? (string) $_POST['_csrf_token'] : '';
if (!verify_csrf_token($csrf)) {
    header('Location: tugas.php?error=' . rawurlencode('Permintaan tidak valid. Silakan muat ulang halaman.'));
    exit;
}

$id       = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$action   = isset($_POST['action']) ? trim($_POST['action']) : '';
$redirect = isset($_POST['redirect']) ? trim($_POST['redirect']) : 'tugas';

if ($id <= 0) {
    header('Location: tugas.php?error=' . rawurlencode('ID tugas tidak valid'));
    exit;
}

if ($action !== 'selesai') {
    header('Location: tugas.php?error=' . rawurlencode('Aksi tidak valid'));
    exit;
}

$sql = 'UPDATE tasks SET status = ?, completed_at = NOW() WHERE id = ? AND status = ?';
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    header('Location: tugas.php?error=' . rawurlencode('Gagal menyiapkan query'));
    exit;
}

$status_baru = 'selesai';
$status_lama = 'pending';

mysqli_stmt_bind_param($stmt, 'sis', $status_baru, $id, $status_lama);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($ok && mysqli_affected_rows($conn) > 0) {
    if ($redirect === 'detail') {
        header('Location: detail.php?id=' . $id . '&done=1');
    } else {
        header('Location: tugas.php?done=1');
    }
} else {
    $err = 'Tugas sudah selesai atau tidak ditemukan';
    if ($redirect === 'detail') {
        header('Location: detail.php?id=' . $id . '&error=' . rawurlencode($err));
    } else {
        header('Location: tugas.php?error=' . rawurlencode($err));
    }
}
exit;
