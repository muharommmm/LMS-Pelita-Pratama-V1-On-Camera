<?php
// Test getMateriKelasSiswa
ob_start();
require_once 'C:\xampp\htdocs\garuda_cbt\index.php';
ob_end_clean();

$CI =& get_instance();
$CI->load->model("Kelas_model", "kelas");
$id_kjm = 4; // Example ID
$jenis = 2;

$materi = $CI->kelas->getMateriKelasSiswa($id_kjm, $jenis);
echo "Materi: ";
var_dump($materi);
