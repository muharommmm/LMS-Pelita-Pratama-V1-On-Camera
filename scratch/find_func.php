<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Siswa.php');
echo "Has syncOfficialAttendance: " . (strpos($c, 'syncOfficialAttendance') !== false ? 'YES' : 'NO') . "\n";
echo "Has getLogAktivitasSiswa: " . (strpos($c, 'getLogAktivitasSiswa') !== false ? 'YES' : 'NO') . "\n";
