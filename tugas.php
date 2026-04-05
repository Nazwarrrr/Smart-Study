<?php
/**
 * Daftar tugas — card, klik judul/detail → halaman detail; tombol tambah tugas
 */
require_once __DIR__ . '/auth.php';
require_login_siswa();

require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/helpers.php';

$navActive = 'tugas';

$sql = "SELECT id, title, description, deadline, priority, category, status, created_at, completed_at
        FROM tasks
        ORDER BY (status = 'pending') DESC,
                 FIELD(priority, 'tinggi', 'sedang', 'rendah'),
                 deadline ASC";
$result = mysqli_query($conn, $sql);

$tasks   = [];
$total   = 0;
$selesai = 0;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $tasks[] = $row;
        $total++;
        if ($row['status'] === 'selesai') {
            $selesai++;
        }
    }
} else {
    $dbError = mysqli_error($conn);
}

$flashSuccess = '';
$flashError   = '';

if (!empty($_GET['success'])) {
    $flashSuccess = 'Tugas berhasil ditambahkan.';
}
if (!empty($_GET['deleted'])) {
    $flashSuccess = 'Tugas berhasil dihapus.';
}
if (!empty($_GET['error'])) {
    $flashError = urldecode($_GET['error']);
}

$showCongrats = !empty($_GET['done']);

$namaSiswa = !empty($_SESSION['siswa_nama']) ? $_SESSION['siswa_nama'] : 'Siswa';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas — Smart Study Planner</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="page-fade-in" data-page="tugas" data-show-congrats="<?php echo $showCongrats ? '1' : '0'; ?>">
    <?php require __DIR__ . '/includes/nav.php'; ?>

    <div class="page">
        <header class="list-header">
            <div class="list-header__text">
                <h1 class="list-header__title">Daftar tugas</h1>
                <p class="list-header__sub">Kelola tugasmu di sini, <?php echo e($namaSiswa); ?>.</p>
            </div>
            <a class="btn btn--primary list-header__cta" href="tambah_tugas.php">+ Tambah tugas</a>
        </header>

        <?php if (!empty($dbError)) : ?>
            <div class="alert alert--error">
                <strong>Database:</strong> <?php echo e($dbError); ?>
            </div>
        <?php endif; ?>

        <?php if ($flashSuccess !== '') : ?>
            <div class="alert alert--success" role="status"><?php echo e($flashSuccess); ?></div>
        <?php endif; ?>
        <?php if ($flashError !== '') : ?>
            <div class="alert alert--error" role="alert"><?php echo e($flashError); ?></div>
        <?php endif; ?>

        <?php if ($total === 0 && empty($dbError)) : ?>
            <div class="empty-state">
                <span class="empty-state__emoji" aria-hidden="true">&#128466;</span>
                <p>Belum ada tugas. Yuk mulai produktif!</p>
                <a class="btn btn--primary empty-state__btn" href="tambah_tugas.php">Tambah tugas pertama</a>
            </div>
        <?php elseif (!empty($tasks)) : ?>
            <div class="task-list">
                <?php foreach ($tasks as $t) :
                    $id          = (int) $t['id'];
                    $dlClass     = kelas_deadline_card($t['deadline'], $t['status']);
                    $isDone      = ($t['status'] === 'selesai');
                    $cardExtra   = $isDone ? 'task-card--done' : '';
                    $descPreview = isset($t['description']) ? trim((string) $t['description']) : '';
                    $catRaw      = isset($t['category']) ? (string) $t['category'] : 'PR';
                    ?>
                    <article class="task-card <?php echo e(trim($dlClass . ' ' . $cardExtra)); ?>">
                        <div class="task-card__top">
                            <h3 class="task-card__title">
                                <a class="task-card__title-link" href="detail.php?id=<?php echo $id; ?>"><?php echo e($t['title']); ?></a>
                            </h3>
                            <div class="task-card__badges">
                                <span class="badge priority-<?php echo e($t['priority']); ?>"><?php echo e($t['priority']); ?></span>
                                <span class="badge <?php echo e(kelas_kategori_css($catRaw)); ?>"><?php echo e($catRaw); ?></span>
                            </div>
                        </div>
                        <?php if ($descPreview !== '') : ?>
                            <p class="task-card__excerpt"><?php echo ringkas_deskripsi($descPreview, 120); ?></p>
                        <?php endif; ?>
                        <div class="deadline-row">
                            <span aria-hidden="true">&#128197;</span>
                            Deadline: <?php echo tampil_tanggal($t['deadline']); ?>
                            <?php if ($t['deadline'] === date('Y-m-d') && !$isDone) : ?>
                                <span>— hari ini</span>
                            <?php elseif ($t['deadline'] === date('Y-m-d', strtotime('+1 day')) && !$isDone) : ?>
                                <span>— besok</span>
                            <?php endif; ?>
                        </div>
                        <div class="task-card__actions">
                            <a class="btn btn--outline" href="detail.php?id=<?php echo $id; ?>">Detail</a>
                            <a class="btn btn--outline" href="edit_tugas.php?id=<?php echo $id; ?>">Edit</a>
                            <?php if (!$isDone) : ?>
                                <form action="update.php" method="post" class="form-inline">
                                    <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars(get_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                                    <input type="hidden" name="action" value="selesai">
                                    <input type="hidden" name="redirect" value="tugas">
                                    <button type="submit" class="btn btn--success">Selesai</button>
                                </form>
                            <?php endif; ?>
                            <form action="hapus.php" method="post" data-confirm="Yakin ingin menghapus tugas ini?" class="form-inline">
                                <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars(get_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <button type="submit" class="btn btn--danger">Hapus</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div id="modal-congrats" class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-congrats-title" aria-hidden="true" hidden>
        <div class="modal__backdrop" data-close-modal></div>
        <div class="modal__box modal__box--pop">
            <div class="modal__emoji" aria-hidden="true">&#127881;</div>
            <h2 id="modal-congrats-title" class="modal__title">Congratulations!</h2>
            <p class="modal__text">&#127881; Congratulations! Tugas selesai!</p>
            <button type="button" class="btn btn--primary modal-congrats-close">Mantap!</button>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
