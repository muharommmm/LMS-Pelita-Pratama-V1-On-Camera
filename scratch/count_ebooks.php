<?php
$conn = new mysqli('localhost', 'root', '', 'garuda');
$res = $conn->query("SELECT COUNT(*) as c FROM ebooks");
$row = $res->fetch_assoc();
echo "Ebooks count: " . $row['c'] . "\n";
