<?php
/**
 * Dashboard — ringkasan: streak, progress, statistik, riwayat tugas selesai
 */
require_once __DIR__ . '/auth.php';
require_login_siswa();

require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/helpers.php';

$navActive = 'dashboard';

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
} else {
    if ($dbError === null) {
        $dbError = mysqli_error($conn);
    }
}

$pending = max(0, $total - $selesai);
$percent = $total > 0 ? (int) round(($selesai / $total) * 100) : 0;
$streak  = ($dbError === null) ? hitung_streak($conn) : 0;

// Riwayat: tugas selesai terbaru
$riwayat = [];
if ($dbError === null) {
    $rq = mysqli_query(
        $conn,
        "SELECT id, title, category, completed_at FROM tasks
         WHERE status = 'selesai' AND completed_at IS NOT NULL
         ORDER BY completed_at DESC LIMIT 8"
    );
    if ($rq) {
        while ($rw = mysqli_fetch_assoc($rq)) {
            $riwayat[] = $rw;
        }
    }
}

$motivasi = [
    'Sedikit demi sedikit, kamu pasti bisa menyelesaikannya.',
    'Fokus hari ini = hasil yang lebih ringan besok.',
    'Progress kecil tetap namanya progress.',
    'Atur tugas, hindari lupa deadline, tingkatkan disiplin belajar.',
];
$motifHariIni = $motivasi[array_rand($motivasi)];

$namaSiswa = !empty($_SESSION['siswa_nama']) ? $_SESSION['siswa_nama'] : 'Siswa';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Smart Study Planner</title>
    <link rel="icon" href="assets/img/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="page-fade-in" data-page="dashboard">
    <?php require __DIR__ . '/includes/nav.php'; ?>

    <div class="page">
        <header class="dash-header dash-header--simple">
            <h1 class="dash-header__title">Dashboard</h1>
            <p class="dash-header__hello">Halo, <strong><?php echo e($namaSiswa); ?></strong> — ringkasan progres belajarmu.</p>
        </header>

        <?php if ($dbError !== null) : ?>
            <div class="alert alert--error">
                <strong>Database:</strong> <?php echo e($dbError); ?>
            </div>
        <?php endif; ?>

        <div class="motivation">
            <strong>Motivasi hari ini:</strong> <?php echo e($motifHariIni); ?>
        </div>

        <div class="stat-grid">
            <div class="stat-card stat-card--primary">
                <span class="stat-card__label">Total tugas</span>
                <span class="stat-card__value"><?php echo (int) $total; ?></span>
            </div>
            <div class="stat-card stat-card--warn">
                <span class="stat-card__label">Menunggu</span>
                <span class="stat-card__value"><?php echo (int) $pending; ?></span>
            </div>
            <div class="stat-card stat-card--ok">
                <span class="stat-card__label">Selesai</span>
                <span class="stat-card__value"><?php echo (int) $selesai; ?></span>
            </div>
        </div>

        <section class="progress-block" aria-label="Progress tugas">
            <div class="progress-block__top">
                <span class="progress-block__label">Progress keseluruhan</span>
                <span class="progress-block__percent"><?php echo $percent; ?>%</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar__fill" data-width="<?php echo $percent; ?>"></div>
            </div>
            <div class="streak" title="Hari berturut-turut ada tugas selesai">
                <span class="streak__icon" aria-hidden="true">&#128293;</span>
                Study streak: <strong><?php echo (int) $streak; ?></strong> hari
            </div>
        </section>

        <div class="dash-actions">
            <a class="btn btn--primary" href="tugas.php">Lihat daftar tugas</a>
            <a class="btn btn--outline" href="tambah_tugas.php">+ Tambah tugas baru</a>
        </div>

        <section class="card history-card">
            <h2 class="card__title">Riwayat selesai (terbaru)</h2>
            <?php if (count($riwayat) === 0) : ?>
                <p class="text-muted history-empty">Belum ada tugas yang ditandai selesai. Ayo mulai dari daftar tugas!</p>
            <?php else : ?>
                <ul class="history-list">
                    <?php foreach ($riwayat as $h) :
                        $hid = (int) $h['id'];
                        $tgl = $h['completed_at'] ? date('d M Y, H:i', strtotime($h['completed_at'])) : '-';
                        ?>
                        <li class="history-list__item">
                            <div class="history-list__top">
                                <a class="history-list__link" href="detail.php?id=<?php echo $hid; ?>"><?php echo e($h['title']); ?></a>
                                <?php $hc = isset($h['category']) ? (string) $h['category'] : 'PR'; ?>
                                <span class="badge badge--tiny <?php echo e(kelas_kategori_css($hc)); ?>"><?php echo e($hc); ?></span>
                            </div>
                            <span class="history-list__meta"><?php echo e($tgl); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
