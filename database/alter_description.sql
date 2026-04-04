-- Opsional: jika migrasi otomatis di koneksi.php tidak jalan, eksekusi manual.
USE smart_study_planner;

ALTER TABLE tasks
  ADD COLUMN description TEXT NULL COMMENT 'Detail tugas' AFTER title;
