<?php
ob_start();
require_once 'C:\xampp\htdocs\garuda_cbt\index.php';
ob_end_clean();

$CI =& get_instance();
$CI->load->model("Cbt_model", "cbt");
$CI->load->model("Kelas_model", "kelas");

$id_kjm = 4;
$jamke = 1;
$jenis = 2; // Tugas

$materi = $CI->kelas->getMateriKelasSiswa($id_kjm, $jenis);
$siswa = $CI->cbt->getDataSiswa('muham518', 4, 1);

var_dump(is_object($materi));
var_dump(is_object($siswa));
