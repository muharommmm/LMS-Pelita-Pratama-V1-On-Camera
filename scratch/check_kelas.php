<?php
$conn = new mysqli('localhost', 'root', '', 'garuda');
$sql = "SELECT id_kelas, nama_kelas, level_id FROM master_kelas";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row["id_kelas"]. " - Kelas: " . $row["nama_kelas"] . " - Level: " . $row["level_id"] . "\n";
}
?>
