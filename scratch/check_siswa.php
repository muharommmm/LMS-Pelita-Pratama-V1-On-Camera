<?php
$conn = new mysqli('localhost', 'root', '', 'garuda');
$sql = "SELECT id_siswa, nama, username, kelas_awal FROM master_siswa WHERE id_siswa = 56 OR username='123456' OR username='112233' OR nama LIKE '%tanti%'";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
    echo "Siswa: " . $row["nama"]. " - Kelas: " . $row["kelas_awal"] . " Username: " . $row["username"] . "\n";
}
?>
