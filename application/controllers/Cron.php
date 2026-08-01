<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cron Controller
 *
 * Controller untuk tugas terjadwal (cron job).
 * 
 * Penggunaan:
 *   CLI:  php index.php cron send_jadwal_reminder
 *   HTTP: https://domain.com/cron/send_jadwal_reminder?token=SECRET_TOKEN
 *   AJAX: POST dari admin dashboard (tombol manual)
 */
class Cron extends CI_Controller {

    // Secret token untuk akses via HTTP (ganti dengan token acak Anda sendiri)
    private $cron_secret = 'lms_pelita_cron_2026';

    public function __construct() {
        parent::__construct();
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Jadwal_fleksibel_model', 'jf_model');
        $this->load->library('Fonnte_lib');
        $this->auto_migrate();
    }

    /**
     * Otomatis membuat tabel & kolom yang dibutuhkan jika belum ada.
     */
    private function auto_migrate() {
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

        if (!$this->db->field_exists('wa_api_token', 'setting')) {
            $this->db->query("ALTER TABLE `setting` ADD COLUMN `wa_api_token` VARCHAR(255) DEFAULT NULL");
        }

        if (!$this->db->field_exists('wa_reminder_enabled', 'setting')) {
            $this->db->query("ALTER TABLE `setting` ADD COLUMN `wa_reminder_enabled` TINYINT(1) DEFAULT 0");
        }

        if (!$this->db->field_exists('wa_reminder_time', 'setting')) {
            $this->db->query("ALTER TABLE `setting` ADD COLUMN `wa_reminder_time` VARCHAR(5) DEFAULT '08:30'");
        }
    }

    /**
     * Validasi akses: hanya CLI, admin login, atau token yang benar.
     * @return bool
     */
    private function is_authorized() {
        // 1. CLI access — selalu diizinkan
        if ($this->input->is_cli_request()) {
            return true;
        }

        // 2. Admin login via browser/AJAX
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            return true;
        }

        // 3. Token via query parameter
        $token = $this->input->get('token', true);
        if ($token === $this->cron_secret) {
            return true;
        }

        return false;
    }

    /**
     * Map angka hari PHP (date('N')) ke nama hari Indonesia.
     */
    private function get_nama_hari($day_num) {
        $hari = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
            5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
        ];
        return isset($hari[$day_num]) ? $hari[$day_num] : 'N/A';
    }

    /**
     * Format info metode belajar berdasarkan jenis_kegiatan (Minimalis).
     */
    private function format_metode($jenis_kegiatan) {
        $jenis = strtolower(trim($jenis_kegiatan));
        switch ($jenis) {
            case 'online':
                return "Online";
            case 'tugas':
                return "Tugas LMS";
            case 'offline':
            default:
                return "Offline";
        }
    }

    /**
     * Bangun pesan WhatsApp untuk satu tutor (Opsi 3 - Minimalis).
     * 
     * @param string $nama_guru Nama tutor
     * @param array  $schedules Array jadwal hari ini
     * @param string $tanggal_str Format tanggal (misal: "Kamis, 31 Juli 2026")
     * @return string
     */
    private function build_message($nama_guru, $schedules, $tanggal_str) {
        $msg  = "🔔 *Jadwal Mengajar - {$tanggal_str}*\n";
        $msg .= "Halo Bpk/Ibu {$nama_guru}!\n\n";

        $i = 1;
        foreach ($schedules as $s) {
            // Gunakan Kode Mapel jika ada, jika tidak pakai Nama Mapel
            $mapel = (!empty($s->kode)) ? $s->kode : (isset($s->nama_mapel) ? $s->nama_mapel : 'N/A');
            
            // Persingkat nama kelas
            $kelas = isset($s->nama_kelas) ? $s->nama_kelas : 'N/A';
            $kelas = str_replace(
                ['NON REGULER', 'REGULER', 'non reguler', 'reguler'], 
                ['N-REG', 'REG', 'N-REG', 'REG'], 
                $kelas
            );
            
            $start = isset($s->start_time) ? substr($s->start_time, 0, 5) : '??:??';
            $jenis = isset($s->jenis_kegiatan) ? $s->jenis_kegiatan : 'offline';
            $metode = $this->format_metode($jenis);

            $msg .= "{$i}. {$mapel} ({$kelas}) - {$start} [{$metode}]\n";
            $i++;
        }

        $msg .= "\nLMS Pelita Pratama 🎓";

        return $msg;
    }

    /**
     * Format tanggal Indonesia.
     */
    private function tanggal_indo() {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $day_num = (int)date('N');
        $tgl = date('j');
        $bln = (int)date('n');
        $thn = date('Y');
        return $this->get_nama_hari($day_num) . ", {$tgl} {$bulan[$bln]} {$thn}";
    }

    /**
     * MAIN METHOD: Kirim reminder jadwal harian ke semua tutor via WhatsApp.
     * 
     * Dipanggil oleh:
     *   - Cron job otomatis (08:30 WIB)
     *   - Tombol manual di dashboard admin (AJAX POST)
     */
    public function send_jadwal_reminder() {
        // Mencegah timeout karena script berjalan lebih lama dengan jeda delay
        set_time_limit(0);

        if (!$this->is_authorized()) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => 'Akses ditolak.']));
            return;
        }

        // Cek apakah WA reminder aktif (skip cek untuk manual trigger dari admin)
        $setting = $this->dashboard->getSetting();
        $is_manual = $this->input->post('manual', true) === '1';
        
        if (!$is_manual && isset($setting->wa_reminder_enabled) && $setting->wa_reminder_enabled != 1) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => 'WA Reminder tidak aktif.']));
            return;
        }

        // Ambil tahun pelajaran & semester aktif
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        if (!$tp || !$smt) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => 'Tahun pelajaran/semester tidak ditemukan.']));
            return;
        }

        // Hari ini (ISO: 1=Senin ... 7=Minggu)
        $day_today = (int)date('N');
        $tanggal_str = $this->tanggal_indo();

        // Ambil semua tutor dari jabatan_guru yang aktif di TP/SMT ini
        $jabatan_rows = $this->db->select('DISTINCT(jabatan_guru.id_guru), master_guru.nama_guru, master_guru.no_hp')
            ->from('jabatan_guru')
            ->join('master_guru', 'master_guru.id_guru = jabatan_guru.id_guru')
            ->where('jabatan_guru.id_tp', $tp->id_tp)
            ->where('jabatan_guru.id_smt', $smt->id_smt)
            ->get()->result();

        $sent_count = 0;
        $failed_count = 0;
        $skipped_count = 0;
        $details = [];

        foreach ($jabatan_rows as $guru) {
            // Cek nomor HP
            if (empty($guru->no_hp)) {
                $skipped_count++;
                $details[] = [
                    'nama' => $guru->nama_guru,
                    'status' => 'skipped',
                    'reason' => 'Nomor HP kosong'
                ];
                continue;
            }

            // Ambil jadwal hari ini untuk tutor ini (sudah ter-filter pola_mingguan)
            $schedules = $this->jf_model->get_schedules_by_tutor(
                $guru->id_guru, $tp->id_tp, $smt->id_smt, $day_today
            );

            if (empty($schedules)) {
                $skipped_count++;
                continue; // Tidak ada jadwal hari ini — skip tanpa log
            }

            // Bangun pesan
            $message = $this->build_message($guru->nama_guru, $schedules, $tanggal_str);

            // Kirim via Fonnte
            $result = $this->fonnte_lib->send($guru->no_hp, $message);

            // Log ke database
            $log_data = [
                'id_guru'  => $guru->id_guru,
                'no_hp'    => $this->fonnte_lib->format_phone($guru->no_hp),
                'message'  => $message,
                'status'   => $result['success'] ? 'sent' : 'failed',
                'response' => $result['response'],
                'type'     => 'jadwal_harian',
                'sent_at'  => date('Y-m-d H:i:s')
            ];
            $this->db->insert('wa_notification_log', $log_data);

            if ($result['success']) {
                $sent_count++;
            } else {
                $failed_count++;
            }

            $details[] = [
                'nama'   => $guru->nama_guru,
                'no_hp'  => $guru->no_hp,
                'status' => $result['success'] ? 'sent' : 'failed',
                'jadwal' => count($schedules) . ' sesi'
            ];

            // Fitur Anti-Ban: Jeda acak 5 sampai 10 detik sebelum mengirim ke tutor berikutnya
            sleep(rand(5, 10));
        }

        $response = [
            'status'  => true,
            'message' => "Reminder selesai: {$sent_count} terkirim, {$failed_count} gagal, {$skipped_count} dilewati.",
            'data'    => [
                'sent'    => $sent_count,
                'failed'  => $failed_count,
                'skipped' => $skipped_count,
                'details' => $details
            ]
        ];

        // CLI output
        if ($this->input->is_cli_request()) {
            echo $response['message'] . "\n";
            foreach ($details as $d) {
                echo "  - {$d['nama']}: {$d['status']}" . (isset($d['jadwal']) ? " ({$d['jadwal']})" : '') . "\n";
            }
            return;
        }

        // JSON output (untuk AJAX dari admin)
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
