<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        
        $this->load->model('Laporan_model', 'laporan');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->library('form_validation');
        $this->load->library('upload');
    }

    /**
     * Main route redirecting users depending on role
     */
    public function index() {
        try {
            $user = $this->ion_auth->user()->row();
            $tp = $this->dashboard->getTahunActive();
            $smt = $this->dashboard->getSemesterActive();
            $setting = $this->dashboard->getSetting();

            $id_tp = $tp ? $tp->id_tp : 0;
            $id_smt = $smt ? $smt->id_smt : 0;

            $data = [
                'user' => $user,
                'profile' => $this->dashboard->getProfileAdmin($user->id),
                'judul' => 'Menu Pelaporan',
                'subjudul' => 'Layanan Lapor & Evaluasi',
                'setting' => $setting,
                'tp' => $this->dashboard->getTahun(),
                'tp_active' => $tp ? $tp : (object)['id_tp' => 0, 'tahun' => date('Y')],
                'smt' => $this->dashboard->getSemester(),
                'smt_active' => $smt ? $smt : (object)['id_smt' => 0, 'smt' => '1'],
                'running_text' => $this->dashboard->getRunningText(),
                'lapor_settings' => $this->laporan->get_settings()
            ];

            if ($this->ion_auth->is_admin()) {
                // Admin View: Kelola Laporan
                $data['judul'] = 'Kotak Pengaduan & Rapor Tutor';
                $data['subjudul'] = 'Kelola kuesioner, rekap evaluasi tutor, dan laporan insiden';
                $data['pertanyaan'] = $this->laporan->get_all_pertanyaan();
                $data['rekap_tutor'] = $this->laporan->get_tutor_rekap();
                $data['laporan_insiden'] = $this->laporan->get_all_laporan_insiden();
                $data['kelas'] = $this->dropdown->getAllKelas($id_tp, $id_smt);

                $this->load->view('_templates/dashboard/_header', $data);
                $this->load->view('laporan/kelola_laporan_admin', $data);
                $this->load->view('_templates/dashboard/_footer');

            } elseif ($this->ion_auth->in_group('siswa')) {
                // Student View: Mengisi Laporan
                $siswa = $this->dashboard->getDataSiswa($user->username, $id_tp, $id_smt);
                
                // Get all active questions
                $data['pertanyaan'] = $this->laporan->get_active_pertanyaan();
                
                // Get list of teachers for tutor selection dropdown
                $data['teachers'] = $this->db->select('id_guru, nama_guru')->order_by('nama_guru', 'ASC')->get('master_guru')->result();
                $data['siswa'] = $siswa;

                // Load modern standalone view
                $this->load->view('laporan/lapor_siswa', $data);
            } elseif ($this->ion_auth->in_group('guru')) {
                // Guru View: Lihat Rapor Kuesioner Evaluasi
                $guru = $this->dashboard->getDataGuruByUserId($user->id, $id_tp, $id_smt);
                if (!$guru) {
                    show_error('Profil guru tidak ditemukan.', 404);
                }

                // Otomatis tandai notifikasi peringatan kuesioner guru ini sebagai dibaca
                $this->db->where('user_id', $user->id)
                         ->where('type', 'kuesioner_warning_tutor')
                         ->update('dashboard_notifications', ['is_read' => 1]);

                $data['judul'] = 'Rapor Evaluasi KBM Anda';
                $data['subjudul'] = 'Daftar masukan dan evaluasi KBM dari siswa';
                $data['guru'] = $guru;

                // Ambil rekapitulasi evaluasi untuk guru ini
                $data['evaluasi'] = $this->laporan->get_rekap_evaluasi_guru($guru->id_guru);

                $this->load->view('members/guru/templates/header', $data);
                $this->load->view('members/guru/rapor/kuesioner', $data);
                $this->load->view('members/guru/templates/footer');
            } else {
                show_error('Akses ditolak. Peran Anda tidak diizinkan untuk melihat menu ini.', 403);
            }
        } catch (Throwable $t) {
            if (ob_get_level() > 0) {
                ob_clean();
            }
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            echo "<h1>Laporan Controller Exception:</h1>";
            echo "<p><b>Message:</b> " . $t->getMessage() . "</p>";
            echo "<p><b>File:</b> " . $t->getFile() . " on line " . $t->getLine() . "</p>";
            echo "<pre>" . $t->getTraceAsString() . "</pre>";
            exit();
        }
    }

    /**
     * Submit tutor evaluation questionnaire (Siswa)
     */
    public function simpan_evaluasi() {
        if (!$this->ion_auth->in_group('siswa')) {
            show_error('Akses ditolak.', 403);
        }

        $id_guru = $this->input->post('id_guru', true);
        $tanggal_evaluasi = $this->input->post('tanggal_evaluasi', true);

        if (!$id_guru) {
            $this->session->set_flashdata('error', 'Silakan pilih tutor yang dievaluasi.');
            redirect('laporan');
        }

        $settings = $this->laporan->get_settings();
        if (isset($settings['evaluasi_tanggal_tampil']) && $settings['evaluasi_tanggal_tampil'] == '1') {
            if (isset($settings['evaluasi_tanggal_wajib']) && $settings['evaluasi_tanggal_wajib'] == '1' && !$tanggal_evaluasi) {
                $this->session->set_flashdata('error', 'Silakan tentukan tanggal pembelajaran yang dievaluasi.');
                redirect('laporan');
            }
        } else {
            $tanggal_evaluasi = null;
        }

        if (empty($tanggal_evaluasi)) {
            $tanggal_evaluasi = null;
        }

        $user = $this->ion_auth->user()->row();
        
        // Prevent spam (once per day per tutor)
        if ($this->laporan->has_evaluated_today($user->id, $id_guru)) {
            $this->session->set_flashdata('error', 'Anda sudah melakukan evaluasi untuk tutor ini hari ini.');
            redirect('laporan');
        }

        $questions = $this->laporan->get_active_pertanyaan();
        $answers = [];
        foreach ($questions as $q) {
            $answer = $this->input->post('question_' . $q->id_pertanyaan, true);
            $answers[$q->id_pertanyaan] = $answer ? $answer : 'Tidak ada jawaban';
        }

        if ($this->laporan->submit_evaluation($user->id, $id_guru, $answers, $tanggal_evaluasi)) {
            // Deteksi kata kunci kuesioner negatif (Opsi 2)
            $trigger_notif = false;
            $flagged_answers = [];
            foreach ($answers as $q_id => $ans_text) {
                $ans_lower = strtolower($ans_text);
                if (strpos($ans_lower, 'tidak hadir') !== false || strpos($ans_lower, 'terlambat') !== false || strpos($ans_lower, 'absen') !== false) {
                    $trigger_notif = true;
                    $flagged_answers[] = $ans_text;
                }
            }

            if ($trigger_notif) {
                $tutor = $this->db->where('id_guru', $id_guru)->get('master_guru')->row();
                $tutor_name = $tutor ? $tutor->nama_guru : 'Tutor';
                
                $this->load->model('Notifikasi_model', 'notif');
                
                // 1. Kirim ke Admin
                $admin_users = $this->db->select('user_id')->where('group_id', 1)->get('users_groups')->result();
                foreach ($admin_users as $admin) {
                    $this->notif->createNotifikasi([
                        'user_id' => $admin->user_id,
                        'role'    => 'admin',
                        'type'    => 'kuesioner_warning',
                        'title'   => 'Peringatan Evaluasi Tutor',
                        'body'    => "Siswa melaporkan $tutor_name: " . implode(', ', $flagged_answers),
                        'url'     => 'laporan'
                    ]);
                }

                // 2. Kirim ke Tutor yang bersangkutan (Secara Anonim)
                if ($tutor && !empty($tutor->id_user)) {
                    $tgl_eval_str = !empty($tanggal_evaluasi) ? " pada tanggal " . date('d-m-Y', strtotime($tanggal_evaluasi)) : "";
                    $this->notif->createNotifikasi([
                        'user_id' => $tutor->id_user,
                        'role'    => 'guru',
                        'type'    => 'kuesioner_warning_tutor',
                        'title'   => 'Peringatan Kehadiran KBM',
                        'body'    => "Siswa melaporkan bahwa Anda terlambat / tidak hadir{$tgl_eval_str}. Detail masukan: " . implode(', ', $flagged_answers),
                        'url'     => 'laporan' // Mengarahkan guru ke halaman Rapor Evaluasi KBM mereka
                    ]);
                }
            }

            $this->session->set_flashdata('success', 'Rapor evaluasi tutor berhasil dikirim! Terima kasih atas masukan Anda.');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengirim evaluasi.');
        }
        redirect('laporan');
    }

    /**
     * Submit tragedy/incident report (Siswa)
     */
    public function simpan_insiden() {
        if (!$this->ion_auth->in_group('siswa')) {
            show_error('Akses ditolak.', 403);
        }

        $this->form_validation->set_rules('kategori', 'Kategori Laporan', 'required|trim');
        $this->form_validation->set_rules('deskripsi', 'Deskripsi Kejadian', 'required|trim');
        $this->form_validation->set_rules('tanggal_kejadian', 'Tanggal Kejadian', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('laporan');
        }

        // Upload attachment if any
        $file_path = null;
        if (!empty($_FILES['bukti_file']['name'])) {
            $upload_path = './uploads/laporan_insiden/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path']   = $upload_path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|zip';
            $config['max_size']      = '10240'; // 10MB max
            $config['encrypt_name']  = TRUE;

            $this->upload->initialize($config);

            if (!$this->upload->do_upload('bukti_file')) {
                $this->session->set_flashdata('error', 'Gagal mengunggah berkas bukti: ' . $this->upload->display_errors('', ''));
                redirect('laporan');
            } else {
                $upload_data = $this->upload->data();
                $file_path = 'uploads/laporan_insiden/' . $upload_data['file_name'];
            }
        }

        $user = $this->ion_auth->user()->row();
        $is_anonymous = $this->input->post('is_anonymous') == '1' ? 1 : 0;

        $data = [
            'id_user' => $user->id,
            'is_anonymous' => $is_anonymous,
            'kategori' => $this->input->post('kategori', true),
            'deskripsi' => $this->input->post('deskripsi', true),
            'tanggal_kejadian' => $this->input->post('tanggal_kejadian', true),
            'bukti_file' => $file_path,
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->laporan->insert_laporan_insiden($data)) {
            // Kirim notifikasi insiden baru ke seluruh admin
            $this->load->model('Notifikasi_model', 'notif');
            $admin_users = $this->db->select('user_id')->where('group_id', 1)->get('users_groups')->result();
            foreach ($admin_users as $admin) {
                $this->notif->createNotifikasi([
                    'user_id' => $admin->user_id,
                    'role'    => 'admin',
                    'type'    => 'insiden_baru',
                    'title'   => 'Laporan Insiden Baru',
                    'body'    => "Laporan aduan kategori '" . $data['kategori'] . "' baru masuk.",
                    'url'     => 'laporan'
                ]);
            }
            $this->session->set_flashdata('success', 'Laporan insiden berhasil dikirim. Kami akan menindaklanjutinya dengan segera.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan laporan.');
        }
        redirect('laporan');
    }

    /* ----------------------------------------------------
       ADMIN - QUESTIONNAIRE MANAGEMENT
       ---------------------------------------------------- */

    public function tambah_pertanyaan() {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $pertanyaan = $this->input->post('pertanyaan', true);
        $tipe = $this->input->post('tipe', true);
        $pilihan = $this->input->post('pilihan_jawaban', true);

        if (empty($pertanyaan)) {
            $this->session->set_flashdata('error', 'Pertanyaan tidak boleh kosong.');
            redirect('laporan');
        }

        $data = [
            'pertanyaan' => $pertanyaan,
            'tipe' => $tipe,
            'pilihan_jawaban' => $tipe === 'pilihan' ? trim($pilihan) : NULL,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->laporan->insert_pertanyaan($data)) {
            $this->session->set_flashdata('success', 'Pertanyaan baru berhasil ditambahkan!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan pertanyaan.');
        }
        redirect('laporan');
    }

    public function edit_pertanyaan($id) {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $pertanyaan = $this->input->post('pertanyaan', true);
        $tipe = $this->input->post('tipe', true);
        $pilihan = $this->input->post('pilihan_jawaban', true);
        $is_active = $this->input->post('is_active') == '1' ? 1 : 0;

        $data = [
            'pertanyaan' => $pertanyaan,
            'tipe' => $tipe,
            'pilihan_jawaban' => $tipe === 'pilihan' ? trim($pilihan) : NULL,
            'is_active' => $is_active
        ];

        if ($this->laporan->update_pertanyaan($id, $data)) {
            $this->session->set_flashdata('success', 'Pertanyaan berhasil diperbarui!');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui pertanyaan.');
        }
        redirect('laporan');
    }

    public function hapus_pertanyaan($id) {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        if ($this->laporan->delete_pertanyaan($id)) {
            $this->session->set_flashdata('success', 'Pertanyaan berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus pertanyaan.');
        }
        redirect('laporan');
    }

    public function simpan_pengaturan_lapor() {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $tampil = $this->input->post('evaluasi_tanggal_tampil', true) ? '1' : '0';
        $wajib = $this->input->post('evaluasi_tanggal_wajib', true) ? '1' : '0';

        $settings = [
            'evaluasi_tanggal_tampil' => $tampil,
            'evaluasi_tanggal_wajib' => $wajib
        ];

        if ($this->laporan->update_settings($settings)) {
            $this->session->set_flashdata('success', 'Pengaturan parameter kuesioner berhasil diperbarui!');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui pengaturan.');
        }
        redirect('laporan');
    }

    /* ----------------------------------------------------
       ADMIN - UPDATE INCIDENT STATUS
       ---------------------------------------------------- */

    public function update_status_insiden($id) {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $status = $this->input->post('status', true);
        $catatan_admin = $this->input->post('catatan_admin', true);

        if ($this->laporan->update_status_laporan($id, $status, $catatan_admin)) {
            $this->session->set_flashdata('success', 'Status laporan aduan berhasil diperbarui!');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui status laporan.');
        }
        redirect('laporan');
    }
}
