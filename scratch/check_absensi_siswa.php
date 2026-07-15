<?php
$conn = new mysqli('localhost', 'root', '', 'garuda');
$sql = "SHOW COLUMNS FROM absensi_siswa";
$result = $conn->query($sql);
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "Field: " . $row["Field"] . "\n";
    }
} else {
    echo "Table does not exist or error: " . $conn->error;
}
?>
