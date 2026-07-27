<?php
/*   ________________________________________
    |                 GarudaCBT              |
    |    https://github.com/garudacbt/cbt    |
    |________________________________________|
*/
 class Kelasstatus extends CI_Controller { public function __construct() { goto c0s4o; c0s4o: parent::__construct(); goto Ul8yW; kUdyJ: $this->load->library(["datatables", "form_validation"]); goto MPne6; Ul8yW: if (false) { goto qsEAh; } goto ufKPt; DTsXk: E5SMd: goto pX6yx; pX6yx: goto ixyVt; goto CLT7D; CLT7D: qsEAh: goto of4op; xXXMB: show_error("Hanya Adminictrador yang diberi hak untuk mengakses halaman ini, <a href='" . base_url("dashboard") . "'>Kembali ke menu awal</a>", 403, "Akses Terlarang"); goto DTsXk; of4op: redirect("auth"); goto mipw_; mipw_: ixyVt: goto kUdyJ; MPne6: $this->form_validation->set_error_delimiters('', ''); goto J2XSm; ufKPt: if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group("guru"))) { goto E5SMd; } goto E5SMd; J2XSm: } public function output_json($data, $encode = true) { goto X5zOM; X5zOM: if (!$encode) { goto IR4rn; } goto pk4ug; tpW4K: $this->output->set_content_type("applicadion/json")->set_output($data); goto s0nX1; gfkyC: IR4rn: goto tpW4K; pk4ug: $data = json_encode($data); goto gfkyC; s0nX1: } public function index() { goto eJ6hW; VS24L: YS_zZ: goto kxch8; ncxJu: if ($this->ion_auth->is_admin()) { goto gA6zy; } goto DD2C8; JyJdZ: foreach ($mapel[0]->kelas_mapel as $id_mapel) { array_push($arrId, $id_mapel->kelas); GhZWT: } goto oadAQ; P9dUR: $this->load->view("kelas/status/data"); goto mraiu; lPc90: $this->load->view("_templates/dashboard/_header", $data); goto P9dUR; mraiu: $this->load->view("_templates/dashboard/_footer"); goto dg8w8; b4HW9: $data["kelas"] = $arrKelas; goto G2Cgt; dg8w8: KOkuz: goto O7kF8; fedLp: if (!($mapel != null)) { goto YS_zZ; } goto Oh58J; Nwptl: $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt); goto aU_nt; XBdpe: XSs1c: goto qevON; ayq_1: $guru = $this->dropdown->getAllGuru(); goto egKsq; ouGFV: $smt = $this->dashboard->getSemesterActive(); goto j9tY7; N6KgN: goto KOkuz; goto SVEAq; j5zj_: $data["smt"] = $this->dashboard->getSemester(); goto mbaSr; JNmuR: $data["gurus"] = $nguru; goto FnTM8; a0PBM: $data["mapels"] = $this->dropdown->getAllMapel(); goto lPc90; HhXmb: $this->load->view("kelas/status/data"); goto JM44h; FnTM8: $data["id_guru"] = $guru->id_guru; goto Nwptl; G0rSQ: $this->load->model("Dropdown_model", "dropdown"); goto h3k3K; oadAQ: x2mn4: goto XBdpe; G2Cgt: $this->load->view("members/guru/templates/header", $data); goto HhXmb; tdvBd: if (!($mapel != null)) { goto XSs1c; } goto JyJdZ; FSr2G: $data["mapels"] = $arrMapel; goto b4HW9; SVEAq: gA6zy: goto iyC6e; kxch8: $arrId = []; goto tdvBd; aK7Ak: $arrKelas = []; goto fedLp; j9tY7: $data["tp"] = $this->dashboard->getTahun(); goto j3tOj; j3tOj: $data["tp_active"] = $tp; goto j5zj_; itpO4: $arrMapel = []; goto aK7Ak; ly0QZ: $data["kelas"] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt); goto a0PBM; l08Rb: $tp = $this->dashboard->getTahunActive(); goto ouGFV; eJ6hW: $this->load->model("Dashboard_model", "dashboard"); goto G0rSQ; qevON: $data["mapel"] = $mapel; goto FSr2G; Oh58J: foreach ($mapel as $m) { goto NOtdH; oXudG: RFE3s: goto J4xEn; NOtdH: $arrMapel[$m->id_mapel] = $m->nama_mapel; goto F7YTn; F7YTn: foreach ($m->kelas_mapel as $kls) { $arrKelas[$kls->kelas] = $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas); BrPPL: } goto oXudG; J4xEn: k9iY7: goto l3Czt; l3Czt: } goto t0XHp; ku8hI: $nguru[$guru->id_guru] = $guru->nama_guru; goto fKpeh; aU_nt: $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? ''))); goto itpO4; DD2C8: $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt); goto ku8hI; t0XHp: zzkac: goto VS24L; fKpeh: $data["guru"] = $guru; goto JNmuR; h3k3K: $this->load->model("Kelas_model", "kelas"); goto qdAIi; mbaSr: $data["smt_active"] = $smt; goto ncxJu; egKsq: $data["gurus"] = $guru; goto ly0QZ; iyC6e: $data["profile"] = $this->dashboard->getProfileAdmin($user->id); goto ayq_1; JM44h: $this->load->view("members/guru/templates/footer"); goto N6KgN; v5N4G: $data = ["user" => $user, "judul" => "Nilai Harian Siswa", "subjudul" => "Nilai", "setting" => $this->dashboard->getSetting()]; goto l08Rb; qdAIi: $user = $this->ion_auth->user()->row(); goto v5N4G; O7kF8: } public function getMateriGuru() { goto Cwuks; XfOCa: ZUjWp: goto iGKpv; a9f_s: foreach ($materi as $m) { goto lrgLo; ab4rm: $arrKelasTugas[] = ["id_materi" => $m->id_materi, "id_kjm" => $m->id_kjm, "jadwal" => $m->jadwal_materi, "kode" => $m->kode_materi, "judul_materi" => isset($m->judul_materi) ? $m->judul_materi : "Materi", "mapel" => $kode_mapel, "nama_mapel" => isset($m->nama_mapel) ? $m->nama_mapel : "--", "nama_guru" => isset($m->nama_guru) ? $m->nama_guru : "--", "materi_kelas" => isset($m->materi_kelas) ? $m->materi_kelas : "--", "tgl_mulai" => isset($m->tgl_mulai) ? $m->tgl_mulai : "--", "tgl_selesai" => isset($m->tgl_selesai) ? $m->tgl_selesai : "--", "kelas" => isset($m->materi_kelas) && !is_string($m->materi_kelas) ? unserialize($m->materi_kelas ?? '') : []]; goto riJn5; lrgLo: $kode_mapel = $m->kode_mapel == null ? "--" : $m->kode_mapel; goto FHTxg; FHTxg: if ($m->jenis == "1") { goto qmteC; } goto ab4rm; BKBfK: T1X1J: goto pp2tu; kUyC3: kKc5R: goto BKBfK; PIdUD: $arrKelasMateri[] = ["id_materi" => $m->id_materi, "id_kjm" => $m->id_kjm, "jadwal" => $m->jadwal_materi, "kode" => $m->kode_materi, "judul_materi" => isset($m->judul_materi) ? $m->judul_materi : "Materi", "mapel" => $kode_mapel, "nama_mapel" => isset($m->nama_mapel) ? $m->nama_mapel : "--", "nama_guru" => isset($m->nama_guru) ? $m->nama_guru : "--", "materi_kelas" => isset($m->materi_kelas) ? $m->materi_kelas : "--", "tgl_mulai" => isset($m->tgl_mulai) ? $m->tgl_mulai : "--", "tgl_selesai" => isset($m->tgl_selesai) ? $m->tgl_selesai : "--", "kelas" => isset($m->materi_kelas) && !is_string($m->materi_kelas) ? unserialize($m->materi_kelas ?? '') : []]; goto kUyC3; IG8yq: qmteC: goto PIdUD; riJn5: goto kKc5R; goto IG8yq; pp2tu: } goto XfOCa; Cwuks: $this->load->model("Dashboard_model", "dashboard"); goto v0Lz7; iGKpv: $this->output_json(array("materi" => $arrKelasMateri, "tugas" => $arrKelasTugas)); goto CcUii; aUn19: $materi = $this->kelas->getAllKodeMateri($tp->id_tp, $smt->id_smt, $id_guru); goto qkm5v; XsZ2z: $id_guru = $this->input->get("id", true); goto UGO2m; z3Q3Y: $smt = $this->dashboard->getSemesterActive(); goto aUn19; v0Lz7: $this->load->model("Kelas_model", "kelas"); goto XsZ2z; UGO2m: $tp = $this->dashboard->getTahunActive(); goto z3Q3Y; X9vcI: $arrKelasTugas = []; goto a9f_s; qkm5v: $arrKelasMateri = []; goto X9vcI; CcUii: } public function getMateriMapel() { goto SK871; uTFyM: $arrKelasMateri = []; goto bR86b; mCSgO: $this->load->model("Kelas_model", "kelas"); goto bGUCc; B91bL: $id_guru = $this->input->get("id_guru", true); goto sBlZB; bR86b: $arrKelasTugas = []; goto E8nMA; iFQQ6: $smt = $this->dashboard->getSemesterActive(); goto m0Xql; Q6K4E: Xpcdh: goto hGWG1; m0Xql: $materi = $this->kelas->getKodeMateriMapel($tp->id_tp, $smt->id_smt, $id_mapel, $id_guru); goto uTFyM; SK871: $this->load->model("Dashboard_model", "dashboard"); goto mCSgO; CqMdo: foreach ($materi as $m) { goto KX094; jgWAL: if (isset($arrKelas[$m->jenis])) { goto nxut7; } goto vhUoH; k_P9f: $arrMateri = ["id_materi" => $m->id_materi, "id_kjm" => $m->id_kjm, "jadwal" => $m->jadwal_materi, "kode" => $m->kode_materi, "judul_materi" => $m->judul_materi, "mapel" => $kode_mapel, "nama_mapel" => isset($m->nama_mapel) ? $m->nama_mapel : "--", "guru" => $m->nama_guru, "nama_guru" => $m->nama_guru, "jenis" => $m->jenis, "materi_kelas" => isset($m->materi_kelas) ? $m->materi_kelas : "--", "tgl_mulai" => isset($m->tgl_mulai) ? $m->tgl_mulai : "--", "tgl_selesai" => isset($m->tgl_selesai) ? $m->tgl_selesai : "--"]; goto lTvPZ; NHSMM: iEZJx: goto siVba; vhUoH: $arrKelas[$m->jenis] = []; goto Pdcmo; CCuvx: Iqazo: goto UCCeG; Bfxlg: if (isset($arrKelasTugas[$m->id_kelas])) { goto Iqazo; } goto IOfpq; RSKrF: MvYpa: goto E8HP0; Eethv: if ($m->jenis == "1") { goto J97DD; } goto BTdY4; lTvPZ: if (isset($arrKelasMateri[$m->id_kelas])) { goto iEZJx; } goto uvuTi; UCCeG: $arrKelasTugas[$m->id_kelas][] = $arrTugas; goto LwE__; pe1Cf: if (in_array($m->id_kelas, $arrKelas[$m->jenis])) { goto EPfHI; } goto AcU2U; IOfpq: $arrKelasTugas[$m->id_kelas] = []; goto CCuvx; AcU2U: $arrKelas[$m->jenis][] = $m->id_kelas; goto yC2Io; siVba: $arrKelasMateri[$m->id_kelas][] = $arrMateri; goto rLpcK; E8HP0: vSRLT: goto ipOHx; yC2Io: EPfHI: goto RSKrF; Pdcmo: $arrKelas[$m->jenis][] = $m->id_kelas; goto e1DCY; uvuTi: $arrKelasMateri[$m->id_kelas] = []; goto NHSMM; e1DCY: goto MvYpa; goto g9Uw3; KX094: $kode_mapel = $m->kode_mapel == null ? "--" : $m->kode_mapel; goto Eethv; qcg4w: J97DD: goto k_P9f; rLpcK: Xttr0: goto jgWAL; g9Uw3: nxut7: goto pe1Cf; LwE__: goto Xttr0; goto qcg4w; BTdY4: $arrTugas = ["id_materi" => $m->id_materi, "id_kjm" => $m->id_kjm, "jadwal" => $m->jadwal_materi, "kode" => $m->kode_materi, "judul_materi" => $m->judul_materi, "mapel" => $kode_mapel, "nama_mapel" => isset($m->nama_mapel) ? $m->nama_mapel : "--", "guru" => $m->nama_guru, "nama_guru" => $m->nama_guru, "jenis" => $m->jenis, "materi_kelas" => isset($m->materi_kelas) ? $m->materi_kelas : "--", "tgl_mulai" => isset($m->tgl_mulai) ? $m->tgl_mulai : "--", "tgl_selesai" => isset($m->tgl_selesai) ? $m->tgl_selesai : "--"]; goto Bfxlg; ipOHx: } goto Q6K4E; E8nMA: $arrKelas = []; goto CqMdo; sBlZB: $tp = $this->dashboard->getTahunActive(); goto iFQQ6; hGWG1: $this->output_json(array("materi" => $arrKelasMateri, "tugas" => $arrKelasTugas, "kelas" => $arrKelas)); goto kiWrB; bGUCc: $id_mapel = $this->input->get("id", true); goto B91bL; kiWrB: } public function loadStatus() {
    error_reporting(0); 
    $this->load->model("Kelas_model", "kelas");
    $id_kelas = $this->input->post('id_kelas', true);
    $id_materi = $this->input->post('id_kjm', true);
    
    // Look up materi to get jenis for frontend
    $m = $this->db->where(['id_materi' => $id_materi])->get('kelas_materi')->row();

    $log = $this->kelas->getStatusMateriSiswaGuru($id_kelas, $id_materi);
    $data = array(
        'log' => $log,
        'materi' => (object)['jenis' => ($m ? $m->jenis : '1')]
    );
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}
    public function saveNilai() { 
        $method = $this->input->post("method", true); 
        $label = $this->input->post("label", true); 
        $id_log = $this->input->post("id_log", true); 
        $id_siswa = $this->input->post("id_siswa", true);
        $id_materi = $this->input->post("id_materi", true);
        $nilai = $this->input->post("nilai", true); 
        $catatan = $this->input->post("catatan", true); 
        
        $this->db->trans_begin();
        
        $this->db->where("id_siswa", $id_siswa); 
        $this->db->where("id_materi", $id_materi); 
        $q = $this->db->get("log_materi"); 
        
        $id_log_final = $id_log;
        if ($q->num_rows() > 0) { 
            $row = $q->row();
            $id_log_final = $row->id_log;
            $this->db->where("id_log", $id_log_final); 
            $update = $this->db->update("log_materi", ["nilai" => $nilai, "catatan" => $catatan]); 
        } else {
            $insert = [
                "id_log" => $id_log,
                "id_siswa" => $id_siswa,
                "id_materi" => $id_materi,
                "nilai" => $nilai,
                "catatan" => $catatan,
                "jam_ke" => 0,
                "log_type" => 1,
                "log_desc" => "Dinilai Manual",
                "address" => "",
                "agent" => "",
                "device" => "",
                "finish_time" => date('Y-m-d H:i:s')
            ];
            $update = $this->db->insert("log_materi", $insert); 
        }
        
        if ($update) { 
            try {
                $this->sendGradingNotification($id_log_final, $nilai, $catatan);
            } catch (Exception $e) {
                log_message('error', 'Gagal mengirim notifikasi nilai: ' . $e->getMessage());
            } catch (Throwable $e) {
                log_message('error', 'Gagal mengirim notifikasi nilai (fatal): ' . $e->getMessage());
            }
        }
        
        if ($this->db->trans_status() === FALSE || !$update) {
            $db_error = $this->db->error(); // Ambil error DB asli
            $this->db->trans_rollback();
            $this->output_json(['status' => false, 'message' => 'DB Error: ' . $db_error['message']]);
        } else {
            $this->db->trans_commit();
            
            // Trigger honor sync outside transaction
            $log_entry = $this->db->where(["id_log" => $id_log_final])->get("log_materi")->row();
            if ($log_entry) {
                $materi = $this->db->where(['id_materi' => $log_entry->id_materi])->get('kelas_materi')->row();
                if ($materi) {
                    try {
                        $this->load->model('Honor_model', 'honor');
                        $this->honor->sync_honor($materi->id_guru, $materi->id_tp, $materi->id_smt);
                    } catch (Exception $e) {
                        log_message('error', 'Gagal sync honor tapi nilai tetap aman: ' . $e->getMessage());
                    } catch (Throwable $e) {
                        log_message('error', 'Gagal sync honor tapi nilai tetap aman: ' . $e->getMessage());
                    }
                }
            }
            $this->output_json(['status' => true, 'message' => 'Nilai berhasil disimpan dan diamankan!']); 
        }
    } private function sendGradingNotification($id_log, $nilai, $catatan) { $log_entry = $this->db->where(["id_log" => $id_log])->get("log_materi")->row(); if ($log_entry) { $id_siswa = $log_entry->id_siswa; $id_materi = $log_entry->id_materi; $siswa = $this->db->where(['id_siswa' => $id_siswa])->get('master_siswa')->row(); if ($siswa) { $user_siswa = $this->db->where(['username' => $siswa->username])->get('users')->row(); if ($user_siswa) { $materi = $this->db->where(['id_materi' => $id_materi])->get('kelas_materi')->row(); $materi_title = $materi ? $materi->judul_materi : 'Tugas'; $this->load->model('Notifikasi_model', 'notif_m'); $this->notif_m->createNotifikasi([ 'user_id' => $user_siswa->id, 'role' => 'siswa', 'type' => 'nilai_keluar', 'title' => 'Nilai tugas keluar: ' . $nilai, 'body' => $materi_title . ($catatan ? ' — Catatan: ' . $catatan : ''), 'url' => 'siswa/hasil', 'metadata' => [ 'id_materi' => $id_materi, 'nilai' => $nilai ] ]); } } } } }
