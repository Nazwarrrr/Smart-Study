<?php
/**
 * Detail tugas — judul, deskripsi, deadline, prioritas, kategori, status
 */
require_once __DIR__ . '/auth.php';
require_login_siswa();

require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/helpers.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$task = null;

if ($id > 0) {
    $sql = 'SELECT id, title, description, deadline, priority, category, status, created_at, completed_at
            FROM tasks WHERE id = ? LIMIT 1';
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

$pageTitle = $task ? e($task['title']) . ' — Detail' : 'Tugas tidak ditemukan';
$showCongrats = !empty($_GET['done']);
$detailError  = !empty($_GET['error']) ? urldecode($_GET['error']) : '';
$navActive    = 'tugas';

// Gabungkan kelas: prioritas (warna tema) + deadline + selesai
$priClass = '';
if ($task) {
    $priClass = kelas_prioritas_detail($task['priority']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> — Smart Study Planner</title>
    <link rel="icon" href="assets/img/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="page-fade-in" data-page="detail" data-show-congrats="<?php echo $showCongrats ? '1' : '0'; ?>">
    <?php require __DIR__ . '/includes/nav.php'; ?>

    <div class="page page--narrow">
        <p class="back-row"><a class="link-back" href="tugas.php">&larr; Kembali ke daftar tugas</a></p>

        <?php if ($detailError !== '') : ?>
            <div class="alert alert--error" role="alert"><?php echo e($detailError); ?></div>
        <?php endif; ?>

        <?php if (!$task) : ?>
            <div class="card detail-card detail-empty">
                <h1 class="detail-heading">Tugas tidak ditemukan</h1>
                <p class="text-muted">ID tidak valid atau sudah dihapus.</p>
            </div>
        <?php else :
            $isDone      = ($task['status'] === 'selesai');
            $dlClass     = kelas_deadline_card($task['deadline'], $task['status']);
            $tid         = (int) $task['id'];
            $descFull    = isset($task['description']) ? trim((string) $task['description']) : '';
            $cat         = isset($task['category']) ? (string) $task['category'] : 'PR';
            $cardClasses = trim($priClass . ' ' . $dlClass . ($isDone ? ' task-card--done' : ''));
            ?>
            <article class="card detail-card detail-card--modern <?php echo e($cardClasses); ?>">
                <div class="detail-card__head detail-card__head--wrap">
                    <span class="badge priority-<?php echo e($task['priority']); ?>"><?php echo e($task['priority']); ?></span>
                    <span class="badge <?php echo e(kelas_kategori_css($cat)); ?>"><?php echo e($cat); ?></span>
                    <?php if ($isDone) : ?>
                        <span class="badge badge--done">Selesai</span>
                    <?php else : ?>
                        <span class="badge badge--pending">Pending</span>
                    <?php endif; ?>
                </div>

                <h1 class="detail-heading"><?php echo e($task['title']); ?></h1>

                <dl class="detail-spec">
                    <div class="detail-spec__row">
                        <dt>Deadline</dt>
                        <dd><span aria-hidden="true">&#128197;</span> <?php echo tampil_tanggal($task['deadline']); ?></dd>
                    </div>
                    <div class="detail-spec__row">
                        <dt>Prioritas</dt>
                        <dd><span class="badge priority-<?php echo e($task['priority']); ?>"><?php echo e($task['priority']); ?></span></dd>
                    </div>
                    <div class="detail-spec__row">
                        <dt>Kategori</dt>
                        <dd><span class="badge <?php echo e(kelas_kategori_css($cat)); ?>"><?php echo e($cat); ?></span></dd>
                    </div>
                    <div class="detail-spec__row">
                        <dt>Status</dt>
                        <dd><?php echo $isDone ? 'Selesai' : 'Belum selesai'; ?></dd>
                    </div>
                </dl>

                <h2 class="detail-sub">Deskripsi lengkap</h2>
                <?php if ($descFull === '') : ?>
                    <p class="text-muted">Tidak ada deskripsi.</p>
                <?php else : ?>
                    <div class="detail-description"><?php echo nl2br(e($descFull)); ?></div>
                <?php endif; ?>

                <div class="task-card__actions detail-actions">
                    <?php if (!$isDone) : ?>
                        <form action="update.php" method="post" class="form-inline">
                            <input type="hidden" name="id" value="<?php echo $tid; ?>">
                            <input type="hidden" name="action" value="selesai">
                            <input type="hidden" name="redirect" value="detail">
                            <button type="submit" class="btn btn--success">Tandai selesai</button>
                        </form>
                    <?php endif; ?>
                    <form action="hapus.php" method="post" data-confirm="Yakin ingin menghapus tugas ini?" class="form-inline">
                        <input type="hidden" name="id" value="<?php echo $tid; ?>">
                        <button type="submit" class="btn btn--danger">Hapus tugas</button>
                    </form>
                </div>
            </article>
        <?php endif; ?>
    </div>

    <div id="modal-congrats" class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-congrats-title-d" aria-hidden="true" hidden>
        <div class="modal__backdrop" data-close-modal></div>
        <div class="modal__box modal__box--pop">
            <div class="modal__emoji" aria-hidden="true">&#127881;</div>
            <h2 id="modal-congrats-title-d" class="modal__title">Congratulations!</h2>
            <p class="modal__text">&#127881; Congratulations! Tugas selesai!</p>
            <button type="button" class="btn btn--primary modal-congrats-close">Mantap!</button>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
