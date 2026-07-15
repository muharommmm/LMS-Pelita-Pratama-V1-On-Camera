<?php
define('ENVIRONMENT', 'development');
define('BASEPATH', dirname(__FILE__) . '/../system/');
define('APPPATH', dirname(__FILE__) . '/../application/');
define('VIEWPATH', dirname(__FILE__) . '/../application/views/');
define('FCPATH', dirname(__FILE__) . '/../');

require_once BASEPATH . 'core/CodeIgniter.php';

$db = get_instance()->db;

// Check jadwal_fleksibel
echo "=== SAMPLE DATA jadwal_fleksibel ===\n";
$rows = $db->limit(5)->get('jadwal_fleksibel')->result();
foreach ($rows as $row) {
    echo "class_id={$row->class_id}, mapel_id={$row->mapel_id}, day={$row->day}, jenis_kegiatan='" . (property_exists($row,'jenis_kegiatan') ? $row->jenis_kegiatan : 'COLUMN NOT FOUND') . "'\n";
}

echo "\n=== COLUMNS jadwal_fleksibel ===\n";
$cols = $db->query("DESCRIBE jadwal_fleksibel")->result();
foreach ($cols as $col) {
    echo "{$col->Field} ({$col->Type})\n";
}
