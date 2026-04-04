-- Jalankan SEKALI di phpMyAdmin jika database Anda sudah dibuat
-- sebelum kolom completed_at ditambahkan ke skema.
USE smart_study_planner;

ALTER TABLE tasks
  ADD COLUMN completed_at TIMESTAMP NULL DEFAULT NULL
  COMMENT 'Waktu ditandai selesai (untuk study streak)'
  AFTER created_at;
