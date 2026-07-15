<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\models\Cbt_model.php');
preg_match_all('/function getDataSiswa\([^\)]*\)/', $c, $matches);
print_r($matches);
