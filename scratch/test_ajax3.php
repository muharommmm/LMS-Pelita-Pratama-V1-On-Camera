<?php
// Simulate the backend loadStatus output exactly
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['id_kelas'] = '2';
$_POST['id_kjm'] = '2';

// Load CodeIgniter
ob_start();
require_once 'C:\xampp\htdocs\garuda_cbt\index.php';
ob_end_clean();

$CI =& get_instance();
$CI->load->model("Kelas_model", "kelas");
$id_kelas = $CI->input->post('id_kelas', true);
$id_kjm = $CI->input->post('id_kjm', true);
$m = $CI->db->get_where('kelas_materi', ['id_kjm' => $id_kjm])->row();
$id_materi = $m ? $m->id_materi : $id_kjm; 

$log = $CI->kelas->getStatusMateriSiswa($id_kelas, $id_materi);
$data = array(
    'log' => $log,
    'materi' => (object)['jenis' => ($m ? $m->jenis : '1')]
);

echo json_encode($data);
