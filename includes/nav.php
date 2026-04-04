<?php
/**
 * Menu atas — dipakai di dashboard, daftar tugas, profil, detail, tambah tugas
 * Set variabel $navActive sebelum include: 'dashboard' | 'tugas' | 'profil'
 */
if (!isset($navActive)) {
    $navActive = '';
}

function nav_link_class($key, $navActive)
{
    return $key === $navActive ? ' app-nav__link--active' : '';
}
?>
<nav class="app-nav" aria-label="Menu utama">
    <div class="app-nav__inner">
        <a href="dashboard.php" class="app-nav__brand">
            <img class="app-nav__logo" src="assets/img/logo.svg" alt="" width="36" height="36">
            <span class="app-nav__brand-text">Smart Study Planner</span>
        </a>
        <div class="app-nav__links">
            <a href="dashboard.php" class="app-nav__link<?php echo nav_link_class('dashboard', $navActive); ?>">Dashboard</a>
            <a href="tugas.php" class="app-nav__link<?php echo nav_link_class('tugas', $navActive); ?>">Daftar Tugas</a>
            <a href="profil.php" class="app-nav__link<?php echo nav_link_class('profil', $navActive); ?>">Profil</a>
        </div>
        <a href="logout.php" class="btn btn--ghost btn--sm app-nav__logout">Keluar</a>
    </div>
</nav>
