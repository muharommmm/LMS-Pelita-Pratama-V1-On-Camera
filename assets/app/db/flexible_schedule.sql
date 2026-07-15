-- Flexible Schedule Table
CREATE TABLE IF NOT EXISTS `jadwal_fleksibel` (
  `id_jadwal` INT NOT NULL AUTO_INCREMENT,
  `class_id` INT NOT NULL,
  `mapel_id` INT NOT NULL,
  `day` INT NOT NULL COMMENT '1=Senin, 2=Selasa, 3=Rabu, 4=Kamis, 5=Jumat, 6=Sabtu, 7=Minggu',
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `learning_link` VARCHAR(255) DEFAULT NULL,
  `tp_id` INT NOT NULL,
  `smt_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_jadwal`),
  KEY `fk_jadwal_flex_class` (`class_id`),
  KEY `fk_jadwal_flex_mapel` (`mapel_id`),
  CONSTRAINT `fk_jadwal_flex_class` FOREIGN KEY (`class_id`) REFERENCES `master_kelas` (`id_kelas`) ON DELETE CASCADE,
  CONSTRAINT `fk_jadwal_flex_mapel` FOREIGN KEY (`mapel_id`) REFERENCES `master_mapel` (`id_mapel`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
