<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\scratch\dashboard_new.php');
$c = str_replace("\0", "", $c);
file_put_contents('C:\xampp\htdocs\garuda_cbt\scratch\dashboard_recovered.php', $c);
