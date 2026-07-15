<?php
$conn = new mysqli('localhost', 'root', '', 'garuda');
$sql = "SELECT m.id_siswa, m.nama, k.id_kelas, m.username FROM master_siswa m LEFT JOIN kelas_siswa k ON m.id_siswa = k.id_siswa WHERE k.id_kelas = 2 LIMIT 5";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
    echo "Siswa: " . $row["nama"]. " - id_kelas: " . $row["id_kelas"] . " - username: " . $row["username"] . "\n";
}
?>
