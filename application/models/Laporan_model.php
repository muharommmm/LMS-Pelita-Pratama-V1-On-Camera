<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->db_init();
    }

    /**
     * Self-healing Database initialization.
     * Automatically creates the required tables if they do not exist.
     */
    private function db_init() {
        // Table: rapor_tutor_pertanyaan
        if (!$this->db->table_exists('rapor_tutor_pertanyaan')) {
            $this->db->query("
                CREATE TABLE rapor_tutor_pertanyaan (
                    id_pertanyaan INT AUTO_INCREMENT PRIMARY KEY,
                    pertanyaan TEXT NOT NULL,
                    tipe ENUM('teks', 'pilihan') NOT NULL DEFAULT 'teks',
                    pilihan_jawaban TEXT NULL,
                    is_active TINYINT NOT NULL DEFAULT 1,
                    created_at DATETIME NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");
            
            // Insert some default questions
            $default_questions = [
                [
                    'pertanyaan' => 'Apakah tutor hadir tepat waktu?',
                    'tipe' => 'pilihan',
                    'pilihan_jawaban' => 'Selalu Tepat Waktu,Terlambat < 10 Menit,Terlambat > 10 Menit,Tidak Hadir Tanpa Kabar',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'pertanyaan' => 'Bagaimana kejelasan penyampaian materi oleh tutor?',
                    'tipe' => 'pilihan',
                    'pilihan_jawaban' => 'Sangat Jelas,Cukup Jelas,Kurang Jelas,Sangat Tidak Jelas',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'pertanyaan' => 'Tuliskan kritik, saran, atau masukan untuk tutor ini.',
                    'tipe' => 'teks',
                    'pilihan_jawaban' => NULL,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            $this->db->insert_batch('rapor_tutor_pertanyaan', $default_questions);
        }

        // Table: rapor_tutor_jawaban
        if (!$this->db->table_exists('rapor_tutor_jawaban')) {
            $this->db->query("
                CREATE TABLE rapor_tutor_jawaban (
                    id_jawaban INT AUTO_INCREMENT PRIMARY KEY,
                    id_user INT NOT NULL,
                    id_guru INT NOT NULL,
                    id_pertanyaan INT NOT NULL,
                    jawaban TEXT NOT NULL,
                    tanggal_evaluasi DATE NULL DEFAULT NULL,
                    tanggal DATETIME NOT NULL,
                    INDEX (id_user),
                    INDEX (id_guru),
                    INDEX (id_pertanyaan)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");
        } else {
            // Auto-migration: Check if column exists, if not ADD it
            if (!$this->db->field_exists('tanggal_evaluasi', 'rapor_tutor_jawaban')) {
                $this->db->query("ALTER TABLE rapor_tutor_jawaban ADD COLUMN tanggal_evaluasi DATE NULL DEFAULT NULL AFTER jawaban;");
            }
        }

        // Table: laporan_insiden
        if (!$this->db->table_exists('laporan_insiden')) {
            $this->db->query("
                CREATE TABLE laporan_insiden (
                    id_laporan INT AUTO_INCREMENT PRIMARY KEY,
                    id_user INT NULL,
                    is_anonymous TINYINT NOT NULL DEFAULT 0,
                    kategori VARCHAR(100) NOT NULL,
                    deskripsi TEXT NOT NULL,
                    tanggal_kejadian DATE NOT NULL,
                    bukti_file VARCHAR(255) NULL,
                    status ENUM('Pending', 'Diproses', 'Selesai') NOT NULL DEFAULT 'Pending',
                    catatan_admin TEXT NULL,
                    created_at DATETIME NOT NULL,
                    INDEX (id_user)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");
        }

        // Table: lapor_setting
        if (!$this->db->table_exists('lapor_setting')) {
            $this->db->query("
                CREATE TABLE lapor_setting (
                    setting_key VARCHAR(100) PRIMARY KEY,
                    setting_value VARCHAR(255) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");
            
            // Insert default settings
            $this->db->insert_batch('lapor_setting', [
                ['setting_key' => 'evaluasi_tanggal_tampil', 'setting_value' => '1'],
                ['setting_key' => 'evaluasi_tanggal_wajib', 'setting_value' => '1']
            ]);
        }
    }

    /* ----------------------------------------------------
       RAPOR TUTOR - SETTINGS MANAGEMENT
       ---------------------------------------------------- */

    public function get_settings() {
        if (!$this->db->table_exists('lapor_setting')) {
            return [
                'evaluasi_tanggal_tampil' => '1',
                'evaluasi_tanggal_wajib' => '1'
            ];
        }
        $res = $this->db->get('lapor_setting')->result();
        $settings = [];
        foreach ($res as $row) {
            $settings[$row->setting_key] = $row->setting_value;
        }
        // Fallbacks
        if (!isset($settings['evaluasi_tanggal_tampil'])) $settings['evaluasi_tanggal_tampil'] = '1';
        if (!isset($settings['evaluasi_tanggal_wajib'])) $settings['evaluasi_tanggal_wajib'] = '1';
        return $settings;
    }

    public function update_settings($settings) {
        $this->db->trans_start();
        foreach ($settings as $key => $val) {
            // Check if key exists
            $exists = $this->db->where('setting_key', $key)->count_all_results('lapor_setting') > 0;
            if ($exists) {
                $this->db->where('setting_key', $key)->update('lapor_setting', ['setting_value' => $val]);
            } else {
                $this->db->insert('lapor_setting', ['setting_key' => $key, 'setting_value' => $val]);
            }
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /* ----------------------------------------------------
       RAPOR TUTOR - QUESTIONS MANAGEMENT (ADMIN)
       ---------------------------------------------------- */

    public function get_all_pertanyaan() {
        return $this->db->order_by('id_pertanyaan', 'ASC')->get('rapor_tutor_pertanyaan')->result();
    }

    public function get_active_pertanyaan() {
        return $this->db->where('is_active', 1)->order_by('id_pertanyaan', 'ASC')->get('rapor_tutor_pertanyaan')->result();
    }

    public function insert_pertanyaan($data) {
        return $this->db->insert('rapor_tutor_pertanyaan', $data);
    }

    public function update_pertanyaan($id, $data) {
        return $this->db->where('id_pertanyaan', $id)->update('rapor_tutor_pertanyaan', $data);
    }

    public function delete_pertanyaan($id) {
        return $this->db->where('id_pertanyaan', $id)->delete('rapor_tutor_pertanyaan');
    }

    /* ----------------------------------------------------
       RAPOR TUTOR - ANSWERS & RATINGS
       ---------------------------------------------------- */

    public function submit_evaluation($id_user, $id_guru, $answers, $tanggal_evaluasi) {
        $this->db->trans_start();
        $tanggal = date('Y-m-d H:i:s');
        foreach ($answers as $id_pertanyaan => $jawaban) {
            $this->db->insert('rapor_tutor_jawaban', [
                'id_user' => $id_user,
                'id_guru' => $id_guru,
                'id_pertanyaan' => $id_pertanyaan,
                'jawaban' => $jawaban,
                'tanggal_evaluasi' => $tanggal_evaluasi,
                'tanggal' => $tanggal
            ]);
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Checks if a student has evaluated a specific tutor today to prevent spam
     */
    public function has_evaluated_today($id_user, $id_guru) {
        $today_start = date('Y-m-d 00:00:00');
        $today_end = date('Y-m-d 23:59:59');

        $this->db->from('rapor_tutor_jawaban');
        $this->db->where('id_user', $id_user);
        $this->db->where('id_guru', $id_guru);
        $this->db->where('tanggal >=', $today_start);
        $this->db->where('tanggal <=', $today_end);
        return $this->db->count_all_results() > 0;
    }

    /**
     * Get evaluation rekap grouped by tutors (Admin/Read-only Tutor views)
     */
    public function get_tutor_rekap() {
        // Query to get all teachers
        $tutors = $this->db->select('id_guru, nama_guru, nip')->get('master_guru')->result();
        
        $rekap = [];
        foreach ($tutors as $tutor) {
            // Count total responses
            $this->db->select('COUNT(DISTINCT(tanggal)) as total_resp');
            $this->db->from('rapor_tutor_jawaban');
            $this->db->where('id_guru', $tutor->id_guru);
            $total_resp = $this->db->get()->row()->total_resp;

            // Fetch answers for textual comments
            $this->db->select('j.jawaban, j.tanggal, j.tanggal_evaluasi, p.pertanyaan');
            $this->db->from('rapor_tutor_jawaban j');
            $this->db->join('rapor_tutor_pertanyaan p', 'p.id_pertanyaan = j.id_pertanyaan');
            $this->db->where('j.id_guru', $tutor->id_guru);
            $this->db->where('p.tipe', 'teks');
            $this->db->order_by('j.tanggal', 'DESC');
            $comments = $this->db->get()->result();

            // Fetch multiple choice summaries with evaluated dates
            $this->db->select('j.jawaban, j.tanggal_evaluasi, p.pertanyaan');
            $this->db->from('rapor_tutor_jawaban j');
            $this->db->join('rapor_tutor_pertanyaan p', 'p.id_pertanyaan = j.id_pertanyaan');
            $this->db->where('j.id_guru', $tutor->id_guru);
            $this->db->where('p.tipe', 'pilihan');
            $this->db->order_by('j.tanggal_evaluasi', 'DESC');
            $choices = $this->db->get()->result();

            $rekap[] = [
                'id_guru' => $tutor->id_guru,
                'nama_guru' => $tutor->nama_guru,
                'nip' => $tutor->nip,
                'total_responses' => $total_resp,
                'comments' => $comments,
                'choices' => $choices
            ];
        }
        return $rekap;
    }

    /* ----------------------------------------------------
       LAPORAN INSIDEN (BULLYING/HARASSMENT)
       ---------------------------------------------------- */

    public function get_all_laporan_insiden() {
        $this->db->select('l.*, u.username, u.first_name, u.last_name, s.nama as nama_siswa, mk.nama_kelas');
        $this->db->from('laporan_insiden l');
        $this->db->join('users u', 'u.id = l.id_user', 'left');
        $this->db->join('master_siswa s', 's.username = u.username', 'left');
        $this->db->join('kelas_siswa ks', 's.id_siswa = ks.id_siswa', 'left');
        $this->db->join('master_kelas mk', 'ks.id_kelas = mk.id_kelas', 'left');
        $this->db->order_by('l.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function insert_laporan_insiden($data) {
        return $this->db->insert('laporan_insiden', $data);
    }

    public function update_status_laporan($id, $status, $catatan_admin) {
        return $this->db->where('id_laporan', $id)->update('laporan_insiden', [
            'status' => $status,
            'catatan_admin' => $catatan_admin
        ]);
    }

    public function get_laporan_by_id($id) {
        return $this->db->where('id_laporan', $id)->get('laporan_insiden')->row();
    }

    public function get_rekap_evaluasi_guru($id_guru) {
        // Count total responses
        $this->db->select('COUNT(DISTINCT(tanggal)) as total_resp');
        $this->db->from('rapor_tutor_jawaban');
        $this->db->where('id_guru', $id_guru);
        $total_resp = $this->db->get()->row()->total_resp;

        // Fetch answers for textual comments
        $this->db->select('j.jawaban, j.tanggal, j.tanggal_evaluasi, p.pertanyaan');
        $this->db->from('rapor_tutor_jawaban j');
        $this->db->join('rapor_tutor_pertanyaan p', 'p.id_pertanyaan = j.id_pertanyaan');
        $this->db->where('j.id_guru', $id_guru);
        $this->db->where('p.tipe', 'teks');
        $this->db->order_by('j.tanggal', 'DESC');
        $comments = $this->db->get()->result();

        // Fetch multiple choice summaries with evaluated dates
        $this->db->select('j.jawaban, j.tanggal_evaluasi, p.pertanyaan');
        $this->db->from('rapor_tutor_jawaban j');
        $this->db->join('rapor_tutor_pertanyaan p', 'p.id_pertanyaan = j.id_pertanyaan');
        $this->db->where('j.id_guru', $id_guru);
        $this->db->where('p.tipe', 'pilihan');
        $this->db->order_by('j.tanggal_evaluasi', 'DESC');
        $choices = $this->db->get()->result();

        return [
            'total_responses' => $total_resp,
            'comments' => $comments,
            'choices' => $choices
        ];
    }
}
