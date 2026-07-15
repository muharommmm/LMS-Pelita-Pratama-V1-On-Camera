<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php');

$search = '<a href="". base_url("dashboard") . "">Kembali ke menu awal</a>';
$replace = '<a href="\' . base_url("dashboard") . \'">Kembali ke menu awal</a>';

$c = str_replace($search, $replace, $c);

// Or even simpler, let's just find `href="". base_url("dashboard") . ""`
$search2 = 'href="". base_url("dashboard") . ""';
$replace2 = 'href=\"". base_url("dashboard") . "\"';
// Actually, it's inside double quotes `show_error("... href="". base_url("dashboard") . ""> ...")`
// Wait, `show_error("Halaman ini membutuhkan akses Guru. <br> <a href="". base_url("dashboard") . "">Kembali ke menu awal</a>", 403, "Akses Terlarang");`
// The outer string uses `"` so it should be `href=\"". base_url("dashboard") . "\">` or `href=\'". base_url("dashboard") . "\'>`

$search3 = 'show_error("Halaman ini membutuhkan akses Guru. <br> <a href="". base_url("dashboard") . "">Kembali ke menu awal</a>", 403, "Akses Terlarang");';
$replace3 = 'show_error("Halaman ini membutuhkan akses Guru. <br> <a href=\'" . base_url("dashboard") . "\'>Kembali ke menu awal</a>", 403, "Akses Terlarang");';

$c = str_replace($search3, $replace3, $c);
file_put_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php', $c);
echo "Fixed show_error string!\n";
