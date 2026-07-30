<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Monitoring extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            redirect('auth');
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Master_model', 'master');
        $this->load->model('Honor_model', 'honor');
    }

    public function output_json($data, $encode = true) {
        if ($encode) {
            $data = json_encode($data);
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }

    public function index() {
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $setting = $this->dashboard->getSetting();

        $siswa_list = $this->db->select('id_siswa, nama, nis')
                               ->from('master_siswa')
                               ->order_by('nama', 'ASC')
                               ->get()->result();

        $tutor_list = $this->db->select('id_guru, nama_guru, id_guru as id')
                               ->from('master_guru')
                               ->order_by('nama_guru', 'ASC')
                               ->get()->result();

        $data = [
            'user' => $user,
            'profile' => $this->dashboard->getProfileAdmin($user->id),
            'judul' => 'Dashboard Pemantau',
            'subjudul' => 'Pemantau Satu Pintu Siswa & Tutor',
            'setting' => $setting,
            'tp_active' => $tp,
            'smt_active' => $smt,
            'running_text' => $this->dashboard->getRunningText(),
            'siswa_list' => $siswa_list,
            'tutor_list' => $tutor_list
        ];

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('monitoring/data', $data);
        $this->load->view('_templates/dashboard/_footer');
    }

    /**
     * AJAX Endpoint to fetch student statistics and logs
     */
    public function get_siswa_data() {
        $id_siswa = $this->input->post('id_siswa', true);
        if (!$id_siswa) {
            $this->output_json(['status' => false, 'message' => 'Siswa tidak ditemukan']);
            return;
        }

        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        // 1. Profil & Kelas Siswa
        $siswa = $this->db->select('a.*, b.nama_kelas, b.id_kelas')
                         ->from('master_siswa a')
                         ->join('kelas_siswa c', 'a.id_siswa = c.id_siswa', 'left')
                         ->join('master_kelas b', 'c.id_kelas = b.id_kelas', 'left')
                         ->where('a.id_siswa', $id_siswa)
                         ->where('c.id_tp', $tp->id_tp)
                         ->where('c.id_smt', $smt->id_smt)
                         ->get()->row();

        if (!$siswa) {
            $this->output_json(['status' => false, 'message' => 'Data kelas siswa tidak aktif pada TP/Semester ini']);
            return;
        }

        // 2. Kehadiran Siswa
        $absensi = $this->db->select('date, status, jenis_kegiatan')
                            ->from('absensi_siswa')
                            ->where('student_id', $id_siswa)
                            ->where('tp_id', $tp->id_tp)
                            ->where('smt_id', $smt->id_smt)
                            ->get()->result();

        $rekap_absen = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
        foreach ($absensi as $ab) {
            $status = strtoupper($ab->status ?? '');
            if (isset($rekap_absen[$status])) {
                $rekap_absen[$status]++;
            }
        }
        $total_absen = array_sum($rekap_absen);

        // 3. Tugas & Materi KBM (kelas_materi)
        $all_materi = $this->db->select('a.id_materi, a.judul_materi, a.jenis, a.created_on, a.materi_kelas, m.nama_mapel, g.nama_guru')
                               ->from('kelas_materi a')
                               ->join('master_mapel m', 'a.id_mapel = m.id_mapel', 'left')
                               ->join('master_guru g', 'a.id_guru = g.id_guru', 'left')
                               ->where('a.id_tp', $tp->id_tp)
                               ->where('a.id_smt', $smt->id_smt)
                               ->where('a.status', 1)
                               ->get()->result();

        $materi_list = [];
        $tugas_list = [];
        $target_materi_ids = [];
        foreach ($all_materi as $row) {
            $classes = @unserialize($row->materi_kelas);
            if (is_array($classes) && in_array($siswa->id_kelas, $classes)) {
                $target_materi_ids[] = $row->id_materi;
                if ($row->jenis == 1) {
                    $materi_list[] = $row;
                } else {
                    $tugas_list[] = $row;
                }
            }
        }

        // Fetch logs
        $logs = [];
        if (!empty($target_materi_ids)) {
            $logs_db = $this->db->select('*')
                                ->from('log_materi')
                                ->where('id_siswa', $id_siswa)
                                ->where_in('id_materi', $target_materi_ids)
                                ->get()->result();
            foreach ($logs_db as $l) {
                $logs[$l->id_materi] = $l;
            }
        }

        // Process KBM status lists
        $materi_data = [];
        foreach ($materi_list as $m) {
            $has_read = isset($logs[$m->id_materi]);
            $materi_data[] = [
                'judul' => $m->judul_materi,
                'mapel' => $m->nama_mapel,
                'guru' => $m->nama_guru,
                'status' => $has_read ? 'Sudah Dibaca' : 'Belum Dibaca',
                'waktu' => $has_read ? date('d M Y H:i', strtotime($logs[$m->id_materi]->log_time)) : '-'
            ];
        }

        $tugas_data = [];
        foreach ($tugas_list as $t) {
            $has_submitted = isset($logs[$t->id_materi]) && !empty($logs[$t->id_materi]->finish_time);
            $nilai = $has_submitted ? $logs[$t->id_materi]->nilai : null;
            $tugas_data[] = [
                'judul' => $t->judul_materi,
                'mapel' => $t->nama_mapel,
                'guru' => $t->nama_guru,
                'status' => $has_submitted ? 'Sudah Dikerjakan' : 'Belum Dikerjakan',
                'waktu' => $has_submitted ? date('d M Y H:i', strtotime($logs[$t->id_materi]->finish_time)) : '-',
                'nilai' => ($nilai !== null && $nilai !== '') ? $nilai : '-'
            ];
        }

        // 4. Jadwal Pelajaran
        $jadwal_raw = $this->db->select('a.*, b.nama_mapel')
                               ->from('jadwal_fleksibel a')
                               ->join('master_mapel b', 'a.mapel_id = b.id_mapel', 'left')
                               ->where('a.class_id', $siswa->id_kelas)
                               ->where('a.tp_id', $tp->id_tp)
                               ->where('a.smt_id', $smt->id_smt)
                               ->order_by('a.day', 'ASC')
                               ->order_by('a.start_time', 'ASC')
                               ->get()->result();

        $hari_nama = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $jadwal_data = [];
        foreach ($jadwal_raw as $j) {
            $jadwal_data[] = [
                'hari' => $hari_nama[$j->day] ?? '-',
                'waktu' => date('H:i', strtotime($j->start_time)) . ' - ' . date('H:i', strtotime($j->end_time)),
                'mapel' => $j->nama_mapel,
                'pola' => $j->pola_mingguan,
                'kegiatan' => $j->jenis_kegiatan
            ];
        }

        // 5. Ujian CBT & Nilai
        $cbt_scores = $this->db->select('a.id_nilai, a.id_siswa, a.id_jadwal, a.pg_nilai, a.essai_nilai, a.kompleks_nilai, a.jodohkan_nilai, a.isian_nilai, a.dikoreksi, a.time_create, c.bank_nama as nama_ujian, b.durasi_ujian as durasi')
                               ->from('cbt_nilai a')
                               ->join('cbt_jadwal b', 'a.id_jadwal = b.id_jadwal')
                               ->join('cbt_bank_soal c', 'b.id_bank = c.id_bank')
                               ->where('a.id_siswa', $id_siswa)
                               ->get()->result();

        $cbt_scores_map = [];
        foreach ($cbt_scores as $cs) {
            $cbt_scores_map[$cs->id_jadwal] = $cs;
        }

        $all_exams = $this->db->select('a.id_jadwal, c.bank_nama as nama_ujian, a.durasi_ujian as durasi, a.tgl_mulai, a.tgl_selesai, c.bank_kelas')
                               ->from('cbt_jadwal a')
                               ->join('cbt_bank_soal c', 'a.id_bank = c.id_bank')
                               ->where('a.id_tp', $tp->id_tp)
                               ->where('a.id_smt', $smt->id_smt)
                               ->get()->result();

        $cbt_completed = [];
        $cbt_pending = [];

        foreach ($all_exams as $row) {
            $classes = @unserialize($row->bank_kelas);
            $is_target = false;
            if (is_array($classes)) {
                foreach ($classes as $c) {
                    if (isset($c['kelas_id']) && $c['kelas_id'] == $siswa->id_kelas) {
                        $is_target = true;
                        break;
                    }
                }
            }

            if ($is_target) {
                if (isset($cbt_scores_map[$row->id_jadwal])) {
                    $cs = $cbt_scores_map[$row->id_jadwal];
                    // Calculate total score from all components
                    $total_nilai = (float)($cs->pg_nilai ?? 0) + (float)($cs->essai_nilai ?? 0) + (float)($cs->kompleks_nilai ?? 0) + (float)($cs->jodohkan_nilai ?? 0) + (float)($cs->isian_nilai ?? 0);
                    $cbt_completed[] = [
                        'nama' => $row->nama_ujian,
                        'waktu' => date('d M Y H:i', strtotime($cs->time_create)),
                        'nilai' => round($total_nilai, 2),
                        'dikoreksi' => $cs->dikoreksi ? 'Sudah' : 'Belum'
                    ];
                } else {
                    $cbt_pending[] = [
                        'nama' => $row->nama_ujian,
                        'durasi' => $row->durasi . ' Menit',
                        'tgl_mulai' => date('d M Y H:i', strtotime($row->tgl_mulai)),
                        'tgl_selesai' => date('d M Y H:i', strtotime($row->tgl_selesai))
                    ];
                }
            }
        }

        $this->output_json([
            'status' => true,
            'siswa' => [
                'nama' => $siswa->nama,
                'nis' => $siswa->nis,
                'kelas' => $siswa->nama_kelas
            ],
            'kehadiran' => [
                'total' => $total_absen,
                'detail' => $rekap_absen
            ],
            'materi' => $materi_data,
            'tugas' => $tugas_data,
            'jadwal' => $jadwal_data,
            'cbt' => [
                'completed' => $cbt_completed,
                'pending' => $cbt_pending
            ]
        ]);
    }

    /**
     * AJAX Endpoint to fetch tutor tracking statistics
     */
    public function get_tutor_data() {
        $id_guru = $this->input->post('id_guru', true);
        $start_date = $this->input->post('start_date', true);
        $end_date = $this->input->post('end_date', true);

        if (!$id_guru) {
            $this->output_json(['status' => false, 'message' => 'Tutor tidak ditemukan']);
            return;
        }

        if (!$start_date) {
            $start_date = date('Y-m-01');
        }
        if (!$end_date) {
            $end_date = date('Y-m-t');
        }

        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        // 1. Profil Guru
        $guru = $this->db->select('*')
                         ->from('master_guru')
                         ->where('id_guru', $id_guru)
                         ->get()->row();

        if (!$guru) {
            $this->output_json(['status' => false, 'message' => 'Data guru tidak ditemukan']);
            return;
        }

        // 2. Aktivitas Tugas & Materi yang Dibuat
        $tutor_kbm = $this->db->select('a.*, m.nama_mapel')
                              ->from('kelas_materi a')
                              ->join('master_mapel m', 'a.id_mapel = m.id_mapel', 'left')
                              ->where('a.id_guru', $id_guru)
                              ->where('a.id_tp', $tp->id_tp)
                              ->where('a.id_smt', $smt->id_smt)
                              ->where('a.created_on >=', $start_date . ' 00:00:00')
                              ->where('a.created_on <=', $end_date . ' 23:59:59')
                              ->get()->result();

        // Fetch all classes to map class IDs to names
        $kelas_list = $this->db->select('id_kelas, nama_kelas')
                               ->from('master_kelas')
                               ->get()->result();
        $kelas_map = [];
        foreach ($kelas_list as $kls) {
            $kelas_map[$kls->id_kelas] = $kls->nama_kelas;
        }

        $tugas_stats = [];
        $materi_count = 0;
        $tugas_count = 0;

        foreach ($tutor_kbm as $kbm) {
            if ($kbm->jenis == 1) {
                $materi_count++;
            } else {
                $tugas_count++;
                
                // Get class names for this task/materi
                $classes_unserialized = @unserialize($kbm->materi_kelas);
                $kelas_names = [];
                if (is_array($classes_unserialized)) {
                    foreach ($classes_unserialized as $cid) {
                        if (isset($kelas_map[$cid])) {
                            $kelas_names[] = $kelas_map[$cid];
                        }
                    }
                }
                $kelas_str = !empty($kelas_names) ? implode(', ', $kelas_names) : '-';

                // Get submission count and grade counts
                $submits = $this->db->select('COUNT(id_log) as total_submits, SUM(CASE WHEN nilai IS NOT NULL AND nilai != "" THEN 1 ELSE 0 END) as graded_submits')
                                    ->from('log_materi')
                                    ->where('id_materi', $kbm->id_materi)
                                    ->get()->row();
                $tugas_stats[] = [
                    'judul' => $kbm->judul_materi,
                    'mapel' => $kbm->nama_mapel,
                    'kelas' => $kelas_str,
                    'created_on' => date('d M Y H:i', strtotime($kbm->created_on)),
                    'total' => $submits->total_submits,
                    'graded' => $submits->graded_submits,
                    'pending' => $submits->total_submits - $submits->graded_submits
                ];
            }
        }

        // 3. Honorarium Rekap
        $honor_records = $this->db->select('type, status, amount, adjusted_amount')
                                  ->from('honor_records')
                                  ->where('tutor_id', $id_guru)
                                  ->where_in('status', ['paid', 'approved'])
                                  ->where('created_at >=', $start_date . ' 00:00:00')
                                  ->where('created_at <=', $end_date . ' 23:59:59')
                                  ->get()->result();

        $honor_rekap = [
            'paid' => ['offline' => 0, 'online' => 0, 'check_task' => 0, 'create_cbt' => 0, 'total' => 0],
            'pending' => ['offline' => 0, 'online' => 0, 'check_task' => 0, 'create_cbt' => 0, 'total' => 0]
        ];

        foreach ($honor_records as $hr) {
            // Hanya approved (belum cair) dan paid (sudah cair) yang dihitung
            $status = ($hr->status == 'paid') ? 'paid' : 'pending'; // 'approved' akan masuk kategori 'pending'
            $type = $hr->type;
            
            // Tentukan nominal akhir (gunakan adjusted_amount jika diset dan > 0, jika tidak gunakan amount)
            $nominal = ($hr->adjusted_amount !== null && floatval($hr->adjusted_amount) > 0) ? floatval($hr->adjusted_amount) : floatval($hr->amount);

            if (isset($honor_rekap[$status][$type])) {
                $honor_rekap[$status][$type] += $nominal;
                $honor_rekap[$status]['total'] += $nominal;
            }
        }

        // 4. Riwayat Chat & Diskusi Kelas Terakhir
        $comments_data = [];

        // A. Dari Post Comments (Diskusi Kelas / Materi KBM)
        $chat_logs = $this->db->select('a.*, p.text as post_text')
                               ->from('post_comments a')
                               ->join('post p', 'a.id_post = p.id_post', 'left')
                               ->where('a.dari', $id_guru)
                               ->order_by('a.tanggal', 'DESC')
                               ->limit(5)
                               ->get()->result();

        foreach ($chat_logs as $c) {
            $comments_data[] = [
                'tanggal' => date('d M Y H:i', strtotime($c->tanggal)),
                'timestamp' => strtotime($c->tanggal),
                'komentar' => $c->text,
                'topik' => 'Diskusi KBM: ' . (substr(strip_tags($c->post_text ?? ''), 0, 30) . '...')
            ];
        }

        // B. Dari Chat Messages (Pesan Obrolan Langsung / Komunitas)
        if ($guru->id_user) {
            $direct_chats = $this->db->select('c.pesan, c.created_at, c.id_kelas_komunitas, c.penerima_id, c.penerima_role,
                                             k.nama_kelas,
                                             CASE 
                                                 WHEN c.penerima_role = "admin" THEN COALESCE(NULLIF(TRIM(CONCAT(u.first_name, " ", u.last_name)), ""), u.username)
                                                 WHEN c.penerima_role = "guru" THEN g.nama_guru 
                                                 ELSE s.nama 
                                             END as nama_penerima')
                                     ->from('chat_messages c')
                                     ->join('master_kelas k', 'c.id_kelas_komunitas = k.id_kelas', 'left')
                                     ->join('users u', 'c.penerima_id = u.id', 'left')
                                     ->join('master_guru g', 'u.id = g.id_user', 'left')
                                     ->join('master_siswa s', 'u.username = s.username', 'left')
                                     ->where('c.pengirim_id', $guru->id_user)
                                     ->order_by('c.created_at', 'DESC')
                                     ->limit(5)
                                     ->get()->result();

            foreach ($direct_chats as $dc) {
                if ($dc->id_kelas_komunitas) {
                    $topik = 'Komunitas: ' . $dc->nama_kelas;
                } else {
                    $topik = 'Chat ke: ' . ($dc->nama_penerima ?? 'User ' . $dc->penerima_id);
                }
                $comments_data[] = [
                    'tanggal' => date('d M Y H:i', strtotime($dc->created_at)),
                    'timestamp' => strtotime($dc->created_at),
                    'komentar' => $dc->pesan,
                    'topik' => $topik
                ];
            }
        }

        // Urutkan gabungan chat berdasarkan waktu terbaru
        usort($comments_data, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        $comments_data = array_slice($comments_data, 0, 5);

        // 5. CBT Ujian yang esainya belum dikoreksi
        $exams_created = $this->db->select('a.id_jadwal')
                                   ->from('cbt_jadwal a')
                                   ->join('cbt_bank_soal b', 'a.id_bank = b.id_bank')
                                   ->where('b.bank_guru_id', $id_guru)
                                   ->get()->result();

        $pending_cbt_grading = [];
        if (!empty($exams_created)) {
            $exam_ids = array_column($exams_created, 'id_jadwal');
            $pending_submits = $this->db->select('a.id_nilai, s.nama as nama_siswa, b.bank_nama as nama_ujian, a.time_create as waktu')
                                        ->from('cbt_nilai a')
                                        ->join('master_siswa s', 'a.id_siswa = s.id_siswa')
                                        ->join('cbt_jadwal j', 'a.id_jadwal = j.id_jadwal')
                                        ->join('cbt_bank_soal b', 'j.id_bank = b.id_bank')
                                        ->where_in('a.id_jadwal', $exam_ids)
                                        ->where('b.jml_esai >', 0)
                                        ->where('(a.dikoreksi = 0 OR a.dikoreksi IS NULL)')
                                        ->get()->result();
            foreach ($pending_submits as $ps) {
                $pending_cbt_grading[] = [
                    'siswa' => $ps->nama_siswa,
                    'ujian' => $ps->nama_ujian,
                    'waktu' => date('d M Y H:i', strtotime($ps->waktu))
                ];
            }
        }

        $this->output_json([
            'status' => true,
            'guru' => [
                'nama' => $guru->nama_guru,
                'nip' => $guru->nip
            ],
            'kbm_summary' => [
                'materi_total' => $materi_count,
                'tugas_total' => $tugas_count
            ],
            'tugas_details' => $tugas_stats,
            'honor' => $honor_rekap,
            'chat' => $comments_data,
            'cbt_pending' => $pending_cbt_grading
        ]);
    }
}
