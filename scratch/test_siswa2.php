<?php
$mysqli = new mysqli("localhost", "root", "", "garuda");
$res = $mysqli->query("SELECT * FROM master_siswa LIMIT 1");
$row = $res->fetch_assoc();
print_r($row);

ob_start();
require_once 'C:\xampp\htdocs\garuda_cbt\index.php';
ob_end_clean();

$CI =& get_instance();
$CI->load->model("Cbt_model", "cbt");
$username = $row['username'];
$tp = 4;
$smt = 1;

$siswa = $CI->cbt->getDataSiswa($username, $tp, $smt);
echo "Siswa: ";
var_dump($siswa);
