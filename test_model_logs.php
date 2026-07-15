<?php
define('BASEPATH', 'C:/xampp/htdocs/garuda_cbt/system/');
define('APPPATH', 'C:/xampp/htdocs/garuda_cbt/application/');
define('VIEWPATH', 'C:/xampp/htdocs/garuda_cbt/application/views/');
require_once 'C:/xampp/htdocs/garuda_cbt/system/core/Common.php';
require_once 'C:/xampp/htdocs/garuda_cbt/system/database/DB.php';
$db = DB('default');

// Let's run getLogsSiswa code directly
$id_siswa = 2;
$id_tp = 4;
$id_smt = 1;

// How is getLogsSiswa defined in Kelas_model?
// Let's simulate it. In CI, it usually does:
$db->from("kelas_jadwal_materi");
$result = $db->get()->result();
print_r($result);
