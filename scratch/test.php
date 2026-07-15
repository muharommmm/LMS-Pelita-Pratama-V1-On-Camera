<?php
$c = file_get_contents('application/models/Kelas_model.php');
$pos = strpos($c, 'public function getMateriKelasSiswa');
if ($pos !== false) {
    echo "Found at $pos\n";
    echo substr($c, $pos, 100);
} else {
    echo "Not found";
}
