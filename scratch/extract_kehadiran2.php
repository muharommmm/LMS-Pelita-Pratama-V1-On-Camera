<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Siswa.php');
$p = strpos($c, 'public function kehadiran');
if ($p !== false) {
    echo substr($c, $p, 5000);
} else {
    echo "Method not found";
}
?>
