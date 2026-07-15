<?php
$db = new mysqli('localhost', 'root', '', 'garuda');
if ($db->connect_error) { die("Koneksi gagal: " . $db->connect_error); }

echo "=== SEMUA TABEL DI DATABASE 'garuda' ===\n\n";
$res = $db->query("SHOW TABLES");
$tables = [];
while ($r = $res->fetch_array()) {
    $tables[] = $r[0];
    // Get row count
    $cnt = $db->query("SELECT COUNT(*) as c FROM `{$r[0]}`");
    $row = $cnt->fetch_assoc();
    echo sprintf("%-40s %s rows\n", $r[0], $row['c']);
}

echo "\n=== TABEL USERS (cek admin) ===\n";
$res = $db->query("SELECT id, username, email, active FROM users ORDER BY id LIMIT 10");
while ($r = $res->fetch_assoc()) {
    echo "ID={$r['id']} | username={$r['username']} | email={$r['email']} | active={$r['active']}\n";
}

echo "\n=== TABEL USERS_GROUPS (cek relasi admin) ===\n";
$res = $db->query("SELECT * FROM users_groups ORDER BY id LIMIT 10");
while ($r = $res->fetch_assoc()) {
    print_r($r);
}

$db->close();
