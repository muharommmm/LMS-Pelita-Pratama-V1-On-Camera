<?php
$c = file_get_contents('c:\xampp\htdocs\temp_extract\garuda_cbt\application\controllers\Kelasstatus.php');
$c = preg_replace_callback('/\\\\(?:x[0-9a-fA-F]{1,2}|[0-7]{1,3})/', function($m){ return stripcslashes($m[0]); }, $c);
file_put_contents('c:\xampp\htdocs\temp_extract\kelasstatus_deobf.php', $c);
echo "Done";
