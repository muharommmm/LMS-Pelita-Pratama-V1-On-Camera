<?php
ob_start();
require_once 'C:\xampp\htdocs\garuda_cbt\index.php';
ob_end_clean();

$CI =& get_instance();
$CI->load->model("Cbt_model", "cbt");
$username = '1234567890'; // Assuming standard student username
$tp = 4;
$smt = 1;

$siswa = $CI->cbt->getDataSiswa($username, $tp, $smt);
echo "Siswa: ";
var_dump($siswa);
