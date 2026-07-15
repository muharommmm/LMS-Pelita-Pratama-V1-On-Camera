<?php
$pdo = new PDO("mysql:host=localhost;dbname=garuda", "root", "");

$q = $pdo->query("SELECT id_materi, tgl_mulai, judul_materi FROM kelas_materi ORDER BY id_materi DESC LIMIT 10");
print_r($q->fetchAll(PDO::FETCH_ASSOC));
