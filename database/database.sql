-- ============================================================
-- Smart Study Planner — Skema MySQL (folder: database/)
-- Jalankan di phpMyAdmin (tab SQL) atau mysql command line
-- ============================================================

-- 1) Buat database baru (nama bebas, disini: smart_study_planner)
CREATE DATABASE IF NOT EXISTS smart_study_planner
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- 2) Pilih database yang akan dipakai
USE smart_study_planner;

-- 3) Tabel tugas (tasks)
--    Satu baris = satu tugas yang dicatat siswa
DROP TABLE IF EXISTS tasks;

CREATE TABLE tasks (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL COMMENT 'Judul tugas',
  description TEXT NULL COMMENT 'Detail / catatan tugas',
  deadline DATE NOT NULL COMMENT 'Batas waktu (tanggal saja)',
  priority ENUM('tinggi', 'sedang', 'rendah') NOT NULL DEFAULT 'sedang'
    COMMENT 'Prioritas: tinggi=merah, sedang=kuning, rendah=hijau',
  category VARCHAR(32) NOT NULL DEFAULT 'PR'
    COMMENT 'Kategori: PR, Ujian, Project',
  status ENUM('pending', 'selesai') NOT NULL DEFAULT 'pending'
    COMMENT 'pending=belum selesai, selesai=sudah dikerjakan',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    COMMENT 'Waktu tugas pertama kali ditambahkan',
  completed_at TIMESTAMP NULL DEFAULT NULL
    COMMENT 'Waktu ditandai selesai (untuk study streak & statistik)',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Selesai. Tabel siap dipakai PHP mysqli di langkah berikutnya.
