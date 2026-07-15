<?php
$db = new mysqli('localhost', 'root', '', 'garuda');
if ($db->connect_error) {
    die('Connection failed: ' . $db->connect_error);
}

echo "=== TABEL honor_records ===\n";
$r = $db->query('DESCRIBE honor_records');
if ($r) {
    while ($row = $r->fetch_assoc()) {
        echo $row['Field'] . ' | ' . $row['Type'] . ' | Key=' . $row['Key'] . ' | Null=' . $row['Null'] . "\n";
    }
} else {
    echo "Table NOT FOUND\n";
}

echo "\n=== Cek kolom admin_notes ada? ===\n";
$r2 = $db->query("SHOW COLUMNS FROM honor_records LIKE 'admin_notes'");
echo ($r2 && $r2->num_rows > 0) ? "admin_notes EXISTS\n" : "admin_notes NOT FOUND\n";

$r3 = $db->query("SHOW COLUMNS FROM honor_records LIKE 'catatan'");
echo ($r3 && $r3->num_rows > 0) ? "catatan EXISTS\n" : "catatan NOT FOUND\n";

$db->close();
