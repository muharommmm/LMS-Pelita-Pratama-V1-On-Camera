<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\models\Kelas_model.php');
$c = str_replace('a.kode_materi, a.materi_kelas', 'a.kode_materi, a.judul_materi, a.materi_kelas', $c);
file_put_contents('C:\xampp\htdocs\garuda_cbt\application\models\Kelas_model.php', $c);
echo "Added judul_materi\n";
