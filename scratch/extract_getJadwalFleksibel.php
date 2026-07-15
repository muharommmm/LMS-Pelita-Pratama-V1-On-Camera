<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\models\Kelas_model.php');
$p = strpos($c, 'public function getJadwalFleksibel');
if ($p !== false) {
    echo substr($c, $p, 2000);
} else {
    echo "Method not found";
}
?>
