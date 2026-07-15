<?php
define('ENVIRONMENT', 'development');
define('BASEPATH', dirname(__FILE__) . '/../system/');
define('APPPATH', dirname(__FILE__) . '/../application/');
define('VIEWPATH', dirname(__FILE__) . '/../application/views/');
define('FCPATH', dirname(__FILE__) . '/../');
require_once BASEPATH . 'core/CodeIgniter.php';
$db = get_instance()->db;

echo "=== DIAGNOSTIK HONOR TUGAS ===\n\n";

$tutor_id = 2; // Assuming guru1 has id 2, we'll check all anyway just in case

echo "1. Data log_materi (tugas yang dinilai):\n";
$logs = $db->query("
    SELECT lm.id_log, lm.id_siswa, lm.id_materi as id_kjm, lm.nilai, lm.text,
           km.id_materi, km.judul_materi, km.jenis, km.id_guru, km.id_tp, km.id_smt
    FROM log_materi lm
    LEFT JOIN kelas_jadwal_materi kjm ON kjm.id_kjm = lm.id_materi
    LEFT JOIN kelas_materi km ON km.id_materi = kjm.id_materi
    ORDER BY lm.id_log DESC LIMIT 10
")->result();
print_r($logs);

echo "\n2. Data honor_records (type = check_task):\n";
$honors = $db->query("
    SELECT id_honor_record, tutor_id, type, reference_id, status, created_at
    FROM honor_records
    WHERE type = 'check_task'
    ORDER BY id_honor_record DESC LIMIT 10
")->result();
print_r($honors);

echo "\nDone.\n";
