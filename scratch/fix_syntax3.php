<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php');

$search = 'show_error("Hanya Adminictrador yang diberi hak untuk mengakses halaman ini, <a href="". base_url("dashboard") . "">Kembali ke menu awal</a>", 403, "Akses Terlarang");';
$replace = 'show_error("Hanya Adminictrador yang diberi hak untuk mengakses halaman ini, <a href=\'". base_url("dashboard") . "\'>Kembali ke menu awal</a>", 403, "Akses Terlarang");';
$c = str_replace($search, $replace, $c);

file_put_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php', $c);
echo "Fixed show_error string!\n";
