-- =============================================================
-- Migration: Dashboard Notifications System
-- GarudaCBT - Activity Feed untuk Guru & Siswa
-- Dibuat: 2026-07-07
-- TIDAK menggunakan SQL Trigger - semua logika di PHP controller
-- =============================================================

-- Tabel utama notifikasi dashboard
CREATE TABLE IF NOT EXISTS `dashboard_notifications` (
  `id`         INT(11) NOT NULL AUTO_INCREMENT,
  `user_id`    INT(11) NOT NULL COMMENT 'users.id (penerima notifikasi)',
  `role`       ENUM('guru','siswa','admin') NOT NULL DEFAULT 'siswa',
  `type`       VARCHAR(60) NOT NULL COMMENT 'tugas_baru|nilai_keluar|chat_masuk|honor_pending|materi_baru|deadline_dekat|ujian_baru|absensi',
  `title`      VARCHAR(255) NOT NULL COMMENT 'Teks singkat notifikasi',
  `body`       TEXT DEFAULT NULL COMMENT 'Detail pesan opsional',
  `url`        VARCHAR(500) DEFAULT NULL COMMENT 'URL tujuan saat diklik',
  `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `metadata`   TEXT DEFAULT NULL COMMENT 'JSON: {id_materi, id_guru, id_kelas, etc}',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Indeks performa: query utama adalah "ambil notif user X yang belum dibaca"
CREATE INDEX `idx_notif_user_read`   ON `dashboard_notifications` (`user_id`, `is_read`);
CREATE INDEX `idx_notif_role_created` ON `dashboard_notifications` (`role`, `created_at`);

-- Indeks pada kelas_materi (live query tugas pending guru)
-- Cek dulu apakah index sudah ada, baru tambahkan
SET @exist := (
    SELECT COUNT(1) FROM information_schema.statistics
    WHERE table_schema = DATABASE()
    AND table_name = 'kelas_materi'
    AND index_name = 'idx_guru_jenis_status'
);
SET @sql := IF(@exist = 0,
    'CREATE INDEX idx_guru_jenis_status ON kelas_materi (id_guru, jenis, status)',
    'SELECT "Index idx_guru_jenis_status already exists" as info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Indeks pada chat_messages (live query chat belum dibalas)
SET @exist2 := (
    SELECT COUNT(1) FROM information_schema.statistics
    WHERE table_schema = DATABASE()
    AND table_name = 'chat_messages'
    AND index_name = 'idx_penerima_read'
);
SET @sql2 := IF(@exist2 = 0,
    'CREATE INDEX idx_penerima_read ON chat_messages (penerima_id, is_read)',
    'SELECT "Index idx_penerima_read already exists" as info'
);
PREPARE stmt2 FROM @sql2; EXECUTE stmt2; DEALLOCATE PREPARE stmt2;

-- Indeks pada honor_records (live query honor pending guru)
SET @exist3 := (
    SELECT COUNT(1) FROM information_schema.statistics
    WHERE table_schema = DATABASE()
    AND table_name = 'honor_records'
    AND index_name = 'idx_tutor_status'
);
SET @sql3 := IF(@exist3 = 0,
    'CREATE INDEX idx_tutor_status ON honor_records (tutor_id, status)',
    'SELECT "Index idx_tutor_status already exists" as info'
);
PREPARE stmt3 FROM @sql3; EXECUTE stmt3; DEALLOCATE PREPARE stmt3;
