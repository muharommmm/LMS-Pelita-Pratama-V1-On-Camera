<?php
$conn = new mysqli('localhost', 'root', '', 'garuda');
$sql = "SELECT id_mapel, nama_mapel FROM master_mapel";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row["id_mapel"]. " - Mapel: " . $row["nama_mapel"] . "\n";
}
?>
