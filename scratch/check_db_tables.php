<?php
$pdo = new PDO('mysql:host=localhost;dbname=garuda', 'root', '');

echo "=== Tables with 'materi' in name ===\n";
$tables = $pdo->query("SHOW TABLES LIKE '%materi%'")->fetchAll(PDO::FETCH_COLUMN);
foreach($tables as $t) { 
    $count = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn(); 
    echo "$t: $count rows\n"; 
}

echo "\n=== Tables with 'log' in name ===\n";
$tables = $pdo->query("SHOW TABLES LIKE '%log%'")->fetchAll(PDO::FETCH_COLUMN);
foreach($tables as $t) { 
    $count = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn(); 
    echo "$t: $count rows\n"; 
}

echo "\n=== log_materi structure ===\n";
$cols = $pdo->query("DESCRIBE log_materi")->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $c) echo $c['Field'].' ('.$c['Type'].")\n";

echo "\n=== kelas_materi structure ===\n";
$cols = $pdo->query("DESCRIBE kelas_materi")->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $c) echo $c['Field'].' ('.$c['Type'].")\n";

echo "\n=== kelas_jadwal_materi structure ===\n";
$cols = $pdo->query("DESCRIBE kelas_jadwal_materi")->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $c) echo $c['Field'].' ('.$c['Type'].")\n";

echo "\n=== Sample log_materi (5 rows) ===\n";
$rows = $pdo->query("SELECT * FROM log_materi LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r) { print_r($r); }

echo "\n=== Sample kelas_jadwal_materi (5 rows) ===\n";
$rows = $pdo->query("SELECT * FROM kelas_jadwal_materi LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r) { print_r($r); }

echo "\n=== Sample kelas_materi (3 rows) ===\n";
$rows = $pdo->query("SELECT * FROM kelas_materi LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r) { print_r($r); }

// Check for any elearning-related tables
echo "\n=== Tables with 'elearning' in name ===\n";
$tables = $pdo->query("SHOW TABLES LIKE '%elearning%'")->fetchAll(PDO::FETCH_COLUMN);
if (empty($tables)) echo "No elearning tables found\n";
foreach($tables as $t) echo "$t\n";

// All tables
echo "\n=== ALL TABLES ===\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach($tables as $t) echo "$t\n";
