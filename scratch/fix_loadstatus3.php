<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php');

$old_func = <<<'EOD'
public function loadStatus() {
    error_reporting(0); 
    $this->load->model("Kelas_model", "kelas");
    $id_kelas = $this->input->post('id_kelas', true);
    $id_kjm = $this->input->post('id_kjm', true);
    
    // Look up real id_materi from kelas_materi based on id_kjm
    $m = $this->db->get_where('kelas_materi', ['id_kjm' => $id_kjm])->row();
    $id_materi = $m ? $m->id_materi : $id_kjm; // fallback to id_kjm if not found just in case

    $log = $this->kelas->getStatusMateriSiswa($id_kelas, $id_materi);
    $data = array('log' => $log);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}
EOD;

$new_func = <<<'EOD'
public function loadStatus() {
    error_reporting(0); 
    $this->load->model("Kelas_model", "kelas");
    $id_kelas = $this->input->post('id_kelas', true);
    $id_kjm = $this->input->post('id_kjm', true);
    
    // Look up real id_materi from kelas_materi based on id_kjm
    $m = $this->db->get_where('kelas_materi', ['id_kjm' => $id_kjm])->row();
    $id_materi = $m ? $m->id_materi : $id_kjm; // fallback to id_kjm if not found just in case

    $log = $this->kelas->getStatusMateriSiswa($id_kelas, $id_materi);
    $data = array(
        'log' => $log,
        'materi' => (object)['jenis' => ($m ? $m->jenis : '1')]
    );
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}
EOD;

$c = str_replace($old_func, $new_func, $c);
file_put_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php', $c);
echo "Added materi to JSON\n";
