<?php
$content = file_get_contents('application/models/Kelas_model.php');

// Find the new method
$search = 'public function getRekapMateriSemester($id_kelas, $id_materi = null) {
        $this->db->select("a.id_siswa, a.id_log, a.log_time, a.finish_time, a.id_materi';

$pos = strpos($content, $search);
if ($pos !== false) {
    echo "NEW method found at position: $pos\n";
    echo "Method snippet:\n";
    echo substr($content, $pos, 800) . "\n";
} else {
    echo "NEW method NOT found. Checking for old obfuscated method...\n";
    $old = strpos($content, 'function getRekapMateriSemester');
    if ($old !== false) {
        echo "Old method found at position: $old\n";
        echo substr($content, $old, 300) . "\n";
    } else {
        echo "Method not found at all!\n";
    }
}
