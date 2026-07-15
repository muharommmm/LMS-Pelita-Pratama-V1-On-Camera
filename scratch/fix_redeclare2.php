<?php
$file = 'application/models/Kelas_model.php';
$content = file_get_contents($file);

// Strip everything from public function getMateriTugasSiswaDeadline
$start = strpos($content, 'public function getMateriTugasSiswaDeadline');
if ($start !== false) {
    $content = substr($content, 0, $start) . "\n}";
    file_put_contents($file, $content);
    echo "Stripped to clean state!\n";
}
