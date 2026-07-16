<?php
/*   ________________________________________
    |                 GarudaCBT              |
    |    https://github.com/garudacbt/cbt    |
    |________________________________________|
*/
 class Kelasnilai extends CI_Controller { public function __construct() { goto KI7R6; boDAk: $this->load->model("\115\x61\163\164\145\x72\x5f\155\157\144\145\154", "\x6d\x61\x73\x74\145\x72"); goto HAEqh; xjNbL: $this->load->library(["\144\x61\x74\x61\164\x61\x62\154\x65\163", "\146\157\162\x6d\137\x76\141\x6c\x69\x64\141\x74\151\157\x6e"]); goto boDAk; drXkN: $this->load->model("\x44\x72\157\160\144\157\167\x6e\x5f\155\x6f\x64\145\154", "\144\x72\x6f\160\144\x6f\x77\x6e"); goto DC77j; DC77j: $this->load->model("\x4b\145\154\141\x73\x5f\155\157\144\145\154", "\x6b\145\154\x61\163"); goto p2Mnf; dupzI: if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group("\147\165\162\x75"))) { goto CnuuS; } goto tC_cD; M5dOK: Y5Vw4: goto nH4yq; a5a1z: goto wIV23; goto M5dOK; E3sSs: wIV23: goto xjNbL; tC_cD: show_error("\x48\141\156\171\141\40\x41\x64\155\x69\x6e\x69\163\164\x72\x61\154\157\x72\x20\x79\141\156\x67\40\144\151\x62\145\x72\151\40\x68\x61\153\x20\x65\x6e\x67\x61\x63\x65\x73\x20\x68\x61\x6c\x61\x6d\x61\x6e\x20\x69\x6e\x69\54\40\74\x61\x20\x68\x72\145\146\75\x22" . base_url("\144\x61\163\150\x62\x6f\x61\x72\x144") . "\42\x3e\x4b\145\x6d\142\x61\x6c\x69\40\153\145\x20\155\x65\x6e\165\40\x61\x77\x61\x6c\x3c\x2f\141\76", 403, "\101\153\x73\145\x73\40\x54\x65\162\x6c\141\x72\141\156\147"); goto Ax_yZ; p2Mnf: $this->form_validation->set_error_delimiters('', ''); goto GBw4w; nH4yq: redirect("\141\x75\164\x68"); goto E3sSs; HAEqh: $this->load->model("\x44\x61\x73\150\142\x6f\141\162\144\137\155\x6f\144\145\154", "\x64\x61\x73\150\x62\x6f\141\x72\x64"); goto drXkN; GUQLt: if (!$this->ion_auth->logged_in()) { goto Y5Vw4; } goto dupzI; KI7R6: parent::__construct(); goto GUQLt; Ax_yZ: CnuuS: goto a5a1z; GBw4w: } public function output_json($data, $encode = true) { goto W5R4M; W5R4M: if (!$encode) { goto E9mC8; } goto QiARU; U2JKx: E9mC8: goto j_v1O; QiARU: $data = json_encode($data); goto U2JKx; j_v1O: $this->output->set_content_type("\141\160\160\154\x69\x63\141\x74\151\x6f\x6e\57\152\163\157\156")->set_output($data); goto mC6p3; mC6p3: } public function index() { goto PDs74; H1SKW: $data["\x67\165\x72\165"] = $guru; goto PYgXp; TiVeo: $data["\x70\162\x6f\146\151\x6c\145"] = $this->dashboard->getProfileAdmin($user->id); goto swj1M; eUcju: if ($this->ion_auth->is_admin()) { goto t1smC; } goto kCiVW; HhKnW: $arrKelas = []; goto bqsCx; mb4fz: foreach ($mapel as $m) { goto Wcerk; AM0Fg: KOoRd: goto pQZO0; Wcerk: $arrMapel[$m->id_mapel] = $m->nama_mapel; goto s3u1j; TzZmj: IKFL_: goto AM0Fg; s3u1j: foreach ($m->kelas_mapel as $kls) { $arrKelas[$m->id_mapel][] = ["\151\x64\x5f\x6b\145\154\x61\163" => $kls->kelas, "\x6e\x61\155\x61\137\x6b\145\x6c\141\x73" => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)]; YSFey: } goto TzZmj; pQZO0: } goto StVUz; kkHsC: $data["\x74\x70\137\141\x63\x74\x69\x76\x65"] = $tp; goto no3To; eYik8: LoYNQ: goto DKo66; IP0ae: $this->load->view("\153\x65\x6c\141\x73\57\x6e\151\x6c\141\151\57\144\x61\x74\x61"); goto VKV34; HBLrD: $tp = $this->dashboard->getTahunActive(); goto CJBjj; hrouK: $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt); goto VVB7G; nu32r: $nguru[$guru->id_guru] = $guru->nama_guru; goto H1SKW; VVB7G: $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? ''))); goto CQdt0; CQdt0: $arrMapel = []; goto HhKnW; CJBjj: $smt = $this->dashboard->getSemesterActive(); goto YKJ3u; ncB9F: $this->load->view("\x6b\145\x6c\141\x73\57\x6e\151\x6c\141\151\x2f\x64\141\x74\141"); goto JRqnD; PDs74: $user = $this->ion_auth->user()->row(); goto Wqfyi; lsRO2: $data["\x61\162\x72\153\145\x6c\141\x73"] = $arrKelas; goto wA3vk; DVbKf: KNxQ5: goto MvZUB; DGbWg: $this->load->view("\137\164\145\x6d\x70\x6c\x61\x74\x65\x73\57\x64\141\x73\x68\x62\x6f\x61\x72\x64\57\x5f\x68\145\x61\144\x65\162", $data); goto IP0ae; nPYo6: $data["\x6b\x65\154\141\x73"] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt); goto DGbWg; YKJ3u: $data["\164\x70"] = $this->dashboard->getTahun(); goto kkHsC; bqsCx: if (!($mapel != null)) { goto LoYNQ; } goto mb4fz; VKV34: $this->load->view("\x5f\164\x65\x6d\160\x6c\x61\x74\x65\x73\x2f\144\x61\x73\x68\x62\157\141\x72\x64\x2f\137\146\x6f\157\164\x65\x72"); goto DVbKf; NZWMV: $this->load->view("\x6d\x65\155\142\x65\x72\x73\x2f\147\165\x72\x75\57\x74\145\155\160\x6c\x61\x74\x65\x73\x2f\150\145\x61\x64\x65\x72", $data); goto ncB9F; c72M0: $data["\x73\x6d\x74\137\x61\x63\x74\x69\x76\x65"] = $smt; goto eUcju; typBx: $data["\155\141\x70\x65\x6c"] = $arrMapel; goto lsRO2; xd3ib: t1smC: goto TiVeo; Wqfyi: $data = ["\165\163\145\x72" => $user, "\x6a\165\144\165\x6c" => "\122\145\x6b\x61\160\x69\164\x75\x6c\141\x73\x69\40\x4e\x69\154\x61\151\40\123\151\163\167\x61", "\x73\165\142\152\165\144\x75\154" => "\x4e\x69\x6c\141\x69\x20\x64\x61\154\x61\155\x20\x73\141\x74\165\x20\x73\145\x6d\145\x63\x74\x65\x72", "\163\x65\164\164\151\156\x67" => $this->dashboard->getSetting()]; goto HBLrD; no3To: $data["\163\155\x74"] = $this->dashboard->getSemester(); goto c72M0; kCiVW: $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt); goto nu32r; dWQs6: goto KNxQ5; goto xd3ib; B07Gm: foreach ($mapel[0]->kelas_mapel as $id_mapel) { array_push($arrId, $id_mapel->kelas); baHYK: } goto Oe4ag; gqJNw: L8_yd: goto typBx; Oe4ag: iUO0l: goto gqJNw; JRqnD: $this->load->view("\155\x65\155\142\x65\x72\x73\x2f\x67\165\162\x75\x2f\x74\x65\155\160\x6c\x61\164\x65\x73\x2f\x66\x6f\x6f\x74\x65\162"); goto dWQs6; wA3vk: $data["\x6b\145\x6c\x61\x73"] = count($arrId) > 0 ? $this->dropdown->getAllKelasByArrayId($tp->id_tp, $smt->id_smt, $arrId) : []; goto NZWMV; kJXPf: if (!($mapel != null)) { goto L8_yd; } goto B07Gm; StVUz: ddx_g: goto eYik8; DKo66: $arrId = []; goto kJXPf; swj1M: $data["\155\141\x70\x65\x6c"] = $this->dropdown->getAllMapel(); goto nPYo6; PYgXp: $data["\151\144\x5f\x67\x75\162\x75"] = $guru->id_guru; goto hrouK; MvZUB: }

    public function loadNilaiMapel() {
        $kelas = $this->input->get("kelas");
        $mapel = $this->input->get("mapel");
        $tahun = $this->input->get("tahun");
        $smt = $this->input->get("smt");

        $siswa = $this->kelas->getKelasSiswa($kelas, $tahun, $smt);
        $materi_list = $this->kelas->getMateriKelasMapel($kelas, $mapel);

        $arrBulan = [];
        $jadwal_materi = [];
        if ($materi_list) {
            foreach ($materi_list as $item) {
                $bln = date('m', strtotime($item->tgl_mulai));
                $jadwal_materi[$bln][$item->id_materi] = $item;
                if (!in_array($bln, $arrBulan)) {
                    $arrBulan[] = $bln;
                }
            }
        }
        sort($arrBulan);

        $log_siswa = $this->kelas->getRekapMateriSemesterV3($kelas, $mapel);

        $log = [];
        if (count($siswa) > 0 && count($jadwal_materi) > 0) {
            foreach ($siswa as $s) {
                $log[$s->id_siswa] = [
                    "nama" => $s->nama,
                    "nis" => $s->nis,
                    "kelas" => $s->nama_kelas,
                    "nilai_materi" => isset($log_siswa[1][$s->id_siswa]) ? $log_siswa[1][$s->id_siswa] : [],
                    "nilai_tugas" => isset($log_siswa[2][$s->id_siswa]) ? $log_siswa[2][$s->id_siswa] : []
                ];
            }
        }

        $data = [
            "log" => $log,
            "materi" => $jadwal_materi,
            "bulans" => $arrBulan,
            "mapels" => [1],
            "nilai" => $log_siswa
        ];
        
        $this->output_json($data);
    }

    public function total_hari($id_day, $bulan, $taun) {
        $dates = [];
        $total_days = cal_days_in_month(CAL_GREGORIAN, $bulan, $taun);
        for ($i = 1; $i <= $total_days; $i++) {
            $date = $taun . '-' . $bulan . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            if (date('N', strtotime($date)) == $id_day) {
                $dates[] = $date;
            }
        }
        return $dates;
    }
}

