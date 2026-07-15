<?php
$file = 'C:\xampp\htdocs\garuda_cbt\application\controllers\Siswa.php';
$content = file_get_contents($file);

// The corrupted block starts from `} public function bukaMateri` and ends at `HDA9Q: } public function saveLogMateri() {`
$start_marker = "} public function bukaMateri(\$id_kjm, \$jamke) {";
$end_marker = "HDA9Q: } public function saveLogMateri() {";

$start_pos = strpos($content, $start_marker);
$end_pos = strpos($content, $end_marker);

if ($start_pos !== false && $end_pos !== false) {
    $end_pos += strlen($end_marker);

    // The clean block, directly copied from previous log output + adding 'jenis' => $jenis
    $clean_block = '} public function bukaMateri($id_kjm, $jamke) { $this->bukaTugasMateri($id_kjm, $jamke, "\x31"); } public function bukaTugas($id_kjm, $jamke) { $this->bukaTugasMateri($id_kjm, $jamke, "\x32"); } private function bukaTugasMateri($id_kjm, $jamke, $jenis) { goto ALIKC; stbxA: $data["\x74\160"] = $this->dashboard->getTahun(); goto QHlMD; g2Ch5: $this->load->view("\155\145\155\142\x65\x72\x73\57\x73\x69\x73\x77\141\x2f\164\x65\155\x70\154\141\x74\x65\163\57\x68\145\x61\144\x65\162", $data); goto Wsecp; Wsecp: $this->load->view("\155\x65\x6d\142\x65\162\163\57\x73\x69\x73\x77\141\x2f\155\141\x74\145\162\151\57\166\x69\x65\x77", $data); goto WJ9zl; LofsZ: $user = $this->ion_auth->user()->row(); goto loxmy; yI1nl: $tp = $this->dashboard->getTahunActive(); goto LRe8R; jWkG7: $data["\162\165\x6e\x6e\x69\156\147\x5f\x74\x65\x78\x74"] = $this->dashboard->getRunningText(); goto g2Ch5; WD8Ii: $data["\152\x61\x6d\x6b\x65"] = $jamke; goto xi_8C; QHlMD: $data["\164\160\x5f\x61\x63\x74\x69\x76\145"] = $tp; goto zQmrz; LRe8R: $smt = $this->dashboard->getSemesterActive(); goto LofsZ; xi_8C: $data["\155\x61\x74\x65\162\x69"] = $this->kelas->getMateriKelasSiswa($id_kjm, $jenis); goto JFmz_; ALIKC: $this->load->model("\104\x61\x73\x68\x62\x6f\x61\x72\x64\137\x6d\x6f\x64\145\154", "\x64\141\x73\150\x62\157\x61\x72\144"); goto alLY4; JFmz_: $logs = $this->kelas->getStatusMateriSiswa($id_kjm); goto QrmXl; WJ9zl: $this->load->view("\x6d\x65\x6d\142\x65\x72\163\57\x73\x69\163\167\x61\x2f\164\x65\155\x70\154\x61\164\x65\163\x2f\146\x6f\x6f\164\x65\162", $data); goto HDA9Q; KSE84: $logs[$siswa->id_siswa]->file = unserialize($logs[$siswa->id_siswa]->file ?? \'\'); goto Eihjw; alLY4: $this->load->model("\x4b\x65\x6c\x61\163\x5f\155\x6f\144\x65\154", "\x6b\145\154\141\x73"); goto shsBq; xbeTW: $data["\x6b\x6a\x6d"] = $id_kjm; goto eKw_H; loxmy: $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt); goto Tpu48; eKw_H: $data["\154\x6f\x67\163"] = isset($logs[$siswa->id_siswa]) ? $logs[$siswa->id_siswa] : null; goto jWkG7; I4Pgv: $data["\163\155\164\137\141\143\164\x69\x76\x65"] = $smt; goto WD8Ii; shsBq: $this->load->model("\x43\142\164\x5f\x6d\157\144\x65\x6c", "\x63\x62\164"); goto yI1nl; QrmXl: if (!isset($logs[$siswa->id_siswa])) { goto bC_Mw; } goto KSE84; zQmrz: $data["\x73\x6d\x74"] = $this->dashboard->getSemester(); goto I4Pgv; Eihjw: bC_Mw: goto xbeTW; Tpu48: $data = ["\165\163\145\162" => $user, "\x73\x69\163\167\141" => $siswa, "\x6a\165\x64\x75\x6c" => $jenis == "\61" ? "\115\x61\164\145\x72\151" : "\x54\165\147\141\x73", "\x73\165\142\x6a\x75\144\165\154" => "\113\x65\x72\x6a\x61\x6b\141\156", "\x73\x65\x74\164\x69\x6e\147" => $this->dashboard->getSetting(), "jenis" => $jenis]; goto stbxA; HDA9Q: } public function saveLogMateri() {';

    // Replace
    $content = substr_replace($content, $clean_block, $start_pos, $end_pos - $start_pos);

    file_put_contents($file, $content);
    echo "Successfully repaired Siswa.php\n";
} else {
    echo "Could not find markers in Siswa.php\n";
}
