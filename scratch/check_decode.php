<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\models\Kelas_model.php');
// Decode octal \123 and hex \xAB
$c = preg_replace_callback('/\\\\([0-7]{1,3})/', function($m) { return chr(octdec($m[1])); }, $c);
$c = preg_replace_callback('/\\\\x([0-9A-Fa-f]{1,2})/', function($m) { return chr(hexdec($m[1])); }, $c);

// Print occurrences of ma3ter, celas, jabadan
if (preg_match_all('/ma3ter|celas|jabadan/i', $c, $matches)) {
    echo "Found corruptions:\n";
    print_r(array_count_values($matches[0]));
} else {
    echo "No corruptions found!\n";
}
