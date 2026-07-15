<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\models\Notifikasi_model.php');
echo "Has createNotifikasi: " . (strpos($c, 'createNotifikasi') !== false ? 'YES' : 'NO') . "\n";
