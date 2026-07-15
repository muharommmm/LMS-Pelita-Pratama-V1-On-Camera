<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Siswa.php');
$start = strpos($c, 'public function saveFileTugasSelesai');
$end = strpos($c, 'public function', $start + 10);
if ($end === false) $end = strlen($c);
echo substr($c, $start, $end-$start);
