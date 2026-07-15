<?php
$f = 'C:\xampp\htdocs\garuda_cbt\application\controllers\Siswa.php';
$c = file_get_contents($f);
$c = str_replace('getNilaiMateriSiswa(', 'getNilaiMateriSiswaFlex(', $c);
file_put_contents($f, $c);
echo "Siswa.php updated.\n";
