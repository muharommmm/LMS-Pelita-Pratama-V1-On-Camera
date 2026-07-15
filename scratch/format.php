<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\scratch\siswa_hasil_line.txt');
$c = str_replace('goto', "\n" . 'goto', $c);
$c = str_replace(';', ";\n", $c);
$c = str_replace('{', "{\n", $c);
file_put_contents('C:\xampp\htdocs\garuda_cbt\scratch\siswa_hasil_line_fmt.php', $c);
echo "Done\n";
