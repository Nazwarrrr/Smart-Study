-- Tambah kolom category untuk skema lama (jika migrasi otomatis di koneksi.php tidak jalan)
USE smart_study_planner;

ALTER TABLE tasks
  ADD COLUMN category VARCHAR(32) NOT NULL DEFAULT 'PR'
  COMMENT 'PR, Ujian, Project'
  AFTER priority;
