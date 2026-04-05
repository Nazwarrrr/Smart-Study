<?php
/**
 * Proses edit tugas (UPDATE) — ubah judul, deskripsi, deadline, prioritas, kategori
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
    header('Location: edit_tugas.php?id=' . (isset($_POST['id']) ? (int) $_POST['id'] : 0) . '&error=' . rawurlencode('Permintaan tidak valid. Silakan muat ulang halaman.'));
    exit;
}

$id          = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$title       = isset($_POST['title']) ? trim($_POST['title']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$deadline    = isset($_POST['deadline']) ? trim($_POST['deadline']) : '';
$priority    = isset($_POST['priority']) ? trim($_POST['priority']) : 'sedang';
$category    = isset($_POST['category']) ? trim($_POST['category']) : '';

$allowed_priority = ['tinggi', 'sedang', 'rendah'];
$allowed_category = ['PR', 'Ujian', 'Project'];

if ($id <= 0) {
    header('Location: tugas.php?error=' . rawurlencode('ID tugas tidak valid'));
    exit;
}

if ($title === '') {
    header('Location: edit_tugas.php?id=' . $id . '&error=' . rawurlencode('Judul tugas wajib diisi'));
    exit;
}

if ($description === '') {
    header('Location: edit_tugas.php?id=' . $id . '&error=' . rawurlencode('Deskripsi tugas wajib diisi'));
    exit;
}

if ($deadline === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
    header('Location: edit_tugas.php?id=' . $id . '&error=' . rawurlencode('Deadline tidak valid'));
    exit;
}

if (!in_array($priority, $allowed_priority, true)) {
    $priority = 'sedang';
}

if (!in_array($category, $allowed_category, true)) {
    header('Location: edit_tugas.php?id=' . $id . '&error=' . rawurlencode('Pilih kategori tugas'));
    exit;
}

if (strlen($description) > 5000) {
    $description = substr($description, 0, 5000);
}

$sql = 'UPDATE tasks SET title = ?, description = ?, deadline = ?, priority = ?, category = ? WHERE id = ?';
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    header('Location: edit_tugas.php?id=' . $id . '&error=' . rawurlencode('Gagal menyiapkan query'));
    exit;
}

mysqli_stmt_bind_param($stmt, 'sssssi', $title, $description, $deadline, $priority, $category, $id);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($ok && mysqli_affected_rows($conn) >= 0) {
    header('Location: detail.php?id=' . $id . '&success=1');
} else {
    header('Location: edit_tugas.php?id=' . $id . '&error=' . rawurlencode('Gagal menyimpan perubahan'));
}
exit;
