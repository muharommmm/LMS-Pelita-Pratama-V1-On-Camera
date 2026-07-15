<?php
$_SERVER['DOCUMENT_ROOT'] = 'C:\xampp\htdocs';
define('BASEPATH', 'C:\xampp\htdocs\garuda_cbt\system\\');
define('APPPATH', 'C:\xampp\htdocs\garuda_cbt\application\\');

// I will just use raw queries to simulate what happens for user 56 (Rageya, username ragey545)
$conn = new mysqli('localhost', 'root', '', 'garuda');

$username = 'ragey545';
$tp = 4; // TP 2025/2026
$smt = 1; // Ganjil

// Simulate getDataSiswa
$sql = "SELECT a.id_siswa, a.nama, b.id_kelas, b.id_tp, b.id_smt 
        FROM master_siswa a 
        LEFT JOIN kelas_siswa b ON a.id_siswa = b.id_siswa 
        WHERE a.username = '$username' AND b.id_tp = $tp AND b.id_smt = $smt";
$result = $conn->query($sql);
$siswa = $result->fetch_object();

echo "Siswa id_kelas: " . ($siswa->id_kelas ?? 'NULL') . "\n";

if ($siswa) {
    $class_id = $siswa->id_kelas;
    // Simulate get_ebooks_for_student
    $sql_eb = "SELECT id_ebook, title, class_id, mapel_id FROM ebooks
               WHERE (class_id = '' OR class_id IS NULL OR FIND_IN_SET('$class_id', class_id) > 0)";
    $res_eb = $conn->query($sql_eb);
    echo "Ebooks found for class $class_id:\n";
    while($row = $res_eb->fetch_object()) {
        echo "- ID: {$row->id_ebook}, Title: {$row->title}, Mapel: {$row->mapel_id}\n";
    }
} else {
    echo "Siswa not found for TP/SMT.\n";
}
?>
