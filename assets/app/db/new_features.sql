-- 1. Modul E-Book
CREATE TABLE IF NOT EXISTS `ebooks` (
  `id_ebook` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `class_id` INT NOT NULL,
  `created_by` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_ebook`),
  KEY `fk_ebooks_class` (`class_id`),
  CONSTRAINT `fk_ebooks_class` FOREIGN KEY (`class_id`) REFERENCES `master_kelas` (`id_kelas`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Modul Keuangan (SPP)
CREATE TABLE IF NOT EXISTS `spp_billing` (
  `id_spp_billing` INT NOT NULL AUTO_INCREMENT,
  `student_id` INT NOT NULL,
  `tp_id` INT NOT NULL,
  `smt_id` INT NOT NULL,
  `month` TINYINT NOT NULL COMMENT '1=Jan, 2=Feb, ..., 12=Des',
  `amount` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `status` ENUM('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `payment_date` DATETIME DEFAULT NULL,
  `invoice_number` VARCHAR(100) DEFAULT NULL,
  `notes` TEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_spp_billing`),
  UNIQUE KEY `idx_student_period_month` (`student_id`, `tp_id`, `smt_id`, `month`),
  CONSTRAINT `fk_spp_student` FOREIGN KEY (`student_id`) REFERENCES `master_siswa` (`id_siswa`) ON DELETE CASCADE,
  CONSTRAINT `fk_spp_tp` FOREIGN KEY (`tp_id`) REFERENCES `master_tp` (`id_tp`) ON DELETE CASCADE,
  CONSTRAINT `fk_spp_smt` FOREIGN KEY (`smt_id`) REFERENCES `master_smt` (`id_smt`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Modul Absensi / Kehadiran
CREATE TABLE IF NOT EXISTS `absensi_setting_barcode` (
  `id_setting_barcode` INT NOT NULL AUTO_INCREMENT,
  `class_id` INT NOT NULL,
  `barcode_code` VARCHAR(255) NOT NULL COMMENT 'Kode unik/hash untuk QR/Barcode',
  `location_name` VARCHAR(100) NOT NULL,
  `latitude` DECIMAL(10,8) DEFAULT NULL,
  `longitude` DECIMAL(11,8) DEFAULT NULL,
  `radius_meter` INT DEFAULT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_setting_barcode`),
  UNIQUE KEY `idx_class_barcode` (`class_id`),
  CONSTRAINT `fk_abs_barcode_class` FOREIGN KEY (`class_id`) REFERENCES `master_kelas` (`id_kelas`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `absensi_siswa` (
  `id_absensi` INT NOT NULL AUTO_INCREMENT,
  `student_id` INT NOT NULL,
  `class_id` INT NOT NULL,
  `tp_id` INT NOT NULL,
  `smt_id` INT NOT NULL,
  `mapel_id` INT DEFAULT NULL,
  `date` DATE NOT NULL,
  `time` TIME NOT NULL,
  `status` ENUM('H','S','I','A') NOT NULL DEFAULT 'A' COMMENT 'H=Hadir, S=Sakit, I=Izin, A=Alpha',
  `method` ENUM('barcode','manual_tutor') NOT NULL DEFAULT 'manual_tutor',
  `tutor_id_input` INT DEFAULT NULL COMMENT 'Tutor/Guru yang menginput secara manual',
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_absensi`),
  UNIQUE KEY `idx_student_mapel_date` (`student_id`, `mapel_id`, `date`),
  CONSTRAINT `fk_abs_student` FOREIGN KEY (`student_id`) REFERENCES `master_siswa` (`id_siswa`) ON DELETE CASCADE,
  CONSTRAINT `fk_abs_class` FOREIGN KEY (`class_id`) REFERENCES `master_kelas` (`id_kelas`) ON DELETE CASCADE,
  CONSTRAINT `fk_abs_tp` FOREIGN KEY (`tp_id`) REFERENCES `master_tp` (`id_tp`) ON DELETE CASCADE,
  CONSTRAINT `fk_abs_smt` FOREIGN KEY (`smt_id`) REFERENCES `master_smt` (`id_smt`) ON DELETE CASCADE,
  CONSTRAINT `fk_abs_mapel` FOREIGN KEY (`mapel_id`) REFERENCES `master_mapel` (`id_mapel`) ON DELETE CASCADE,
  CONSTRAINT `fk_abs_tutor` FOREIGN KEY (`tutor_id_input`) REFERENCES `master_guru` (`id_guru`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Modul Honorarium Tutor
CREATE TABLE IF NOT EXISTS `honor_rates` (
  `id_rate` INT NOT NULL AUTO_INCREMENT,
  `tutor_id` INT DEFAULT NULL COMMENT 'NULL berarti tarif default global',
  `rate_offline` DECIMAL(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Per sesi tatap muka',
  `rate_online` DECIMAL(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Per sesi online',
  `rate_check_task` DECIMAL(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Per pemeriksaan tugas siswa',
  `rate_create_cbt` DECIMAL(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Per pembuatan bank soal CBT',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_rate`),
  UNIQUE KEY `idx_tutor_rate` (`tutor_id`),
  CONSTRAINT `fk_hrate_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `master_guru` (`id_guru`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `honor_mutations` (
  `id_mutation` INT NOT NULL AUTO_INCREMENT,
  `tutor_id` INT NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `type` ENUM('credit','debit') NOT NULL COMMENT 'credit=penambahan honor, debit=penarikan/pembayaran',
  `notes` TEXT,
  `transaction_date` DATETIME NOT NULL,
  `status_konfirmasi_tutor` TINYINT NOT NULL DEFAULT '0' COMMENT '0=belum konfirmasi, 1=sudah konfirmasi',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mutation`),
  CONSTRAINT `fk_hmut_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `master_guru` (`id_guru`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `honor_records` (
  `id_honor_record` INT NOT NULL AUTO_INCREMENT,
  `tutor_id` INT NOT NULL,
  `tp_id` INT NOT NULL,
  `smt_id` INT NOT NULL,
  `type` ENUM('offline','online','check_task','create_cbt') NOT NULL,
  `reference_id` INT NOT NULL COMMENT 'ID referensi ke tabel materi/tugas/CBT terkait',
  `qty` INT NOT NULL DEFAULT '1',
  `rate` DECIMAL(12,2) NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `status` ENUM('pending','approved','paid') NOT NULL DEFAULT 'pending',
  `mutation_id` INT DEFAULT NULL COMMENT 'Terisi jika status = paid',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_honor_record`),
  CONSTRAINT `fk_hrec_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `master_guru` (`id_guru`) ON DELETE CASCADE,
  CONSTRAINT `fk_hrec_tp` FOREIGN KEY (`tp_id`) REFERENCES `master_tp` (`id_tp`) ON DELETE CASCADE,
  CONSTRAINT `fk_hrec_smt` FOREIGN KEY (`smt_id`) REFERENCES `master_smt` (`id_smt`) ON DELETE CASCADE,
  CONSTRAINT `fk_hrec_mut` FOREIGN KEY (`mutation_id`) REFERENCES `honor_mutations` (`id_mutation`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Modul Agenda Terdekat
CREATE TABLE IF NOT EXISTS `agendas` (
  `id_agenda` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `start_date` DATETIME NOT NULL,
  `end_date` DATETIME NOT NULL,
  `target_role` ENUM('all','admin','guru','siswa') NOT NULL DEFAULT 'all',
  `target_class_id` INT DEFAULT NULL COMMENT 'NULL jika untuk semua kelas',
  `created_by` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_agenda`),
  KEY `fk_agendas_class` (`target_class_id`),
  CONSTRAINT `fk_agendas_class` FOREIGN KEY (`target_class_id`) REFERENCES `master_kelas` (`id_kelas`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
