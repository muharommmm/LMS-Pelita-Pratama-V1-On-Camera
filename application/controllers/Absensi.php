<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        $this->load->model('Absensi_model', 'absensi');
        $this->load->model('Ebook_model', 'ebook'); // Helper for tutor classes
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->library('form_validation');
    }

    /**
     * Main index router for Admin and Tutor
     */
    public function index() {
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $setting = $this->dashboard->getSetting();

        $data = [
            'user' => $user,
            'profile' => $this->dashboard->getProfileAdmin($user->id),
            'judul' => 'Kehadiran & Absensi',
            'subjudul' => 'Kelola Absensi Siswa',
            'setting' => $setting,
            'tp' => $this->dashboard->getTahun(),
            'tp_active' => $tp,
            'smt' => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'running_text' => $this->dashboard->getRunningText()
        ];

        if ($this->ion_auth->is_admin()) {
            // Admin View: list of classes and barcode generation
            $data['classes'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
            
            // Fetch current barcode settings
            $barcodes = [];
            foreach ($data['classes'] as $id_kelas => $name) {
                $barcodes[$id_kelas] = $this->absensi->get_barcode_by_class($id_kelas);
            }
            $data['barcodes'] = $barcodes;

            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('absensi/admin_dashboard', $data);
            $this->load->view('_templates/dashboard/_footer');

        } elseif ($this->ion_auth->in_group('guru')) {
            // Tutor View: select class/mapel/date to input attendance
            $guru = $this->db->where(['id_user' => $user->id])->get('master_guru')->row();
            
            // Tarik data mapel_kelas dari jabatan_guru milik tutor tersebut
            $jg = $this->db->where([
                'id_guru' => $guru->id_guru,
                'id_tp'   => $tp->id_tp,
                'id_smt'  => $smt->id_smt
            ])->get('jabatan_guru')->row();

            $mapel_ids = [];
            $class_ids = [];
            
            if ($jg && !empty($jg->mapel_kelas)) {
                $mapels_data = @unserialize($jg->mapel_kelas);
                if (is_array($mapels_data)) {
                    foreach ($mapels_data as $mapel) {
                        $mapel_arr = is_object($mapel) ? (array)$mapel : $mapel;
                        if (isset($mapel_arr['id_mapel'])) {
                            $mapel_ids[] = intval($mapel_arr['id_mapel']);
                        }
                        if (isset($mapel_arr['kelas_mapel']) && (is_array($mapel_arr['kelas_mapel']) || is_object($mapel_arr['kelas_mapel']))) {
                            foreach ($mapel_arr['kelas_mapel'] as $km) {
                                $km_arr = is_object($km) ? (array)$km : $km;
                                if (isset($km_arr['kelas']) && $km_arr['kelas'] !== null) {
                                    $class_ids[] = intval($km_arr['kelas']);
                                }
                            }
                        }
                    }
                }
            }

            // Filter Mapel
            $filtered_mapels = [];
            if (!empty($mapel_ids)) {
                $this->db->select('id_mapel, nama_mapel, kode');
                $this->db->from('master_mapel');
                $this->db->where_in('id_mapel', $mapel_ids);
                $mapel_rows = $this->db->get()->result();
                foreach ($mapel_rows as $mr) {
                    $filtered_mapels[$mr->id_mapel] = $mr->nama_mapel . ' (' . $mr->kode . ')';
                }
            }
            $data['mapels'] = $filtered_mapels;

            // Filter Kelas
            $filtered_classes = [];
            if (!empty($class_ids)) {
                $this->db->select('id_kelas, nama_kelas');
                $this->db->from('master_kelas');
                $this->db->where_in('id_kelas', $class_ids);
                $class_rows = $this->db->get()->result();
                foreach ($class_rows as $cr) {
                    $filtered_classes[$cr->id_kelas] = $cr->nama_kelas;
                }
            }
            $data['classes'] = $filtered_classes;
            $data['guru'] = $guru;

            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('absensi/tutor_input', $data);
            $this->load->view('members/guru/templates/footer');
        } else {
            show_error('Akses ditolak.', 403);
        }
    }

    /**
     * Generate or show Barcode for a Class (Admin Only)
     */
    public function generate_barcode($class_id) {
        if (!$this->ion_auth->is_admin()) {
            show_error('Akses ditolak.', 403);
        }

        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $class_row = $this->db->where(['id_kelas' => $class_id])->get('master_kelas')->row();

        if (!$class_row) {
            $this->session->set_flashdata('error', 'Kelas tidak ditemukan.');
            redirect('absensi');
        }

        $existing = $this->absensi->get_barcode_by_class($class_id);
        if (!$existing) {
            // Generate a random unique code
            $barcode_code = md5($class_id . '-' . time() . '-garudacbt-absensi');
            $data = [
                'class_id' => $class_id,
                'barcode_code' => $barcode_code,
                'location_name' => 'Kelas Offline ' . $class_row->nama_kelas
            ];
            $this->absensi->save_barcode($data);
            $existing = (object)$data;
        }

        $data = [
            'user' => $user_row = $this->ion_auth->user()->row(),
            'profile' => $this->dashboard->getProfileAdmin($user_row->id),
            'judul' => 'Barcode Absensi',
            'subjudul' => 'Barcode Lokasi Kelas ' . $class_row->nama_kelas,
            'setting' => $this->dashboard->getSetting(),
            'tp_active' => $tp,
            'smt_active' => $smt,
            'class' => $class_row,
            'barcode' => $existing,
            'running_text' => $this->dashboard->getRunningText()
        ];

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('absensi/barcode_view', $data);
        $this->load->view('_templates/dashboard/_footer');
    }

    /**
     * View Attendance Recap for a Class
     */
    public function recap($class_id) {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $class_row = $this->db->where(['id_kelas' => $class_id])->get('master_kelas')->row();

        if (!$class_row) {
            $this->session->set_flashdata('error', 'Kelas tidak ditemukan.');
            redirect('absensi');
        }

        $data = [
            'user' => $user_row = $this->ion_auth->user()->row(),
            'profile' => $this->dashboard->getProfileAdmin($user_row->id),
            'judul' => 'Rekap Absensi',
            'subjudul' => 'Rekapitulasi Kehadiran Siswa Kelas ' . $class_row->nama_kelas,
            'setting' => $this->dashboard->getSetting(),
            'tp_active' => $tp,
            'smt_active' => $smt,
            'class' => $class_row,
            'recap' => $this->absensi->get_attendance_recap($class_id, $tp->id_tp, $smt->id_smt),
            'running_text' => $this->dashboard->getRunningText()
        ];

        if ($this->ion_auth->is_admin()) {
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('absensi/recap_view', $data);
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('absensi/recap_view', $data);
            $this->load->view('members/guru/templates/footer');
        }
    }

    /**
     * Load Students for Tutor Attendance Form (AJAX)
     */
    public function load_students_tutor() {
        $class_id = $this->input->post('class_id', true);
        $class_id_2 = $this->input->post('class_id_2', true);
        $mapel_id = $this->input->post('mapel_id', true);
        $date = $this->input->post('date', true);
        $jenis_kegiatan = $this->input->post('jenis_kegiatan', true);

        if (!$class_id || !$mapel_id || !$date) {
            echo json_encode(['status' => false, 'message' => 'Parameter tidak lengkap.']);
            return;
        }

        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        // Get class names
        $class_1 = $this->db->where(['id_kelas' => $class_id])->get('master_kelas')->row();
        $class_name_1 = $class_1 ? $class_1->nama_kelas : '';
        
        $class_name_2 = '';
        if ($class_id_2) {
            $class_2 = $this->db->where(['id_kelas' => $class_id_2])->get('master_kelas')->row();
            $class_name_2 = $class_2 ? $class_2->nama_kelas : '';
        }

        // Logika Pengecekan Hari
        $days_indo = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        $day_number_selected = date('N', strtotime($date));
        $hari_dipilih = isset($days_indo[$day_number_selected]) ? $days_indo[$day_number_selected] : 'Senin';

        $schedule = $this->db->where([
            'class_id' => $class_id,
            'mapel_id' => $mapel_id,
            'tp_id' => $tp->id_tp,
            'smt_id' => $smt->id_smt
        ])->get('jadwal_fleksibel')->row();

        $hari_seharusnya = 'Belum Ada';
        if ($schedule) {
            $hari_seharusnya = isset($days_indo[$schedule->day]) ? $days_indo[$schedule->day] : 'Senin';
        }

        $is_hari_berbeda = false;
        if ($schedule && strtolower($hari_dipilih) !== strtolower($hari_seharusnya)) {
            $is_hari_berbeda = true;
        }

        // Logika Pengecekan Metode Mengajar (Jenis Kegiatan)
        $metode_labels = [
            'offline'      => 'Tatap Muka Offline',
            'online'       => 'Tatap Muka Online',
            'check_task'   => 'Tugas',
            'tugas'        => 'Tugas',
            'create_cbt'   => 'Soal Ujian Modul'
        ];
        
        // Normalisasi nilai lama dari jadwal (mis: 'Tatap Muka' -> 'offline')
        $normalize_metode = function($val) {
            $val = strtolower(trim($val));
            $map = [
                'tatap muka'         => 'offline',
                'tatap muka offline' => 'offline',
                'tatap muka online'  => 'online',
                'tugas'              => 'tugas',
                'soal ujian modul'   => 'create_cbt',
            ];
            return isset($map[$val]) ? $map[$val] : $val;
        };
        
        $metode_seharusnya = 'Belum Ada';
        if ($schedule) {
            $raw = !empty($schedule->jenis_kegiatan) ? $schedule->jenis_kegiatan : 'offline';
            $metode_seharusnya = $normalize_metode($raw);
        }
        $metode_dipilih = !empty($jenis_kegiatan) ? $normalize_metode($jenis_kegiatan) : 'offline';

        $is_metode_berbeda = false;
        if ($schedule && strtolower($metode_dipilih) !== strtolower($metode_seharusnya)) {
            $is_metode_berbeda = true;
        }

        $metode_seharusnya_lbl = isset($metode_labels[$metode_seharusnya]) ? $metode_labels[$metode_seharusnya] : $metode_seharusnya;
        $metode_dipilih_lbl = isset($metode_labels[$metode_dipilih]) ? $metode_labels[$metode_dipilih] : $metode_dipilih;

        $session_time = $this->input->post('session_time', true);
        $session_time = !empty($session_time) ? $session_time : null;

        $students = $this->absensi->get_students_by_class($class_id, $tp->id_tp, $smt->id_smt);
        $attendance = $this->absensi->get_attendance($class_id, $mapel_id, $date, $session_time);

        if (!empty($students)) {
            foreach ($students as $student) {
                $student->class_name = $class_name_1;
            }
        }

        if ($class_id_2) {
            $students_2 = $this->absensi->get_students_by_class($class_id_2, $tp->id_tp, $smt->id_smt);
            $attendance_2 = $this->absensi->get_attendance($class_id_2, $mapel_id, $date, $session_time);
            if (!empty($students_2)) {
                foreach ($students_2 as $student) {
                    $student->class_name = $class_name_2;
                }
                $students = array_merge($students, $students_2);
            }
            if (is_array($attendance) && is_array($attendance_2)) {
                $attendance = $attendance + $attendance_2;
            }
        }

        // Sort students by class name and then name
        usort($students, function($a, $b) {
            $class_cmp = strcmp($a->class_name, $b->class_name);
            if ($class_cmp !== 0) return $class_cmp;
            return strcmp($a->nama, $b->nama);
        });

        $html = '';
        if (!empty($students)) {
            $no = 1;
            foreach ($students as $student) {
                // Default status to 'A' (Alpha) instead of 'H' (Hadir)
                $status = isset($attendance[$student->id_siswa]) ? $attendance[$student->id_siswa]->status : 'A';
                $notes = isset($attendance[$student->id_siswa]) ? $attendance[$student->id_siswa]->notes : '';

                $h_checked = ($status == 'H') ? 'checked' : '';
                $s_checked = ($status == 'S') ? 'checked' : '';
                $i_checked = ($status == 'I') ? 'checked' : '';
                $a_checked = ($status == 'A') ? 'checked' : '';

                $html .= "<tr>
                    <td class='text-center'>{$no}</td>
                    <td class='text-center'><span class='badge badge-secondary'>{$student->class_name}</span></td>
                    <td class='text-center'>{$student->nis}</td>
                    <td>" . htmlspecialchars($student->nama) . "</td>
                    <td class='text-center'>
                        <div class='btn-group btn-group-toggle' data-toggle='buttons'>
                            <label class='btn btn-xs btn-outline-success " . ($h_checked ? 'active' : '') . "'>
                                <input type='radio' name='status[{$student->id_siswa}]' value='H' {$h_checked}> Hadir
                            </label>
                            <label class='btn btn-xs btn-outline-warning " . ($s_checked ? 'active' : '') . "'>
                                <input type='radio' name='status[{$student->id_siswa}]' value='S' {$s_checked}> Sakit
                            </label>
                            <label class='btn btn-xs btn-outline-info " . ($i_checked ? 'active' : '') . "'>
                                <input type='radio' name='status[{$student->id_siswa}]' value='I' {$i_checked}> Izin
                            </label>
                            <label class='btn btn-xs btn-outline-danger " . ($a_checked ? 'active' : '') . "'>
                                <input type='radio' name='status[{$student->id_siswa}]' value='A' {$a_checked}> Alpha
                            </label>
                        </div>
                    </td>
                    <td>
                        <input type='text' name='notes[{$student->id_siswa}]' class='form-control form-control-sm' value='" . htmlspecialchars($notes) . "' placeholder='Catatan opsional'>
                    </td>
                </tr>";
                $no++;
            }
        } else {
            $html = "<tr><td colspan='6' class='text-center text-muted'>Tidak ada siswa di kelas ini.</td></tr>";
        }

        echo json_encode([
            'status' => true,
            'html' => $html,
            'hari_dipilih' => $hari_dipilih,
            'hari_seharusnya' => $hari_seharusnya,
            'is_hari_berbeda' => $is_hari_berbeda,
            'metode_dipilih' => $metode_dipilih_lbl,
            'metode_seharusnya' => $metode_seharusnya_lbl,
            'metode_seharusnya_raw' => $metode_seharusnya,
            'is_metode_berbeda' => $is_metode_berbeda
        ]);
    }

    /**
     * Save Tutor Attendance Input
     */
    public function save_tutor_attendance() {
        if (!$this->ion_auth->in_group('guru')) {
            echo json_encode(['status' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        $class_id = $this->input->post('class_id', true);
        $class_id_2 = $this->input->post('class_id_2', true);
        $mapel_id = $this->input->post('mapel_id', true);
        $date = $this->input->post('date', true);
        $status_array = $this->input->post('status', true);
        $notes_array = $this->input->post('notes', true);
        $jenis_kegiatan = $this->input->post('jenis_kegiatan', true) ? $this->input->post('jenis_kegiatan', true) : 'offline';


        if (!$class_id || !$mapel_id || !$date || empty($status_array)) {
            echo json_encode(['status' => false, 'message' => 'Gagal menyimpan absensi: Parameter tidak lengkap. class_id=' . $class_id . ', mapel_id=' . $mapel_id . ', date=' . $date . ', status_count=' . count((array)$status_array)]);
            return;
        }

        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $tutor = $this->db->where(['id_user' => $this->ion_auth->user()->row()->id])->get('master_guru')->row();

        // Use identical time for all students in the loop to group them under 1 session.
        // If an attendance session already exists for this tutor, mapel, date, and jenis_kegiatan,
        // reuse its 'time' value so the session remains grouped under the same timestamp (prevents honor duplication).
        $session_time = $this->input->post('session_time', true);

        if (!empty($session_time)) {
            $now_time = $session_time;
        } else {
            $existing_session = $this->db->select('time')
                                         ->from('absensi_siswa')
                                         ->where([
                                             'mapel_id' => $mapel_id,
                                             'date' => $date,
                                             'jenis_kegiatan' => $jenis_kegiatan,
                                             'tutor_id_input' => $tutor->id_guru
                                         ])
                                         ->limit(1)
                                         ->get()->row();

            $now_time = $existing_session ? $existing_session->time : date('H:i:s');
        }

        foreach ($status_array as $student_id => $status) {
            $notes = isset($notes_array[$student_id]) ? $notes_array[$student_id] : '';
            
            // Find student's actual class_id
            $std = $this->db->select('id_kelas')->where([
                'id_siswa' => $student_id,
                'id_tp' => $tp->id_tp,
                'id_smt' => $smt->id_smt
            ])->get('kelas_siswa')->row();
            $actual_class_id = $std ? $std->id_kelas : $class_id;

            // Check if this student already has an attendance record for this session (matched by time)
            $existing_record = $this->db->select('id_absensi')
                                        ->from('absensi_siswa')
                                        ->where([
                                            'student_id' => $student_id,
                                            'class_id' => $actual_class_id,
                                            'mapel_id' => $mapel_id,
                                            'date' => $date,
                                            'time' => $now_time,
                                            'jenis_kegiatan' => $jenis_kegiatan,
                                            'tutor_id_input' => $tutor->id_guru
                                        ])
                                        ->get()->row();

            $data = [
                'student_id' => $student_id,
                'class_id' => $actual_class_id,
                'tp_id' => $tp->id_tp,
                'smt_id' => $smt->id_smt,
                'mapel_id' => $mapel_id,
                'date' => $date,
                'time' => $now_time,
                'status' => $status,
                'method' => 'manual_tutor',
                'tutor_id_input' => $tutor->id_guru,
                'notes' => $notes,
                'jenis_kegiatan' => $jenis_kegiatan
            ];

            // If record already exists, include id_absensi (primary key) so REPLACE behaves as UPDATE
            if ($existing_record) {
                $data['id_absensi'] = $existing_record->id_absensi;
            }

            $this->absensi->save_attendance($data);
        }

        // Jalankan sinkronisasi otomatis agar honor record langsung ter-update dengan Smart Validation status
        $this->load->model('Honor_model', 'honor');
        $this->honor->sync_honor($tutor->id_guru, $tp->id_tp, $smt->id_smt);

        echo json_encode(['status' => true, 'message' => 'Absensi siswa berhasil disimpan!']);
        return;
    }



    /**
     * Scan Barcode Endpoint (For Student App / Mobile Scan)
     */
    public function scan($barcode_code = null) {
        if (!$this->ion_auth->in_group('siswa')) {
            show_error('Hanya siswa yang dapat melakukan absensi QR Code/Barcode.', 403);
        }

        if (!$barcode_code) {
            $barcode_code = $this->input->get('code', true);
        }

        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);

        // Find barcode class
        $barcode_setting = $this->db->where(['barcode_code' => $barcode_code])->get('absensi_setting_barcode')->row();

        if (!$barcode_setting) {
            $this->session->set_flashdata('error', 'Barcode lokasi tidak valid atau sudah kadaluarsa.');
            redirect('dashboard');
        }

        // Validate if student belongs to this class
        if ($siswa->id_kelas != $barcode_setting->class_id) {
            $this->session->set_flashdata('error', 'Akses ditolak: Anda tidak terdaftar di kelas lokasi absensi ini.');
            redirect('dashboard');
        }

        // Insert attendance record as present (H)
        $data = [
            'student_id' => $siswa->id_siswa,
            'class_id' => $siswa->id_kelas,
            'tp_id' => $tp->id_tp,
            'smt_id' => $smt->id_smt,
            'mapel_id' => NULL, // Mapel can be NULL for general class offline presence
            'date' => date('Y-m-d'),
            'time' => date('H:i:s'),
            'status' => 'H',
            'method' => 'barcode',
            'tutor_id_input' => NULL,
            'notes' => 'Presensi Mandiri Barcode: ' . $barcode_setting->location_name
        ];

        if ($this->absensi->save_attendance($data)) {
            $this->session->set_flashdata('success', 'Absensi Lunas! Anda berhasil absen di lokasi: ' . $barcode_setting->location_name);
        } else {
            $this->session->set_flashdata('error', 'Gagal memproses absensi.');
        }

        redirect('dashboard');
    }

    public function log_payload_test() {
        $payload = $this->input->post('payload');
        file_put_contents(FCPATH . 'scratch/payload.log', $payload);
    }

    /**
     * AJAX Endpoint to load attendance history with class/month filtering
     */
    public function load_history_tutor() {
        if (!$this->ion_auth->in_group('guru')) {
            echo json_encode(['status' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        $month = $this->input->post('month', true);
        $class_id = $this->input->post('class_id', true);

        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $tutor = $this->db->where(['id_user' => $this->ion_auth->user()->row()->id])->get('master_guru')->row();

        // Build query
        $this->db->select('a.mapel_id, a.date, a.time, a.jenis_kegiatan, COUNT(a.id_absensi) as student_count, GROUP_CONCAT(DISTINCT a.class_id) as class_ids, m.nama_mapel')
                 ->from('absensi_siswa a')
                 ->join('master_mapel m', 'a.mapel_id = m.id_mapel', 'left')
                 ->where('a.tutor_id_input', $tutor->id_guru)
                 ->where('a.tp_id', $tp->id_tp)
                 ->where('a.smt_id', $smt->id_smt)
                 ->where('a.method', 'manual_tutor');

        // Apply filters
        if (!empty($class_id)) {
            // Find session records that contain the selected class_id
            $this->db->where('a.class_id', $class_id);
        }

        if (!empty($month)) {
            $this->db->where('MONTH(a.date)', $month);
        } else {
            // Default: if no class filter is selected, default history display is 7 days
            if (empty($class_id)) {
                $one_week_ago = date('Y-m-d', strtotime('-7 days'));
                $this->db->where('a.date >=', $one_week_ago);
            }
        }

        $riwayat_sessions = $this->db->group_by(['a.mapel_id', 'a.date', 'a.time', 'a.jenis_kegiatan'])
                                     ->order_by('a.date', 'DESC')
                                     ->order_by('a.time', 'DESC')
                                     ->get()->result();

        // Get class names map for mapping class_ids
        $filtered_classes = [];
        $jg = $this->db->where([
            'id_guru' => $tutor->id_guru,
            'id_tp'   => $tp->id_tp,
            'id_smt'  => $smt->id_smt
        ])->get('jabatan_guru')->row();

        $class_ids_list = [];
        if ($jg && !empty($jg->mapel_kelas)) {
            $mapels_data = @unserialize($jg->mapel_kelas);
            if (is_array($mapels_data)) {
                foreach ($mapels_data as $mapel) {
                    $mapel_arr = is_object($mapel) ? (array)$mapel : $mapel;
                    if (isset($mapel_arr['kelas_mapel']) && (is_array($mapel_arr['kelas_mapel']) || is_object($mapel_arr['kelas_mapel']))) {
                        foreach ($mapel_arr['kelas_mapel'] as $km) {
                            $km_arr = is_object($km) ? (array)$km : $km;
                            if (isset($km_arr['kelas']) && $km_arr['kelas'] !== null) {
                                $class_ids_list[] = intval($km_arr['kelas']);
                            }
                        }
                    }
                }
            }
        }
        if (!empty($class_ids_list)) {
            $class_rows = $this->db->select('id_kelas, nama_kelas')
                                   ->from('master_kelas')
                                   ->where_in('id_kelas', array_unique($class_ids_list))
                                   ->get()->result();
            foreach ($class_rows as $cr) {
                $filtered_classes[$cr->id_kelas] = $cr->nama_kelas;
            }
        }

        $html = '';
        if (!empty($riwayat_sessions)) {
            $no = 1;
            foreach ($riwayat_sessions as $row) {
                $hari_indo = [
                    'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
                ];
                $day_name = date('l', strtotime($row->date));
                $hari_tampil = isset($hari_indo[$day_name]) ? $hari_indo[$day_name] : $day_name;
                
                $metode_labels = [
                    'offline' => 'Offline', 'online' => 'Online',
                    'check_task' => 'Tugas', 'tugas' => 'Tugas',
                    'create_cbt' => 'Soal Ujian'
                ];
                $metode_tampil = isset($metode_labels[$row->jenis_kegiatan]) ? $metode_labels[$row->jenis_kegiatan] : $row->jenis_kegiatan;

                $cids = explode(',', $row->class_ids);
                $names = [];
                foreach ($cids as $cid) {
                    if (isset($filtered_classes[$cid])) {
                        $names[] = $filtered_classes[$cid];
                    }
                }
                $nama_kelas_string = !empty($names) ? implode(', ', $names) : '-';

                $html .= '<tr>
                    <td class="text-center">' . $no . '</td>
                    <td>
                        <strong>' . $hari_tampil . ', ' . date('d M Y', strtotime($row->date)) . '</strong>
                        <br><small class="text-muted"><i class="far fa-clock mr-1"></i> ' . date('H:i', strtotime($row->time)) . '</small>
                    </td>
                    <td>' . htmlspecialchars($row->nama_mapel) . '</td>
                    <td><span class="badge badge-info px-2 py-1">' . htmlspecialchars($nama_kelas_string) . '</span></td>
                    <td><span class="badge badge-secondary px-2 py-1">' . htmlspecialchars($metode_tampil) . '</span></td>
                    <td class="text-center font-weight-bold">' . $row->student_count . ' Siswa</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-xs btn-primary btn-edit-absensi text-white"
                                data-classids="' . $row->class_ids . '"
                                data-mapel="' . $row->mapel_id . '"
                                data-date="' . $row->date . '"
                                data-jenis="' . $row->jenis_kegiatan . '"
                                data-time="' . $row->time . '">
                            <i class="fas fa-edit mr-1"></i> Edit Kehadiran
                        </button>
                    </td>
                </tr>';
                $no++;
            }
        } else {
            $html = '<tr>
                <td colspan="7" class="text-center text-muted py-4">Tidak ada riwayat pengisian absensi dengan filter terpilih.</td>
            </tr>';
        }

        echo json_encode(['status' => true, 'html' => $html]);
    }
}
