<?php
define('ENVIRONMENT', 'development');
define('BASEPATH', dirname(__FILE__) . '/../system/');
define('APPPATH', dirname(__FILE__) . '/../application/');
define('VIEWPATH', dirname(__FILE__) . '/../application/views/');
define('FCPATH', dirname(__FILE__) . '/../');
require_once BASEPATH . 'core/CodeIgniter.php';
$CI = get_instance();
$db = $CI->db;

echo "=== FIXING TASK HONOR ORPHANS ===\n";
// Delete all reference_id = 2147483647 again just in case
$db->where('reference_id', 2147483647);
$db->delete('honor_records');
echo "Deleted " . $db->affected_rows() . " broken max-int records.\n";

$CI->load->model('Honor_model', 'honor');
$tp = $CI->db->get_where('master_tp', ['active' => 1])->row();
$smt = $CI->db->get_where('master_smt', ['active' => 1])->row();

echo "\nRe-running sync_honor for Tutor 1...\n";
$CI->honor->sync_honor(1, $tp->id_tp, $smt->id_smt);

echo "Re-running sync_honor for Tutor 2...\n";
$CI->honor->sync_honor(2, $tp->id_tp, $smt->id_smt);

echo "Done.\n";
