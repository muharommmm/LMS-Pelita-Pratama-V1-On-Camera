<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php');
$c = str_replace('href="". base_url("dashboard") . ""', 'href=\"". base_url("dashboard") . "\"', $c);
$c = str_replace('<a href="". base_url("dashboard") . "">', '<a href=\'". base_url("dashboard") . "\'>', $c);
file_put_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php', $c);
