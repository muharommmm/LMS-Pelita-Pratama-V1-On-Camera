<?php
$lines = file('C:\xampp\htdocs\garuda_cbt\application\models\Kelas_model.php');
// Remove lines 103 to 110 (which is index 103 to 110 in 0-based if line 104 is index 103)
// Let's just array_splice 
array_splice($lines, 103, 9);
file_put_contents('C:\xampp\htdocs\garuda_cbt\application\models\Kelas_model.php', implode("", $lines));
