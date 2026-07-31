<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Wa_settings Controller
 *
 * Halaman admin untuk mengatur token WA API Fonnte,
 * toggle reminder, dan kirim manual.
 */
class Wa_settings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        if (!$this->ion_auth->is_admin()) {
            show_error('Hanya Administrator yang diperbolehkan mengakses halaman ini.', 403);
        }
        $this->load->model('Dashboard_model', 'dashboard');

        // Auto-migrate database tables/columns if not exist
        $this->auto_migrate();
    }

    /**
     * Otomatis membuat tabel & kolom yang dibutuhkan jika belum ada.
     */
    private function auto_migrate() {
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
        }

        // 2. Tambahkan kolom wa_api_token ke tabel setting jika belum ada
        if (!$this->db->field_exists('wa_api_token', 'setting')) {
            $this->db->query("ALTER TABLE `setting` ADD COLUMN `wa_api_token` VARCHAR(255) DEFAULT NULL");
        }

        // 3. Tambahkan kolom wa_reminder_enabled ke tabel setting jika belum ada
        if (!$this->db->field_exists('wa_reminder_enabled', 'setting')) {
            $this->db->query("ALTER TABLE `setting` ADD COLUMN `wa_reminder_enabled` TINYINT(1) DEFAULT 0");
        }

        // 4. Tambahkan kolom wa_reminder_time ke tabel setting jika belum ada
        if (!$this->db->field_exists('wa_reminder_time', 'setting')) {
            $this->db->query("ALTER TABLE `setting` ADD COLUMN `wa_reminder_time` VARCHAR(5) DEFAULT '08:30'");
        }
    }

    /**
     * Halaman utama WA Settings.
     */
    public function index() {
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();

        $data = [
            'user'       => $user,
            'judul'      => 'Pengaturan WhatsApp',
            'subjudul'   => 'Konfigurasi Fonnte API & Reminder',
            'profile'    => $this->dashboard->getProfileAdmin($user->id),
            'setting'    => $setting,
            'tp_active'  => $this->dashboard->getTahunActive(),
            'smt_active' => $this->dashboard->getSemesterActive(),
            'tp'         => $this->dashboard->getTahun(),
            'smt'        => $this->dashboard->getSemester(),
        ];

        // Ambil log terbaru
        $data['logs'] = $this->db->order_by('sent_at', 'DESC')
            ->limit(50)
            ->get('wa_notification_log')
            ->result();

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('setting/wa_settings', $data);
        $this->load->view('_templates/dashboard/_footer');
    }

    /**
     * Simpan pengaturan WA (AJAX).
     */
    public function save() {
        $wa_api_token = $this->input->post('wa_api_token', true);
        $wa_reminder_enabled = $this->input->post('wa_reminder_enabled', true) ? 1 : 0;
        $wa_reminder_time = $this->input->post('wa_reminder_time', true);

        if (empty($wa_reminder_time)) {
            $wa_reminder_time = '08:30';
        }

        $update = [
            'wa_api_token'        => $wa_api_token,
            'wa_reminder_enabled' => $wa_reminder_enabled,
            'wa_reminder_time'    => $wa_reminder_time,
        ];

        $this->db->where('id_setting', 1)->update('setting', $update);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Pengaturan WhatsApp berhasil disimpan.'
            ]));
    }

    /**
     * Hapus semua log (AJAX).
     */
    public function clear_logs() {
        $this->db->truncate('wa_notification_log');
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Log notifikasi berhasil dihapus.'
            ]));
    }
}
