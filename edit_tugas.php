<?php
/**
 * Form edit tugas — isi value berdasarkan data tugas yang ada
 */
require_once __DIR__ . '/auth.php';
require_login_siswa();

require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/helpers.php';

$navActive = 'tugas';
$error = '';
$task = null;
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    $sql = 'SELECT id, title, description, deadline, priority, category, status FROM tasks WHERE id = ? LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res) {
            $task = mysqli_fetch_assoc($res);
        }
        mysqli_stmt_close($stmt);
    }
}

if (!$task) {
    $error = 'Tugas tidak ditemukan.';
}

$flashError = !empty($_GET['error']) ? urldecode($_GET['error']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tugas — Smart Study Planner</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="page-fade-in" data-page="edit">
    <?php require __DIR__ . '/includes/nav.php'; ?>

    <div class="page page--narrow">
        <p class="back-row"><a class="link-back" href="tugas.php">&larr; Kembali ke daftar tugas</a></p>

        <header class="dash-header dash-header--simple">
            <h1 class="dash-header__title">Edit tugas</h1>
            <p class="dash-header__hello">Ubah data tugas dan simpan perubahan.</p>
        </header>

        <?php if ($error !== '') : ?>
            <div class="alert alert--error" role="alert"><?php echo e($error); ?></div>
        <?php elseif ($flashError !== '') : ?>
            <div class="alert alert--error" role="alert"><?php echo e($flashError); ?></div>
        <?php endif; ?>

        <?php if ($task) : ?>
            <section class="card card--form-tambah" aria-labelledby="form-judul">
                <h2 id="form-judul" class="card__title">Edit data tugas</h2>
                <form id="form-edit" action="edit.php" method="post" novalidate>
                    <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars(get_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="id" value="<?php echo (int) $task['id']; ?>">
                    <div class="form-grid">
                        <div class="form-group form-group--full">
                            <label for="title">Judul tugas</label>
                            <input type="text" id="title" name="title" maxlength="255" placeholder="Contoh: Essay Bahasa Indonesia" autocomplete="off" value="<?php echo e($task['title']); ?>">
                            <div id="err-title" class="form-error">Judul wajib diisi.</div>
                        </div>
                        <div class="form-group form-group--full">
                            <label for="description">Deskripsi tugas</label>
                            <textarea id="description" name="description" rows="4" maxlength="5000" placeholder="Jelaskan tugas, sumber, atau langkah pengerjaan..."><?php echo e($task['description']); ?></textarea>
                            <div id="err-description" class="form-error">Deskripsi wajib diisi.</div>
                        </div>
                        <div class="form-group">
                            <label for="deadline">Deadline</label>
                            <input type="date" id="deadline" name="deadline" value="<?php echo e($task['deadline']); ?>">
                            <div id="err-deadline" class="form-error">Pilih tanggal deadline.</div>
                        </div>
                        <div class="form-group">
                            <label for="priority">Prioritas</label>
                            <select id="priority" name="priority">
                                <option value="tinggi" <?php echo $task['priority'] === 'tinggi' ? 'selected' : ''; ?>>Tinggi</option>
                                <option value="sedang" <?php echo $task['priority'] === 'sedang' ? 'selected' : ''; ?>>Sedang</option>
                                <option value="rendah" <?php echo $task['priority'] === 'rendah' ? 'selected' : ''; ?>>Rendah</option>
                            </select>
                        </div>
                        <div class="form-group form-group--full">
                            <label for="category">Kategori</label>
                            <select id="category" name="category">
                                <option value="">— Pilih kategori —</option>
                                <option value="PR" <?php echo $task['category'] === 'PR' ? 'selected' : ''; ?>>PR</option>
                                <option value="Ujian" <?php echo $task['category'] === 'Ujian' ? 'selected' : ''; ?>>Ujian</option>
                                <option value="Project" <?php echo $task['category'] === 'Project' ? 'selected' : ''; ?>>Project</option>
                            </select>
                            <div id="err-category" class="form-error">Kategori wajib dipilih.</div>
                        </div>
                    </div>
                    <div class="form-actions form-actions--split">
                        <button type="submit" class="btn btn--primary">Simpan perubahan</button>
                        <a class="btn btn--ghost" href="detail.php?id=<?php echo (int) $task['id']; ?>">Batal</a>
                    </div>
                </form>
            </section>
        <?php endif; ?>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
