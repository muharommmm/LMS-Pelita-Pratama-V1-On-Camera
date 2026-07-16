<?php
define('ENVIRONMENT', 'development');
define('BASEPATH', dirname(__FILE__) . '/../system/');
define('APPPATH', dirname(__FILE__) . '/../application/');
define('VIEWPATH', dirname(__FILE__) . '/../application/views/');
define('FCPATH', dirname(__FILE__) . '/../');

require_once BASEPATH . 'core/CodeIgniter.php';

$db = get_instance()->db;

echo "=== COLUMNS cbt_jadwal ===\n";
$cols = $db->query("DESCRIBE cbt_jadwal")->result();
foreach ($cols as $col) {
    echo "{$col->Field} ({$col->Type})\n";
}

echo "\n=== SAMPLE DATA cbt_jadwal ===\n";
$rows = $db->limit(1)->get('cbt_jadwal')->result();
foreach ($rows as $row) {
    print_r($row);
}
