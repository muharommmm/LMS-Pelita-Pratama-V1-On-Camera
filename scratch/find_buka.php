<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Siswa.php');
$start = strpos($c, 'function bukaTugas');
$end = strpos($c, 'function', $start + 20);
echo substr($c, $start, $end-$start);
