<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php');

$new_func = <<<'EOD'
public function loadStatus() {
    error_reporting(0); 
    $id_kelas = $this->input->post('id_kelas', true);
    $id_materi = $this->input->post('id_kjm', true);
    $log = $this->kelas->getStatusMateriSiswa($id_kelas, $id_materi);
    $data = array('log' => $log);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}
EOD;

$c = preg_replace('/public function loadStatus\(\)\s*\{.*(?=public function saveNilai\(\))/s', $new_func . "\n", $c);

file_put_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php', $c);
echo "Replaced loadStatus\n";
