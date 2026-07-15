<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php');
$c = str_replace('"" . base_url("dashboard") . ""', '\'" . base_url("dashboard") . "\'', $c);
file_put_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php', $c);
echo "Fixed all base_url syntax errors!\n";
