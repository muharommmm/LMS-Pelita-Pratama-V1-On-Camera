<?php
$content = file_get_contents('application/controllers/Kelasnilai.php');
$start = strpos($content, 'public function loadNilaiMapel()');
if ($start !== false) {
    echo substr($content, $start, 3000);
} else {
    echo "loadNilaiMapel not found";
}
