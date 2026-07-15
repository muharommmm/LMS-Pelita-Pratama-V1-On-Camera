<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Honor extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        $this->load->model('Honor_model', 'honor');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->library('form_validation');
    }

    /**
     * Main route for Admin and Tutor
     */
    public function index() {
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $setting = $this->dashboard->getSetting();

        $data = [
            'user' => $user,
            'profile' => $this->dashboard->getProfileAdmin($user->id),
            'judul' => 'Honorarium Tutor',
            'subjudul' => 'Kelola Keuangan Honorarium',
            'setting' => $setting,
            'tp_active' => $tp,
            'smt_active' => $smt,
            'running_text' => $this->dashboard->getRunningText()
        ];

        // Type Names Helper
        $data['type_names'] = [
            'offline' => 'Tatap Muka Offline',
            'online' => 'Tatap Muka Online',
            'check_task' => 'Pemeriksaan Tugas',
            'create_cbt' => 'Pembuatan Bank Soal CBT'
        ];

        if ($this->ion_auth->is_admin()) {
            // Admin Panel
            $start_date = $this->input->get('start_date', true) ? $this->input->get('start_date', true) : date('Y-m-01');
            $end_date = $this->input->get('end_date', true) ? $this->input->get('end_date', true) : date('Y-m-t');

            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            $data['summaries'] = $this->honor->get_all_tutors_summary($start_date, $end_date);
            $data['rates'] = $this->honor->get_rates();
            $data['tutors'] = $this->dropdown->getAllGuru(); // Dropdown select for custom rates and payout

            // Fetch default global rate (where tutor_id is NULL)
            $global_rate = $this->db->where(['tutor_id' => NULL])->get('honor_rates')->row();
            if (!$global_rate) {
                $global_rate = (object)[
                    'id_rate' => 0,
                    'tutor_id' => NULL,
                    'rate_offline' => 50000.00,
                    'rate_online' => 35000.00,
                    'rate_check_task' => 2000.00,
                    'rate_create_cbt' => 15000.00
                ];
            }
            $data['global_rate'] = $global_rate;

            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('honor/admin_dashboard', $data);
            $this->load->view('_templates/dashboard/_footer');

        } elseif ($this->ion_auth->in_group('guru')) {
            // Tutor Panel
            $guru = $this->db->where(['id_user' => $user->id])->get('master_guru')->row();
            
            // 1. Sync activities
            $this->honor->sync_honor($guru->id_guru, $tp->id_tp, $smt->id_smt);
            
            // 2. Fetch data
            $data['guru'] = $guru;
            $data['pending_records'] = $this->honor->get_honor_records($guru->id_guru, 'pending');
            $data['approved_records'] = $this->honor->get_honor_records($guru->id_guru, 'approved');
            $data['paid_records'] = $this->honor->get_honor_records($guru->id_guru, 'paid');
            $data['rejected_records'] = $this->honor->get_honor_records($guru->id_guru, 'rejected');
            $data['mutations'] = $this->honor->get_mutations($guru->id_guru);

            // Hitung ringkasan pendapatan tahunan aktif secara terpadu
            $yearly_summary = $this->honor->get_tutor_yearly_summary($guru->id_guru, $tp->id_tp);
            $total_menunggu_pencairan = 0;
            $total_sudah_dibayar = 0;
            foreach ($yearly_summary as $summary) {
                $total_menunggu_pencairan += floatval($summary->unpaid_amount);
                $total_sudah_dibayar += floatval($summary->paid_amount);
            }
            $data['yearly_summary'] = $yearly_summary;
            $data['total_menunggu_pencairan'] = $total_menunggu_pencairan;
            $data['total_sudah_dibayar'] = $total_sudah_dibayar;

            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('honor/tutor_view', $data);
            $this->load->view('members/guru/templates/footer');
        } else {
            show_error('Akses ditolak.', 403);
        }
    }

    /**
     * Save Rates (Admin Only)
     */
    public function save_rate() {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $tutor_id = $this->input->post('tutor_id', true);
        $tutor_id = ($tutor_id === 'global' || empty($tutor_id)) ? NULL : intval($tutor_id);

        $data = [
            'tutor_id' => $tutor_id,
            'rate_offline' => floatval($this->input->post('rate_offline', true)),
            'rate_online' => floatval($this->input->post('rate_online', true)),
            'rate_check_task' => floatval($this->input->post('rate_check_task', true)),
            'rate_create_cbt' => floatval($this->input->post('rate_create_cbt', true))
        ];

        // Check if rate already exists for this tutor_id to update
        $existing = $this->db->where(['tutor_id' => $tutor_id])->get('honor_rates')->row();
        if ($existing) {
            $data['id_rate'] = $existing->id_rate;
        }

        if ($this->honor->save_rate($data)) {
            $this->session->set_flashdata('success', 'Tarif honorarium berhasil disimpan!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan tarif honorarium.');
        }

        redirect('honor');
    }

    /**
     * Process Payout (Admin Only)
     */
    public function payout() {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $tutor_id = intval($this->input->post('tutor_id', true));
        $amount = floatval($this->input->post('amount', true));
        $notes = $this->input->post('notes', true);
        $start_date = $this->input->post('start_date', true);
        $end_date = $this->input->post('end_date', true);

        if (empty($start_date)) {
            $start_date = date('Y-m-01');
        }
        if (empty($end_date)) {
            $end_date = date('Y-m-t');
        }

        if (!$tutor_id || $amount <= 0) {
            $this->session->set_flashdata('error', 'Gagal memproses pembayaran: Parameter tidak valid.');
            redirect('honor');
        }

        if ($this->honor->pay_honor($tutor_id, $amount, $notes, $start_date, $end_date)) {
            $this->session->set_flashdata('success', 'Mutasi pembayaran honor berhasil diproses!');
            
            // Send notification to tutor
            $tutor = $this->db->where(['id_guru' => $tutor_id])->get('master_guru')->row();
            if ($tutor && $tutor->id_user) {
                $this->load->model('Notifikasi_model', 'notif_m');
                $this->notif_m->createNotifikasi([
                    'user_id'  => $tutor->id_user,
                    'role'     => 'guru',
                    'type'     => 'honor_pending',
                    'title'    => 'Honorarium Anda dicairkan Rp ' . number_format($amount, 0, ',', '.'),
                    'body'     => 'Silakan klik untuk konfirmasi penerimaan honor.',
                    'url'      => 'honor',
                    'metadata' => [
                        'amount'   => $amount,
                        'tutor_id' => $tutor_id
                    ]
                ]);
            }
        } else {
            $this->session->set_flashdata('error', 'Gagal memproses pembayaran.');
        }

        $bulan = $this->input->post('filter_bulan', true);
        $tahun = $this->input->post('filter_tahun', true);
        $query = "";
        if ($bulan && $tahun) {
            $query = "?bulan=" . urlencode($bulan) . "&tahun=" . urlencode($tahun);
        }
        redirect('honor' . $query);
    }

    /**
     * Confirm Payout Mutation (Tutor Only)
     */
    public function confirm_payout($id_mutation) {
        if (!$this->ion_auth->in_group('guru')) {
            show_error('Akses ditolak.', 403);
        }

        $user = $this->ion_auth->user()->row();
        $guru = $this->db->where(['id_user' => $user->id])->get('master_guru')->row();

        $mutation = $this->honor->get_mutation_by_id($id_mutation);
        if (!$mutation || $mutation->tutor_id != $guru->id_guru) {
            $this->session->set_flashdata('error', 'Data mutasi tidak ditemukan.');
            redirect('honor');
        }

        if ($this->honor->confirm_mutation($id_mutation)) {
            $this->session->set_flashdata('success', 'Pembayaran telah berhasil Anda konfirmasi!');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengonfirmasi pembayaran.');
        }

        redirect('honor');
    }

    /**
     * View detailed list of honor records for a specific tutor (Admin Only)
     */
    public function detail_tutor($tutor_id) {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $setting = $this->dashboard->getSetting();

        $tutor = $this->db->where(['id_guru' => $tutor_id])->get('master_guru')->row();
        if (!$tutor) {
            $this->session->set_flashdata('error', 'Tutor tidak ditemukan.');
            redirect('honor');
        }

        // Get filter parameter for date range
        $start_date = $this->input->get('start_date', true) ? $this->input->get('start_date', true) : date('Y-m-01');
        $end_date = $this->input->get('end_date', true) ? $this->input->get('end_date', true) : date('Y-m-t');

        $data = [
            'user' => $user,
            'profile' => $this->dashboard->getProfileAdmin($user->id),
            'judul' => 'Detail Rincian Honor',
            'subjudul' => 'Kelola Rincian Gaji ' . $tutor->nama_guru,
            'setting' => $setting,
            'tp_active' => $tp,
            'smt_active' => $smt,
            'running_text' => $this->dashboard->getRunningText(),
            'tutor' => $tutor,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'pending_records' => $this->honor->get_honor_records($tutor_id, 'pending', $start_date, $end_date),
            'approved_records' => $this->honor->get_honor_records($tutor_id, 'approved', $start_date, $end_date),
            'paid_records' => $this->honor->get_honor_records($tutor_id, 'paid', $start_date, $end_date),
            'rejected_records' => $this->honor->get_honor_records($tutor_id, 'rejected', $start_date, $end_date),
            'mutations' => $this->honor->get_mutations($tutor_id)
        ];

        // Hitung total nominal akhir secara dinamis (Hanya approved yang valid dihitung)
        $total_unpaid = 0;
        foreach ($data['approved_records'] as $rec) {
            $total_unpaid += ($rec->adjusted_amount !== null && floatval($rec->adjusted_amount) > 0) ? floatval($rec->adjusted_amount) : floatval($rec->amount);
        }

        $total_paid = 0;
        foreach ($data['paid_records'] as $rec) {
            $total_paid += ($rec->adjusted_amount !== null && floatval($rec->adjusted_amount) > 0) ? floatval($rec->adjusted_amount) : floatval($rec->amount);
        }

        $data['total_unpaid'] = $total_unpaid;
        $data['total_paid'] = $total_paid;
        $data['total_honor'] = $total_unpaid + $total_paid;

        // Fetch tutor's rates
        $rates = $this->honor->get_rate_by_tutor($tutor_id);
        $data['master_tarif'] = [
            'offline' => floatval($rates->rate_offline),
            'online' => floatval($rates->rate_online),
            'check_task' => floatval($rates->rate_check_task),
            'create_cbt' => floatval($rates->rate_create_cbt)
        ];

        $data['type_names'] = [
            'offline' => 'Tatap Muka Offline',
            'online' => 'Tatap Muka Online',
            'check_task' => 'Pemeriksaan Tugas',
            'create_cbt' => 'Pembuatan Bank Soal CBT'
        ];

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('honor/detail_tutor', $data);
        $this->load->view('_templates/dashboard/_footer');
    }

    /**
     * Process adjustment of amount, status, and admin notes for a specific record (Admin Only)
     */
    public function update_adjustment() {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $id_honor = intval($this->input->post('id_honor_record', true));
        $tutor_id = intval($this->input->post('tutor_id', true));
        $adjusted_amount = $this->input->post('adjusted_amount', true);
        $admin_notes = $this->input->post('admin_notes', true);
        $status = $this->input->post('status', true);
        $type = $this->input->post('type', true);

        $record = $this->db->where(['id_honor_record' => $id_honor])->get('honor_records')->row();
        if (!$record || $record->status == 'paid') {
            $post_data_str = json_encode($this->input->post());
            $this->session->set_flashdata('error', 'Gagal memproses penyesuaian: Record sudah dibayar atau tidak ditemukan. Received POST: ' . $post_data_str);
            redirect('honor/detail_tutor/' . $tutor_id);
        }

        $data = [
            'adjusted_amount' => ($adjusted_amount === '' || $adjusted_amount === null) ? null : floatval($adjusted_amount),
            'admin_notes' => empty($admin_notes) ? null : $admin_notes
        ];

        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $data['status'] = $status;
        }

        if ($type && in_array($type, ['offline', 'online', 'check_task', 'create_cbt'])) {
            $data['type'] = $type;
        }

        if ($this->honor->update_honor_by_admin($id_honor, $data)) {
            $this->session->set_flashdata('success', 'Penyesuaian nominal dan status berhasil disimpan!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan penyesuaian.');
        }

        // Preserve filter dates
        $start_date = $this->input->post('start_date', true);
        $end_date = $this->input->post('end_date', true);
        $query = "";
        if ($start_date && $end_date) {
            $query = "?start_date=" . urlencode($start_date) . "&end_date=" . urlencode($end_date);
        }

        redirect('honor/detail_tutor/' . $tutor_id . $query);
    }

    /**
     * Render slip view for printing (Admin / Guru)
     */
    public function cetak_slip($tutor_id) {
        $user = $this->ion_auth->user()->row();
        
        // Security check: must be admin or the tutor themselves
        if (!$this->ion_auth->is_admin()) {
            $guru = $this->db->where(['id_user' => $user->id])->get('master_guru')->row();
            if (!$guru || $guru->id_guru != $tutor_id) {
                show_error('Akses ditolak.', 403);
            }
        }

        $start_date = $this->input->get('start', true);
        $end_date = $this->input->get('end', true);

        $tutor = $this->db->where(['id_guru' => $tutor_id])->get('master_guru')->row();
        if (!$tutor) {
            show_error('Tutor tidak ditemukan.', 404);
        }

        $slip_data = $this->honor->get_slip_per_tutor($tutor_id, $start_date, $end_date);
        
        $data = [
            'tutor' => $tutor,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'slip_data' => $slip_data,
            'setting' => $this->dashboard->getSetting()
        ];

        $this->load->view('honor/print_slip_tutor', $data);
    }

    /**
     * Render dynamic matrix report of all tutors across class levels for printing (Admin Only)
     */
    public function cetak_rekap_yayasan() {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $start_date = $this->input->get('start', true);
        $end_date = $this->input->get('end', true);

        // Fetch raw data
        $raw_data = $this->honor->get_rekap_yayasan_raw($start_date, $end_date);
        
        // Group raw data into Nama Tutor x Level ID (4 s/d 12)
        $tutors_matrix = [];
        $total_overall = 0.00;

        $this->db->select('id_guru, nama_guru, NIP');
        $this->db->from('master_guru');
        $this->db->order_by('nama_guru', 'ASC');
        $tutors = $this->db->get()->result();

        // Initialize matrix
        foreach ($tutors as $t) {
            $tutors_matrix[$t->id_guru] = [
                'nama_guru' => $t->nama_guru,
                'nip' => $t->NIP,
                'levels' => array_fill(4, 9, 0.00), // columns for level 4 to 12
                'total_tutor' => 0.00,
                'is_active' => false
            ];
        }

        // Map raw data onto matrix
        foreach ($raw_data as $row) {
            if (isset($tutors_matrix[$row->tutor_id])) {
                $lvl = intval($row->level_id);
                if ($lvl >= 4 && $lvl <= 12) {
                    $tutors_matrix[$row->tutor_id]['levels'][$lvl] += $row->amount;
                    $tutors_matrix[$row->tutor_id]['total_tutor'] += $row->amount;
                    $tutors_matrix[$row->tutor_id]['is_active'] = true;
                    $total_overall += $row->amount;
                }
            }
        }

        $data = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'tutors_matrix' => $tutors_matrix,
            'total_overall' => $total_overall,
            'setting' => $this->dashboard->getSetting()
        ];

        $this->load->view('honor/print_rekap_yayasan', $data);
    }

    /**
     * Delete tutor honor record permanently (Admin Only)
     */
    public function hapus_record($id_honor_record) {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $record = $this->db->where(['id_honor_record' => $id_honor_record])->get('honor_records')->row();
        if (!$record) {
            $this->session->set_flashdata('error', 'Record tidak ditemukan.');
            redirect('honor');
        }

        $tutor_id = $record->tutor_id;

        if ($this->honor->delete_record_by_admin($id_honor_record)) {
            $this->session->set_flashdata('success', 'Catatan honorarium berhasil dihapus (dibatalkan)!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus catatan honorarium.');
        }

        // Preserve filter date context if available in GET parameters
        $start_date = $this->input->get('start_date', true);
        $end_date = $this->input->get('end_date', true);
        $query = "";
        if ($start_date && $end_date) {
            $query = "?start_date=" . urlencode($start_date) . "&end_date=" . urlencode($end_date);
        }

        redirect('honor/detail_tutor/' . $tutor_id . $query);
    }

    /**
     * Batalkan status lunas tutor — kembalikan semua record 'paid' ke 'approved' (Admin Only)
     */
    public function batal_lunas($tutor_id) {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $tutor_id = intval($tutor_id);
        if (!$tutor_id) {
            $this->session->set_flashdata('error', 'ID Tutor tidak valid.');
            redirect('honor');
        }

        $start_date = $this->input->get('start_date', true);
        $end_date = $this->input->get('end_date', true);

        if (empty($start_date)) {
            $start_date = date('Y-m-01');
        }
        if (empty($end_date)) {
            $end_date = date('Y-m-t');
        }

        if ($this->honor->batalkan_status_lunas($tutor_id, $start_date, $end_date)) {
            $this->session->set_flashdata('success', 'Status pembayaran berhasil dibatalkan. Data honorarium kembali ke rincian belum dibayar dan dapat dikoreksi ulang.');
        } else {
            $this->session->set_flashdata('error', 'Gagal membatalkan status lunas. Pastikan tutor ini memiliki record berstatus Paid.');
        }

        // Kembali ke halaman sebelumnya, fallback ke dashboard honor
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url('honor');
        redirect($referer);
    }

    /**
     * Delete tutor honor record (Tutor only)
     */
    public function delete_record($id_honor) {
        if (!$this->ion_auth->in_group('guru')) {
            show_error('Akses ditolak.', 403);
        }

        $user = $this->ion_auth->user()->row();
        $guru = $this->db->where(['id_user' => $user->id])->get('master_guru')->row();

        if ($this->honor->delete_honor_record($id_honor, $guru->id_guru)) {
            $this->session->set_flashdata('success', 'Catatan honorarium berhasil dihapus (dibatalkan)!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus catatan honorarium: status sudah dibayar atau tidak ditemukan.');
        }

        redirect('honor');
    }

    /**
     * Execute Batch Insert of Honor Records (Admin Only)
     */
    public function simpan_massal_honor() {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $tutor_ids = $this->input->post('tutor_ids', true);
        $aktivitas = $this->input->post('aktivitas', true); // 'offline', 'online', 'check_task', 'create_cbt'
        $tarif = floatval($this->input->post('tarif', true));
        $tanggal_aktivitas = $this->input->post('tanggal_aktivitas', true);
        $mapel_kelas = $this->input->post('mapel_kelas', true);
        $kuantitas = intval($this->input->post('kuantitas', true));

        if (empty($tutor_ids) || empty($aktivitas) || $tarif <= 0 || empty($tanggal_aktivitas) || $kuantitas <= 0) {
            $this->session->set_flashdata('error', 'Gagal menyimpan input massal: Parameter tidak lengkap.');
            redirect('honor');
        }

        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        $data_insert = [];
        foreach ($tutor_ids as $id) {
            $data_insert[] = [
                'tutor_id' => intval($id),
                'tp_id' => $tp->id_tp,
                'smt_id' => $smt->id_smt,
                'type' => $aktivitas,
                'reference_id' => mt_rand(10000000, 99999999), // Random unique reference
                'qty' => $kuantitas,
                'rate' => $tarif,
                'amount' => $kuantitas * $tarif,
                'status' => 'approved', // Auto-approved by admin
                'admin_notes' => '[Input Massal] ' . $mapel_kelas,
                'created_at' => $tanggal_aktivitas . ' ' . date('H:i:s')
            ];
        }

        if (!empty($data_insert)) {
            $this->db->insert_batch('honor_records', $data_insert);
            $this->session->set_flashdata('success', 'Data honor berhasil ditambahkan secara massal!');
        }

        redirect('honor');
    }

    /**
     * Handle bulk actions for honor records (Approve, Reject, Delete)
     */
    public function bulk_action() {
        // Set response type di awal — agar jika terjadi error apapun, browser tetap terima JSON
        $this->output->set_content_type('application/json');
        log_message('error', '[DEBUG_BULK] Received POST: ' . json_encode($_POST));

        try {
            if (!$this->ion_auth->is_admin()) {
                log_message('error', '[DEBUG_BULK] Access denied - not admin');
                $this->output->set_output(json_encode(['status' => false, 'message' => 'Akses ditolak.']));
                return;
            }

            $ids = $this->input->post('honor_ids');
            $action = $this->input->post('action');
            $catatan = $this->input->post('catatan');

            if (empty($ids)) {
                log_message('error', '[DEBUG_BULK] empty honor_ids');
                $this->output->set_output(json_encode(['status' => false, 'message' => 'Tidak ada data yang dipilih.']));
                return;
            }

            // Robust parsing of IDs
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            } elseif (!is_array($ids)) {
                $ids = [$ids];
            }

            // Sanitasi: pastikan semua ID integer valid
            $clean_ids = array_map('intval', $ids);
            $clean_ids = array_filter($clean_ids, function($v) { return $v > 0; });

            log_message('error', '[DEBUG_BULK] clean_ids: ' . json_encode($clean_ids) . ' | action: ' . $action);

            if (empty($clean_ids)) {
                log_message('error', '[DEBUG_BULK] empty clean_ids after sanitization');
                $this->output->set_output(json_encode(['status' => false, 'message' => 'ID data tidak valid.']));
                return;
            }

            // Whitelist action yang diperbolehkan
            $allowed_actions = ['approved', 'rejected', 'delete'];
            if (!in_array($action, $allowed_actions)) {
                log_message('error', '[DEBUG_BULK] invalid action: ' . $action);
                $this->output->set_output(json_encode(['status' => false, 'message' => 'Aksi tidak dikenali: ' . htmlspecialchars($action)]));
                return;
            }

            // Eksekusi dengan transaction agar atomic
            $this->db->trans_start();

            if ($action === 'delete') {
                $this->db->where_in('id_honor_record', $clean_ids);
                $this->db->delete('honor_records');
            } else {
                $data_update = ['status' => $action];
                if (!empty($catatan)) {
                    $data_update['admin_notes'] = $catatan;
                }

                $this->db->where_in('id_honor_record', $clean_ids);
                $this->db->update('honor_records', $data_update);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $db_error = $this->db->error();
                log_message('error', '[DEBUG_BULK] DB Error: ' . json_encode($db_error));
                $this->output->set_output(json_encode(['status' => false, 'message' => 'Database error: ' . $db_error['message']]));
            } else {
                $jumlah = count($clean_ids);
                log_message('error', '[DEBUG_BULK] SUCCESS. Processed: ' . $jumlah);
                $this->output->set_output(json_encode(['status' => true, 'message' => $jumlah . ' data berhasil diproses (' . $action . ')!']));
            }

        } catch (Exception $e) {
            log_message('error', '[DEBUG_BULK] Exception: ' . $e->getMessage());
            $this->output->set_output(json_encode(['status' => false, 'message' => 'Server error: ' . $e->getMessage()]));
        }
    }
}

