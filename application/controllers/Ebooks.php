<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ebooks extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        
        $this->load->model('Ebook_model', 'ebook');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->library('form_validation');
        $this->load->library('upload');
    }

    /**
     * Main display page for eBooks based on user role
     */
    public function index() {
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $setting = $this->dashboard->getSetting();

        $data = [
            'user' => $user,
            'profile' => $this->dashboard->getProfileAdmin($user->id),
            'judul' => 'E-Book Pelajaran',
            'subjudul' => 'Daftar E-Book Pembelajaran',
            'setting' => $setting,
            'tp' => $this->dashboard->getTahun(),
            'tp_active' => $tp,
            'smt' => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'running_text' => $this->dashboard->getRunningText()
        ];

        if ($this->ion_auth->is_admin()) {
            // Admin View
            $data['ebooks'] = $this->ebook->get_all_ebooks();
            $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
            $data['mapels'] = $this->ebook->get_all_mapels();
            $data['ekskuls'] = $this->ebook->get_all_ekskuls();
            
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('ebooks/admin_list', $data);
            $this->load->view('_templates/dashboard/_footer');

        } elseif ($this->ion_auth->in_group('guru')) {
            // Tutor / Guru View
            $guru = $this->db->where('id_user', $user->id)->get('master_guru')->row();
            $tutor_classes = $this->ebook->get_tutor_classes($guru->id_guru, $tp->id_tp, $smt->id_smt);
            
            // Extract mapel IDs taught by this guru from assignments in jabatan_guru
            $tutor_mapels = [];
            $this->db->select('mapel_kelas');
            $this->db->from('jabatan_guru');
            $this->db->where('id_guru', $guru->id_guru);
            $this->db->where('id_tp', $tp->id_tp);
            $this->db->where('id_smt', $smt->id_smt);
            $assignments = $this->db->get()->result();
            foreach ($assignments as $assign) {
                if (!empty($assign->mapel_kelas)) {
                    $mapels = @unserialize($assign->mapel_kelas);
                    if (is_array($mapels)) {
                        foreach ($mapels as $mapel) {
                            $mapel_arr = is_object($mapel) ? (array)$mapel : $mapel;
                            if (isset($mapel_arr['id_mapel'])) {
                                $tutor_mapels[$mapel_arr['id_mapel']] = $mapel_arr['nama_mapel'];
                            }
                        }
                    }
                }
            }

            $data['classes'] = $tutor_classes;
            $data['mapels'] = $tutor_mapels;
            $data['ebooks'] = $this->ebook->get_ebooks_for_tutor(array_keys($tutor_classes), array_keys($tutor_mapels));
            $data['guru'] = $guru;

            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('ebooks/tutor_list', $data);
            $this->load->view('members/guru/templates/footer');

        } elseif ($this->ion_auth->in_group('siswa')) {
            // Student / Siswa View
            $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
            $data['siswa'] = $siswa;
            
            $data['ebooks'] = $this->ebook->get_ebooks_for_student($siswa->id_kelas, $user->id);
            $data['mapels'] = $this->ebook->get_all_mapels(true);
            $data['ekskuls'] = $this->ebook->get_all_ekskuls(true);

            $this->load->view('members/siswa/templates/header', $data);
            $this->load->view('ebooks/siswa_list', $data);
            $this->load->view('members/siswa/templates/footer');
        } else {
            show_error('Akses ditolak. Peran pengguna tidak dikenal.', 403);
        }
    }

    /**
     * Upload an eBook (Admin Only)
     */
    public function upload() {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $this->form_validation->set_rules('title', 'Judul E-Book', 'required|trim');
        $this->form_validation->set_rules('class_id[]', 'Target Kelas', 'required');
        $this->form_validation->set_rules('sub_category_type', 'Sub-Kategori', 'required|trim');

        $sub_category_type = $this->input->post('sub_category_type', true);
        if ($sub_category_type === 'mapel') {
            $this->form_validation->set_rules('mapel_id', 'Mata Pelajaran', 'required|integer');
        } elseif ($sub_category_type === 'ekskul') {
            $this->form_validation->set_rules('ekstra_id', 'Ekstrakurikuler', 'required|integer');
        } elseif ($sub_category_type === 'lainnya') {
            $this->form_validation->set_rules('custom_category', 'Catatan Khusus', 'required|trim');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('ebooks');
        }

        // Ensure upload directory exists
        $upload_path = './uploads/ebooks/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        // Config upload
        $config['upload_path']   = $upload_path;
        $config['allowed_types'] = 'pdf|epub|mobi';
        $config['max_size']      = '51200'; // 50MB max
        $config['encrypt_name']  = TRUE;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('ebook_file')) {
            $this->session->set_flashdata('error', 'Gagal mengunggah file: ' . $this->upload->display_errors('', ''));
            redirect('ebooks');
        }

        $upload_data = $this->upload->data();
        $file_path = 'uploads/ebooks/' . $upload_data['file_name'];

        $class_id_post = $this->input->post('class_id');
        if (is_array($class_id_post)) {
            if (in_array('0', $class_id_post)) {
                $class_id = null;
            } else {
                $class_id = implode(',', $class_id_post);
            }
        } else {
            if ($class_id_post === '0' || empty($class_id_post)) {
                $class_id = null;
            } else {
                $class_id = $class_id_post;
            }
        }

        $mapel_id = null;
        $ekstra_id = null;
        $custom_category = null;

        if ($sub_category_type === 'mapel') {
            $mapel_id = $this->input->post('mapel_id', true);
        } elseif ($sub_category_type === 'ekskul') {
            $ekstra_id = $this->input->post('ekstra_id', true);
        } elseif ($sub_category_type === 'lainnya') {
            $custom_category = $this->input->post('custom_category', true);
        }

        $data = [
            'title' => $this->input->post('title', true),
            'class_id' => $class_id,
            'mapel_id' => $mapel_id,
            'ekstra_id' => $ekstra_id,
            'custom_category' => $custom_category,
            'file_path' => $file_path,
            'created_by' => $this->ion_auth->user()->row()->id
        ];

        if ($this->ebook->insert_ebook($data)) {
            $this->session->set_flashdata('success', 'E-Book berhasil diunggah!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan data e-book ke database.');
        }

        redirect('ebooks');
    }

    /**
     * View PDF eBook online directly
     */
    /**
     * Update an eBook (Admin Only)
     */
    public function update($id_ebook) {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $this->form_validation->set_rules('title', 'Judul E-Book', 'required|trim');
        $this->form_validation->set_rules('sub_category_type', 'Sub-Kategori', 'required|trim');

        $sub_category_type = $this->input->post('sub_category_type', true);
        if ($sub_category_type === 'mapel') {
            $this->form_validation->set_rules('mapel_id', 'Mata Pelajaran', 'required|integer');
        } elseif ($sub_category_type === 'ekskul') {
            $this->form_validation->set_rules('ekstra_id', 'Ekstrakurikuler', 'required|integer');
        } elseif ($sub_category_type === 'lainnya') {
            $this->form_validation->set_rules('custom_category', 'Catatan Khusus', 'required|trim');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('ebooks');
        }

        $ebook = $this->ebook->get_ebook_by_id($id_ebook);
        if (!$ebook) {
            $this->session->set_flashdata('error', 'E-Book tidak ditemukan.');
            redirect('ebooks');
        }

        $file_path = $ebook->file_path;
        if (!empty($_FILES['ebook_file']['name'])) {
            $upload_path = './uploads/ebooks/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path']   = $upload_path;
            $config['allowed_types'] = 'pdf|epub|mobi';
            $config['max_size']      = '51200'; // 50MB max
            $config['encrypt_name']  = TRUE;

            $this->upload->initialize($config);

            if (!$this->upload->do_upload('ebook_file')) {
                $this->session->set_flashdata('error', 'Gagal mengunggah file baru: ' . $this->upload->display_errors('', ''));
                redirect('ebooks');
            } else {
                // Delete old file
                if (file_exists(FCPATH . $file_path)) {
                    unlink(FCPATH . $file_path);
                }
                $upload_data = $this->upload->data();
                $file_path = 'uploads/ebooks/' . $upload_data['file_name'];
            }
        }

        $class_id_post = $this->input->post('class_id');
        if (is_array($class_id_post)) {
            if (in_array('0', $class_id_post)) {
                $class_id = null;
            } else {
                $class_id = implode(',', $class_id_post);
            }
        } else {
            if ($class_id_post === '0' || empty($class_id_post)) {
                $class_id = null;
            } else {
                $class_id = $class_id_post;
            }
        }

        $mapel_id = null;
        $ekstra_id = null;
        $custom_category = null;

        if ($sub_category_type === 'mapel') {
            $mapel_id = $this->input->post('mapel_id', true);
        } elseif ($sub_category_type === 'ekskul') {
            $ekstra_id = $this->input->post('ekstra_id', true);
        } elseif ($sub_category_type === 'lainnya') {
            $custom_category = $this->input->post('custom_category', true);
        }

        $data = [
            'title' => $this->input->post('title', true),
            'class_id' => $class_id,
            'mapel_id' => $mapel_id,
            'ekstra_id' => $ekstra_id,
            'custom_category' => $custom_category,
            'file_path' => $file_path
        ];

        $this->db->where('id_ebook', $id_ebook);
        if ($this->db->update('ebooks', $data)) {
            $this->session->set_flashdata('success', 'E-Book berhasil diperbarui!');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui data e-book ke database.');
        }

        redirect('ebooks');
    }

    public function view($id_ebook) {
        $ebook = $this->ebook->get_ebook_by_id($id_ebook);
        if (!$ebook) {
            show_404();
        }

        $user = $this->ion_auth->user()->row();
        $history = $this->ebook->get_reading_history($user->id, $id_ebook);

        $data = [
            'user' => $user,
            'ebook' => $ebook,
            'last_page' => $history ? $history->last_page : 1,
            'judul' => $ebook->title,
            'subjudul' => 'Membaca E-Book'
        ];

        $this->load->view('ebooks/viewer', $data);
    }

    /**
     * AJAX endpoint to save reading progress
     */
    public function save_progress() {
        $ebook_id = $this->input->post('ebook_id', true);
        $last_page = $this->input->post('last_page', true);
        $total_pages = $this->input->post('total_pages', true);
        $user = $this->ion_auth->user()->row();

        if ($ebook_id && $last_page) {
            $this->ebook->save_reading_history($user->id, $ebook_id, $last_page, $total_pages);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
        }
    }

    /**
     * Delete an eBook (Admin Only)
     */
    public function delete($id_ebook) {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $ebook = $this->ebook->get_ebook_by_id($id_ebook);
        if (!$ebook) {
            $this->session->set_flashdata('error', 'E-Book tidak ditemukan.');
            redirect('ebooks');
        }

        // Remove file from disk
        if (file_exists('./' . $ebook->file_path)) {
            unlink('./' . $ebook->file_path);
        }

        // Remove from database
        if ($this->ebook->delete_ebook($id_ebook)) {
            $this->session->set_flashdata('success', 'E-Book berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data e-book dari database.');
        }

        redirect('ebooks');
    }
}
