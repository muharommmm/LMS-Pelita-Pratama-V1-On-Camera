<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Siswa.php');
$start = strpos($c, 'public function saveFileMateriSelesai');
$end = strpos($c, 'public function saveFileTugasSelesai', $start);
if ($start !== false && $end !== false) {
    echo substr($c, $start, $end-$start);
} else {
    echo "Could not find bounds";
}
