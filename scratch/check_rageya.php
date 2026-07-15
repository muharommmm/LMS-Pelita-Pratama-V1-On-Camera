<?php
$conn = new mysqli('localhost', 'root', '', 'garuda');
$sql = "SELECT k.id_kelas FROM master_siswa m LEFT JOIN kelas_siswa k ON m.id_siswa = k.id_siswa WHERE m.id_siswa = 56";
$result = $conn->query($sql);
echo "Rageya id_kelas: " . $result->fetch_assoc()['id_kelas'] . "\n";
?>
