<?php
$mysqli = new mysqli("localhost", "root", "", "garuda_cbt");
$result = $mysqli->query("DESCRIBE log_materi;");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
