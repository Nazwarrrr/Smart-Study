<?php
/**
 * Fungsi bantu tampilan (dipakai dashboard.php & detail.php)
 */

/** Amankan teks untuk HTML */
function e($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/**
 * Streak: hari berturut-turut dari hari ini mundur,
 * selama ada minimal satu tugas selesai di tanggal itu (completed_at).
 */
function hitung_streak(mysqli $conn)
{
    $sql = "SELECT DISTINCT DATE(completed_at) AS h
            FROM tasks
            WHERE status = 'selesai' AND completed_at IS NOT NULL
            ORDER BY h DESC";
    $res = mysqli_query($conn, $sql);
    if (!$res || mysqli_num_rows($res) === 0) {
        return 0;
    }

    $set = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $set[$row['h']] = true;
    }

    $streak  = 0;
    $check   = date('Y-m-d');
    $maxHari = 400;

    for ($i = 0; $i < $maxHari; $i++) {
        if (!empty($set[$check])) {
            $streak++;
            $check = date('Y-m-d', strtotime($check . ' -1 day'));
        } else {
            break;
        }
    }

    return $streak;
}

/** Kelas CSS highlight deadline (hari ini / besok), hanya jika belum selesai */
function kelas_deadline_card($deadline, $status)
{
    if ($status === 'selesai') {
        return '';
    }
    $today    = date('Y-m-d');
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    if ($deadline === $today) {
        return 'deadline-today';
    }
    if ($deadline === $tomorrow) {
        return 'deadline-tomorrow';
    }
    return '';
}

/** Format tanggal singkat */
function tampil_tanggal($ymd)
{
    $ts = strtotime($ymd);
    if ($ts === false) {
        return e($ymd);
    }
    $hari  = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    $bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $w = (int) date('w', $ts);
    $d = (int) date('j', $ts);
    $m = (int) date('n', $ts);
    $y = date('Y', $ts);
    return e($hari[$w] . ', ' . $d . ' ' . ($bulan[$m] ?? '') . ' ' . $y);
}

/**
 * Kelas CSS untuk aksen warna prioritas di kartu detail (border + latar lembut)
 */
function kelas_prioritas_detail($priority)
{
    $map = [
        'tinggi' => 'detail-card--pri-tinggi',
        'sedang' => 'detail-card--pri-sedang',
        'rendah' => 'detail-card--pri-rendah',
    ];
    return $map[$priority] ?? 'detail-card--pri-sedang';
}

/** Kelas badge kategori (PR, Ujian, Project) */
function kelas_kategori_css($category)
{
    $cat = (string) $category;
    $slug = [
        'PR' => 'pr',
        'Ujian' => 'ujian',
        'Project' => 'project',
    ];
    return 'badge-cat--' . ($slug[$cat] ?? 'lain');
}

/** Potong teks deskripsi untuk card */
function ringkas_deskripsi($text, $max = 100)
{
    $text = trim((string) $text);
    if ($text === '') {
        return '';
    }
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text, 'UTF-8') <= $max) {
            return e($text);
        }
        return e(mb_substr($text, 0, $max, 'UTF-8')) . '&hellip;';
    }
    if (strlen($text) <= $max) {
        return e($text);
    }
    return e(substr($text, 0, $max)) . '&hellip;';
}
