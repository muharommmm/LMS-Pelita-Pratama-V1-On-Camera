<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jadwal_fleksibel extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        $this->load->model('Jadwal_fleksibel_model', 'jf_model');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->library('form_validation');
    }

    /**
     * Admin dashboard for managing flexible schedule
     */
    public function index() {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $setting = $this->dashboard->getSetting();

        $data = [
            'user' => $user,
            'profile' => $this->dashboard->getProfileAdmin($user->id),
            'judul' => 'Jadwal Fleksibel',
            'subjudul' => 'Kelola Jadwal & Link Kelas Online',
            'setting' => $setting,
            'tp_active' => $tp,
            'smt_active' => $smt,
            'running_text' => $this->dashboard->getRunningText(),
            'schedules' => $this->jf_model->get_all_flexible_schedules($tp->id_tp, $smt->id_smt),
            'classes' => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt),
            'mapels' => $this->dropdown->getAllMapel(),
            'tutors' => $this->dropdown->getAllGuru()
        ];

        // Day names translation
        $data['day_names'] = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
            5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
        ];

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('jadwal_fleksibel/admin_list', $data);
        $this->load->view('_templates/dashboard/_footer');
    }

    /**
     * Store new flexible schedule
     */
    public function create() {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $this->form_validation->set_rules('class_id', 'Kelas', 'required|integer');
        $this->form_validation->set_rules('mapel_id', 'Mata Pelajaran', 'required|integer');
        $this->form_validation->set_rules('day', 'Hari', 'required|integer');
        $this->form_validation->set_rules('start_time', 'Jam Mulai', 'required');
        $this->form_validation->set_rules('end_time', 'Jam Selesai', 'required');
        $this->form_validation->set_rules('pola_mingguan', 'Pola Mingguan', 'required|in_list[Semua,Ganjil,Genap]');
        $this->form_validation->set_rules('jenis_kegiatan', 'Jenis Kegiatan', 'required|in_list[offline,online,tugas,Tatap Muka,Tugas]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('jadwal_fleksibel');
        }

        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        $data = [
            'class_id' => intval($this->input->post('class_id', true)),
            'mapel_id' => intval($this->input->post('mapel_id', true)),
            'day' => intval($this->input->post('day', true)),
            'start_time' => $this->input->post('start_time', true),
            'end_time' => $this->input->post('end_time', true),
            'learning_link' => $this->input->post('learning_link', true),
            'pola_mingguan' => $this->input->post('pola_mingguan', true),
            'jenis_kegiatan' => $this->input->post('jenis_kegiatan', true),
            'tp_id' => $tp->id_tp,
            'smt_id' => $smt->id_smt
        ];

        if ($this->jf_model->insert_schedule($data)) {
            $this->session->set_flashdata('success', 'Jadwal fleksibel berhasil ditambahkan!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan jadwal fleksibel.');
        }

        redirect('jadwal_fleksibel');
    }

    /**
     * Delete a flexible schedule
     */
    public function delete($id_jadwal) {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        if ($this->jf_model->delete_schedule($id_jadwal)) {
            $this->session->set_flashdata('success', 'Jadwal fleksibel berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus jadwal fleksibel.');
        }

        redirect('jadwal_fleksibel');
    }

    /**
     * Get detail of a schedule (for Edit Modal)
     */
    public function get_detail($id_jadwal) {
        if (!$this->ion_auth->is_admin()) {
            echo json_encode(['status' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        $jadwal = $this->db->where('id_jadwal', $id_jadwal)->get('jadwal_fleksibel')->row();
        if ($jadwal) {
            echo json_encode($jadwal);
        } else {
            echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan.']);
        }
    }

    /**
     * Update an existing flexible schedule
     */
    public function update() {
        if (!$this->ion_auth->is_admin()) {
            echo json_encode(['status' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        $this->form_validation->set_rules('id_jadwal', 'ID Jadwal', 'required|integer');
        $this->form_validation->set_rules('class_id', 'Kelas', 'required|integer');
        $this->form_validation->set_rules('mapel_id', 'Mata Pelajaran', 'required|integer');
        $this->form_validation->set_rules('day', 'Hari', 'required|integer');
        $this->form_validation->set_rules('start_time', 'Jam Mulai', 'required');
        $this->form_validation->set_rules('end_time', 'Jam Selesai', 'required');
        $this->form_validation->set_rules('pola_mingguan', 'Pola Mingguan', 'required|in_list[Semua,Ganjil,Genap]');
        $this->form_validation->set_rules('jenis_kegiatan', 'Jenis Kegiatan', 'required|in_list[offline,online,tugas,Tatap Muka,Tugas]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => false, 'message' => strip_tags(validation_errors())]);
            return;
        }

        $id_jadwal = intval($this->input->post('id_jadwal', true));
        $data = [
            'class_id' => intval($this->input->post('class_id', true)),
            'mapel_id' => intval($this->input->post('mapel_id', true)),
            'day' => intval($this->input->post('day', true)),
            'start_time' => $this->input->post('start_time', true),
            'end_time' => $this->input->post('end_time', true),
            'learning_link' => $this->input->post('learning_link', true),
            'pola_mingguan' => $this->input->post('pola_mingguan', true),
            'jenis_kegiatan' => $this->input->post('jenis_kegiatan', true)
        ];

        $this->db->where('id_jadwal', $id_jadwal);
        if ($this->db->update('jadwal_fleksibel', $data)) {
            $this->session->set_flashdata('success', 'Jadwal fleksibel berhasil diperbarui!');
            echo json_encode(['status' => true, 'message' => 'Jadwal berhasil diperbarui!']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Gagal memperbarui jadwal.']);
        }
    }

    /**
     * Tutor weekly flexible schedule view
     */
    public function tutor() {
        if (!$this->ion_auth->in_group('guru')) {
            show_error('Akses ditolak.', 403);
        }

        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $setting = $this->dashboard->getSetting();

        $guru = $this->db->where(['id_user' => $user->id])->get('master_guru')->row();

        $minggu_sekarang = (int)date('W');
        $pola_berjalan = ($minggu_sekarang % 2 == 0) ? 'Genap' : 'Ganjil';
        $info_jadwal = "Saat ini adalah Minggu Ke-{$minggu_sekarang} (Pola {$pola_berjalan}). Jadwal yang ditampilkan di bawah ini telah disesuaikan secara otomatis untuk pola minggu {$pola_berjalan} beserta jadwal rutin (Setiap Minggu) Anda.";

        $data = [
            'user' => $user,
            'profile' => $this->dashboard->getProfileAdmin($user->id),
            'guru' => $guru,
            'judul' => 'Jadwal Mengajar',
            'subjudul' => 'Jadwal Pembelajaran Anda',
            'setting' => $setting,
            'tp_active' => $tp,
            'smt_active' => $smt,
            'info_jadwal' => $info_jadwal,
            'running_text' => $this->dashboard->getRunningText(),
            'schedules' => $this->jf_model->get_schedules_by_tutor($guru->id_guru, $tp->id_tp, $smt->id_smt)
        ];

        $data['day_names'] = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
            5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('jadwal_fleksibel/tutor_list', $data);
        $this->load->view('members/guru/templates/footer');
    }

    /**
     * Student weekly flexible schedule view
     */
    public function siswa() {
        if (!$this->ion_auth->in_group('siswa')) {
            show_error('Akses ditolak.', 403);
        }

        $this->load->model('Cbt_model', 'cbt');

        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $setting = $this->dashboard->getSetting();

        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);

        $minggu_sekarang = (int)date('W');
        $pola_berjalan = ($minggu_sekarang % 2 == 0) ? 'Genap' : 'Ganjil';
        $info_jadwal = "Saat ini adalah Minggu Ke-{$minggu_sekarang} (Pola {$pola_berjalan}). Jadwal yang ditampilkan di bawah ini telah disesuaikan secara otomatis untuk pola minggu {$pola_berjalan} beserta jadwal rutin (Setiap Minggu) Anda.";

        $data = [
            'user' => $user,
            'profile' => $this->dashboard->getProfileAdmin($user->id),
            'siswa' => $siswa,
            'judul' => 'Jadwal Pelajaran',
            'subjudul' => 'Jadwal Pembelajaran Anda',
            'setting' => $setting,
            'tp_active' => $tp,
            'smt_active' => $smt,
            'info_jadwal' => $info_jadwal,
            'running_text' => $this->dashboard->getRunningText(),
            'schedules' => $this->jf_model->get_schedules_by_class($siswa->id_kelas, $tp->id_tp, $smt->id_smt)
        ];

        $data['day_names'] = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
            5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
        ];

        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('jadwal_fleksibel/siswa_list', $data);
        $this->load->view('members/siswa/templates/footer');
    }

    /**
     * AJAX endpoint to fetch mata pelajaran assigned to a tutor (unserialized from jabatan_guru)
     */
    public function get_mapel_by_tutor_ajax() {
        if (!$this->ion_auth->is_admin()) {
            echo json_encode([]);
            return;
        }

        $tutor_id = intval($this->input->post('tutor_id', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        if (!$tutor_id || !$tp || !$smt) {
            echo json_encode([]);
            return;
        }

        $jabatan = $this->db->where([
            'id_guru' => $tutor_id,
            'id_tp'   => $tp->id_tp,
            'id_smt'  => $smt->id_smt
        ])->get('jabatan_guru')->row();

        $mapel_ids = [];
        if ($jabatan && !empty($jabatan->mapel_kelas)) {
            $arr_mapel_kelas = @unserialize($jabatan->mapel_kelas);
            if (is_array($arr_mapel_kelas)) {
                foreach ($arr_mapel_kelas as $mk) {
                    $mk_arr = is_object($mk) ? (array)$mk : $mk;
                    if (isset($mk_arr['id_mapel'])) {
                        $mapel_ids[] = $mk_arr['id_mapel'];
                    }
                }
            }
        }

        if (!empty($mapel_ids)) {
            $this->db->select('id_mapel, nama_mapel');
            $this->db->from('master_mapel');
            $this->db->where_in('id_mapel', $mapel_ids);
            $result = $this->db->get()->result();
            echo json_encode($result);
        } else {
            echo json_encode([]);
        }
    }

    /**
     * AJAX endpoint to check overlapping schedules (conflict warning check)
     */
    public function cek_bentrok_ajax() {
        if (!$this->ion_auth->is_admin()) {
            echo json_encode(['status' => 'aman']);
            return;
        }

        $tutor_id = intval($this->input->post('tutor_id', true));
        $day = $this->input->post('day', true);
        if (empty($day)) {
            $day = $this->input->post('tanggal', true);
        }
        $day = intval($day);

        $start_time = $this->input->post('start_time', true);
        if (empty($start_time)) {
            $start_time = $this->input->post('jam_mulai', true);
        }

        $end_time = $this->input->post('end_time', true);
        if (empty($end_time)) {
            $end_time = $this->input->post('jam_selesai', true);
        }

        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        if (!$tutor_id || !$day || !$start_time || !$end_time) {
            echo json_encode(['status' => 'aman']);
            return;
        }

        // Cek bentrok
        $bentrok = $this->jf_model->cek_bentrok($tutor_id, $day, $start_time, $end_time, $tp->id_tp, $smt->id_smt);

        if ($bentrok) {
            echo json_encode([
                'status' => 'bentrok',
                'pesan' => 'Tutor sudah memiliki jadwal lain yang beririsan pada hari dan jam tersebut.'
            ]);
        } else {
            echo json_encode(['status' => 'aman']);
        }
    }

    /**
     * Copy an existing flexible schedule
     */
    public function copy($id_jadwal) {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $schedule = $this->db->where('id_jadwal', $id_jadwal)->get('jadwal_fleksibel')->row();
        if (!$schedule) {
            $this->session->set_flashdata('error', 'Jadwal tidak ditemukan.');
            redirect('jadwal_fleksibel');
        }

        $data = [
            'class_id' => $schedule->class_id,
            'mapel_id' => $schedule->mapel_id,
            'day' => $schedule->day,
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
            'learning_link' => $schedule->learning_link,
            'pola_mingguan' => $schedule->pola_mingguan,
            'jenis_kegiatan' => $schedule->jenis_kegiatan,
            'tp_id' => $schedule->tp_id,
            'smt_id' => $schedule->smt_id
        ];

        if ($this->jf_model->insert_schedule($data)) {
            $this->session->set_flashdata('success', 'Jadwal fleksibel berhasil disalin!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyalin jadwal fleksibel.');
        }

        redirect('jadwal_fleksibel');
    }
}

