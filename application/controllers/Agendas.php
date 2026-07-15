<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agendas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        $this->load->model('Agenda_model', 'agenda');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->library('form_validation');
    }

    /**
     * Agenda management list (Admin only)
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
            'judul' => 'Agenda Sekolah',
            'subjudul' => 'Kelola Agenda & Kegiatan Terdekat',
            'setting' => $setting,
            'tp_active' => $tp,
            'smt_active' => $smt,
            'running_text' => $this->dashboard->getRunningText(),
            'agendas' => $this->agenda->get_all_agendas(),
            'classes' => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt)
        ];

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('agenda/admin_list', $data);
        $this->load->view('_templates/dashboard/_footer');
    }

    /**
     * Create agenda (Admin only)
     */
    public function create() {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $this->form_validation->set_rules('title', 'Judul Agenda', 'required|trim');
        $this->form_validation->set_rules('start_date', 'Tanggal Mulai', 'required');
        $this->form_validation->set_rules('end_date', 'Tanggal Selesai', 'required');
        $this->form_validation->set_rules('target_role', 'Target Peran', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('agendas');
        }

        $target_class = $this->input->post('target_class_id', true);
        $target_class = (empty($target_class)) ? NULL : intval($target_class);

        $data = [
            'title' => $this->input->post('title', true),
            'description' => $this->input->post('description', true),
            'start_date' => date('Y-m-d H:i:s', strtotime($this->input->post('start_date', true))),
            'end_date' => date('Y-m-d H:i:s', strtotime($this->input->post('end_date', true))),
            'target_role' => $this->input->post('target_role', true),
            'target_class_id' => $target_class,
            'created_by' => $this->ion_auth->user()->row()->id
        ];

        if ($this->agenda->insert_agenda($data)) {
            $this->session->set_flashdata('success', 'Agenda berhasil ditambahkan!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan agenda.');
        }

        redirect('agendas');
    }

    /**
     * Delete agenda (Admin only)
     */
    public function delete($id_agenda) {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        if ($this->agenda->delete_agenda($id_agenda)) {
            $this->session->set_flashdata('success', 'Agenda berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus agenda.');
        }

        redirect('agendas');
    }
}
