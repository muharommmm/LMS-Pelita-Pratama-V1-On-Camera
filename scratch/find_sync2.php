<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Siswa.php');
$start = strpos($c, 'private function syncOfficialAttendance');
$end = strpos($c, 'public function', $start);
echo substr($c, $start, $end-$start);
