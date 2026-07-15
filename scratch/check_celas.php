<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\models\Kelas_model.php');
if (strpos($c, 'ma3ter') !== false || strpos($c, 'celas') !== false || strpos($c, 'jabadan') !== false) {
    echo "Found unencoded corruption!\n";
} else {
    echo "Not found unencoded.\n";
}

// What if it's encoded? Let's decode and search again using a proper eval or string unescape!
// The previous decoder used hexdec and octdec, but what if there are mixed characters?
function unescape_php_string($str) {
    return stripcslashes(str_replace(['\x', '\\\\'], ['\\x', '\\'], $str));
}

$c2 = preg_replace_callback('/"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"/', function($m) {
    eval("\$res = \"$m[1]\";");
    return '"'.$res.'"';
}, $c);

file_put_contents('C:\xampp\htdocs\garuda_cbt\scratch\Kelas_model_decoded_test2.php', $c2);

if (preg_match_all('/ma3ter|celas|jabadan/i', $c2, $matches)) {
    echo "Found encoded corruption!\n";
    print_r(array_count_values($matches[0]));
} else {
    echo "Still not found!\n";
}
