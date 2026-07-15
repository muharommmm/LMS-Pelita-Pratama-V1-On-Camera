<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Spp extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        $this->load->model('Spp_model', 'spp');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->library('upload');
    }

    /**
     * Main index view
     */
    public function index() {
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $setting = $this->dashboard->getSetting();

        $data = [
            'user' => $user,
            'profile' => $this->dashboard->getProfileAdmin($user->id),
            'judul' => 'Keuangan SPP',
            'subjudul' => 'Monitoring Pembayaran SPP',
            'setting' => $setting,
            'tp' => $this->dashboard->getTahun(),
            'tp_active' => $tp,
            'smt' => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'running_text' => $this->dashboard->getRunningText()
        ];

        // Month Names helper list
        $data['month_names'] = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        if ($this->ion_auth->is_admin()) {
            // Admin View - All billings
            $data['billings'] = $this->spp->get_all_billing($tp->id_tp, $smt->id_smt);
            
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('spp/admin_list', $data);
            $this->load->view('_templates/dashboard/_footer');

        } elseif ($this->ion_auth->in_group('siswa')) {
            // Siswa View - Read-only single student billings
            $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
            $data['siswa'] = $siswa;
            $data['billings'] = $this->spp->get_billing_by_student($siswa->id_siswa, $tp->id_tp, $smt->id_smt);

            $this->load->view('members/siswa/templates/header', $data);
            $this->load->view('spp/siswa_view', $data);
            $this->load->view('members/siswa/templates/footer');
        } else {
            show_error('Akses ditolak.', 403);
        }
    }

    /**
     * Import SPP via CSV (Admin Only)
     */
    public function import_csv() {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        // Upload setup
        $upload_path = './uploads/csv/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $config['upload_path']   = $upload_path;
        $config['allowed_types'] = 'csv|txt';
        $config['max_size']      = '2048';
        $config['encrypt_name']  = TRUE;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('csv_file')) {
            $this->session->set_flashdata('error', 'Gagal upload CSV: ' . $this->upload->display_errors('', ''));
            redirect('spp');
        }

        $file_data = $this->upload->data();
        $file_path = $upload_path . $file_data['file_name'];

        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        $success_count = 0;
        $fail_count = 0;
        $errors = [];

        if (($handle = fopen($file_path, "r")) !== FALSE) {
            // Skip header if exists (detect if first row has non-numeric NIS)
            $row_idx = 0;
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row_idx++;
                
                // Let's assume columns: nis, month (1-12), amount, status (paid/unpaid), payment_date (YYYY-MM-DD), notes
                if (count($row) < 4) {
                    if ($row_idx == 1) continue; // Skip if invalid header row
                    $fail_count++;
                    $errors[] = "Baris {$row_idx}: Kolom tidak lengkap (minimal 4 kolom).";
                    continue;
                }

                $nis = trim($row[0]);
                $month = intval(trim($row[1]));
                $amount = floatval(trim($row[2]));
                $status = strtolower(trim($row[3]));
                $pay_date = isset($row[4]) && !empty(trim($row[4])) ? trim($row[4]) : NULL;
                $notes = isset($row[5]) ? trim($row[5]) : '';

                // Validate headers
                if ($row_idx == 1 && (!is_numeric($nis) && (strtolower($nis) == 'nis' || strtolower($nis) == 'sudent_id'))) {
                    continue; // Skip header row
                }

                // Find student
                $student = $this->spp->get_student_by_nis($nis);
                if (!$student) {
                    $student = $this->spp->get_student_by_username($nis);
                }
                if (!$student) {
                    $student = $this->spp->get_student_by_nisn($nis);
                }

                if (!$student) {
                    $fail_count++;
                    $errors[] = "Baris {$row_idx}: Siswa dengan NIS/Username '{$nis}' tidak ditemukan.";
                    continue;
                }

                if ($month < 1 || $month > 12) {
                    $fail_count++;
                    $errors[] = "Baris {$row_idx}: Bulan '{$month}' tidak valid (harus 1 s.d. 12).";
                    continue;
                }

                if ($status !== 'paid' && $status !== 'unpaid') {
                    $status = 'unpaid';
                }

                $invoice_number = ($status === 'paid') ? 'INV-' . $student->id_siswa . '-' . $tp->id_tp . '-' . $month . '-' . time() : NULL;

                $data = [
                    'student_id' => $student->id_siswa,
                    'tp_id' => $tp->id_tp,
                    'smt_id' => $smt->id_smt,
                    'month' => $month,
                    'amount' => $amount,
                    'status' => $status,
                    'payment_date' => ($status === 'paid') ? ($pay_date ? $pay_date : date('Y-m-d H:i:s')) : NULL,
                    'invoice_number' => $invoice_number,
                    'notes' => $notes
                ];

                if ($this->spp->save_billing($data)) {
                    $success_count++;
                } else {
                    $fail_count++;
                    $errors[] = "Baris {$row_idx}: Gagal menyimpan tagihan ke database.";
                }
            }
            fclose($handle);
        }

        // Clean file
        unlink($file_path);

        $msg = "Upload selesai. Berhasil: {$success_count} baris, Gagal: {$fail_count} baris.";
        if (!empty($errors)) {
            $msg .= "<br>Detail Error:<br>" . implode("<br>", array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $msg .= "<br>...dan " . (count($errors) - 5) . " error lainnya.";
            }
            $this->session->set_flashdata('error', $msg);
        } else {
            $this->session->set_flashdata('success', $msg);
        }

        redirect('spp');
    }
}
