<?php
/**
 * Smart Study Planner — Login dummy (hardcode), session, redirect ke dashboard
 */
require_once __DIR__ . '/auth.php';

// --- Kredensial dummy (ganti di sini jika perlu; untuk lomba bisa disesuaikan) ---
$LOGIN_USER = 'siswa';
$LOGIN_PASS = 'smart123';

if (!empty($_SESSION['siswa_login'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = isset($_POST['username']) ? trim($_POST['username']) : '';
    $p = isset($_POST['password']) ? (string) $_POST['password'] : '';

    if ($u === $LOGIN_USER && $p === $LOGIN_PASS) {
        $_SESSION['siswa_login'] = true;
        $_SESSION['siswa_nama']  = 'Siswa';
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Smart Study Planner</title>
    <link rel="icon" href="assets/img/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">
    <div class="login-page">
        <div class="login-card">
            <img class="login-card__logo" src="assets/img/logo.svg" alt="Logo Smart Study Planner" width="72" height="72">
            <h1 class="login-card__title">Smart Study Planner</h1>
            <p class="login-card__subtitle"><span id="typing-text" data-text="Atur tugasmu dengan pintar."></span><span class="typing-cursor" aria-hidden="true">|</span></p>

            <?php if ($error !== '') : ?>
                <div class="alert alert--error login-alert" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form class="login-form" method="post" action="login.php" autocomplete="on">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autocomplete="username" placeholder="Contoh: siswa">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Password">
                </div>
                <button type="submit" class="btn btn--primary btn--block login-submit">Masuk</button>
            </form>
            <p class="login-hint">Demo: username <strong>siswa</strong> — password <strong>smart123</strong></p>
        </div>
    </div>
    <script src="assets/js/app.js"></script>
</body>
</html>
