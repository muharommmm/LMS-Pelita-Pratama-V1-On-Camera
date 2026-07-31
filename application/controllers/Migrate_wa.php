<?php
/**
 * Migration Script: Buat tabel wa_notification_log dan tambahkan kolom WA ke setting.
 * 
 * Jalankan script ini 1x via browser:
 *   https://domain.com/migrate_wa
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate_wa extends CI_Controller {

    public function index() {
        // 1. Buat tabel wa_notification_log jika belum ada
        if (!$this->db->table_exists('wa_notification_log')) {
            $this->db->query("
                CREATE TABLE `wa_notification_log` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_guru` INT(11) DEFAULT NULL,
                    `no_hp` VARCHAR(20) DEFAULT NULL,
                    `message` TEXT DEFAULT NULL,
                    `status` ENUM('sent','failed','pending') DEFAULT 'pending',
                    `response` TEXT DEFAULT NULL,
                    `type` VARCHAR(50) DEFAULT 'jadwal_harian',
                    `sent_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    INDEX `idx_guru` (`id_guru`),
                    INDEX `idx_status` (`status`),
                    INDEX `idx_sent_at` (`sent_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
            echo '✅ Tabel wa_notification_log berhasil dibuat.<br>';
        } else {
            echo 'ℹ️ Tabel wa_notification_log sudah ada.<br>';
        }

        // 2. Tambahkan kolom wa_api_token ke tabel setting jika belum ada
        if (!$this->db->field_exists('wa_api_token', 'setting')) {
            $this->db->query("ALTER TABLE `setting` ADD COLUMN `wa_api_token` VARCHAR(255) DEFAULT NULL");
            echo '✅ Kolom wa_api_token ditambahkan ke tabel setting.<br>';
        } else {
            echo 'ℹ️ Kolom wa_api_token sudah ada.<br>';
        }

        // 3. Tambahkan kolom wa_reminder_enabled ke tabel setting jika belum ada
        if (!$this->db->field_exists('wa_reminder_enabled', 'setting')) {
            $this->db->query("ALTER TABLE `setting` ADD COLUMN `wa_reminder_enabled` TINYINT(1) DEFAULT 0");
            echo '✅ Kolom wa_reminder_enabled ditambahkan ke tabel setting.<br>';
        } else {
            echo 'ℹ️ Kolom wa_reminder_enabled sudah ada.<br>';
        }

        // 4. Tambahkan kolom wa_reminder_time ke tabel setting jika belum ada
        if (!$this->db->field_exists('wa_reminder_time', 'setting')) {
            $this->db->query("ALTER TABLE `setting` ADD COLUMN `wa_reminder_time` VARCHAR(5) DEFAULT '08:30'");
            echo '✅ Kolom wa_reminder_time ditambahkan ke tabel setting.<br>';
        } else {
            echo 'ℹ️ Kolom wa_reminder_time sudah ada.<br>';
        }

        echo '<br><strong>🎉 Migrasi WA selesai!</strong>';
    }
}
