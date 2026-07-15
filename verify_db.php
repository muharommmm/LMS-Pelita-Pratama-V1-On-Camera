<?php
$conn = new mysqli('localhost', 'root', '', 'garuda');
echo "=== dashboard_notifications ===\n";
$r = $conn->query('DESCRIBE dashboard_notifications');
if($r) {
    while($row = $r->fetch_assoc()) echo $row['Field'].' | '.$row['Type'].' | '.$row['Key']."\n";
} else {
    echo "ERROR: " . $conn->error . "\n";
}

echo "\n=== Indexes ===\n";
$checks = [
    ['kelas_materi', 'idx_guru_jenis_status'],
    ['chat_messages', 'idx_penerima_read'],
    ['honor_records', 'idx_tutor_status'],
    ['dashboard_notifications', 'idx_notif_user_read'],
];
foreach ($checks as $c) {
    $r2 = $conn->query("SHOW INDEX FROM `{$c[0]}` WHERE Key_name='{$c[1]}'");
    echo "{$c[0]}.{$c[1]}: " . ($r2 && $r2->num_rows > 0 ? "OK ✓" : "NOT FOUND") . "\n";
}
$conn->close();
