<?php
$pdo = new PDO("mysql:host=localhost;dbname=garuda", "root", "");

echo "=== kelas_jadwal_materi ===\n";
$q = $pdo->query("SELECT * FROM kelas_jadwal_materi ORDER BY jadwal_materi DESC LIMIT 10");
print_r($q->fetchAll(PDO::FETCH_ASSOC));

echo "=== kelas_materi ===\n";
$q = $pdo->query("SELECT id_materi, kode_materi, judul_materi, jenis FROM kelas_materi ORDER BY id_materi DESC LIMIT 5");
print_r($q->fetchAll(PDO::FETCH_ASSOC));
