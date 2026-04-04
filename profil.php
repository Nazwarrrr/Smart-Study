<?php
/**
 * Profil — nama (dummy session), statistik dari database, progress, streak, quote
 */
require_once __DIR__ . '/auth.php';
require_login_siswa();

require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/helpers.php';

$navActive = 'profil';
$nama      = !empty($_SESSION['siswa_nama']) ? $_SESSION['siswa_nama'] : 'Siswa';

$dbError = null;
$total   = 0;
$selesai = 0;

$r1 = mysqli_query($conn, 'SELECT COUNT(*) AS c FROM tasks');
if ($r1) {
    $row = mysqli_fetch_assoc($r1);
    $total = (int) ($row['c'] ?? 0);
} else {
    $dbError = mysqli_error($conn);
}

$r2 = mysqli_query($conn, "SELECT COUNT(*) AS c FROM tasks WHERE status = 'selesai'");
if ($r2) {
    $row = mysqli_fetch_assoc($r2);
    $selesai = (int) ($row['c'] ?? 0);
} elseif ($dbError === null) {
    $dbError = mysqli_error($conn);
}

$pending = max(0, $total - $selesai);
$percent = $total > 0 ? (int) round(($selesai / $total) * 100) : 0;
$streak  = ($dbError === null) ? hitung_streak($conn) : 0;

$quotes = [
    'Belajar konsisten hari ini adalah hadiah untuk dirimu di esok hari.',
    'Satu tugas selesai = satu langkah lebih dekat ke tujuan.',
    'Jangan tunggai mood; mulai kecil, hasilnya akan mengikuti.',
    'Progress 1% setiap hari dalam sebulan jadi banyak perubahan.',
    'Deadline bukan musuh; itu penanda supaya kamu tetap on track.',
];
$quoteHariIni = $quotes[array_rand($quotes)];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil — Smart Study Planner</title>
    <link rel="icon" href="assets/img/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="page-fade-in" data-page="profil">
    <?php require __DIR__ . '/includes/nav.php'; ?>

    <div class="page page--narrow">
        <header class="dash-header dash-header--simple">
            <h1 class="dash-header__title">Profil</h1>
            <p class="dash-header__hello">Ringkasan performa belajar dari data tugasmu.</p>
        </header>

        <?php if ($dbError !== null) : ?>
            <div class="alert alert--error" role="alert">
                <strong>Database:</strong> <?php echo e($dbError); ?>
            </div>
        <?php endif; ?>

        <section class="card profil-card profil-card--hero">
            <img class="profil-card__logo" src="assets/img/logo.svg" alt="" width="88" height="88">
            <h2 class="profil-card__nama"><?php echo e($nama); ?></h2>
            <p class="profil-card__role">Peran: <strong>Siswa</strong> (akun demo)</p>
        </section>

        <?php if ($total === 0 && $dbError === null) : ?>
            <div class="empty-state empty-state--profil">
                <span class="empty-state__emoji" aria-hidden="true">&#128203;</span>
                <p>Belum ada data tugas. Tambah tugas dulu agar statistik muncul di sini.</p>
                <a class="btn btn--primary empty-state__btn" href="tambah_tugas.php">Tambah tugas</a>
            </div>
        <?php else : ?>
            <section class="card profil-stats">
                <h2 class="card__title">Statistik tugas</h2>
                <div class="profil-stats__grid">
                    <div class="profil-stat profil-stat--fade">
                        <span class="profil-stat__label">Total tugas</span>
                        <span class="profil-stat__value"><?php echo (int) $total; ?></span>
                    </div>
                    <div class="profil-stat profil-stat--fade profil-stat--delay1">
                        <span class="profil-stat__label">Selesai</span>
                        <span class="profil-stat__value profil-stat__value--ok"><?php echo (int) $selesai; ?></span>
                    </div>
                    <div class="profil-stat profil-stat--fade profil-stat--delay2">
                        <span class="profil-stat__label">Pending</span>
                        <span class="profil-stat__value profil-stat__value--warn"><?php echo (int) $pending; ?></span>
                    </div>
                </div>

                <div class="profil-progress">
                    <div class="profil-progress__top">
                        <span>Progress selesai</span>
                        <strong><?php echo $percent; ?>%</strong>
                    </div>
                    <div class="progress-bar progress-bar--profil" role="progressbar" aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar__fill" data-width="<?php echo $percent; ?>"></div>
                    </div>
                </div>

                <div class="profil-streak-box">
                    <span class="streak__icon" aria-hidden="true">&#128293;</span>
                    <span>Study streak: <strong><?php echo (int) $streak; ?></strong> hari berturut-turut</span>
                </div>
            </section>
        <?php endif; ?>

        <section class="card card--quote">
            <h2 class="card__title">Motivasi hari ini</h2>
            <blockquote class="profil-quote"><?php echo e($quoteHariIni); ?></blockquote>
        </section>

        <div class="dash-actions">
            <a class="btn btn--primary" href="dashboard.php">Dashboard</a>
            <a class="btn btn--outline" href="tugas.php">Daftar tugas</a>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
