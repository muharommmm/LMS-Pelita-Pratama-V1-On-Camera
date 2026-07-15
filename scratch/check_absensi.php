<?php
define('ENVIRONMENT', 'development');
define('BASEPATH', dirname(__FILE__) . '/../system/');
define('APPPATH', dirname(__FILE__) . '/../application/');
define('VIEWPATH', dirname(__FILE__) . '/../application/views/');
define('FCPATH', dirname(__FILE__) . '/../');

require_once BASEPATH . 'core/CodeIgniter.php';

$db = get_instance()->db;
$result = $db->query("SHOW CREATE TABLE absensi_siswa")->row();
print_r($result);
