<?php
$pdo = new PDO("mysql:host=localhost;dbname=garuda", "root", "");

function desc($pdo, $table) {
    echo "=== DESCRIBE $table ===\n";
    $q = $pdo->query("DESCRIBE $table");
    if ($q) {
        $rows = $q->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            echo $row['Field'] . " - " . $row['Type'] . "\n";
        }
    } else {
        echo "Table not found.\n";
    }
}

desc($pdo, 'kelas_jadwal_materi');
desc($pdo, 'log_materi');
//desc($pdo, 'log_tugas'); // Let's check log_materi first. (Actually Garuda uses log_materi for both)

// Check a sample new id_kjm in kelas_jadwal_materi
echo "\n=== Sample Data in kelas_jadwal_materi ===\n";
$q = $pdo->query("SELECT id_kjm, id_materi, id_kelas FROM kelas_jadwal_materi ORDER BY jadwal_materi DESC LIMIT 5");
print_r($q->fetchAll(PDO::FETCH_ASSOC));
