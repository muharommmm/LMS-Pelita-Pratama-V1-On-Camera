<?php
define('ENVIRONMENT', 'development');
define('BASEPATH', dirname(__FILE__) . '/../system/');
define('APPPATH', dirname(__FILE__) . '/../application/');
define('VIEWPATH', dirname(__FILE__) . '/../application/views/');
define('FCPATH', dirname(__FILE__) . '/../');
require_once BASEPATH . 'core/CodeIgniter.php';
$db = get_instance()->db;

echo "=== 1. LAST 15 ABSENSI_SISWA (raw) ===\n";
$abs = $db->query("SELECT id_absensi, student_id, class_id, mapel_id, date, time, jenis_kegiatan, method, tutor_id_input FROM absensi_siswa ORDER BY id_absensi DESC LIMIT 15")->result();
foreach ($abs as $a) {
    echo "  ID:{$a->id_absensi} stu:{$a->student_id} cls:{$a->class_id} mapel:{$a->mapel_id} date:{$a->date} time:{$a->time} jenis:{$a->jenis_kegiatan} method:{$a->method} tutor:{$a->tutor_id_input}\n";
}

echo "\n=== 2. LAST 15 HONOR_RECORDS (raw) ===\n";
$hon = $db->query("SELECT id_honor_record, tutor_id, type, reference_id, qty, rate, amount, status, created_at FROM honor_records ORDER BY id_honor_record DESC LIMIT 15")->result();
foreach ($hon as $h) {
    echo "  HR:{$h->id_honor_record} tutor:{$h->tutor_id} type:{$h->type} ref:{$h->reference_id} status:{$h->status} rate:{$h->rate} amount:{$h->amount} created:{$h->created_at}\n";
}

echo "\n=== 3. HONOR_RECORDS TABLE STRUCTURE (UNIQUE KEYS) ===\n";
$idx = $db->query("SHOW CREATE TABLE honor_records")->row();
echo $idx->{'Create Table'} . "\n";

echo "\n=== 4. SIMULATE CRC32 for recent absensi sessions ===\n";
$sessions = $db->query("
    SELECT MIN(class_id) as class_id, mapel_id, date, jenis_kegiatan, time, COUNT(*) as cnt 
    FROM absensi_siswa 
    WHERE tutor_id_input IS NOT NULL AND method='manual_tutor'
    GROUP BY mapel_id, date, time, jenis_kegiatan 
    ORDER BY date DESC, time DESC 
    LIMIT 10
")->result();
foreach ($sessions as $s) {
    $jk = !empty($s->jenis_kegiatan) ? $s->jenis_kegiatan : 'offline';
    $raw_crc = crc32($s->class_id . '-' . $s->mapel_id . '-' . $s->date . '-' . $jk);
    $fixed_crc = $raw_crc & 0x7FFFFFFF;
    echo "  cls:{$s->class_id} mapel:{$s->mapel_id} date:{$s->date} time:{$s->time} jenis:{$jk} cnt:{$s->cnt}\n";
    echo "    raw_crc={$raw_crc}  fixed_crc={$fixed_crc}\n";
    
    // Check if honor record exists for this
    $hr = $db->query("SELECT id_honor_record, type, status, reference_id FROM honor_records WHERE reference_id = ?", [$fixed_crc])->row();
    if ($hr) {
        echo "    -> FOUND honor_record HR:{$hr->id_honor_record} type:{$hr->type} status:{$hr->status}\n";
    } else {
        // Check with raw crc
        $hr2 = $db->query("SELECT id_honor_record, type, status, reference_id FROM honor_records WHERE reference_id = ?", [$raw_crc])->row();
        if ($hr2) {
            echo "    -> FOUND (raw) honor_record HR:{$hr2->id_honor_record} type:{$hr2->type} status:{$hr2->status}\n";
        } else {
            echo "    -> NO matching honor_record!\n";
        }
    }
}
