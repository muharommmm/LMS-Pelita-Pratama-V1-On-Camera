<?php
$pdo = new PDO("mysql:host=localhost;dbname=garuda", "root", "");

$q = $pdo->query("DESCRIBE kelas_materi");
$rows = $q->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Extra'] . "\n";
}
