<?php
/**
 * Proses tambah tugas (INSERT) — judul, deskripsi, deadline, prioritas, kategori wajib
 */
require_once __DIR__ . '/auth.php';
require_login_siswa();

require_once __DIR__ . '/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah_tugas.php');
    exit;
}

$csrf = isset($_POST['_csrf_token']) ? (string) $_POST['_csrf_token'] : '';
if (!verify_csrf_token($csrf)) {
    header('Location: tambah_tugas.php?error=' . rawurlencode('Permintaan tidak valid. Silakan muat ulang halaman.'));
    exit;
}

$title       = isset($_POST['title']) ? trim($_POST['title']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$deadline    = isset($_POST['deadline']) ? trim($_POST['deadline']) : '';
$priority    = isset($_POST['priority']) ? trim($_POST['priority']) : 'sedang';
$category    = isset($_POST['category']) ? trim($_POST['category']) : '';

$allowed_priority = ['tinggi', 'sedang', 'rendah'];
$allowed_category = ['PR', 'Ujian', 'Project'];

if ($title === '') {
    header('Location: tambah_tugas.php?error=' . rawurlencode('Judul tugas wajib diisi'));
    exit;
}

if ($description === '') {
    header('Location: tambah_tugas.php?error=' . rawurlencode('Deskripsi tugas wajib diisi'));
    exit;
}

if ($deadline === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
    header('Location: tambah_tugas.php?error=' . rawurlencode('Deadline tidak valid'));
    exit;
}

if (!in_array($priority, $allowed_priority, true)) {
    $priority = 'sedang';
}

if (!in_array($category, $allowed_category, true)) {
    header('Location: tambah_tugas.php?error=' . rawurlencode('Pilih kategori tugas'));
    exit;
}

if (strlen($description) > 5000) {
    $description = substr($description, 0, 5000);
}

$sql = 'INSERT INTO tasks (title, description, deadline, priority, category, status) VALUES (?, ?, ?, ?, ?, ?)';
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    header('Location: tambah_tugas.php?error=' . rawurlencode('Gagal menyiapkan query'));
    exit;
}

$status = 'pending';
mysqli_stmt_bind_param($stmt, 'ssssss', $title, $description, $deadline, $priority, $category, $status);

$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($ok) {
    header('Location: tugas.php?success=1');
} else {
    header('Location: tambah_tugas.php?error=' . rawurlencode('Gagal menyimpan tugas'));
}
exit;
