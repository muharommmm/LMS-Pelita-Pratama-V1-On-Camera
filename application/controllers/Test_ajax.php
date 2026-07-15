<?php
$_SERVER['HTTP_HOST'] = 'localhost';
class Test_ajax extends CI_Controller {
    public function index() {
        $this->load->model('Kelas_model', 'kelas');
        $id_kelas = 1;
        $id_materi = 2;
        $log = $this->kelas->getStatusMateriSiswaGuru($id_kelas, $id_materi);
        echo json_encode($log);
    }
}
