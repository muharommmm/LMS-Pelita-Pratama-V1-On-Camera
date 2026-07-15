<?php
define('ENVIRONMENT', 'development');
define('BASEPATH', dirname(__FILE__) . '/../system/');
define('APPPATH', dirname(__FILE__) . '/../application/');
define('VIEWPATH', dirname(__FILE__) . '/../application/views/');
define('FCPATH', dirname(__FILE__) . '/../');
require_once BASEPATH . 'core/CodeIgniter.php';

$db = get_instance()->db;

echo "--- COLUMNS ---\n";
$cols = $db->query("SHOW COLUMNS FROM honor_records")->result();
print_r($cols);
