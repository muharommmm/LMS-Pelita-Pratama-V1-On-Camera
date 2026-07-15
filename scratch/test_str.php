<?php
$content = file_get_contents('application/controllers/Kelasstatus.php');

$search1 = '"kode" => $m->kode_materi, "mapel" => $kode_mapel, "kelas" => unserialize($m->materi_kelas ?? \'\')';
$search2 = '"kode" => $m->kode_materi, "judul_materi" => $m->judul_materi, "mapel" => $kode_mapel, "guru" => $m->nama_guru, "jenis" => $m->jenis';

echo "Search 1: " . (strpos($content, $search1) !== false ? "FOUND" : "NOT FOUND") . "\n";
echo "Search 2: " . (strpos($content, $search2) !== false ? "FOUND" : "NOT FOUND") . "\n";
