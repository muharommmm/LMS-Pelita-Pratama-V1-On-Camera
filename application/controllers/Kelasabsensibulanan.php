<?php
/*   ________________________________________
    |                 GarudaCBT              |
    |    https://github.com/garudacbt/cbt    |
    |________________________________________|
*/
class Kelasabsensibulanan extends CI_Controller {
    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru')) {
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Dibatasi');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');
        $this->form_validation->set_error_delimiters('', '');
    }

    public function output_json($data, $encode = true) {
        if ($encode) {
            $data = json_encode($data);
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }

    public function index() {
        $user = $this->ion_auth->user()->row();
        $data = [
            'user' => $user,
            'judul' => 'Daftar Hadir Bulanan',
            'subjudul' => 'Daftar Hadir Bulanan Siswa',
            'setting' => $this->dashboard->getSetting()
        ];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['bulan'] = $this->dropdown->getBulan();

        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
            $data['guru'] = $this->dropdown->getAllGuru();
            $data['mapel'] = $this->dropdown->getAllMapel();
            $data['arrkelas'] = [];
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/absenbulanan/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['guru'] = $guru;
            $data['id_guru'] = $guru->id_guru;
            $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
            $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
            $arrMapel = [];
            $arrKelas = [];
            if ($mapel != null) {
                foreach ($mapel as $m) {
                    $arrMapel[$m->id_mapel] = $m->nama_mapel;
                    foreach ($m->kelas_mapel as $kls) {
                        $arrKelas[$m->id_mapel][] = [
                            'id_kelas' => $kls->kelas,
                            'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)
                        ];
                    }
                }
            }
            $arrId = [];
            if ($mapel != null) {
                foreach ($mapel[0]->kelas_mapel as $id_mapel) {
                    array_push($arrId, $id_mapel->kelas);
                }
            }
            $data['mapel'] = $arrMapel;
            $data['arrkelas'] = $arrKelas;
            $data['kelas'] = count($arrId) > 0 ? $this->dropdown->getAllKelasByArrayId($tp->id_tp, $smt->id_smt, $arrId) : [];
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('kelas/absenbulanan/data');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function loadAbsensiMapel() {
        $id_kelas = $this->input->post("kelas", true);
        $id_mapel = $this->input->post("mapel", true);
        $tahun = $this->input->post("thn", true);
        $bulan = $this->input->post("bln", true);

        $id_tp = $this->master->getTahunActive()->id_tp;
        $id_smt = $this->master->getSemesterActive()->id_smt;

        $jadwal = $this->dashboard->getJadwalKbm($id_tp, $id_smt, $id_kelas);
        if ($jadwal == null) {
            // Mock a default KBM parameter object to prevent frontend null reference
            $jadwal = (object) [
                'id_kbm' => $id_tp . $id_smt . $id_kelas,
                'id_tp' => $id_tp,
                'id_smt' => $id_smt,
                'id_kelas' => $id_kelas,
                'kbm_jam_pel' => 45,
                'kbm_jam_mulai' => '07:30',
                'kbm_jml_mapel_hari' => 8,
                'istirahat' => serialize([]),
                'ada' => false
            ];
        }

        $jadwal->istirahat = @unserialize($jadwal->istirahat);
        if ($jadwal->istirahat === false) {
            $jadwal->istirahat = [];
        }
        $tgl = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        
        $jadwal_materi = [];
        for ($i = 0; $i < $tgl; $i++) {
            $t = $i + 1 < 10 ? "0" . ($i + 1) : $i + 1;
            $b = $bulan < 10 ? "0" . $bulan : $bulan;
            $jadwal_materi[$t] = (array) $this->kelas->getAllMateriByTglV2($id_kelas, $tahun . "-" . $b . "-" . $t, [$id_mapel]);
        }

        // Query log_materi joined with kelas_materi and jadwal_fleksibel for flexible schedule rekap
        $this->db->select("b.id_siswa, b.log_time, b.finish_time, b.id_materi, b.jam_ke, c.jenis, c.id_mapel, d.id_jadwal");
        $this->db->from("kelas_siswa a");
        $this->db->join("log_materi b", "a.id_siswa=b.id_siswa", "inner");
        $this->db->join("kelas_materi c", "b.id_materi=c.id_materi", "inner");
        $this->db->join("jadwal_fleksibel d", "c.id_mapel=d.mapel_id AND a.id_kelas=d.class_id AND (WEEKDAY(b.log_time) + 1)=d.day", "left");
        $this->db->where("a.id_kelas", $id_kelas);
        $this->db->where("c.id_mapel", $id_mapel);
        $this->db->where("MONTH(b.log_time)", $bulan);
        $this->db->where("YEAR(b.log_time)", $tahun);
        $results = $this->db->get()->result();
        
        $materi_perbulan = [];
        foreach ($results as $row) {
            $date = date('Y-m-d', strtotime($row->log_time));
            $jenis = $row->jenis; // 1 or 2
            $jam = !empty($row->id_jadwal) ? $row->id_jadwal : '0';
            $materi_perbulan[$row->id_siswa][$jenis][$date][$jam] = $row;
        }

        // Fetch manual attendance from absensi_siswa
        $absensi_manual = $this->db->select('student_id, date, status')
            ->where([
                'class_id' => $id_kelas,
                'mapel_id' => $id_mapel,
                'MONTH(date)' => $bulan,
                'YEAR(date)' => $tahun
            ])
            ->get('absensi_siswa')
            ->result();

        $manual_map = [];
        foreach ($absensi_manual as $row) {
            $manual_map[$row->student_id][$row->date] = $row->status;
        }

        $log = [];
        $siswa = $this->kelas->getKelasSiswa($id_kelas, $id_tp, $id_smt);
        foreach ($siswa as $s) {
            $arrMateri = [];
            $arrManual = [];
            for ($i = 0; $i < $tgl; $i++) {
                $t = $i + 1 < 10 ? "0" . ($i + 1) : $i + 1;
                $b = $bulan < 10 ? "0" . $bulan : $bulan;
                $full_date = $tahun . "-" . $b . "-" . $t;

                $arrMateri[1][] = ($materi_perbulan != null && isset($materi_perbulan[$s->id_siswa]) && isset($materi_perbulan[$s->id_siswa][1]) && isset($materi_perbulan[$s->id_siswa][1][$full_date])) 
                    ? $materi_perbulan[$s->id_siswa][1][$full_date] 
                    : null;

                $arrMateri[2][] = ($materi_perbulan != null && isset($materi_perbulan[$s->id_siswa]) && isset($materi_perbulan[$s->id_siswa][2]) && isset($materi_perbulan[$s->id_siswa][2][$full_date])) 
                    ? $materi_perbulan[$s->id_siswa][2][$full_date] 
                    : null;

                $arrManual[] = isset($manual_map[$s->id_siswa][$full_date]) ? $manual_map[$s->id_siswa][$full_date] : null;
            }
            $log[$s->id_siswa] = [
                "nama" => $s->nama,
                "nis" => $s->nis,
                "kelas" => $s->nama_kelas,
                "materi" => $arrMateri[1],
                "tugas" => $arrMateri[2],
                "manual" => $arrManual
            ];
        }

        $mapel_bulan_ini = [];
        $infos = $this->kelas->getJadwalMapelByMapelV2($id_kelas, $id_mapel, $id_tp, $id_smt);
        foreach ($infos as $info) {
            $dates = $this->total_hari($info->id_hari, $bulan, $tahun);
            foreach ($dates as $date) {
                $d = explode("-", $date ?? '');
                $day_d = (int)$d[2];
                $week = (int)ceil($day_d / 7);
                $is_odd = ($week % 2 === 1);
                
                // Get pola_mingguan for this schedule (using the table jadwal_fleksibel)
                $pola = $this->db->select('pola_mingguan')->where('id_jadwal', $info->jam_ke)->get('jadwal_fleksibel')->row();
                if ($pola) {
                    $pola_val = $pola->pola_mingguan;
                    if ($pola_val === 'Semua' ||
                        ($pola_val === 'Ganjil' && $is_odd) ||
                        ($pola_val === 'Genap' && !$is_odd)) {
                        $mapel_bulan_ini[$d[2]][$info->jam_ke] = $date;
                    }
                }
            }
        }

        $this->output_json([
            "log" => $log,
            "jadwal" => $jadwal,
            "materi" => $jadwal_materi,
            "mapels" => $mapel_bulan_ini
        ]);
    } function total_hari($id_day, $bulan, $taun) { goto Q9A_v; BBuRG: if (!(date("\116", strtotime($taun . "\x2d" . $bulan . "\55" . $i)) == $idday)) { goto sR1Ek; } goto kNNFD; O1O2L: yJCsE: goto CFvbp; kNNFD: $days++; goto v284y; wRjDk: $total_days = cal_days_in_month(CAL_GREGORIAN, $bulan, $taun); goto N2Gn3; N2Gn3: $idday = $id_day == "\67" ? 0 : $id_day; goto ngWcY; utQ1X: sR1Ek: goto O1O2L; Q9A_v: $days = 0; goto NQOl1; J2sHR: unNoL: goto ovAOk; xpJA9: return $dates; goto TOYcL; moK81: goto unNoL; goto rqnZm; CFvbp: $i++; goto moK81; ovAOk: if (!($i < $total_days)) { goto OrO5z; } goto BBuRG; NQOl1: $dates = []; goto wRjDk; v284y: array_push($dates, date("\131\x2d\x6d\55\144", strtotime($taun . "\x2d" . $bulan . "\55" . $i))); goto utQ1X; rqnZm: OrO5z: goto xpJA9; ngWcY: $i = 1; goto J2sHR; TOYcL: } }
