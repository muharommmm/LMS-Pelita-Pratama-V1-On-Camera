<?php
/**
 * Fix honor records yang terlanjur rejected karena bug cleanup.
 * Jalankan SEKALI setelah fix Honor_model diterapkan.
 */
define('ENVIRONMENT', 'development');
define('BASEPATH', dirname(__FILE__) . '/../system/');
define('APPPATH', dirname(__FILE__) . '/../application/');
define('VIEWPATH', dirname(__FILE__) . '/../application/views/');
define('FCPATH', dirname(__FILE__) . '/../');
require_once BASEPATH . 'core/CodeIgniter.php';
$db = get_instance()->db;

echo "=== FIXING WRONGLY REJECTED HONOR RECORDS ===\n\n";

// Find all active absensi sessions from manual_tutor
$sessions = $db->query("
    SELECT MIN(class_id) as class_id, mapel_id, date, time, jenis_kegiatan, tutor_id_input, 
           MIN(tp_id) as tp_id, MIN(smt_id) as smt_id
    FROM absensi_siswa 
    WHERE method = 'manual_tutor' AND tutor_id_input IS NOT NULL
    GROUP BY mapel_id, date, time, jenis_kegiatan, tutor_id_input
")->result();

$fixed = 0;
foreach ($sessions as $s) {
    $jk = !empty($s->jenis_kegiatan) ? $s->jenis_kegiatan : 'offline';
    $ref_id = crc32($s->class_id . '-' . $s->mapel_id . '-' . $s->date . '-' . $jk) & 0x7FFFFFFF;
    
    // Map jenis to type
    if ($jk == 'online') $type = 'online';
    elseif ($jk == 'check_task' || $jk == 'tugas') $type = 'check_task';
    elseif ($jk == 'create_cbt' || $jk == 'cbt') $type = 'create_cbt';
    else $type = 'offline';
    
    // Check if there's a rejected record with this ref
    $hr = $db->get_where('honor_records', [
        'tutor_id' => $s->tutor_id_input,
        'tp_id' => $s->tp_id,
        'smt_id' => $s->smt_id,
        'reference_id' => $ref_id,
        'status' => 'rejected'
    ])->row();
    
    if ($hr) {
        // Un-reject it: set back to pending
        $db->where('id_honor_record', $hr->id_honor_record);
        $db->update('honor_records', ['status' => 'pending']);
        echo "  FIXED HR:{$hr->id_honor_record} type:{$hr->type} ref:{$ref_id} -> pending\n";
        $fixed++;
    }
}

// Also clean up OLD records with reference_id = 2147483647 (the capped ones)
// These are unfixable orphans from before the crc32 fix
$db->where('reference_id', 2147483647);
$db->where('status', 'rejected');
$orphans = $db->delete('honor_records');
$orphan_count = $db->affected_rows();

echo "\n  Cleaned up {$orphan_count} orphaned records (ref=2147483647)\n";
echo "\nDone. Fixed {$fixed} honor records.\n";
