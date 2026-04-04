<?php
/**
 * Form tambah tugas — field lengkap + validasi di JS
 */
require_once __DIR__ . '/auth.php';
require_login_siswa();

require_once __DIR__ . '/includes/helpers.php';

$navActive = 'tugas';

$flashError = !empty($_GET['error']) ? urldecode($_GET['error']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tugas — Smart Study Planner</title>
    <link rel="icon" href="assets/img/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="page-fade-in" data-page="tambah">
    <?php require __DIR__ . '/includes/nav.php'; ?>

    <div class="page page--narrow">
        <p class="back-row"><a class="link-back" href="tugas.php">&larr; Kembali ke daftar tugas</a></p>

        <header class="dash-header dash-header--simple">
            <h1 class="dash-header__title">Tambah tugas</h1>
            <p class="dash-header__hello">Lengkapi semua field wajib untuk menyimpan tugas.</p>
        </header>

        <?php if ($flashError !== '') : ?>
            <div class="alert alert--error" role="alert"><?php echo e($flashError); ?></div>
        <?php endif; ?>

        <section class="card card--form-tambah" aria-labelledby="form-judul">
            <h2 id="form-judul" class="card__title">Data tugas</h2>
            <form id="form-tambah" action="tambah.php" method="post" novalidate>
                <div class="form-grid">
                    <!-- Judul (wajib) -->
                    <div class="form-group form-group--full">
                        <label for="title">Judul tugas</label>
                        <input type="text" id="title" name="title" maxlength="255" placeholder="Contoh: Essay Bahasa Indonesia" autocomplete="off">
                        <div id="err-title" class="form-error">Judul wajib diisi.</div>
                    </div>
                    <!-- Deskripsi (wajib) -->
                    <div class="form-group form-group--full">
                        <label for="description">Deskripsi tugas</label>
                        <textarea id="description" name="description" rows="4" maxlength="5000" placeholder="Jelaskan tugas, sumber, atau langkah pengerjaan..."></textarea>
                        <div id="err-description" class="form-error">Deskripsi wajib diisi.</div>
                    </div>
                    <!-- Deadline (wajib) -->
                    <div class="form-group">
                        <label for="deadline">Deadline</label>
                        <input type="date" id="deadline" name="deadline">
                        <div id="err-deadline" class="form-error">Pilih tanggal deadline.</div>
                    </div>
                    <!-- Prioritas (wajib — selalu ada nilai) -->
                    <div class="form-group">
                        <label for="priority">Prioritas</label>
                        <select id="priority" name="priority">
                            <option value="tinggi">Tinggi</option>
                            <option value="sedang" selected>Sedang</option>
                            <option value="rendah">Rendah</option>
                        </select>
                    </div>
                    <!-- Kategori (wajib) -->
                    <div class="form-group form-group--full">
                        <label for="category">Kategori</label>
                        <select id="category" name="category">
                            <option value="">— Pilih kategori —</option>
                            <option value="PR">PR</option>
                            <option value="Ujian">Ujian</option>
                            <option value="Project">Project</option>
                        </select>
                        <div id="err-category" class="form-error">Kategori wajib dipilih.</div>
                    </div>
                </div>
                <div class="form-actions form-actions--split">
                    <button type="submit" class="btn btn--primary">Simpan tugas</button>
                    <a class="btn btn--ghost" href="tugas.php">Batal</a>
                </div>
            </form>
        </section>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
