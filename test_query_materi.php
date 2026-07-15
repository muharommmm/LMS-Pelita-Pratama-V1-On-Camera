<?php
$pdo = new PDO("mysql:host=localhost;dbname=garuda", "root", "");

$id_kjm = '141202607081218';
$jenis = 2;

echo "=== getMateriKelasSiswa Query ===\n";
$q = $pdo->prepare("SELECT a.id_kjm, a.id_materi, a.jadwal_materi, b.*, c.nama_guru, c.foto, e.id_mapel, e.nama_mapel, d.mapel_kelas as kelas_guru
FROM kelas_jadwal_materi a
JOIN kelas_materi b ON a.id_materi=b.id_materi
JOIN master_guru c ON b.id_guru=c.id_guru
JOIN jabatan_guru d ON b.id_guru=d.id_guru
JOIN master_mapel e ON b.id_mapel=e.id_mapel
WHERE a.jenis = :jenis AND a.id_kjm = :id_kjm");
$q->execute([':jenis' => $jenis, ':id_kjm' => $id_kjm]);
$res = $q->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
