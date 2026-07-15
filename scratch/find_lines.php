<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\models\Kelas_model.php');
$lines = explode("\n", $c);
foreach($lines as $i => $line) {
    if (strpos($line, 'public function getJadwalFleksibel') !== false) {
        echo "Start Line: " . ($i+1) . "\n";
    }
    if (strpos($line, 'public function getNilaiMateriSiswaFlex') !== false) {
        echo "End Line (exclusive): " . ($i+1) . "\n";
        break;
    }
}
?>
