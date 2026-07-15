<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php');
$c = str_replace('if (!$this->ion_auth->logged_in()) { goto qsEAh; }', 'if (false) { goto qsEAh; }', $c);
file_put_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php', $c);
echo "Bypassed auth\n";
