<?php
/*   ________________________________________
    |                 GarudaCBT              |
    |    https://github.com/garudacbt/cbt    |
    |________________________________________|
*/
 defined("\102\101\x53\105\x50\101\124\110") or exit("\x4e\x6f\x20\x64\x69\x72\x65\x63\164\40\163\143\x72\151\x70\x74\x20\x61\143\143\x65\x73\x73\40\x61\154\x6c\x6f\167\x65\144"); class Dashboard extends CI_Controller { public function __construct() { parent::__construct(); if (!$this->ion_auth->logged_in()) { redirect('auth'); } $this->load->model('Master_model', 'master'); $this->load->model('Dashboard_model', 'dashboard'); $this->load->model('Log_model', 'logging'); $this->load->model('Dropdown_model', 'dropdown'); $this->load->model('Cbt_model', 'cbt'); }    public function admin_box($setting, $tp, $smt) {
        $where = '';
        if ($setting->jenjang == "1") {
            $where = "jenjang=0 OR jenjang=1";
        } elseif ($setting->jenjang == "2") {
            $where = "jenjang=2 OR jenjang=1";
        }

        $box = [
            [
                "box" => "blue",
                "total" => $this->dashboard->total("master_siswa"),
                "title" => "Siswa",
                "url" => "datasiswa",
                "icon" => "users"
            ],
            [
                "box" => "cyan",
                "total" => $this->dashboard->total("master_kelas", "id_tp=" . $tp . " AND id_smt=" . $smt),
                "title" => "Rombel",
                "url" => "datakelas",
                "icon" => "bell"
            ],
            [
                "box" => "teal",
                "total" => $this->dashboard->total("master_guru"),
                "title" => "Guru",
                "url" => "dataguru",
                "icon" => "user"
            ],
            [
                "box" => "fuchsia",
                "total" => $this->dashboard->totalWaliKelas($tp, $smt),
                "title" => "Wali Kelas",
                "url" => "dataguru",
                "icon" => "user"
            ],
            [
                "box" => "success",
                "total" => $this->dashboard->total("master_mapel", $where),
                "title" => "Mapel",
                "url" => "datamapel",
                "icon" => "book"
            ],
            [
                "box" => "yellow",
                "total" => $this->dashboard->total("master_ekstra"),
                "title" => "Ekstrakurikuler",
                "url" => "dataekstra",
                "icon" => "book"
            ]
        ];
        return json_decode(json_encode($box), FALSE);
    }

    public function guru_box($setting) {
        $where = '';
        if ($setting->jenjang == "1") {
            $where = "jenjang=0 OR jenjang=1";
        } elseif ($setting->jenjang == "2") {
            $where = "jenjang=2 OR jenjang=1";
        }

        $box = [
            [
                "box" => "teal",
                "total" => $this->dashboard->total("master_kelas"),
                "title" => "Rombel",
                "icon" => "user"
            ],
            [
                "box" => "blue",
                "total" => $this->dashboard->total("master_siswa"),
                "title" => "Siswa",
                "icon" => "users"
            ],
            [
                "box" => "fuchsia",
                "total" => $this->dashboard->total("master_guru"),
                "title" => "Guru",
                "icon" => "user"
            ],
            [
                "box" => "success",
                "total" => $this->dashboard->total("master_mapel", $where),
                "title" => "Mapel",
                "icon" => "book"
            ]
        ];
        return json_decode(json_encode($box), FALSE);
    }

    public function ujian_box() {
        $box = [
            [
                "box" => "indigo",
                "total" => $this->dashboard->total("cbt_ruang"),
                "title" => "Ruang Ujian",
                "url" => "cbtruang",
                "icon" => "school"
            ],
            [
                "box" => "maroon",
                "total" => $this->dashboard->total("cbt_sesi"),
                "title" => "Sesi",
                "url" => "cbtsesi",
                "icon" => "clock"
            ],
            [
                "box" => "green",
                "total" => $this->dashboard->total("cbt_bank_soal"),
                "title" => "Bank Soal",
                "url" => "cbtbanksoal",
                "icon" => "folder"
            ],
            [
                "box" => "teal",
                "total" => $this->dashboard->totalJadwal(),
                "title" => "Jadwal",
                "url" => "cbtjadwal",
                "icon" => "clock"
            ]
        ];
        return json_decode(json_encode($box), FALSE);
    }

    public function menu_siswa_box() {
        $box = [
            ["title" => "Jadwal Pelajaran", "icon" => "ic_online.png", "link" => "siswa/jadwalpelajaran"],
            ["title" => "Materi", "icon" => "ic_elearning.png", "link" => "siswa/materi"],
            ["title" => "Tugas", "icon" => "ic_questions.png", "link" => "siswa/tugas"],
            ["title" => "Ujian / Ulangan", "icon" => "ic_question.png", "link" => "siswa/cbt"],
            ["title" => "Nilai Hasil", "icon" => "ic_exam.png", "link" => "siswa/hasil"],
            ["title" => "Absensi", "icon" => "ic_clipboard.png", "link" => "siswa/kehadiran"],
            ["title" => "Catatan Guru", "icon" => "ic_student.png", "link" => "siswa/catatan"]
        ];
        return json_decode(json_encode($box), FALSE);
    } public function index() { 
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $fields = $this->db->list_fields('users');
            if (!in_array('last_active', $fields)) {
                $this->db->query("ALTER TABLE users ADD COLUMN last_active DATETIME DEFAULT NULL");
            }
        }
        goto rBZyN; atEUv: $this->load->view("\x64\x69\x73\x61\x62\x6c\x65\x5f\x6c\x6f\x67\x69\x6e", $data); goto tVYOD; s4_Pt: $data["\164\x70\x5f\x61\x63\x74\x69\x76\x65"] = $tp; goto aRJrf; sVjKw: $data["\x6a\x61\x64\x77\x61\x6c\x73"] = $arrJadwalKelas[$siswa->id_kelas] ?? []; goto IKEBO; WlEAi: goto g6Wl3; goto NyXLV; mgeAf: $data["\x6d\x65\x6e\x75"] = $this->menu_siswa_box(); goto wjBb2; kwaCI: Tw_wq: goto txM76; F_9B5: g6Wl3: goto PLFl3; NabKA: ZuXDW: goto ciSze; qAH2D: $this->load->view("\x64\x61\x73\x68\x62\x6f\x61\x72\x64"); goto D7bfY; qsnA1: $tkn["\152\x61\x72\x61\163\x6b"] = "\61"; goto l527C; ukmCm: $this->load->view("\155\x65\155\142\x65\x72\x73\57\x67\x75\x72\x75\57\x64\141\x73\x68\x62\x6f\x61\x72\144"); goto mwvIC; DI1Ru: $data["\x6b\x62\x6d\x73"] = $arrKbm; goto mnLW2; tdqKq: oy7eP: goto atEUv; Zb0Cn: if ($guru == null) { goto i3oKw; } goto qyYX0; UYzH1: $data["\x6c\x6f\x67\x69\x6e"] = $siswa; goto c5snQ; YqqkN: $tkn["\x61\x75\x74\x6f"] = "\x30"; goto qsnA1; bE1P1: ldjqx: goto WlEAi; J3bDy: $data["\147\x75\x72\x75\x73"] = $this->dropdown->getAllGuru(); goto J90Ks; cCAaY: if (!($tp != null)) { goto GbfNE; } goto Zqvi5; mwvIC: $this->load->view("\155\145\155\142\x65\x72\x73\57\x67\x75\x72\x75\57\x74\x65\x6d\x70\x6c\x61\x74\x65\x73\57\146\x6f\x6f\x74\x65\x72"); goto heI6W; heI6W: goto g4Mwg; goto YKDFi; cREOV: foreach ($tglJadwals as $tgl => $jadwalss) { goto KIuIC; KIuIC: foreach ($jadwalss as $mpl => $jadwals) { goto t6HBk; IUnJt: cNjKp: goto KpcBp; KpcBp: vVPDZ: goto F0Wz3; t6HBk: foreach ($jadwals as $jadwal) { goto r1Nrb; r1Nrb: $jadwal->bank_kelas = unserialize($jadwal->bank_kelas); goto Q03Qc; srWHl: zubwu: goto M__xo; M__xo: n4Bvc: goto Tnne_; Q03Qc: foreach ($jadwal->bank_kelas as $kb) { goto yAgw9; E1SZ7: $jadwal->peserta[] = $p; goto OhRW6; MeQ_Q: V8ARJ: goto vtO00; yAgw9: if (!($kb["\x6b\x65\x6c\x61\x73\x5f\x69\144"] != '')) { goto GFoVr; } goto AJxLy; OhRW6: GFoVr: goto MeQ_Q; AJxLy: $p = $this->cbt->getKelasUjian($kb["\x6b\x65\x6c\x61\x73\x5f\x69\144"]); goto E1SZ7; vtO00: } goto srWHl; Tnne_: } goto IUnJt; F0Wz3: } goto AC_sA; AC_sA: kLUuZ: goto fAqDy; fAqDy: ocOLg: goto wdb58; wdb58: } goto midPf; I2Fjt: if ($siswa == null) { goto oy7eP; } goto UYzH1; l527C: $tkn["\145\x6c\x61\x70\x73\x65\144"] = "\x30\60\72\60\60\72\60\x30"; goto ZmFAS; rR3d7: $token = $this->cbt->getToken(); goto a_TPC; tVYOD: UCwOk: goto F_9B5; xt0MG: $this->load->view("\x64\x69\x73\x61\x62\x6c\x65\x5f\x6c\x6f\x67\x69\x6e", $data); goto Q3MBu; aRJrf: $data["\x73\x6d\x74"] = $this->dashboard->getSemester(); goto NT6PK; txM76: $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt); goto Zb0Cn; rO_vY: $this->load->view("\137\x74\x65\x6d\x70\x6c\x61\x74\x65\x73\57\x64\x61\x73\x68\x62\x6f\x61\x72\144\57\137\x68\x65\x61\x64\x65\x72", $data); goto qAH2D; xFpzG: $this->load->view("\x6d\x65\x6d\x62\x65\x72\x73\57\x67\x75\x72\x75\57\x74\x65\x6d\x70\x6c\x61\x74\x65\x73\57\x68\x65\x61\x64\x65\x72", $data); goto ukmCm; IKEBO: $data["\x72\x75\x6e\x6e\x69\x6e\x67\x5f\x74\x65\x78\x74"] = $this->dashboard->getRunningText(); goto M4Igt; ZmFAS: $data["\x74\x6f\x73\x6b\x65\x6e"] = $token != null ? $token : json_decode(json_encode($tkn)); goto L58e8; QSZb_: $data["\x70\x72\x6f\x66\x69\x6c\x65"] = $this->dashboard->getProfileAdmin($user->id); goto rO_vY; Zqvi5: $kelass = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt); goto olSeG; VVkMQ: if ($this->ion_auth->in_group("\x73\x69\x73\x77\x61")) { goto ilkNy; } goto rR3d7; ZU2M4: $tp = $this->dashboard->getTahunActive(); goto HOC3E; hm6WI: $data["\x75\x6a\x69\x61\x6e\x5f\x62\x6f\x78"] = $this->ujian_box(); goto QSZb_; HOC3E: $smt = $this->dashboard->getSemesterActive(); goto AbFlg; AbFlg: $data["\x74\x70"] = $this->dashboard->getTahun(); goto s4_Pt; oblbw: $data["\x75\x6a\x69\x61\x6e\x5f\x62\x6f\x78"] = $this->ujian_box(); goto d6pfq; BawJl: $user = $this->ion_auth->user()->row(); goto Cc9Cy; C5igC: goto ldjqx; goto QqPGT; D7bfY: $this->load->view("\137\x74\x65\x6d\x70\x6c\x61\x74\x65\x73\57\x64\x61\x73\x68\x62\x6f\x61\x72\144\57\137\146\157\157\164\x65\162"); goto QjcyH; mnLW2: $data["\155\x61\x70\x65\x6c\x73"] = $this->master->getAllMapel(); goto nTnW9; qyYX0: $data["\x69\x6e\x66\x6f\x5f\x62\x6f\x78"] = $this->admin_box($setting, $tp->id_tp, $smt->id_smt); goto oblbw; GC0f5: $siswa = $this->dashboard->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt); goto I2Fjt; l1p8s: $kbms = $this->dashboard->getJadwalKbm($tp->id_tp, $smt->id_smt); goto uVNKe; ciSze: $arrJadwalKelas = []; goto Wy3v7; sLhns: $data["\x6a\x61\x64\x77\x61\x6c\x73"] = $arrJadwalKelas; goto DI1Ru; rBZyN: $setting = $this->dashboard->getSetting(); goto BawJl; kTedZ: $kelass = []; goto cCAaY; L58e8: $data["\x61\x64\x61\x5f\x75\x6a\x69\x61\x6e"] = $this->cbt->getDataJadwalByTgl(date("\x59\55\x6d\55\x64")); goto sLhns; nPf3L: $data["\x72\x75\x61\x6e\x67\x73"] = $this->cbt->getDistinctRuang($tp->id_tp, $smt->id_smt, []); goto J3bDy; QqPGT: cBpGb: goto kECju; Wy3v7: foreach ($jadwal as $key => $item) { $arrJadwalKelas[$item->id_kelas][$item->jam_ke] = $item; RPrjr: } goto Pguyi; d6pfq: $data["\x67\x75\x72\x75"] = $guru; goto xFpzG; uVNKe: foreach ($kbms as $kbm) { $kbm->istirahat = unserialize($kbm->istirahat); nYf0Z: } goto NabKA; midPf: jDxfm: goto Q12bW; kECju: $data["\x69\x6e\x66\x6f\x5f\x62\x6f\x78"] = $this->admin_box($setting, $tp->id_tp, $smt->id_smt); goto hm6WI; Pguyi: QoWAz: goto Oq1bU; zOeWL: $jadwal = $this->dashboard->loadJadwalHariIni($tp->id_tp, $smt->id_smt, null, $day); goto l1p8s; kGr4s: $data["\x6b\x65\x6c\x61\x73\x65\x73"] = $kelass; goto IunIf; c5snQ: $data["\x73\x69\x73\x77\x61"] = $siswa; goto mgeAf; Q3MBu: g4Mwg: goto bE1P1; MpBtx: foreach ($kbms as $key => $item) { $arrKbm[$item->id_kelas] = $item; VMeWO: } goto O0H5k; rIVlN: $data["\x70\x65\x6e\x67\x61\x77\x61\x73"] = $this->cbt->getAllPengawas($tp->id_tp, $smt->id_smt, null, null); goto nPf3L; wjBb2: $data["\x6b\x62\x6d\x73"] = $arrKbm[$siswa->id_kelas] ?? null; goto sVjKw; EIrMh: if ($this->ion_auth->in_group("\x67\x75\x72\x75")) { goto Tw_wq; } goto C5igC; a_TPC: $tkn["\x74\x6f\x6b\x65\x6e"] = ''; goto YqqkN; M4Igt: $this->load->view("\x6d\x65\x6d\x62\x65\x72\x73\57\x73\x69\x73\x77\x61\57\x74\x65\x6d\x70\x6c\x61\x74\x65\x73\57\x68\x65\x61\x64\x65\x72", $data); goto siJAt; Q12bW: $data["\x6a\x61\x64\x77\x61\x6c\x73\x5f\x75\x6a\x69\x61\x6e"] = $tglJadwals; goto rIVlN; BfhTi: $this->load->view("\x6d\x65\x6d\x62\x65\x72\x73\57\x73\x69\x73\x77\x61\57\x74\x65\x6d\x70\x6c\x61\x74\x65\x73\57\x66\x6f\x6f\x74\x65\x72"); goto VvtWv; nTnW9: $tglJadwals = $this->cbt->getAllJadwalByJenis(null, $tp->id_tp, $smt->id_smt); goto cREOV; QjcyH: goto ldjqx; goto kwaCI; O0H5k: qPqt9: goto VVkMQ; Oq1bU: $arrKbm = []; goto MpBtx; siJAt: $this->load->view("\x6d\x65\x6d\x62\x65\x72\x73\57\x73\x69\x73\x77\x61\57\x64\x61\x73\x68\x62\x6f\x61\x72\144"); goto BfhTi; olSeG: GbfNE: goto kGr4s; VvtWv: goto UCwOk; goto tdqKq; NyXLV: ilkNy: goto GC0f5; J90Ks: if ($this->ion_auth->is_admin()) { goto cBpGb; } goto EIrMh; Cc9Cy: $data = ["\x75\x73\x65\x72" => $user, "\x6a\x75\x64\x75\x6c" => "\102\x65\x72\x61\x6e\x64\x61", "\x73\x75\x62\x6a\x75\x64\x75\x6c" => "\110\x61\x6c\x61\155\x61\156\40\x55\x74\x61\155\x61", "\x73\x65\x74\x74\x69\x6e\x67" => $setting]; goto ZU2M4; IunIf: $day = date("\x4e", strtotime(date("\x59\55\x6d\55\x64"))); goto zOeWL; YKDFi: i3oKw: goto xt0MG; NT6PK: $data["\x73\x6d\x74\x5f\x61\x63\x74\x69\x76\x65"] = $smt; goto kTedZ; PLFl3: } public function checkTokenJadwal() { goto eOJlO; LVeNY: $token = $this->cbt->getToken(); goto msqIE; GpTF5: $data["\x74\x6f\x6b\x65\x6e"] = $token; goto G25m1; eOJlO: $data["\x61\x64\x61\x5f\x75\x6a\x69\x61\x6e"] = $this->cbt->getDataJadwalByTgl(date("\x59\55\x6d\55\x64")); goto LVeNY; msqIE: $token->now = date("\x64\55\x6d\55\x59\40\x48\x3a\x69\x3a\x73"); goto GpTF5; G25m1: $this->output_json($data); goto lmvw0; lmvw0: } public function output_json($data, $encode = true) { goto JgnAy; B2RKj: $data = json_encode($data); goto e7ofU; JgnAy: if (!$encode) { goto sRLWD; } goto B2RKj; f0zvn: $this->output->set_content_type("\x61\x70\x70\x6c\x69\x63\x61\x74\x69\x6f\x6e\57\x6a\x73\x6f\x6e")->set_output($data); goto zfdVQ; e7ofU: sRLWD: goto f0zvn; zfdVQ: } public function gantiTahun() { goto lV16a; f7fA2: if (!($i <= $rows)) { goto HFA1u; } goto OV6Wa; lV16a: $aktif = $this->input->post("\x61\x63\x74\x69\x76\x65", true); goto IlFqn; kDMQc: HFA1u: goto ZtlPC; Ahz4D: $this->output_json($data); goto TEj7J; EUdMH: if ($id_tp === $aktif) { goto s9woU; } goto kcW84; IlFqn: $rows = count($this->input->post("\x74\x61\x68\x75\x6e", true)); goto jsf9d; jjNQn: $this->logging->saveLog(4, "\x6d\x65\x6e\x67\x67\x61\x6e\x74\x69\40\x74\x61\x68\x75\x6e\40\x61\x6a\x61\x72\x61\x6e\40\x61\x6b\x74\x69\x66"); goto Ahz4D; jsf9d: $i = 0; goto GSf6h; WZ5_S: $i++; goto gOUL1; oLkpj: goto W8Mwj; goto RcnoX; fekmL: $data["\x75\x70\x64\x61\x74\x65"] = $update; goto e9SyD; gOUL1: goto KVIrz; goto kDMQc; ZtlPC: $this->dashboard->update("\x6d\x61\x73\x74\x65\x72\x5f\x74\x70", $update, "\x69\x64\x5f\x74\x70", null, true); goto fekmL; RcnoX: s9woU: goto jYiAD; OV6Wa: $id_tp = $this->input->post("\x69\x64\x5f\x74\x70\133" . $i . "\135", true); goto JWI71; LOcgG: $update[] = array("\x69\x64\x5f\x74\x70" => $id_tp, "\x74\x61\x68\x75\x6e" => $tahun, "\x61\x63\x74\x69\x76\x65" => $active); goto USTus; USTus: Wsukh: goto WZ5_S; bb875: W8Mwj: goto LOcgG; GSf6h: KVIrz: goto f7fA2; kcW84: $active = 0; goto oLkpj; e9SyD: $data["\x73\x74\x61\x74\x75\x73"] = true; goto jjNQn; jYiAD: $active = 1; goto bb875; JWI71: $tahun = $this->input->post("\x74\x61\x68\x75\x6e\133" . $i . "\135", true); goto EUdMH; TEj7J: } public function gantiSemester() { goto m5Dv5; x5CgQ: ATBy5: goto bl9r2; aIam4: $update[] = array("\x69\x64\x5f\x73\x6d\x74" => $id_smt, "\x73\x6d\x74" => $smt, "\x61\x63\x74\x69\x76\x65" => $active); goto x5CgQ; nXB3V: $i = 1; goto UVEBC; KxqOO: $rows = count($this->input->post("\x73\x6d\x74", true)); goto nXB3V; EGLj1: $this->output_json($data); goto huGoS; odDck: goto H3g04; goto bE8Hb; W3lwk: $smt = $this->input->post("\x73\x6d\x74\133" . $i . "\135", true); goto abcEO; sEhGV: if (!($i <= $rows)) { goto TCG5j; } goto YK_AH; bl9r2: $i++; goto odDck; CjMQD: $this->logging->saveLog(4, "\x6d\x65\x6e\x67\x67\x61\x6e\x74\x69\40\x73\x65\x6d\x65\x73\x74\x65\x72\40\x61\x6b\x74\x69\x66"); goto EGLj1; BBvpK: $active = 1; goto OM6OV; m5Dv5: $aktif = $this->input->post("\x61\x63\x74\x69\x76\x65", true); goto KxqOO; OM6OV: Vfo1B: goto aIam4; UVEBC: H3g04: goto sEhGV; IYnJw: $this->dashboard->update("\x6d\x61\x73\x74\x65\x72\x5f\x73\x6d\x74", $update, "\x69\x64\x5f\x73\x6d\x74", null, true); goto Elvo4; Elvo4: $data["\x75\x70\x64\x61\x74\x65"] = $update; goto U2wof; abcEO: if ($id_smt === $aktif) { goto VJFne; } goto USQQM; bE8Hb: TCG5j: goto IYnJw; U2wof: $data["\x73\x74\x61\x74\x75\x73"] = true; goto CjMQD; USQQM: $active = 0; goto d0iiI; sqb53: VJFne: goto BBvpK; d0iiI: goto Vfo1B; goto sqb53; YK_AH: $id_smt = $this->input->post("\x69\x64\x5f\x73\x6d\x74\133" . $i . "\135", true); goto W3lwk; huGoS: } public function getNotifikasi() { } public function getLog($limit) { $this->output_json($this->logging->loadAktifitas($limit)); } public function hapusLog() { goto iSHqe; OxDBr: $deleted = ["\x73\x74\x61\x74\x75\x73" => true, "\x6d\x65\x73\x73\x61\x67\x65" => "\x62\x65\x72\x68\x61\x73\x69\x6c"]; goto Ggz1D; phkQ1: $this->db->trans_complete(); goto EYQPI; EYQPI: $this->output_json($deleted); goto APv6S; D7NCQ: ChVDX: goto OxDBr; V9_Ya: goto Igqhc; goto D7NCQ; iSHqe: $this->db->trans_start(); goto ifiww; oVLz7: $deleted = ["\x73\x74\x61\x74\x75\x73" => false, "\x6d\x65\x73\x73\x61\x67\x65" => "\x67\x61\x67\x61\x6c"]; goto V9_Ya; ifiww: if ($this->db->empty_table("\x6c\x6f\x67")) { goto ChVDX; } goto oVLz7; Ggz1D: Igqhc: goto phkQ1; APv6S: } public function getLogSiswa($limit) { $this->output_json($this->logging->loadAktifitasSiswa($limit)); } public function getPengumuman($for) { $this->output_json($this->dashboard->loadPengumuman($for)); } public function getJadwalHariIni($id_kelas, $id_hari) { goto khcMF; khcMF: $tp = $this->dashboard->getTahunActive(); goto cb7l2; cb7l2: $smt = $this->dashboard->getSemesterActive(); goto ZCeRB; ZCeRB: $this->output_json($this->dashboard->loadJadwalHariIni($tp->id_tp, $smt->id_smt, $id_kelas, $id_hari)); goto R87Xp; R87Xp: } public function getJadwalKbm($id_kelas) { goto v7VOM; AcOJq: $jadwal = $this->dashboard->getJadwalKbm($tp->id_tp, $smt->id_smt, $id_kelas); goto VnvsK; xdy0t: $smt = $this->dashboard->getSemesterActive(); goto AcOJq; TWIxN: $this->output_json(array("\x6a\x61\x64\x77\x61\x6c" => $jadwal, "\x69\x73\x74\x69\x72\x61\x68\x61\x74" => $istirahat)); goto OxP39; VnvsK: $istirahat = unserialize($jadwal->istirahat); goto TWIxN; v7VOM: $tp = $this->dashboard->getTahunActive(); goto xdy0t; OxP39: } public function loadNotifikasi() { $this->output_json($this->dashboard->getNotifikasi()); } public function readNotifikasi($id) { $this->output_json($this->dashboard->markAsRead($id)); } 
        public function get_aktivitas_ajax() {
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        
        if ($guru) {
            // Menggunakan method getAktivitasGuru yang baru, menggabungkan tugas, ujian, dan chat
            $data = $this->dashboard->getAktivitasGuru($user->id, $guru->id_guru);
            $output = [];
            foreach ($data as $row) {
                $waktu_ts = strtotime($row->waktu ?? '');
                if (!$waktu_ts || $row->waktu == '0000-00-00 00:00:00') {
                    $waktu_str = 'Baru saja';
                } else {
                    $waktu_str = date('d M Y, H:i', $waktu_ts);
                }

                $output[] = [
                    'id'           => $row->id,
                    'tipe'         => $row->tipe,
                    'judul'        => $row->judul,
                    'nama_siswa'   => $row->nama_siswa,
                    'id_referensi' => $row->id_referensi,
                    'id_mapel'     => isset($row->id_mapel) ? $row->id_mapel : 0,
                    'id_siswa'     => isset($row->id_siswa) ? $row->id_siswa : 0,
                    'id_kelas'     => isset($row->id_kelas) ? $row->id_kelas : 0,
                    'waktu'        => $waktu_str,
                    'is_read'      => $row->is_read == 1 ? true : false
                ];
            }
            $this->output_json(['status' => true, 'data' => $output]);
        } else {
            $this->output_json(['status' => false, 'data' => []]);
        }
    }

    /**
     * AJAX Endpoint to fetch currently online/active users within the last 1 minute
     */
    public function get_online_users() {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $this->output_json(['status' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        // Active users criteria: last_active within the last 1 minute (60 seconds)
        $one_minute_ago = date('Y-m-d H:i:s', time() - 60);

        $this->db->select('u.id, u.username, u.last_active, g.name as role_name');
        $this->db->from('users u');
        $this->db->join('users_groups ug', 'u.id = ug.user_id', 'left');
        $this->db->join('groups g', 'ug.group_id = g.id', 'left');
        $this->db->where('u.last_active >=', $one_minute_ago);
        $this->db->order_by('u.last_active', 'DESC');
        $online_users = $this->db->get()->result();

        $output = [];
        foreach ($online_users as $row) {
            $nama = '';
            $kelas = '';

            if ($row->role_name == 'siswa') {
                $siswa = $this->db->select('s.nama, mk.nama_kelas')
                                  ->from('master_siswa s')
                                  ->join('kelas_siswa ks', 's.id_siswa = ks.id_siswa', 'left')
                                  ->join('master_kelas mk', 'ks.id_kelas = mk.id_kelas', 'left')
                                  ->where('s.username', $row->username)
                                  ->get()->row();
                if ($siswa) {
                    $nama = $siswa->nama;
                    $kelas = $siswa->nama_kelas;
                } else {
                    $nama = $row->username;
                }
            } elseif ($row->role_name == 'guru') {
                $guru = $this->db->select('nama_guru')
                                 ->from('master_guru')
                                 ->where('id_user', $row->id)
                                 ->get()->row();
                if ($guru) {
                    $nama = $guru->nama_guru;
                } else {
                    $nama = $row->username;
                }
            } else {
                $user_detail = $this->db->select('first_name, last_name')
                                        ->from('users')
                                        ->where('id', $row->id)
                                        ->get()->row();
                if ($user_detail) {
                    $nama = trim($user_detail->first_name . ' ' . $user_detail->last_name) ?: $row->username;
                } else {
                    $nama = $row->username;
                }
            }

            $diff = time() - strtotime($row->last_active);
            if ($diff < 10) {
                $waktu_str = 'Baru saja';
            } else {
                $waktu_str = $diff . ' detik yang lalu';
            }

            $output[] = [
                'nama' => $nama,
                'role' => ucfirst($row->role_name),
                'kelas' => $kelas,
                'waktu' => $waktu_str
            ];
        }

        $this->output_json(['status' => true, 'data' => $output]);
    }
}
