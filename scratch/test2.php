<?php
$c = file_get_contents('application/models/Kelas_model.php');
$pos = strpos($c, 'public function getStatusMateriSiswa(');
if ($pos !== false) {
    $end = strpos($c, '}', $pos + 200);
    echo substr($c, $pos, $end - $pos + 5);
} else {
    echo "Not found";
}
