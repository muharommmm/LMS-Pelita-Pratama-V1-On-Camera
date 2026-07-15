-- Database Migration: Chat Internal Module
-- Target: MySQL / phpMyAdmin

CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id_pesan` INT(11) NOT NULL AUTO_INCREMENT,
  `pengirim_id` INT(10) UNSIGNED NOT NULL, -- References users.id
  `pengirim_role` VARCHAR(50) NOT NULL, -- 'siswa', 'guru', 'admin'
  `penerima_id` INT(10) UNSIGNED DEFAULT NULL, -- References users.id (NULL for Community Chat)
  `penerima_role` VARCHAR(50) DEFAULT NULL, -- 'siswa', 'guru', 'admin' (NULL for Community Chat)
  `id_kelas_komunitas` INT(11) DEFAULT NULL, -- NULL for Private/Global Chat, Class ID for Class Community Chat
  `pesan` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0, -- 0 = Unread, 1 = Read
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pesan`),
  KEY `idx_pengirim` (`pengirim_id`),
  KEY `idx_penerima` (`penerima_id`),
  KEY `idx_is_read` (`is_read`),
  KEY `idx_id_kelas_komunitas` (`id_kelas_komunitas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
