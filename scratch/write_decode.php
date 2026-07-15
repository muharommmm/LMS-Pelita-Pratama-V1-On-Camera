<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\models\Kelas_model.php');
// Decode octal \123 and hex \xAB
$c = preg_replace_callback('/\\\\([0-7]{1,3})/', function($m) { return chr(octdec($m[1])); }, $c);
$c = preg_replace_callback('/\\\\x([0-9A-Fa-f]{1,2})/', function($m) { return chr(hexdec($m[1])); }, $c);

file_put_contents('C:\xampp\htdocs\garuda_cbt\scratch\Kelas_model_decoded_test.php', $c);
