<?php
define('ENVIRONMENT', 'development');
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('BASEPATH', FCPATH . 'system/');
define('APPPATH', FCPATH . 'application/');

require BASEPATH . 'core/Common.php';

$c = new mysqli('localhost', 'root', '', 'garuda'); 
$r = $c->query('SELECT * FROM log_materi LIMIT 1'); 
if($r) {
    while ($row = $r->fetch_assoc()) var_dump($row['file']);
}
