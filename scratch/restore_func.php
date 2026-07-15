<?php
$lines = file('C:\xampp\htdocs\garuda_cbt\application\models\Kelas_model.php');
$lines[103] = "public function loadJadwalSiswaHariIni(\$id_tp, \$id_smt, \$id_kelas, \$id_hari, \$termasuk_istirahat = false) {\n" . $lines[103];
file_put_contents('C:\xampp\htdocs\garuda_cbt\application\models\Kelas_model.php', implode("", $lines));
