<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php');
$c = str_replace('goto xXXMB;', 'goto E5SMd;', $c);
file_put_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php', $c);
echo "Bypassed second auth\n";
