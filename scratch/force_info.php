<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\views\kelas\status\data.php');

$search = <<<EOD
                      $('#info-jam').text(': ' + (detail.jam_ke || '-'));
                      $('#info-dari').text(': ' + (waktu.dari || '-'));
                      $('#info-sampai').text(': ' + (waktu.sampai || '-'));
EOD;

$replace = <<<EOD
                      $('#info-jam').text(': -');
                      $('#info-dari').text(': -');
                      $('#info-sampai').text(': -');
EOD;

$c = str_replace($search, $replace, $c);
file_put_contents('C:\xampp\htdocs\garuda_cbt\application\views\kelas\status\data.php', $c);
echo "Fixed detail JS\n";
