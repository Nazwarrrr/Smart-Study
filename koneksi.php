<?php
/**
 * Smart Study Planner — Langkah 2: Koneksi database (mysqli)
 * File ini dipanggil dari tambah.php, hapus.php, update.php, dashboard.php, detail.php, dll.
 */

// --- Pengaturan koneksi ---
// XAMPP default: user root, password KOSONG (''). Jika Anda set password di MySQL, isi di bawah ini.
$host     = 'localhost';
$username = 'root';
$password = ''; // contoh jika pakai password: 'password_mysql_anda'
$database = 'smart_study_planner'; // Impor skema dari folder database/database.sql

// --- Membuat koneksi dengan mysqli (bukan PDO) ---
$conn = mysqli_connect($host, $username, $password, $database);

// --- Cek apakah koneksi berhasil ---
if (!$conn) {
    // mysqli_connect_error() berisi pesan error dari MySQL
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

// --- Charset UTF-8 agar huruf Indonesia tersimpan benar ---
mysqli_set_charset($conn, 'utf8mb4');

/**
 * Database lama (tanpa kolom completed_at) menyebabkan error di index/update.
 * Sekali jalan: cek kolom, kalau belum ada → ALTER TABLE (aman dipanggil berulang).
 */
try {
    $chk = mysqli_query($conn, "SHOW COLUMNS FROM tasks LIKE 'completed_at'");
    if ($chk !== false && mysqli_num_rows($chk) === 0) {
        mysqli_free_result($chk);
        mysqli_query($conn, "ALTER TABLE tasks ADD COLUMN completed_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Waktu ditandai selesai' AFTER created_at");
    } elseif ($chk !== false) {
        mysqli_free_result($chk);
    }
} catch (Throwable $e) {
    // Tabel belum dibuat / hak akses: biarkan halaman berikutnya yang menampilkan pesan error.
}

/** Kolom description (untuk skema lama) */
try {
    $chk = mysqli_query($conn, "SHOW COLUMNS FROM tasks LIKE 'description'");
    if ($chk !== false && mysqli_num_rows($chk) === 0) {
        mysqli_free_result($chk);
        mysqli_query($conn, "ALTER TABLE tasks ADD COLUMN description TEXT NULL COMMENT 'Detail tugas' AFTER title");
    } elseif ($chk !== false) {
        mysqli_free_result($chk);
    }
} catch (Throwable $e) {
}

/** Kolom category (PR / Ujian / Project) */
try {
    $chk = mysqli_query($conn, "SHOW COLUMNS FROM tasks LIKE 'category'");
    if ($chk !== false && mysqli_num_rows($chk) === 0) {
        mysqli_free_result($chk);
        mysqli_query(
            $conn,
            "ALTER TABLE tasks ADD COLUMN category VARCHAR(32) NOT NULL DEFAULT 'PR' COMMENT 'PR, Ujian, Project' AFTER priority"
        );
    } elseif ($chk !== false) {
        mysqli_free_result($chk);
    }
} catch (Throwable $e) {
}

// Variabel $conn siap dipakai di file lain dengan: require_once 'koneksi.php';
