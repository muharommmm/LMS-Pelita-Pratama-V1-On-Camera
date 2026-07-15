<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Siswa.php');
$start = strpos($c, 'private function bukaTugasMateri');
$end = strpos($c, 'public function saveLogTugas');
if ($end === false) $end = strlen($c);
echo substr($c, $start, $end-$start);
