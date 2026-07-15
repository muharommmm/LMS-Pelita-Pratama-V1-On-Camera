<?php
$conn = new mysqli('localhost', 'root', '', 'garuda');
$sql = "SELECT m.id_siswa, m.nama, k.id_kelas FROM master_siswa m LEFT JOIN kelas_siswa k ON m.id_siswa = k.id_siswa LIMIT 5";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
    echo "Siswa: " . $row["nama"]. " - id_kelas: " . $row["id_kelas"] . "\n";
}
?>
