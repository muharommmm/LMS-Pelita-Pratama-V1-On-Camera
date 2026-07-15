<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Siswa.php');
$p = strpos($c, 'public function output_json');
if ($p !== false) {
    echo substr($c, $p, 300);
} else {
    echo "output_json not found directly.";
}
